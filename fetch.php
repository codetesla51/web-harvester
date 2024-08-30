<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $url = $_POST['url'];
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        die("Invalid URL");
    }
    $html = file_get_contents($url);
    if ($html === FALSE) {
        die("Failed to fetch the website content.");
    }
    // Create a temporary directory
    $outputDir = 'temp_output_' . uniqid();
    mkdir($outputDir, 0755, true);

    // Save html content
    $htmlFile = $outputDir . '/index.html';
    file_put_contents($htmlFile, $html);
    $dom = new DOMDocument();
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);
    // Base URL for relative paths
    $baseUrl = get_base_url($url);
    // Fetch and save images
    $images = $xpath->query("//img/@src");
    foreach ($images as $img) {
        $imgUrl = resolve_url($baseUrl, $img->value);
        save_asset($imgUrl, $outputDir);
    }

    // Fetch and save CSS files
    $stylesheets = $xpath->query("//link[@rel='stylesheet']/@href");
    foreach ($stylesheets as $stylesheet) {
        $cssUrl = resolve_url($baseUrl, $stylesheet->value);
        save_asset($cssUrl, $outputDir);
    }
    // Fetch and save JS files
    $scripts = $xpath->query("//script/@src");
    foreach ($scripts as $script) {
        $jsUrl = resolve_url($baseUrl, $script->value);
        save_asset($jsUrl, $outputDir);
    }
    // Create a zip file
    $zipFile = 'website_' . uniqid() . '.zip';
    create_zip($outputDir, $zipFile);
    // Remove the temporary output directory
    delete_directory($outputDir);
    // Send  zip file to the browser for download
    header('Content-Type: application/zip');
    header('Content-disposition: attachment; filename=' . basename($zipFile));
    header('Content-Length: ' . filesize($zipFile));
    readfile($zipFile);
    // Delete  zip file after download
    unlink($zipFile);
}
function get_base_url($url) {
    $parsedUrl = parse_url($url);
    return $parsedUrl['scheme'] . '://' . $parsedUrl['host'] . rtrim(dirname($parsedUrl['path']), '/') . '/';
}
function resolve_url($baseUrl, $relativeUrl) {
    // If the URL is absloute
    if (parse_url($relativeUrl, PHP_URL_SCHEME) != '') {
        return $relativeUrl;
    }
    // else, combine with the base URL
    return $baseUrl . ltrim($relativeUrl, '/');
}
function save_asset($url, $outputDir) {
    $content = @file_get_contents($url);
    if ($content === FALSE) {
        return;
    }
    $parsedUrl = parse_url($url);
    $pathParts = pathinfo($parsedUrl['path']);

    
    $assetDir = $outputDir . '/' . $pathParts['dirname'];
    if (!is_dir($assetDir)) {
        mkdir($assetDir, 0755, true);
    }

    // Save  file
    $filename = $pathParts['basename'];
    file_put_contents($assetDir . '/' . $filename, $content);
}

function create_zip($sourceDir, $zipFile) {
    $zip = new ZipArchive();
    if ($zip->open($zipFile, ZipArchive::CREATE) === TRUE) {
        $sourceDir = realpath($sourceDir);
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($sourceDir), RecursiveIteratorIterator::LEAVES_ONLY);
        foreach ($files as $name => $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($sourceDir) + 1);
                $zip->addFile($filePath, $relativePath);
            }
        }
        $zip->close();
    } else {
        die("Failed to create ZIP file.");
    }
}

function delete_directory($dir) {
    if (!is_dir($dir)) {
        return;
    }
    $files = array_diff(scandir($dir), array('.', '..'));
    foreach ($files as $file) {
        (is_dir("$dir/$file")) ? delete_directory("$dir/$file") : unlink("$dir/$file");
    }
    rmdir($dir);
}
?>
