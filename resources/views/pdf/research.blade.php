<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Research PDF</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            line-height: 1.6;
        }
        h1, h2, h3, h4, h5, h6 {
            color: #333;
            margin-bottom: 10px;
        }
        h1 {
            font-size: 2em;
        }
        h2 {
            font-size: 1.75em;
        }
        h3 {
            font-size: 1.5em;
        }
        p {
            margin-bottom: 15px;
        }
        a {
            color: #0066cc;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        strong {
            font-weight: bold;
        }
        em {
            font-style: italic;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="container">
        {!! $content !!}
    </div>
</body>
</html>