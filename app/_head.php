<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $_title ?? 'Untitled' ?></title>
    <link rel="shortcut icon" href="/images/favicon.png">
    <link rel="stylesheet" href="/css/app.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="/js/app.js"></script>
</head>
<body>
    <!-- Flash message -->
    <div id="info"><?= temp('info') ?></div>

    <header>
        <h1><a href="/">Database Programming</a></h1>
    </header>

    <nav>
        <a href="/logout.php">Logout</a>
        <a href="/adminLogin.php">admin Login</a>
        <a href="/adminLogout.php">admin Logout</a>
    </nav>

    <main>
        <h1><?= $_title ?? 'Untitled' ?></h1>