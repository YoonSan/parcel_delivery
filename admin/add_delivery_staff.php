<?php
include 'db.php'; // database connection

// Handle form submission
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);

    // Basic validation
    if(empty($name) || empty($email)){
        $error = "Name and Email are required.";
    } else {
        // Insert into database
        $stmt = $conn->prepare("INSERT INTO deliverystaff (name, email) VALUES (?, ?)");

        $stmt->bind_param("ss", $name, $email);

        if($stmt->execute()){
            // Redirect to delivery staff list with success message
            header("Location: deliverystaff.php");
            exit;
        } else {
            $error = "Error: " . $conn->error;
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add Delivery Staff | SwiftParcel Admin</title>
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

form { max-width:500px; margin:0 auto; background:#fff; padding:25px; border-radius:10px; box-shadow:0 10px 25px rgba(0,0,0,0.1); }
form input { width:100%; padding:12px 15px; margin-bottom:15px; border:1px solid #ccc; border-radius:6px; font-size:0.95rem; }
form button { background:#4f46e5; color:#fff; padding:12px 20px; border:none; border-radius:6px; font-size:1rem; cursor:pointer; transition:0.3s; }
form button:hover { background:#3730a3; }

/* Back button */
.back-btn {
    background:#6b7280; 
    color:#fff; 
    padding:10px 20px; 
    border-radius:6px; 
    text-decoration:none;
    font-weight:bold;
    transition:0.3s;
    display:inline-block;
    margin-bottom:15px;
}
.back-btn:hover { background:#4b5563; }

/* Alert messages */
.alert { padding:12px 15px; border-radius:6px; margin-bottom:15px; text-align:center; }
.error { background:#fee2e2; color:#991b1b; }

@media(max-width:768px){ .container { flex-direction:column; } .sidebar{ width:100%; } }
</style>
</head>
<body>

<div class="container">
    <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main">
        <h1>Add Delivery Staff</h1>


        <!-- Error Message -->
        <?php if(isset($error)): ?>
            <div class="alert error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <!-- Add Staff Form -->
        <form method="POST" action="">
            <input type="text" name="name" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <button type="submit"><i class="fas fa-plus"></i> Add Staff</button>
        </form>
    </div>
</div>

</body>
</html>
