<?php
session_start();
include 'config.php';

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

/* =========================
   Handle Form Submit
========================= */
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $name    = trim($_POST['name']);
    $phone   = trim($_POST['phone']);
    $address = trim($_POST['address']);

    $stmt = $conn->prepare("
        UPDATE users 
        SET name = ?, phone = ?, address = ?
        WHERE id = ?
    ");
    $stmt->bind_param("sssi", $name, $phone, $address, $user_id);

    if($stmt->execute()){
        // Update session values
        $_SESSION['user_name']    = $name;
        $_SESSION['user_phone']   = $phone;
        $_SESSION['user_address'] = $address;

        header("Location: staffprofile.php");
        exit();
    } else {
        $error = "Something went wrong. Please try again.";
    }
}

/* =========================
   Current User Data
========================= */
$user_name    = $_SESSION['user_name'];
$user_email   = $_SESSION['user_email'];
$user_phone   = $_SESSION['user_phone'];
$user_address = $_SESSION['user_address'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Profile | SwiftParcel</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Poppins',sans-serif;}

body{
    background:linear-gradient(135deg,#2A3EB1,#5563DE);
    min-height:100vh;
    color:#111;
}

.container{
    max-width:600px;
    margin:3rem auto;
    padding:1rem;
}

.card{
    background:#fff;
    padding:2rem;
    border-radius:12px;
    box-shadow:0 10px 25px rgba(0,0,0,0.15);
}

h2{text-align:center;margin-bottom:1.5rem}

.form-group{
    margin-bottom:1.2rem;
}

label{
    display:block;
    font-size:0.85rem;
    margin-bottom:0.4rem;
    color:#374151;
}

input,textarea{
    width:100%;
    padding:0.7rem;
    border-radius:8px;
    border:1px solid #d1d5db;
    font-size:0.95rem;
}

textarea{resize:none}

.actions{
    display:flex;
    justify-content:space-between;
    margin-top:1.5rem;
}

.btn{
    padding:0.7rem 1.5rem;
    border:none;
    border-radius:8px;
    font-weight:600;
    cursor:pointer;
}

.btn-primary{
    background:#2A3EB1;
    color:#fff;
}

.btn-secondary{
    background:#e5e7eb;
}

.error{
    background:#fee2e2;
    color:#991b1b;
    padding:0.7rem;
    border-radius:8px;
    margin-bottom:1rem;
    text-align:center;
}
</style>
</head>

<body>

<?php include 'navbar.php'; ?>

<div class="container">
    <div class="card">
        <h2>Edit Profile</h2>

        <?php if(!empty($error)): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Name</label>
                <input type="text" name="name" required
                       value="<?= htmlspecialchars($user_name) ?>">
            </div>

            <div class="form-group">
                <label>Email (cannot be changed)</label>
                <input type="email" value="<?= htmlspecialchars($user_email) ?>" disabled>
            </div>

            <div class="form-group">
                <label>Phone</label>
                <input type="text" name="phone" required
                       value="<?= htmlspecialchars($user_phone) ?>">
            </div>

            <div class="form-group">
                <label>Address</label>
                <textarea name="address" rows="3" required><?= htmlspecialchars($user_address) ?></textarea>
            </div>

            <div class="actions">
                <button type="button" class="btn btn-secondary"
                        onclick="location.href='staffprofile.php'">
                    Cancel
                </button>
                <button type="submit" class="btn btn-primary">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>


</body>
</html>
