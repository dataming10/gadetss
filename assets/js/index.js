//FETCHING ITEMS

$(document).ready(function() {
    // Initialize DataTable for itemsTable
    $('#itemsTable').DataTable({
        "ajax": "fetch_items.php",
        "columns": [
            { "data": "product_num" },
            { "data": "name" },
            { "data": "quantity" },
            { "data": "image" },
            { "data": "actions" },
        ]
    });
});

// ADDING ITEMS

$(document).ready(function() {
    $('#addItemForm').submit(function(e) {
        e.preventDefault();
        var formData = new FormData(this);

        $.ajax({
            url: 'add_item.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            contentType: false,
            processData: false,
            success: function(response) {
                if (response.success) {
                    $('#response').html('<div class="message">' + response.message + '</div>');
                } else {
                    $('#response').html('<div class="error">' + response.error + '</div>');
                }
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    });
});

// FETCHING USERS

$(document).ready(function() {
    // Initialize DataTable for itemsTable
    $('#usersTable').DataTable({
        "ajax": "fetch_users.php",
        "columns": [
            { "data": "username" },
            { "data": "status" },
            { "data": "is_admin" },
            { "data": "actions" },
        ]
    });
});

//DEACTIVATED ITEMS

$(document).ready(function() {
    $('#deactivatedItemsTable').DataTable({
        "ajax": {
            "url": "fetch_deactivate.php",
            "type": "POST", // Use POST method to ensure proper authentication
            "dataType": "json"
        },
        "columns": [
            { "data": "product_num" },
            { "data": "name" },
            { "data": "quantity" },
            { "data": "image" },
            { "data": "actions" }
        ]
    });
});

//REGISTERING USERS

$(document).ready(function() {
    $('#registerButton').click(function() {
        var username = $('#username').val();
        var password = $('#password').val();

        $.ajax({
            type: 'POST',
            url: 'fetch_register.php',
            data: {
                username: username,
                password: password
            },
            success: function(response) {
                $('#message').html(response);
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    });
});

//LOGGING ACCOUNTS

$(document).ready(function() {
    $('#loginButton').click(function() {
        var username = $('#username').val();
        var password = $('#password').val();

        $.ajax({
            type: 'POST',
            url: 'fetch_login.php',
            data: {
                username: username,
                password: password
            },
            success: function(response) {
                $('#message').html(response);
                if (response.indexOf('successful') !== -1) {
                    // Redirect to dashboard if login is successful
                    window.location.href = 'dashboard.php';
                }
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    });
});

//UPDATING PROFILE ACCOUNT

$(document).ready(function() {
    $('#updateProfileButton').click(function() {
        var username = $('#usernameInput').val();
        var password = $('#passwordInput').val();
        var currentPassword = $('#currentPasswordInput').val();

        $.ajax({
            type: 'POST',
            url: 'fetch_update_profile.php',
            data: {
                username: username,
                password: password,
                current_password: currentPassword
            },
            success: function(response) {
                $('#updateResult').html(response);
                if (response.indexOf('successfully') !== -1) {
                    // Optionally, reload page or perform any additional actions upon successful update
                }
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    });
});

//EDITING ITEM
