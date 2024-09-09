<?php
session_start();
include 'database.php'; // Include the database connection

// Initialize variables
$item_name = "";
$quantity = 0;
$errors = [];
$success = false;

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate input
    if (isset($_POST['item_name']) && !empty(trim($_POST['item_name']))) {
        $item_name = mysqli_real_escape_string($conn, trim($_POST['item_name']));
    } else {
        $errors[] = "Nama item diperlukan.";
    }

    if (isset($_POST['quantity']) && is_numeric($_POST['quantity']) && $_POST['quantity'] >= 0) {
        $quantity = (int) $_POST['quantity'];
    } else {
        $errors[] = "Kuantitas harus berupa angka non-negatif.";
    }

    // Check if there are no errors
    if (empty($errors)) {
        // Prepare SQL statement to insert item
        $sql = "INSERT INTO inventory (item_name, quantity) VALUES (?, ?)";

        // Prepare and bind
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "si", $item_name, $quantity);

            // Execute the statement
            if (mysqli_stmt_execute($stmt)) {
                $success = true; // Set success flag
            } else {
                $errors[] = "Error executing query: " . mysqli_error($conn);
            }

            // Close statement
            mysqli_stmt_close($stmt);
        } else {
            $errors[] = "Error preparing statement: " . mysqli_error($conn);
        }
    }

    // Close connection
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Item</title>
    <link rel="stylesheet" href="css/styles.css">
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
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
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
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-bottom: 15px;
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
            transition: background 0.3s ease;
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
            margin-right: 10px;
        }

        .popup-button:hover {
            background: #0056b3;
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
    </style>
</head>
<body>
    <header>
        <h1>Inventory Manager</h1>
        <nav>
            <a href="dashboard.php">Dashboard</a>
            <a href="inventory.php">Inventory</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <div class="container">
        <h1>Tambah Item Baru</h1>

        <?php if (!empty($errors)): ?>
            <div class="errors">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form action="add_item.php" method="post">
            <label for="item_name">Nama Item:</label>
            <input type="text" id="item_name" name="item_name" value="<?php echo htmlspecialchars($item_name); ?>" required>

            <label for="quantity">Kuantitas:</label>
            <input type="number" id="quantity" name="quantity" value="<?php echo htmlspecialchars($quantity); ?>" required>

            <button type="submit">Tambah Item</button>
        </form>
    </div>

    <!-- Popup -->
    <?php if ($success): ?>
    <div class="popup-overlay" id="popup-overlay">
        <div class="popup">
            <span class="popup-close" onclick="hidePopup()">&times;</span>
            <h3>Item Berhasil Ditambahkan</h3>
            <p>Item dengan nama "<strong><?php echo htmlspecialchars($item_name); ?></strong>" dan kuantitas "<strong><?php echo htmlspecialchars($quantity); ?></strong>" berhasil ditambahkan.</p>
            <button class="popup-button" onclick="redirect()">OK</button>
            <button class="popup-button" onclick="hidePopup()">Tidak</button>
        </div>
    </div>
    <?php endif; ?>

    <script>
        function showPopup() {
            document.getElementById('popup-overlay').style.display = 'flex';
        }

        function hidePopup() {
            document.getElementById('popup-overlay').style.display = 'none';
        }

        function redirect() {
            window.location.href = 'dashboard.php'; // Redirect to dashboard after clicking OK
        }

        // Show popup if successful
        <?php if ($success): ?>
        window.onload = function() {
            showPopup();
        };
        <?php endif; ?>
    </script>
</body>
</html>
