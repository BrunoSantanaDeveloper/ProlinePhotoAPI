<html>
<head>
    <title>API Documentation</title>
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/swagger-ui-dist/swagger-ui.css">
</head>
<body>
    <div id="swagger-ui"></div>
    <script src="https://unpkg.com/swagger-ui-dist/swagger-ui-bundle.js"></script>
    <script>
        const ui = SwaggerUIBundle({
            url: "{{ $yamlPath }}",
            dom_id: '#swagger-ui',
        });
    </script>
</body>
</html>
