<?php
session_start();
include 'config.php';

if(!isset($_SESSION['role']) || $_SESSION['role'] != 'staff'){
    header("Location: login.php");
    exit();
}

$staff_id = $_SESSION['staff_id'];
$history = $conn->query("SELECT * FROM bookings WHERE staff_id=$staff_id AND status='Delivered'");
include 'navbar.php';
?>

<div class="container" style="padding:25px;">
    <h2 style="margin-bottom:20px; color:#333;">Delivery History</h2>

    <?php if($history->num_rows>0): ?>
    <table style="width:100%; border-collapse:collapse; background:#fff; border-radius:12px; overflow:hidden; box-shadow:0 4px 12px rgba(0,0,0,0.05);">
        <thead style="background:#f7f7f7;">
            <tr>
                <th style="padding:12px;">ID</th>
                <th>Sender</th>
                <th>Receiver</th>
                <th>Weight</th>
                <th>Package Type</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row=$history->fetch_assoc()): ?>
            <tr style="border-bottom:1px solid #eee; transition:0.2s;" 
                onmouseover="this.style.background='#f0f8ff'" onmouseout="this.style.background='#fff'">
                <td style="padding:10px;"><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['sender_name']) ?></td>
                <td><?= htmlspecialchars($row['receiver_name']) ?></td>
                <td><?= htmlspecialchars($row['parcel_weight']) ?> kg</td>
                <td><?= htmlspecialchars($row['package_type']) ?></td>
                <td style="font-weight:bold; color:#28a745;">
                    <i class="fas fa-check-circle"></i> <?= $row['status'] ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <?php else: ?>
        <p style="color:#555;">No deliveries completed yet.</p>
    <?php endif; ?>
</div>
