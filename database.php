<?php
$hostname = "localhost";
$username = "root";
$password = "";
$dbname = "perpustakaan";

// Membuat Koneksi
$conn = mysqli_connect($hostname, $username, $password, $dbname);

// Cek Koneksi
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}


mysqli_set_charset($conn, "utf8");
?>
