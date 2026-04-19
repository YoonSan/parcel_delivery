<?php
session_start();
include 'config.php';

if(!isset($_SESSION['role']) || $_SESSION['role'] != 'staff'){
    header("Location: login.php");
    exit();
}

$staff_id = $_SESSION['staff_id'];

// Mark as delivered
if(isset($_POST['deliver'])){
    $parcel_id = $_POST['parcel_id'];
    $conn->query("UPDATE bookings SET status='Delivered' WHERE id=$parcel_id AND staff_id=$staff_id");
}

$pending_parcels = $conn->query("SELECT * FROM bookings WHERE staff_id=$staff_id AND status!='Delivered'");
include 'navbar.php';
?>

<div class="container" style="padding:25px;">
    <h2 style="margin-bottom:20px; color:#333;">Update Status</h2>

    <?php if($pending_parcels->num_rows>0): ?>
    <table style="width:100%; border-collapse:collapse; background:#fff; border-radius:12px; overflow:hidden; box-shadow:0 4px 12px rgba(0,0,0,0.05);">
        <thead style="background:#f7f7f7;">
            <tr>
                <th style="padding:12px;">ID</th>
                <th>Sender</th>
                <th>Receiver</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row=$pending_parcels->fetch_assoc()): ?>
            <tr style="border-bottom:1px solid #eee; transition:0.2s;" 
                onmouseover="this.style.background='#f0f8ff'" onmouseout="this.style.background='#fff'">
                <td style="padding:10px;"><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['sender_name']) ?></td>
                <td><?= htmlspecialchars($row['receiver_name']) ?></td>
                <td style="font-weight:bold; color:#f39c12;">
                    <i class="fas fa-clock"></i> <?= $row['status'] ?>
                </td>
                <td>
                    <form method="POST">
                        <input type="hidden" name="parcel_id" value="<?= $row['id'] ?>">
                        <button type="submit" name="deliver" style="padding:6px 12px; border:none; border-radius:6px; background:#28a745; color:#fff; cursor:pointer;">
                            <i class="fas fa-check"></i> Mark Delivered
                        </button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <?php else: ?>
        <p style="color:#555;">No pending parcels.</p>
    <?php endif; ?>
</div>
