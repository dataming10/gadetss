<?php
session_start();
include('includes/config.php');
?>

<!-- HTML CODE -->

<!DOCTYPE html>
<html>
<head>
    <title>Edit Profile</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include('includes/side_navbar.php'); ?>
    <h2>Edit Profile</h2>
    <form id="profileForm" method="post">
        <label>New Username:</label>
        <input type="text" name="username" id="usernameInput">
        <label>New Password:</label>
        <input type="password" name="password" id="passwordInput">
        <label>Current Password:</label>
        <input type="password" name="current_password" id="currentPasswordInput" required><br>
        <button type="button" id="updateProfileButton">Update Profile</button>
    </form>
    <div id="updateResult"></div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="assets/js/index.js"></script>
</body>
</html>
