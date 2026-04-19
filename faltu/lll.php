<?php
session_start();
include 'db.php'; // adjust path if needed

if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header("Location:login.php");
    exit();
}

// Assign staff if submitted
if(isset($_POST['assign'])){
    $parcel_id = $_POST['parcel_id'];
    $staff_id = $_POST['staff_id'];
    $conn->query("UPDATE bookings SET staff_id=$staff_id, status='In Transit' WHERE id=$parcel_id");
}

// Get pending parcels
$parcels = $conn->query("
    SELECT b.*, u.name AS user_name 
    FROM bookings b 
    LEFT JOIN users u ON b.user_id = u.id 
    WHERE b.status = 'Pending'
");

// Get all delivery staff
$staffs = $conn->query("SELECT id, name FROM deliverystaff");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin - Assign Parcels</title>
<style>
    /* Navbar styling */
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        background-color: #f4f6f8;
    }
    .navbar {
        background-color: #1e90ff;
        color: #fff;
        padding: 15px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .navbar a {
        color: #fff;
        text-decoration: none;
        margin-left: 15px;
        font-weight: bold;
    }
    .navbar a:hover {
        text-decoration: underline;
    }

    /* Page heading */
    h2 {
        text-align: center;
        margin: 20px 0;
        color: #333;
    }

    /* Table styling */
    table {
        width: 90%;
        margin: auto;
        border-collapse: collapse;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        background-color: #fff;
        border-radius: 5px;
        overflow: hidden;
    }
    th, td {
        padding: 12px 15px;
        text-align: center;
        border-bottom: 1px solid #ddd;
    }
    th {
        background-color: #1e90ff;
        color: white;
    }
    tr:hover {
        background-color: #f1f1f1;
    }

    /* Form styling */
    select, button {
        padding: 6px 10px;
        border-radius: 4px;
        border: 1px solid #ccc;
        margin-right: 5px;
        font-size: 14px;
    }
    button {
        background-color: #28a745;
        color: white;
        border: none;
        cursor: pointer;
    }
    button:hover {
        background-color: #218838;
    }
</style>
</head>
<body>

<!-- Navbar -->
<div class="navbar">
    <div>Admin Panel</div>
    <div>
        <a href="admin.php">Dashboard</a>
        <a href="adminparcels.php">Assign Parcels</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

<h2>Assign Delivery Staff</h2>

<table>
<tr>
    <th>ID</th>
    <th>User</th>
    <th>Sender</th>
    <th>Receiver</th>
    <th>Weight</th>
    <th>Package Type</th>
    <th>Assign Staff</th>
</tr>
<?php while($row = $parcels->fetch_assoc()): ?>
<tr>
    <td><?= $row['id'] ?></td>
    <td><?= $row['user_name'] ?></td>
    <td><?= $row['sender_name'] ?></td>
    <td><?= $row['receiver_name'] ?></td>
    <td><?= $row['parcel_weight'] ?></td>
    <td><?= $row['package_type'] ?></td>
    <td>
        <form method="POST" style="display:flex; justify-content:center;">
            <input type="hidden" name="parcel_id" value="<?= $row['id'] ?>">
            <select name="staff_id" required>
                <option value="">Select Staff</option>
                <?php 
                $staffs->data_seek(0);
                while($s = $staffs->fetch_assoc()): ?>
                    <option value="<?= $s['id'] ?>"><?= $s['name'] ?></option>
                <?php endwhile; ?>
            </select>
            <button type="submit" name="assign">Assign</button>
        </form>
    </td>
</tr>
<?php endwhile; ?>
</table>

</body>
</html>
