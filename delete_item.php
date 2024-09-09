<?php
session_start();
include 'database.php';

$item_id = $item_name = "";
$errors = [];
$success = false;

// Get item details if ID is set
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $item_id = (int) $_GET['id'];
    
    $sql = "SELECT item_name FROM inventory WHERE id = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $item_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $item_name);
        if (mysqli_stmt_fetch($stmt)) {
            // Success
        } else {
            $errors[] = "Item tidak ditemukan.";
        }
        mysqli_stmt_close($stmt);
    } else {
        $errors[] = "Error preparing statement: " . mysqli_error($conn);
    }
} else {
    $errors[] = "ID item tidak valid.";
}

// Handle deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['confirm']) && $_POST['confirm'] === 'yes') {
    $sql = "DELETE FROM inventory WHERE id = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $item_id);
        if (mysqli_stmt_execute($stmt)) {
            $success = true;
        } else {
            $errors[] = "Error executing query: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    } else {
        $errors[] = "Error preparing statement: " . mysqli_error($conn);
    }
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hapus Item</title>
    <link rel="stylesheet" href="css/styles.css">
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
    <header>
        <h1>Inventory Manager</h1>
        <nav>
            <a href="dashboard.php">Dashboard</a>
            <a href="inventory.php">Inventory</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <div class="container">
        <h1>Hapus Item</h1>

        <?php if (!empty($errors)): ?>
            <div class="errors">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form action="delete_item.php?id=<?php echo htmlspecialchars($item_id); ?>" method="post">
            <p>Apakah Anda yakin ingin menghapus item dengan nama "<strong><?php echo htmlspecialchars($item_name); ?></strong>"?</p>
            <button type="submit" name="confirm" value="yes">Hapus</button>
            <button type="button" onclick="window.location.href='inventory.php'">Batal</button>
        </form>
    </div>

    <?php if ($success): ?>
    <div class="popup-overlay" id="popup-overlay">
        <div class="popup">
            <span class="popup-close" onclick="hidePopup()">&times;</span>
            <h3>Item Berhasil Dihapus</h3>
            <p>Item dengan nama "<strong><?php echo htmlspecialchars($item_name); ?></strong>" berhasil dihapus dari inventaris.</p>
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
            window.location.href = 'dashboard.php';
        }

        <?php if ($success): ?>
        window.onload = function() {
            showPopup();
        };
        <?php endif; ?>
    </script>
</body>
</html>
