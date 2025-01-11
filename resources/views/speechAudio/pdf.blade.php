<!DOCTYPE html>
<html>
<head>
    <title>Text Content</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            color: #2d3748;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 10px;
        }
        .header h1 {
            font-size: 2rem;
            font-weight: 700;
            color: #2d3748;
        }
        .content {
            margin-bottom: 20px;
            line-height: 1.6;
            font-style: italic;
        }
        .content p {
            margin-bottom: 10px;
        }
        .language {
            font-style: italic;
            color: #4a5568;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            border-top: 2px solid #e2e8f0;
            padding-top: 10px;
            font-size: 0.875rem;
            color: #718096;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Read the Content</h1>
    </div>
    <div class="content">
        <p>{{ $text }}</p>
    </div>
    <div class="footer">
        <p>&copy; {{ date('Y') }} All rights reserved.</p>
    </div>
</body>
</html>
