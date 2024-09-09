<?php
include "database.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Hash the password for security
    $whatsapp_number = $_POST['whatsapp_number'];
    
    // Check if username already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $error_message = "Username already exists.";
    } else {
        // Insert new user
        $stmt = $conn->prepare("INSERT INTO users (username, password, whatsapp_number) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $password, $whatsapp_number);

        if ($stmt->execute()) {
            header("Location: login.php"); // Redirect to login page upon successful registration
            exit;
        } else {
            $error_message = "Error: " . $conn->error;
        }
    }

    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
        <style>
    /* Basic reset */
    body, h1, h2, p {
        margin: 0;
        padding: 0;
    }

    body {
        font-family: 'Arial', sans-serif;
        background-color: #f4f4f4;
        color: #333;
        line-height: 1.6;
    }

    header {
        background: #007bff;
        color: #fff;
        padding: 20px;
        text-align: center;
        border-radius: 8px 8px 0 0;
    }

    header h1 {
        font-size: 2rem;
        margin-bottom: 10px;
    }

    nav {
        margin: 20px 0;
    }

    nav a {
        color: #fff;
        text-decoration: none;
        margin: 0 15px;
        font-size: 1rem;
        transition: color 0.3s ease;
    }

    nav a:hover {
        color: #ffc107;
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
        background: #ffffff;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .errors {
        margin-bottom: 20px;
        padding: 10px;
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
        border-radius: 4px;
    }

    .errors p {
        margin: 0;
    }

    form {
        margin-top: 20px;
    }

    label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
    }

    input[type="text"], input[type="number"] {
        width: 100%;
        padding: 10px;
        margin-bottom: 20px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }

    button {
        padding: 10px 20px;
        color: #fff;
        background: #007bff;
        border: none;
        border-radius: 4px;
        font-size: 1rem;
        font-weight: bold;
        cursor: pointer;
        margin-right: 10px;
    }

    button:hover {
        background: #0056b3;
    }

    footer {
        background: #007bff;
        color: #fff;
        padding: 10px;
        text-align: center;
        border-radius: 0 0 8px 8px;
        margin-top: 20px;
    }

    footer p {
        margin: 0;
    }

    /* Popup styles */
    .popup-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: none;
        align-items: center;
        justify-content: center;
    }

    .popup {
        background: #fff;
        border-radius: 8px;
        padding: 20px;
        max-width: 500px;
        width: 100%;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        position: relative;
    }

    .popup h3 {
        margin-top: 0;
        font-size: 1.25rem;
        color: #007bff;
    }

    .popup p {
        font-size: 1rem;
        color: #555;
    }

    .popup ul {
        margin: 10px 0;
        padding-left: 20px;
    }

    .popup ul li {
        margin-bottom: 10px;
    }

    .popup-close {
        position: absolute;
        top: 10px;
        right: 10px;
        font-size: 1.5rem;
        color: #333;
        cursor: pointer;
    }

    .popup-close:hover {
        color: #007bff;
    }

    .popup-button {
        display: inline-block;
        padding: 10px 20px;
        background: #007bff;
        color: #fff;
        border: none;
        border-radius: 4px;
        font-size: 1rem;
        font-weight: bold;
        cursor: pointer;
        margin-top: 10px;
        text-align: center;
        text-decoration: none;
    }

    .popup-button:hover {
        background: #0056b3;
    }
</style>

    </style>
</head>
<body>
<?php include "includes/header.php"; ?>

<div class="container">
    <h1>Register</h1>
    <?php if (!empty($error_message)) { echo "<p class='error'>" . $error_message . "</p>"; } ?>
    <form method="post" action="register.php">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>
        <div class="form-group">
            <label for="whatsapp_number">WhatsApp Number</label>
            <input type="text" id="whatsapp_number" name="whatsapp_number" required>
        </div>
        <button type="submit" class="btn">Register</button>
    </form>
</div>
</body>
