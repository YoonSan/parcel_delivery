<?php
// DO NOT start session here
// Access $_SESSION variables only
$staff_name = $_SESSION['staff_name'] ?? 'Staff';
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
body { 
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
    margin:0; 
    background:#f4f6f8; 
}

.navbar {
    display:flex; 
    justify-content:space-between; 
    align-items:center;
    padding:16px 30px; /* increased padding */
    background:#fff; 
    border-bottom:1px solid #ddd; 
    box-shadow:0 2px 5px rgba(0,0,0,0.05);
    position:sticky; 
    top:0; 
    z-index:100;
}

.navbar .brand { 
    font-weight:bold; 
    font-size:1.6rem; /* bigger brand text */
    color:#333; 
}

.navbar a { 
    margin-left:20px; 
    text-decoration:none; 
    color:#555; 
    font-weight:500; 
    padding:8px 12px; /* bigger padding for links */
    border-radius:5px; 
    transition:0.2s; 
    font-size:1.1rem; /* bigger link text */
}

.navbar a i { 
    margin-right:6px; /* small spacing between icon and text */ 
    font-size:1.1rem; /* slightly bigger icons */
}

.navbar a:hover { 
    background:#007bff; 
    color:#fff; 
}

.navbar .logout { 
    color:#fff; 
    background:#e74c3c; 
}

.navbar .logout:hover { 
    background:#c0392b; 
}
</style>


<div class="navbar">
    <div class="brand">Welcome <?= htmlspecialchars($staff_name) ?></div>
    <div>
        <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="delivery.php"><i class="fas fa-box"></i> My Deliveries</a>
        <a href="updatestatus.php"><i class="fas fa-edit"></i> Update Status</a>
        <a href="history.php"><i class="fas fa-history"></i> Delivery History</a>
        <a href="staffprofile.php"><i class="fas fa-user"></i> Profile</a>
        <a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</div>
