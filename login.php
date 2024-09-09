<?php
include "database.php";
session_start();

$loginMessage = "";

if (isset($_POST['logincuy'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // Use SQL inject
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
    
    // Check persiapan
    if ($stmt === false) {
        die("Error preparing statement: " . htmlspecialchars($conn->error));
    }

    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        $_SESSION["username"] = $data["username"];
        $_SESSION["is_login"] = true;
        
        // WAKTU POP UP
        $_SESSION["show_popup"] = true;
        
        header("location: dashboard.php");
        exit;
    } else {
        $loginMessage = "Akun gagal ditemukan";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>LOGIN FORM</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <i><?= htmlspecialchars($loginMessage, ENT_QUOTES, 'UTF-8') ?></i>
    <form autocomplete="off" class="form" action="login.php" method="POST">
        <div class="control">
            <h1>Sign In</h1>
        </div>
        <div class="control block-cube block-input">
            <input name="username" placeholder="Username" type="text" required>
            <div class="bg-top">
                <div class="bg-inner"></div>
            </div>
            <div class="bg">
                <div class="bg-inner"></div>
            </div>
        </div>
        <div class="control block-cube block-input">
            <input name="password" placeholder="Password" type="password" required>
            <div class="bg-top">
                <div class="bg-inner"></div>
            </div>
            <div class="bg-right">
                <div class="bg-inner"></div>
            </div>
            <div class="bg">
                <div class="bg-inner"></div>
            </div> 
        </div>
        <button class="btn block-cube block-cube-hover" type="submit" name="logincuy">
            <div class="bg-top">
                <div class="bg-inner"></div>
            </div>
            <div class="bg-right">
                <div class="bg-inner"></div>
            </div>
            <div class="text">Log in</div>
        </button>
        <h3>Belum punya akun?</h3>
        <a href="regis.php">Register</a>
    </form>
</body>
</html>
