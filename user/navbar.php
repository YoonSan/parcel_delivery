<?php
$user_name = $_SESSION['user_name'] ?? 'User';
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css">

<style>
body {
    font-family:'Poppins',sans-serif;
    margin:0;
}

.container {
    display:flex;
    min-height:100vh;
}

/* Sidebar */
.sidebar {
    width:300px;
    background:#1e1e2f;
    color:#fff;
    padding:20px;
    box-sizing:border-box;
}

.sidebar h2 {
    text-align:center;
    margin-bottom:30px;
    font-size:1.5rem;
}

.sidebar a {
    display:block;
    color:#ccc;
    text-decoration:none;
    padding:12px 15px;
    border-radius:6px;
    margin-bottom:8px;
    transition:0.3s;
}

.sidebar a:hover {
    background:#4f46e5;
    color:#fff;
}

.sidebar a.active {
    background:#4f46e5;
    color:#fff;
}

/* Main */
.main {
    flex:1;
    padding:25px;
    background:#f4f6f8;
}

/* Mobile */
@media(max-width:768px){
    .container { flex-direction:column; }
    .sidebar{ width:100%; }
}
</style>

<div class="container">

    <!-- Sidebar -->
    <div class="sidebar">
        <h3>Welcome, <?php echo $user_name; ?></h3>

        <a href="dashboard.php" class="<?= basename($_SERVER['PHP_SELF'])=='dashboard.php'?'active':'' ?>">
            <i class="fas fa-home"></i> Dashboard
        </a>

        <a href="book.php" class="<?= basename($_SERVER['PHP_SELF'])=='book.php'?'active':'' ?>">
            <i class="fas fa-box"></i> Book Parcel
        </a>

        <a href="track.php" class="<?= basename($_SERVER['PHP_SELF'])=='track.php'?'active':'' ?>">
            <i class="fas fa-map-marker-alt"></i> Track Parcel
       </a>

        <a href="profile.php" class="<?= basename($_SERVER['PHP_SELF'])=='profile.php'?'active':'' ?>">
            <i class="fas fa-map-marker-alt"></i> My Profile
        </a>
        <a href="../logout.php">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>
