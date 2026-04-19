<?php
session_start();
include 'config.php';
include 'navbar.php';

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$user_id   = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'] ?? 'User';

// Fetch bookings of this user
$sql = "
    SELECT tracking_number, sender_name, receiver_name,
           parcel_weight, booking_date, status
    FROM bookings
    WHERE user_id = $user_id
    ORDER BY booking_date DESC
";
$parcels = $conn->query($sql);
?>

<style>
.dashboard-container{
    max-width:1100px;
    margin:30px auto;
    padding:25px;
    background:#fff;
    border-radius:12px;
    box-shadow:0 4px 12px rgba(0,0,0,0.08);
    font-family:Arial, sans-serif;
}

.dashboard-title{
    font-size:26px;
    font-weight:600;
    color:#2C3E50;
}

.dashboard-sub{
    color:#666;
    margin-bottom:20px;
}

.table-wrapper{overflow-x:auto;
margin-top:100px;}

.parcel-table{
    width:100%;
    border-collapse:collapse;
}

.parcel-table th{
    background:#2C3E50;
    color:#fff;
    padding:12px;
}

.parcel-table td{
    padding:12px;
    border-bottom:1px solid #eee;
    text-align:center;
}

.parcel-table tr:hover{
    background:#f4f6f8;
}

.status{
    padding:5px 12px;
    border-radius:20px;
    font-size:12px;
    color:#fff;
}

.pending{background:#f59e0b;}
.booked{background:#6b7280;}
.delivered{background:#10b981;}
.cancelled{background:#dc2626;}
.intransit{background:#0891b2;}
</style>


<div class="dashboard-container">

    <div class="dashboard-title">Welcome, <?= htmlspecialchars($user_name) ?></div>
    <div class="dashboard-sub">Here are all your booked deliveries</div>

    <?php if($parcels->num_rows > 0): ?>
    <div class="table-wrapper">
        <table class="parcel-table">
            <tr>
                <th>Tracking No</th>
                <th>Sender</th>
                <th>Receiver</th>
                <th>Weight</th>
                <th>Date</th>
                <th>Status</th>
            </tr>

            <?php while($row = $parcels->fetch_assoc()): ?>
            <tr>
                <td>
                    <a href="track.php?tracking=<?= $row['tracking_number'] ?>">
                        <?= $row['tracking_number'] ?>
                    </a>
                </td>
                <td><?= htmlspecialchars($row['sender_name']) ?></td>
                <td><?= htmlspecialchars($row['receiver_name']) ?></td>
                <td><?= $row['parcel_weight'] ?> kg</td>
                <td><?= $row['booking_date'] ?></td>
                <td>
                    <span class="status <?= strtolower(str_replace(' ','',$row['status'])) ?>">
                        <?= $row['status'] ?>
                    </span>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>

    <?php else: ?>
        <p>No deliveries booked yet.</p>
    <?php endif; ?>

</div>
