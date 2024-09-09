<?php
session_start();

// Redirect if not logged in
if (!isset($_SESSION['is_login']) || $_SESSION['is_login'] !== true) {
    header("Location: login.php");
    exit;
}

// Include database connection
include "database.php"; // Sesuaikan path ini

// Initialize error message variable
$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $item_name = $_POST['item_name'];
    $quantity = $_POST['quantity'];

    // Prepare and execute the statement
    if (isset($conn)) {
        $stmt = $conn->prepare("INSERT INTO inventory (item_name, quantity) VALUES (?, ?)");
        if ($stmt === false) {
            $error_message = "Prepare failed: " . $conn->error;
        } else {
            $stmt->bind_param("si", $item_name, $quantity);

            if ($stmt->execute()) {
                header("Location: inventory.php");
                exit;
            } else {
                $error_message = "Execute failed: " . $stmt->error;
            }

            $stmt->close();
        }
    } else {
        $error_message = "Database connection not established";
    }
}

// Close the connection
if (isset($conn)) {
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Item</title>
    <link rel="stylesheet" href="../css/styles.css"> <!-- Sesuaikan path jika perlu -->
    <style>
        body {
            background-color: #f4f4f4;
            font-family: Arial, sans-serif;
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .container h1 {
            margin-bottom: 20px;
            color: #333;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        .error {
            color: #d9534f;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Add New Item</h1>
        <?php if (!empty($error_message)) { echo "<p class='error'>" . htmlspecialchars($error_message) . "</p>"; } ?>
        <form method="post" action="add_item.php">
            <div class="form-group">
                <label for="item_name">Item Name</label>
                <input type="text" id="item_name" name="item_name" required>
            </div>
            <div class="form-group">
                <label for="quantity">Quantity</label>
                <input type="number" id="quantity" name="quantity" required>
            </div>
            <button type="submit" class="btn">Add Item</button>
        </form>
    </div>
</body>
</html>
