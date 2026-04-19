<?php
session_start();
include 'config.php';
include 'navbar.php';

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
        $_SESSION['user_name']    = $name;
        $_SESSION['user_phone']   = $phone;
        $_SESSION['user_address'] = $address;

        header("Location: profile.php");
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

<style>
.main{
    flex:1;
    padding:25px;
    background:#f4f6f8;
}

.profile-container{
    max-width:700px;
    margin:0 auto;
    background:#fff;
    padding:25px;
    border-radius:12px;
    box-shadow:0 4px 12px rgba(0,0,0,0.08);
}

.profile-container h2{
    text-align:center;
    margin-bottom:20px;
    color:#2C3E50;
}

.form-group{
    margin-bottom:15px;
}

label{
    display:block;
    font-size:14px;
    color:#555;
    margin-bottom:5px;
}

input,textarea{
    width:100%;
    padding:10px;
    border-radius:8px;
    border:1px solid #ccc;
    font-size:14px;
}

textarea{resize:none}

.actions{
    display:flex;
    justify-content:flex-end;
    gap:15px;
    margin-top:15px;
}

.btn{
    padding:10px 18px;
    border:none;
    border-radius:8px;
    cursor:pointer;
    font-weight:600;
}

.btn-primary{background:#4f46e5;color:#fff;}
.btn-secondary{background:#e5e7eb;}

.error{
    background:#fee2e2;
    color:#991b1b;
    padding:10px;
    border-radius:8px;
    margin-bottom:15px;
    text-align:center;
}
</style>

<div class="main">
    <div class="profile-container">
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
                        onclick="location.href='profile.php'">
                    Cancel
                </button>
                <button type="submit" class="btn btn-primary">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
