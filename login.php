<?php
session_start();
include('includes/config.php');
?>

<!-- HTML CODE -->

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <h2>Login</h2>
    <form id="loginForm" method="post">
        <label>Username:</label>
        <input type="text" name="username" id="username" required><br>
        <label>Password:</label>
        <input type="password" name="password" id="password" required><br>
        <button type="button" id="loginButton">Login</button><br>
        <a href="index.php">Register an account</a>
    </form>
    <div id="message"></div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="assets/js/index.js"></script>
</body>
</html>
