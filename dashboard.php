<?php
session_start();
include 'database.php'; // Pastikan file ini memuat koneksi ke database

// Check if the user is logged in
if (!isset($_SESSION['is_login']) || $_SESSION['is_login'] !== true) {
    header("Location: login.php");
    exit;
}

// Check if the popup should be shown
$showPopup = isset($_SESSION['show_popup']) && $_SESSION['show_popup'] === true;
if ($showPopup) {
    // Unset the session variable so the popup doesn't show again
    unset($_SESSION['show_popup']);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Inventory Manager</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
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

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
    background: #ffffff;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

header {
    background: #007bff;
    color: #fff;
    padding: 20px;
    border-radius: 8px 8px 0 0;
}

header h1 {
    font-size: 2rem;
    margin-bottom: 10px;
}

nav {
    display: flex;
    justify-content: flex-end;
    margin-top: -40px; 
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

.dashboard-content {
    margin-top: 20px;
}

.dashboard-content h2 {
    font-size: 1.5rem;
    color: #007bff;
    margin-bottom: 15px;
}

.dashboard-content p {
    font-size: 1rem;
    color: #555;
}

.table-container {
    margin-top: 20px;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

table, th, td {
    border: 1px solid #ddd;
}

th, td {
    padding: 10px;
    text-align: left;
}

th {
    background: #f8f9fa;
    color: #333;
}

td {
    background: #ffffff;
    color: #333;
}

td a {
    color: #007bff;
    text-decoration: none;
    transition: color 0.3s ease;
}

td a:hover {
    color: #ffc107;
}

.btn {
    display: inline-block;
    padding: 10px 20px;
    color: #fff;
    background: red;
    border: none;
    border-radius: 4px;
    text-decoration: none;
    font-size: 1rem;
    font-weight: bold;
    transition: background 0.3s ease;
}

.btn:hover { /*Button Add Item Warna*/
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
    display: block;
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
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container">
        <header>
            <h1>Inventory Manager</h1>
            <nav>
                <a href="dashboard.php">Dashboard</a>
                <a href="inventory.php">Inventory</a>
                <a href="buku.php">buku</a>
                <a href="logout.php">Logout</a>
            </nav>
        </header>

        <div class="dashboard-content">
            <h2>Selamat Datang, <?php echo htmlspecialchars($_SESSION['username'] ?? 'Pengguna'); ?>!</h2>
            <p>Ini adalah dashboard Anda. Gunakan navigasi untuk mengelola inventaris Anda. Untuk menambah item baru atau mengedit item yang ada, navigasikan ke bagian Inventaris. Anda juga dapat melihat dan menghapus item sesuai kebutuhan.</p>
            <button class="btn" onclick="showPopup()">Informasi & Panduan</button>

            <!-- Inventory Table -->
            <div class="table-container">
                <h2>Inventaris</h2>
                <a href="add_item.php" class="btn">Tambah Item Baru</a>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama Item</th>
                            <th>Kuantitas</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (isset($conn)) {
                            $sql = "SELECT * FROM inventory";
                            $result = $conn->query($sql);

                            if ($result === false) {
                                echo "<tr><td colspan='4'>Error executing query: " . htmlspecialchars($conn-> Error) . "</td></tr>";
                            } elseif ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($row["id"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["item_name"], ENT_QUOTES, 'UTF-8') . "</td>";
                                    echo "<td>" . htmlspecialchars($row["quantity"]) . "</td>";
                                    echo "<td><a href='edit_item.php?id=" . htmlspecialchars($row["id"]) . "'>Edit</a> | <a href='delete_item.php?id=" . htmlspecialchars($row["id"]) . "'>Hapus</a></td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='4'>Tidak ada item ditemukan</td></tr>";
                            }

                            $conn->close();
                        } else {
                            echo "<tr><td colspan='4'>Koneksi database tidak terhubung</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <footer>
            <p>&copy; RizzX</p>
        </footer>
    </div>

    <!-- Popup -->
    <div class="popup-overlay" id="popup-overlay" style="<?= $showPopup ? 'display: flex;' : 'display: none;' ?>">
        <div class="popup">
            <span class="popup-close" onclick="hidePopup()">&times;</span>
            <h3>Selamat Datang di Dashboard</h3>
            <p>Berikut adalah panduan singkat untuk menggunakan dashboard:</p>
            <ul>
                <li><strong>Tambah Item Baru:</strong> Klik 'Tambah Item Baru' untuk memasukkan item baru ke dalam inventaris.</li>
                <li><strong>Edit Item:</strong> Klik 'Edit' di samping item untuk memodifikasi detailnya.</li>
                <li><strong>Hapus Item:</strong> Klik 'Hapus' untuk menghapus item dari inventaris.</li>
                <li><strong>Informasi:</strong> Klik tombol ini untuk menutup panduan.</li>
            </ul>
            <button class="popup-button" onclick="hidePopup()">Terima Kasih</button>
        </div>
    </div>

    <script>
        function showPopup() {
            document.getElementById('popup-overlay').style.display = 'flex';
        }

        function hidePopup() {
            document.getElementById('popup-overlay').style.display = 'none';
        }

        // Optionally, auto-show the popup on page load
        <?php if ($showPopup) : ?>
            window.onload = function() {
                showPopup();
            };
        <?php endif; ?>
    </script>
</body>
</html>
