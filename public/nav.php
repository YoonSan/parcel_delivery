<?php
// navbar.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<head>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css">
</head>


<nav class="navbar">
    <div class="container nav-container">

        <!-- Brand -->
        <div class="nav-brand">
            <i class="fas fa-truck"></i>
            <span>SwiftParcel</span>
        </div>

        <!-- Mobile Toggle -->
        <button class="nav-toggle" id="navToggle">
            <i class="fas fa-bars"></i>
        </button>

        <!-- Menu -->
        <ul class="nav-menu" id="navMenu">
            <li>
                <a href="index.php" class="<?= basename($_SERVER['PHP_SELF'])=='index.php'?'active':'' ?>">
                    Home
                </a>
            </li>

            <li>
                <a href="about.php" class="<?= basename($_SERVER['PHP_SELF'])=='about.php'?'active':'' ?>">
                    About
                </a>
            </li>

            <li>
                <a href="book.php" class="<?= basename($_SERVER['PHP_SELF'])=='book.php'?'active':'' ?>">
                    Book
                </a>
            </li>

            <?php if(isset($_SESSION['user_id'])): ?>
                <li>
                    <a href="profile.php" class="<?= basename($_SERVER['PHP_SELF'])=='profile.php'?'active':'' ?>">
                        My Profile
                    </a>
                </li>
                <li>
                    <a href="logout.php" class="btn-primary">
                        Logout
                    </a>
                </li>
            <?php else: ?>
                <li>
                    <a href="login.php" class="btn-primary <?= basename($_SERVER['PHP_SELF'])=='login.php'?'active':'' ?>">
                        Login / Register
                    </a>
                </li>
            <?php endif; ?>
        </ul>

    </div>
</nav>

<!-- Mobile Toggle Script -->
<script>
document.getElementById('navToggle')?.addEventListener('click', () => {
    document.getElementById('navMenu').classList.toggle('active');
});
</script>
