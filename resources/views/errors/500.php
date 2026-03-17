<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>500 - Server Error | TuPass</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        * { margin:0; padding:0; box-sizing:border-box; }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background:#f4f6f9;
            display:flex;
            justify-content:center;
            align-items:center;
            min-height:100vh;
            text-align:center;
            color:#333;
        }

        .container {
            max-width:500px;
            padding:40px 20px;
        }

        .code {
            font-size:120px;
            font-weight:bold;
            color:#e0e0e0;
            position:relative;
        }

        .code span {
            position:absolute;
            left:0;
            right:0;
            top:35%;
            font-size:28px;
            color:#f59e0b;
        }

        h2 { margin-top:30px; font-size:28px; }

        p {
            margin-top:15px;
            color:#666;
            line-height:1.6;
        }

        .buttons { margin-top:30px; }

        .btn {
            display:inline-block;
            padding:10px 18px;
            margin:8px;
            border-radius:6px;
            text-decoration:none;
            font-size:14px;
            transition:0.2s ease;
        }

        .btn-primary {
            background:#4f46e5;
            color:#fff;
        }

        .btn-primary:hover { background:#4338ca; }

        footer {
            margin-top:40px;
            font-size:13px;
            color:#999;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="code">
        500
        <span>Server Error</span>
    </div>

    <h2>Something Went Wrong</h2>

    <p>
        An unexpected error occurred on the server.
        Please try again later or contact support if the problem persists.
    </p>

    <div class="buttons">
        <a href="/dashboard" class="btn btn-primary">Back to Dashboard</a>
    </div>

    <footer>
        © <?= date('Y') ?> TuPass — Gatepass Management System
    </footer>
</div>

</body>
</html>