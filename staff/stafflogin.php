<?php
session_start();
include "config.php";

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare(
        "SELECT * FROM deliverystaff WHERE email=? AND password=?"
    );
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $staff = $result->fetch_assoc();

        $_SESSION['role'] = 'staff';
        $_SESSION['staff_id'] = $staff['id'];
        $_SESSION['staff_name'] = $staff['name'];

        header("Location:dashboard.php");
        exit;
    } else {
        $error = "Invalid email or password";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Delivery Staff Login</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500&display=swap" rel="stylesheet">

<style>
body{
    background:#f4f6f9;
    display:flex;
    justify-content:center;
    align-items:center;
    height:100vh;
    font-family:Poppins;
}
.box{
    background:#fff;
    padding:30px;
    width:350px;
    border-radius:12px;
    box-shadow:0 10px 25px rgba(0,0,0,0.1);
}
h2{text-align:center;margin-bottom:20px}
input{
    width:100%;
    padding:12px;
    margin-bottom:15px;
    border-radius:6px;
    border:1px solid #ccc;
}
button{
    width:100%;
    padding:12px;
    background:#4f46e5;
    color:#fff;
    border:none;
    border-radius:6px;
    font-size:16px;
}
.error{
    background:#fee2e2;
    color:#b91c1c;
    padding:10px;
    margin-bottom:10px;
    border-radius:6px;
    text-align:center;
}
</style>
</head>

<body>

<div class="box">
    <h2>Delivery Staff Login</h2>

    <?php if($error): ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>
</div>

</body>
</html>
