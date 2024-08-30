  
        $(document).ready(function() {
            $('#fetchForm').on('submit', function(event) {
                event.preventDefault(); 
                $('#message').show(); 

                $.ajax({
                    url: 'fetch.php',
                    type: 'POST',
                    data: $(this).serialize(),
                    xhrFields: {
                        responseType: 'blob'
                    },
                    success: function(data) {
                        var a = document.createElement('a');
                        var url = window.URL.createObjectURL(data);
                        a.href = url;
                        a.download = 'website.zip';
                        document.body.append(a);
                        a.click();
                        window.URL.revokeObjectURL(url);
                        a.remove();
                        $('#message').hide(); 
                    },
                    error: function() {
                        alert('Error fetching the website.');
                        $('#message').hide(); 
                    }
                });
            });
        });
    