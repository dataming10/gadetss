<?php
include('includes/config.php');
?>

<!DOCTYPE html>
<html>
<head>
    <title>Registration</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <h2>Registration</h2>
    <form id="registrationForm" method="post">
        <label>Username:</label>
        <input type="text" name="username" id="username" required><br>
        <label>Password:</label>
        <input type="password" name="password" id="password" required><br>
        <button type="button" id="registerButton">Register</button><br>
        <a href="login.php">Already have an account?</a>
    </form>
    <div id="message"></div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="assets/js/index.js"></script>
</body>
</html>
