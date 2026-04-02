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

// All possible statuses
$all_statuses = ['Booked','Picked Up','In Transit','At Sorting Center','Out for Delivery','Delivered','Delayed','Returned','Cancelled'];

// Handle accept/reject
if(isset($_POST['respond'])){
    $parcel_id = $_POST['parcel_id'] ?? 0;
    $response = $_POST['respond'] ?? '';
    if($parcel_id && in_array($response, ['Accepted','Rejected'])){
        if($response==='Accepted'){
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
    if($parcel_id && in_array($new_status, $all_statuses)){
        $conn->query("UPDATE bookings SET status='$new_status' WHERE id=$parcel_id AND staff_id=$staff_id");
    }
}

// Fetch parcels for this staff
$parcels = $conn->query("SELECT * FROM bookings WHERE staff_id=$staff_id ORDER BY id DESC");

// Function for colored badges
function statusBadge($status){
    $colors = [
        'Booked'=>'#6c757d','Picked Up'=>'#007bff','In Transit'=>'#17a2b8',
        'At Sorting Center'=>'#6610f2','Out for Delivery'=>'#fd7e14','Delivered'=>'#28a745',
        'Delayed'=>'#ffc107','Returned'=>'#6f42c1','Cancelled'=>'#dc3545','Pending'=>'#6c757d'
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
        <div class="table-container" style="overflow-x:auto;">
            <table class="parcel-table" style="width:100%; border-collapse:collapse; min-width:900px;">
                <thead>
                    <tr style="background:#2C3E50; color:#fff;">
                        <th>ID</th>
                        <th>Sender</th>
                        <th>Receiver</th>
                        <th>Weight</th>
                        <th>Package Type</th>
                        <th>Status</th>
                        <th>Staff Response</th>
                        <th>Action</th>
                        <th>Route Map</th>
                    </tr>
                </thead>
                <tbody>
                <?php while($row=$parcels->fetch_assoc()): ?>
                    <tr style="border-bottom:1px solid #eee; transition:0.2s;">
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['sender_name']) ?></td>
                        <td><?= htmlspecialchars($row['receiver_name']) ?></td>
                        <td><?= $row['parcel_weight'] ?> kg</td>
                        <td><?= htmlspecialchars($row['package_type']) ?></td>
                        <td><?= statusBadge($row['status']) ?></td>
                        <td><?= $row['staff_response'] ?></td>
                        <td>
                            <?php if($row['staff_response']=='Pending'): ?>
                                <form method="POST" style="display:flex; gap:5px;">
                                    <input type="hidden" name="parcel_id" value="<?= $row['id'] ?>">
                                    <button type="submit" name="respond" value="Accepted" class="btn-accept">Accept</button>
                                    <button type="submit" name="respond" value="Rejected" class="btn-reject">Reject</button>
                                </form>
                            <?php elseif($row['staff_response']=='Accepted' && !in_array($row['status'], ['Delivered','Cancelled'])): ?>
                                <form method="POST" style="display:flex; gap:5px; align-items:center;">
                                    <input type="hidden" name="parcel_id" value="<?= $row['id'] ?>">
                                    <select name="current_status" class="status-select">
                                        <?php foreach($all_statuses as $status): ?>
                                            <option value="<?= $status ?>" <?= $row['status']==$status?'selected':'' ?>><?= $status ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="submit" name="update_status" class="btn-update">Update</button>
                                </form>
                            <?php else: ?>
                                <span style="color:gray;">No Action</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div id="map<?= $row['id'] ?>" class="parcel-map"></div>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
            <p style="color:#555;">No parcels assigned yet.</p>
        <?php endif; ?>
    </div>
</div>

<style>
/* Table styles */
.parcel-table th, .parcel-table td {
    padding:12px 15px;
    text-align:left;
    vertical-align:middle;
}

.parcel-table tbody tr:hover {
    background:#f1f5f8;
}

.parcel-table th {
    font-weight:600;
}

/* Map container inside table */
.parcel-map {
    width: 300%;
    height: 200px;
    border-radius:8px;
    position: relative;  /* key to keep Leaflet inside the cell */
    z-index: 0;          /* maps stay below navbar */
}



/* Buttons */
.btn-accept {
    background:#28a745;
    color:#fff;
    border:none;
    padding:6px 10px;
    border-radius:6px;
    cursor:pointer;
    transition:0.2s;
}
.btn-accept:hover { background:#218838; }

.btn-reject {
    background:#dc3545;
    color:#fff;
    border:none;
    padding:6px 10px;
    border-radius:6px;
    cursor:pointer;
    transition:0.2s;
}
.btn-reject:hover { background:#c82333; }

.btn-update {
    background:#007bff;
    color:#fff;
    border:none;
    padding:6px 10px;
    border-radius:6px;
    cursor:pointer;
    transition:0.2s;
}
.btn-update:hover { background:#0069d9; }

.status-select {
    padding:5px 8px;
    border-radius:5px;
    border:1px solid #ccc;
}

/* Make table responsive */
.table-container {
    overflow-x:auto;
}

/* Responsive maps */
@media(max-width:1000px){
    .parcel-map {
        width: 100%;
        height: 180px;
    }
}
</style>


<!-- Leaflet Map -->
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
<link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.css"/>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.js"></script>

<script>
<?php
$parcels = $conn->query("SELECT * FROM bookings WHERE staff_id=$staff_id ORDER BY id DESC");
while($row = $parcels->fetch_assoc()):
    $pickup_lat = $row['pickup_lat'] ?: 27.6941;   // fallback
    $pickup_lng = $row['pickup_lng'] ?: 85.3185;
    $delivery_lat = $row['delivery_lat'] ?: 28.2096;
    $delivery_lng = $row['delivery_lng'] ?: 83.9856;
?>
var map<?= $row['id'] ?> = L.map('map<?= $row['id'] ?>').setView([<?= $pickup_lat ?>, <?= $pickup_lng ?>], 7);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
}).addTo(map<?= $row['id'] ?>);

L.Routing.control({
    waypoints: [
        L.latLng(<?= $pickup_lat ?>, <?= $pickup_lng ?>),
        L.latLng(<?= $delivery_lat ?>, <?= $delivery_lng ?>)
    ],
    routeWhileDragging: false,
    lineOptions: { styles: [{color: 'blue', opacity: 0.7, weight: 4}] },
    createMarker: function(i, wp, nWps) {
        return L.marker(wp.latLng, {
            icon: L.icon({
                iconUrl: i === 0 ? 'https://cdn-icons-png.flaticon.com/512/25/25694.png' : 'https://cdn-icons-png.flaticon.com/512/149/149059.png',
                iconSize: [25, 25],
                iconAnchor: [12, 25]
            })
        });
    }
}).addTo(map<?= $row['id'] ?>);
<?php endwhile; ?>
</script>
