<?php
session_start();
include "db.php";

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM admin WHERE username=? AND password=?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {

        // ✅ REQUIRED SESSION VALUES
        $_SESSION['role'] = 'admin';
        $_SESSION['admin'] = $username;

        header("Location: admin.php");
        exit;
    } else {
        $error = "Invalid username or password";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Admin Login</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500&display=swap" rel="stylesheet">
<style>
body{
    background:#1e1e2f;
    display:flex;
    justify-content:center;
    align-items:center;
    height:100vh;
    font-family:Poppins;
}
.box{
    background:#fff;
    padding:30px;
    width:340px;
    border-radius:12px;
}
h2{text-align:center;margin-bottom:20px}
input{
    width:100%;
    padding:12px;
    margin-bottom:15px;
    border:1px solid #ccc;
    border-radius:6px;
}
button{
    width:100%;
    padding:12px;
    background:#4f46e5;
    border:none;
    color:#fff;
    border-radius:6px;
    font-size:16px;
}
.error{
    background:#fee2e2;
    color:#b91c1c;
    padding:10px;
    margin-bottom:10px;
    text-align:center;
    border-radius:6px;
}
</style>
</head>
<body>

<div class="box">
    <h2>Admin Login</h2>

    <?php if($error): ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>
</div>

</body>
</html>
