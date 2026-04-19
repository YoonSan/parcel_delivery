<?php
session_start();
include 'db.php';

// Only admin
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header("Location: ../login.php");
    exit();
}

// Handle assignment
if(isset($_POST['assign'])){
    $parcel_id = intval($_POST['parcel_id']);
    $staff_id = intval($_POST['staff_id']);

    if($parcel_id && $staff_id){
        $sql = "UPDATE bookings 
                SET staff_id=$staff_id, status='Pending', staff_response='Pending' 
                WHERE id=$parcel_id";
        $res = $conn->query($sql);

        if($res){
            $_SESSION['msg'] = "Parcel #$parcel_id assigned successfully!";
        } else {
            $_SESSION['msg_error'] = "Assignment failed: " . $conn->error;
        }
    } else {
        $_SESSION['msg_error'] = "Invalid parcel or staff selection!";
    }

    header("Location: adminparcels.php");
    exit();
}

// Fetch parcels
$parcels = $conn->query("
    SELECT b.*, u.name AS user_name, d.name AS staff_name
    FROM bookings b
    LEFT JOIN users u ON b.user_id = u.id
    LEFT JOIN deliverystaff d ON b.staff_id = d.id
    ORDER BY b.id DESC
");

// Fetch staff
$staffs = $conn->query("SELECT id, name FROM deliverystaff");
$staffArray = [];
while($s = $staffs->fetch_assoc()){
    $staffArray[] = $s;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Assign Parcels | SwiftParcel Admin</title>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css">

<style>
body {
    font-family:'Poppins',sans-serif;
    margin:0;
    background:#f4f6f8;
    color:#333;
}

.container {
    display:flex;
    min-height:100vh;
}

.sidebar {
    width:240px;
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

.main {
    flex:1;
    padding:25px;
    box-sizing:border-box;
}

h1 {
    text-align:center;
    margin-bottom:2rem;
}

.msg {
    padding:12px;
    margin-bottom:15px;
    border-radius:6px;
    font-weight:bold;
    text-align:center;
}

.msg-success { background:#28a745; color:#fff; }
.msg-error { background:#dc3545; color:#fff; }

table {
    width:100%;
    border-collapse:collapse;
    background:#fff;
    box-shadow:0 10px 25px rgba(0,0,0,0.1);
    border-radius:10px;
    overflow:hidden;
}

th, td {
    padding:12px 15px;
    text-align:left;
    font-size:0.95rem;
}

th {
    background:#2A3EB1;
    color:#fff;
}

tr:nth-child(even){
    background:#f9f9f9;
}

select, button {
    padding:6px 10px;
    border-radius:4px;
    border:1px solid #ccc;
    font-size:14px;
}

button {
    background:#4f46e5;
    color:#fff;
    border:none;
    cursor:pointer;
}

button:hover {
    background:#3730a3;
}

@media(max-width:768px){
    .container { flex-direction:column; }
    .sidebar{ width:100%; }
}
</style>
</head>

<body>

<div class="container">

    <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>

    <!-- Main -->
    <div class="main">

        <h1>Assign Parcels</h1>

        <?php
        if(isset($_SESSION['msg'])){
            echo "<div class='msg msg-success'>{$_SESSION['msg']}</div>";
            unset($_SESSION['msg']);
        }
        if(isset($_SESSION['msg_error'])){
            echo "<div class='msg msg-error'>{$_SESSION['msg_error']}</div>";
            unset($_SESSION['msg_error']);
        }
        ?>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Sender</th>
                    <th>Receiver</th>
                    <th>Weight</th>
                    <th>Package Type</th>
                    <th>Status</th>
                    <th>Staff</th>
                    <th>Staff Response</th>
                    <th>Assign Staff</th>
                </tr>
            </thead>
            <tbody>

            <?php while($row = $parcels->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['user_name'] ?? '-') ?></td>
                <td><?= htmlspecialchars($row['sender_name']) ?></td>
                <td><?= htmlspecialchars($row['receiver_name']) ?></td>
                <td><?= $row['parcel_weight'] ?> kg</td>
                <td><?= htmlspecialchars($row['package_type']) ?></td>
                <td><?= htmlspecialchars($row['status']) ?></td>
                <td><?= htmlspecialchars($row['staff_name'] ?? '-') ?></td>
                <td><?= htmlspecialchars($row['staff_response'] ?? 'Pending') ?></td>
                <td>
                    <?php if(!$row['staff_id'] || $row['staff_response']=='Rejected'): ?>
                    <form method="POST" style="display:flex; gap:5px;">
                        <input type="hidden" name="parcel_id" value="<?= $row['id'] ?>">
                        <select name="staff_id" required>
                            <option value="">Select</option>
                            <?php foreach($staffArray as $s): ?>
                                <option value="<?= $s['id'] ?>">
                                    <?= htmlspecialchars($s['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" name="assign">Assign</button>
                    </form>
                    <?php else: ?>
                        <span>Assigned</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>

            </tbody>
        </table>

    </div>
</div>

</body>
</html>
