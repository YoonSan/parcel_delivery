<?php
include 'db.php'; // adjust path if needed

// Fetch all delivery staff from 'deliverystaff' table
$sql = "SELECT id, name, email, created_at FROM deliverystaff ORDER BY id DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Delivery Staff | SwiftParcel Admin</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css">
<style>
body { font-family:'Poppins',sans-serif; margin:0; background:#f4f6f8; color:#333; }
.container { display:flex; min-height:100vh; }
.sidebar { width:240px; background:#1e1e2f; color:#fff; padding:20px; box-sizing:border-box; }
.sidebar h2 { text-align:center; margin-bottom:30px; font-size:1.5rem; }
.sidebar a { display:block; color:#ccc; text-decoration:none; padding:12px 15px; border-radius:6px; margin-bottom:8px; transition:0.3s; }
.sidebar a:hover { background:#4f46e5; color:#fff; }

.main { flex:1; padding:25px; box-sizing:border-box; }

h1 { text-align:center; margin-bottom:2rem; }

table { width:100%; border-collapse:collapse; background:#fff; box-shadow:0 10px 25px rgba(0,0,0,0.1); border-radius:10px; overflow:hidden; }
th, td { padding:12px 15px; text-align:left; font-size:0.95rem; }
th { background:#2A3EB1; color:#fff; }
tr:nth-child(even){ background:#f9f9f9; }

/* Button Styles */
.add-btn {
    background:#4f46e5; 
    color:#fff; 
    padding:10px 20px; 
    border-radius:6px; 
    text-decoration:none;
    font-weight:bold;
    transition:0.3s;
}
.add-btn:hover { background:#3730a3; }

/* Success message */
.alert-success {
    background:#d1fae5; 
    color:#065f46; 
    padding:12px 15px; 
    border-radius:6px; 
    margin-bottom:15px; 
    text-align:center;
}

/* Responsive */
@media(max-width:768px){ .container { flex-direction:column; } .sidebar{ width:100%; } }
</style>
</head>
<body>

<div class="container">
    <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main">
        <h1>Delivery Staff</h1>

        <!-- Success message after adding staff -->
        <?php if(isset($_GET['success'])): ?>
            <div class="alert-success">
                <?= htmlspecialchars($_GET['success']) ?>
            </div>
        <?php endif; ?>

        <!-- Add Delivery Staff Button -->
        <div style="text-align:right; margin-bottom:15px;">
            <a href="add_delivery_staff.php" class="add-btn">
                <i class="fas fa-plus"></i> Add Delivery Staff
            </a>
        </div>

        <!-- Staff Table -->
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Joined At</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if($result->num_rows > 0){
                    $i = 1;
                    while($staff = $result->fetch_assoc()){ ?>
                        <tr>
                            <td><?= $i++ ?></td>
                            <td><?= htmlspecialchars($staff['name']) ?></td>
                            <td><?= htmlspecialchars($staff['email']) ?></td>
                            <td><?= date("d M Y, H:i", strtotime($staff['created_at'])) ?></td>
                        </tr>
                    <?php }
                } else { ?>
                    <tr><td colspan="4" style="text-align:center;">No delivery staff found</td></tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
