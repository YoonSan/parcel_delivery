<?php
session_start();
include 'config.php';
include 'navbar.php';

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$user_name    = $_SESSION['user_name'];
$user_email   = $_SESSION['user_email'];
$user_phone   = $_SESSION['user_phone'];
$user_address = $_SESSION['user_address'];
?>

<style>
.profile-container{
    max-width:900px;
    margin:20px auto;
    background:#fff;
    padding:30px;
    border-radius:12px;
    box-shadow:0 4px 12px rgba(0,0,0,0.08);
}

.profile-title{
    font-size:24px;
    font-weight:600;
    margin-bottom:20px;
    color:#2C3E50;
}

.profile-grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:20px;
}

.profile-item label{
    font-size:13px;
    color:#777;
}

.profile-item span{
    font-size:15px;
    font-weight:500;
}

.full{
    grid-column:1 / -1;
}

.actions{
    margin-top:25px;
    display:flex;
    gap:15px;
}

.btn{
    padding:10px 18px;
    border:none;
    border-radius:8px;
    cursor:pointer;
    font-weight:600;
}

.btn-primary{
    background:#4f46e5;
    color:#fff;
}

.btn-secondary{
    background:#e5e7eb;
}

@media(max-width:700px){
    .profile-grid{grid-template-columns:1fr;}
}
</style>


<!-- RIGHT SIDE CONTENT (sidebar comes from navbar.php) -->
<div class="main">

    <div class="profile-container">

        <div class="profile-title">My Profile</div>

        <div class="profile-grid">

            <div class="profile-item">
                <label>Name</label>
                <span><?= htmlspecialchars($user_name) ?></span>
            </div>

            <div class="profile-item">
                <label>Email</label>
                <span><?= htmlspecialchars($user_email) ?></span>
            </div>

            <div class="profile-item">
                <label>Phone</label>
                <span><?= htmlspecialchars($user_phone) ?></span>
            </div>

            <div class="profile-item full">
                <label>Address</label>
                <span><?= htmlspecialchars($user_address) ?></span>
            </div>

        </div>

        <div class="actions">
            <button class="btn btn-primary" onclick="location.href='editprofile.php'">
                Edit Profile
            </button>

            <button class="btn btn-secondary" onclick="location.href='../logout.php'">
                Logout
            </button>
        </div>

    </div>

</div> <!-- end main -->
</div> <!-- end container from navbar -->
