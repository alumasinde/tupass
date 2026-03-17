<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found | TuPass</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #f4f6f9;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            text-align: center;
            color: #333;
        }

        .container {
            max-width: 500px;
            padding: 40px 20px;
        }

        .code {
            font-size: 120px;
            font-weight: bold;
            color: #e0e0e0;
            position: relative;
        }

        .code span {
            position: absolute;
            left: 0;
            right: 0;
            top: 35%;
            font-size: 32px;
            color: #4f46e5;
        }

        h2 {
            margin-top: 30px;
            font-size: 28px;
        }

        p {
            margin-top: 15px;
            color: #666;
            line-height: 1.6;
        }

        .buttons {
            margin-top: 30px;
        }

        .btn {
            display: inline-block;
            padding: 10px 18px;
            margin: 8px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
            transition: 0.2s ease;
        }

        .btn-primary {
            background: #4f46e5;
            color: #fff;
        }

        .btn-primary:hover {
            background: #4338ca;
        }

        .btn-secondary {
            background: #fff;
            color: #333;
            border: 1px solid #ccc;
        }

        .btn-secondary:hover {
            background: #f0f0f0;
        }

        footer {
            margin-top: 40px;
            font-size: 13px;
            color: #999;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="code">
            404
            <span>Oops!</span>
        </div>

        <h2>Page Not Found</h2>

        <p>
            The page you are looking for might have been removed,
            renamed, or is temporarily unavailable.
        </p>

        <div class="buttons">
            <a href="javascript:history.back()" class="btn btn-primary">Go Back</a>
            <a href="/" class="btn btn-secondary">Home</a>
        </div>

        <footer>
            © <?= date('Y') ?> TuPass — Gatepass Management System
        </footer>
    </div>

</body>
</html>