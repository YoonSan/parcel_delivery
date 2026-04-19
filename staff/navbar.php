<?php
$staff_name = $_SESSION['staff_name'] ?? 'Staff';
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
body { 
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
    margin:0; 
    background:#f4f6f8; 
}

/* Navbar container */
.navbar {
    display:flex; 
    justify-content:space-between; 
    align-items:center;
    padding:20px 100px;
    background:#2C3E50; /* dark background for contrast */
    color:#fff;
    box-shadow:0 4px 8px rgba(0,0,0,0.1);
    position:sticky; 
    top:0; 
    z-index:100;
    border-radius:0 0 12px 12px; /* subtle rounded bottom */
}

/* Brand / Welcome text */
.navbar .brand { 
    font-weight:bold; 
    font-size:1.6rem;
    color:#ECF0F1; /* lighter text for contrast */
}

/* Navbar links container */
.navbar .nav-links a {
    margin-left:15px;
    text-decoration:none; 
    color:#ECF0F1; 
    font-weight:500; 
    padding:10px 15px;
    border-radius:8px;
    display:inline-flex;
    align-items:center;
    transition:0.3s;
    font-size:1rem;
}

/* Icons spacing */
.navbar .nav-links a i { 
    margin-right:6px; 
    font-size:1.1rem; 
}

/* Hover effect */
.navbar .nav-links a:hover { 
    background:#3498DB; 
    color:#fff;
    transform:translateY(-2px); /* subtle lift */
    box-shadow:0 4px 8px rgba(0,0,0,0.15);
}

/* Logout button */
.navbar .nav-links .logout { 
    color:#fff; 
    background:#E74C3C; 
}

.navbar .nav-links .logout:hover { 
    background:#C0392B; 
    transform:translateY(-2px);
    box-shadow:0 4px 8px rgba(0,0,0,0.15);
}

/* Responsive - stack links on small screens */
@media(max-width: 768px){
    .navbar {
        flex-direction: column;
        align-items: flex-start;
        padding:12px 20px;
    }
    .navbar .nav-links {
        width:100%;
        display:flex;
        flex-wrap:wrap;
        margin-top:10px;
    }
    .navbar .nav-links a {
        margin:5px 10px 5px 0;
    }
}
</style>

<div class="navbar">
    <div class="brand">Welcome <?= htmlspecialchars($staff_name) ?></div>
    <div class="nav-links">
        <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="delivery.php"><i class="fas fa-box"></i> My Deliveries</a>
        <a href="updatestatus.php"><i class="fas fa-edit"></i> Update Status</a>
        <a href="history.php"><i class="fas fa-history"></i> Delivery History</a>
        <a href="staffprofile.php"><i class="fas fa-user"></i> Profile</a>
        <a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</div>
