<?php
// Optional: start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<style>
/* ===== Admin Sidebar Styles ===== */
.sidebar{
    width:240px;
    background:#1e1e2f;
    color:#fff;
    padding:20px;
    min-height:100vh;
    box-sizing:border-box;
}
.sidebar h2{
    text-align:center;
    margin-bottom:30px;
    font-size:1.5rem;
}
.sidebar a{
    display:block;
    color:#ccc;
    text-decoration:none;
    padding:12px 15px;
    border-radius:6px;
    margin-bottom:8px;
    transition:0.3s;
}
.sidebar a:hover{
    background:#4f46e5;
    color:#fff;
}
</style>

<div class="sidebar">
    <h2>Admin Panel</h2>
    <a href="admin.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
    <a href="users.php"><i class="fas fa-users"></i> Users</a>
    <a href="adminparcels.php"><i class="fas fa-box"></i> Parcels</a>
    <a href="deliverystaff.php"><i class="fas fa-truck"></i> Delivery Staff</a>
    <a href="reports.php"><i class="fas fa-chart-line"></i> Reports</a>
    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>
