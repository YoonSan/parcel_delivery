<?php
session_start();
include 'config.php';
include 'navbar.php';

if(!isset($_SESSION['role']) || $_SESSION['role'] != 'staff'){
    header("Location: stafflogin.php");
    exit();
}

$staff_id = $_SESSION['staff_id'];
$staff_name = $_SESSION['staff_name'] ?? 'Staff';

// List of all possible statuses
$all_statuses = [
    'Booked',
    'Picked Up',
    'In Transit',
    'At Sorting Center',
    'Out for Delivery',
    'Delivered',
    'Delayed',
    'Returned',
    'Cancelled'
];

// Handle accept/reject
if(isset($_POST['respond'])){
    $parcel_id = $_POST['parcel_id'] ?? 0;
    $response = $_POST['respond'] ?? '';

    if($parcel_id && in_array($response, ['Accepted','Rejected'])){
        if($response === 'Accepted'){
            $conn->query("UPDATE bookings SET staff_response='Accepted', status='Picked Up' WHERE id=$parcel_id AND staff_id=$staff_id");
        } else {
            $conn->query("UPDATE bookings SET staff_response='Rejected', staff_id=NULL, status='Pending' WHERE id=$parcel_id AND staff_id=$staff_id");
        }
    }
}

// Handle status update
if(isset($_POST['update_status'])){
    $parcel_id = $_POST['parcel_id'] ?? 0;
    $new_status = $_POST['current_status'] ?? '';

    // Validate status
    if($parcel_id && in_array($new_status, $all_statuses)){
        $conn->query("UPDATE bookings SET status='$new_status' WHERE id=$parcel_id AND staff_id=$staff_id");
    }
}

// Fetch parcels assigned to this staff
$parcels = $conn->query("SELECT * FROM bookings WHERE staff_id=$staff_id ORDER BY id DESC");

// Function to display colored badges for status
function statusBadge($status){
    $colors = [
        'Booked' => '#6c757d', // gray
        'Picked Up' => '#007bff', // blue
        'In Transit' => '#17a2b8', // cyan
        'At Sorting Center' => '#6610f2', // purple
        'Out for Delivery' => '#fd7e14', // orange
        'Delivered' => '#28a745', // green
        'Delayed' => '#ffc107', // yellow
        'Returned' => '#6f42c1', // dark purple
        'Cancelled' => '#dc3545', // red
        'Pending' => '#6c757d' // fallback gray
    ];
    $color = $colors[$status] ?? '#6c757d';
    return "<span style='background:$color;color:#fff;padding:3px 8px;border-radius:12px;font-size:0.85em;'>$status</span>";
}
?>

<div class="container" style="padding:25px;">
    <h2 style="margin-bottom:20px; color:#333;">Welcome, <?= htmlspecialchars($staff_name) ?></h2>

    <div style="background:#fff; padding:20px; border-radius:12px; box-shadow:0 4px 12px rgba(0,0,0,0.05);">
        <h3 style="margin-bottom:15px; color:#333;">My Parcels</h3>

        <?php if($parcels->num_rows>0): ?>
        <table style="width:100%; border-collapse:collapse;">
            <thead style="background:#f7f7f7;">
                <tr>
                    <th style="padding:8px;">ID</th>
                    <th style="padding:8px;">Sender</th>
                    <th style="padding:8px;">Receiver</th>
                    <th style="padding:8px;">Weight</th>
                    <th style="padding:8px;">Package Type</th>
                    <th style="padding:8px;">Status</th>
                    <th style="padding:8px;">Staff Response</th>
                    <th style="padding:8px;">Action</th>
                </tr>
            </thead>
            <tbody>
            <?php while($row = $parcels->fetch_assoc()): ?>
                <tr style="border-bottom:1px solid #eee;">
                    <td style="padding:8px;"><?= $row['id'] ?></td>
                    <td style="padding:8px;"><?= htmlspecialchars($row['sender_name']) ?></td>
                    <td style="padding:8px;"><?= htmlspecialchars($row['receiver_name']) ?></td>
                    <td style="padding:8px;"><?= htmlspecialchars($row['parcel_weight']) ?> kg</td>
                    <td style="padding:8px;"><?= htmlspecialchars($row['package_type']) ?></td>
                    <td style="padding:8px;"><?= statusBadge($row['status']) ?></td>
                    <td style="padding:8px;"><?= $row['staff_response'] ?></td>
                    <td style="padding:8px;">
                        <?php if($row['staff_response']=='Pending'): ?>
                            <form method="POST" style="display:flex; gap:5px;">
                                <input type="hidden" name="parcel_id" value="<?= $row['id'] ?>">
                                <button type="submit" name="respond" value="Accepted" style="background:#28a745;color:#fff;border:none;padding:5px 8px;border-radius:4px;">Accept</button>
                                <button type="submit" name="respond" value="Rejected" style="background:#dc3545;color:#fff;border:none;padding:5px 8px;border-radius:4px;">Reject</button>
                            </form>
                        <?php elseif($row['staff_response']=='Accepted' && !in_array($row['status'], ['Delivered','Cancelled'])): ?>
                            <form method="POST" style="display:flex; gap:5px; align-items:center;">
                                <input type="hidden" name="parcel_id" value="<?= $row['id'] ?>">
                                <select name="current_status" style="padding:4px 6px; border-radius:4px; border:1px solid #ccc;">
                                    <?php foreach($all_statuses as $status): ?>
                                        <option value="<?= $status ?>" <?= $row['status']==$status?'selected':'' ?>><?= $status ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="submit" name="update_status" style="background:#007bff;color:#fff;border:none;padding:5px 8px;border-radius:4px;">
                                    Update
                                </button>
                            </form>
                        <?php else: ?>
                            <span style="color:gray;">No Action</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
        <?php else: ?>
            <p style="color:#555;">No parcels assigned yet.</p>
        <?php endif; ?>
    </div>
</div>
