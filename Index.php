
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Website Fetcher</title>
    <link rel="stylesheet" href="styles/main.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <form id="fetchForm" class="form">
            <div class="header">
      <h2>WebHarvest</h2>
<p>Enter the URL of a website to download its entire structure, including HTML files, images, CSS, JavaScript, and other assets.</p>
    </div>
    </div>
      <div class="input_field">
        <label for="url">Enter Website URL:</label>
        <input type="text" id="url" name="url" required></div>
        <button type="submit" class="log">Fetch Source Code</button>
    </form>
    
    <div id="message" class="message" style="display:none;">Harvesting Website please wait</div>

  <script src="script.js"></script>
</body>
</html>
