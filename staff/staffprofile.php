<?php
session_start();
include 'config.php';

if(!isset($_SESSION['role']) || $_SESSION['role'] != 'staff'){
    header("Location: login.php");
    exit();
}

$staff_name = $_SESSION['staff_name'] ?? 'Staff';
$staff_email = $_SESSION['staff_email'] ?? 'example@email.com';
$staff_id = $_SESSION['staff_id'] ?? 'ID123';

include 'navbar.php';
?>

<div class="container" style="padding:30px; display:flex; justify-content:center; align-items:center; min-height:80vh;">
    <div class="profile-card">
        <!-- Avatar -->
        <div class="avatar">
            <i class="fas fa-user-circle"></i>
        </div>

        <!-- Name -->
        <h3><?= htmlspecialchars($staff_name) ?></h3>

        <!-- Staff ID -->
        <p><strong>Staff ID:</strong> <?= htmlspecialchars($staff_id) ?></p>

        <!-- Email -->
        <p><strong>Email:</strong> <?= htmlspecialchars($staff_email) ?></p>

        <!-- Buttons -->
        <div class="buttons">
            <a href="editprofile.php" class="btn btn-primary"><i class="fas fa-user"></i> EditProfile</a>
            <a href="index.php" class="btn btn-secondary"><i class="fas fa-home"></i> Customer</a>
            <a href="logout.php" class="btn btn-danger"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>
</div>

<style>
/* Container and card */
.profile-card {
    background:#fff;
    padding:35px;
    border-radius:15px;
    box-shadow:0 8px 25px rgba(0,0,0,0.15);
    text-align:center;
    width:100%;
    max-width:500px;
    opacity:0;
    transform:translateY(20px);
    animation: fadeInUp 0.7s forwards;
}

/* Avatar */
.avatar i {
    font-size:100px;
    color:#007bff;
    margin-bottom:20px;
}

/* Text */
.profile-card h3 {
    margin:10px 0;
    font-size:1.6rem;
    font-weight:600;
    color:#333;
}
.profile-card p {
    margin:5px 0;
    font-size:1rem;
    color:#555;
}

/* Buttons */
.buttons {
    margin-top:25px;
    display:flex;
    justify-content:center;
    gap:15px;
    flex-wrap:wrap;
}
.btn {
    padding:10px 20px;
    border-radius:8px;
    text-decoration:none;
    font-weight:500;
    transition:0.3s;
    box-shadow:0 4px 8px rgba(0,0,0,0.15);
    display:flex;
    align-items:center;
    gap:6px;
}
.btn i { font-size:1rem; }

.btn-primary { background:#007bff; color:#fff; }
.btn-primary:hover { background:#0056b3; transform:translateY(-3px); }

.btn-secondary { background:#6c757d; color:#fff; }
.btn-secondary:hover { background:#495057; transform:translateY(-3px); }

.btn-danger { background:#e74c3c; color:#fff; }
.btn-danger:hover { background:#c0392b; transform:translateY(-3px); }

/* Card animation */
@keyframes fadeInUp {
    to {
        opacity:1;
        transform:translateY(0);
    }
}
</style>
