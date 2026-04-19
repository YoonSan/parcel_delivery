<?php
session_start();
include 'config.php';
include 'navbar.php';

if(!isset($_SESSION['role']) || $_SESSION['role'] != 'staff'){
    header("Location: stafflogin.php");
    exit();
}

$staff_id   = $_SESSION['staff_id'];
$staff_name = $_SESSION['staff_name'] ?? 'Staff';

// Warehouse coordinates
$warehouse_lat = 27.7172;
$warehouse_lon = 85.3240;

/* ---------------- Haversine Function ---------------- */
function haversineDistance($lat1, $lon1, $lat2, $lon2) {
    $earthRadius = 6371;
    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);

    $a = sin($dLat/2) * sin($dLat/2) +
         cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
         sin($dLon/2) * sin($dLon/2);
    $c = 2 * atan2(sqrt($a), sqrt(1-$a));

    return $earthRadius * $c;
}

/* ---------------- Summary Counts ---------------- */

$total = $conn->query("SELECT COUNT(*) as t FROM bookings WHERE staff_id=$staff_id")->fetch_assoc()['t'];
$pending = $conn->query("SELECT COUNT(*) as t FROM bookings WHERE staff_id=$staff_id AND status='Pending'")->fetch_assoc()['t'];
$transit = $conn->query("SELECT COUNT(*) as t FROM bookings WHERE staff_id=$staff_id AND status='In Transit'")->fetch_assoc()['t'];
$delivered = $conn->query("SELECT COUNT(*) as t FROM bookings WHERE staff_id=$staff_id AND status='Delivered'")->fetch_assoc()['t'];

/* ---------------- Fetch Parcels for Prim ---------------- */

$query = "
SELECT id, sender_name, receiver_name, parcel_weight, booking_date, status,
       pickup_lat, pickup_lng, delivery_lat, delivery_lng
FROM bookings
WHERE staff_id=$staff_id AND status!='Delivered'
";
$result = $conn->query($query);

/* ---------------- Build Nodes ---------------- */

$nodes = [];
$nodes[] = ['lat'=>$warehouse_lat,'lon'=>$warehouse_lon,'name'=>'Warehouse'];

while($row = $result->fetch_assoc()){
    $nodes[] = ['lat'=>$row['pickup_lat'],'lon'=>$row['pickup_lng'],'name'=>$row['sender_name']." Pickup"];
    $nodes[] = ['lat'=>$row['delivery_lat'],'lon'=>$row['delivery_lng'],'name'=>$row['receiver_name']." Delivery"];
}

/* ---------------- Prim Algorithm ---------------- */

$n = count($nodes);
$totalDistance = 0;
$nextStop = "Warehouse";

if($n > 1){

    $graph = [];

    for($i=0;$i<$n;$i++){
        for($j=0;$j<$n;$j++){
            $graph[$i][$j] = haversineDistance(
                $nodes[$i]['lat'],$nodes[$i]['lon'],
                $nodes[$j]['lat'],$nodes[$j]['lon']
            );
        }
    }

    $selected = array_fill(0,$n,false);
    $selected[0] = true;

    for($edge=0;$edge<$n-1;$edge++){
        $min = PHP_INT_MAX;
        $x = $y = 0;

        for($i=0;$i<$n;$i++){
            if($selected[$i]){
                for($j=0;$j<$n;$j++){
                    if(!$selected[$j] && $graph[$i][$j]){
                        if($min > $graph[$i][$j]){
                            $min = $graph[$i][$j];
                            $x=$i;
                            $y=$j;
                        }
                    }
                }
            }
        }

        $totalDistance += $min;
        $selected[$y] = true;

        if($edge==0){
            $nextStop = $nodes[$y]['name'];
        }
    }
}

/* ---------------- Parcel Table ---------------- */

$parcelTable = $conn->query("
SELECT tracking_number, sender_name, receiver_name,
parcel_weight, booking_date, status
FROM bookings
WHERE staff_id=$staff_id
ORDER BY booking_date DESC
");
?>

<!-- Leaflet Map CDN -->
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<style>
.dashboard{max-width:1200px;margin:30px auto;padding:20px;background:#fff;border-radius:12px;box-shadow:0 4px 12px rgba(0,0,0,0.08);margin-top:100px;}
.cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:15px;margin-bottom:20px;}
.card{background:#2C3E50;color:#fff;padding:18px;border-radius:10px;text-align:center;}
.card h2{margin:0;}
.smart-box{background:#ecfdf5;padding:15px;border-radius:10px;margin-bottom:20px;}
table{width:100%;border-collapse:collapse;}
th{background:#2C3E50;color:#fff;padding:12px;}
td{padding:12px;text-align:center;border-bottom:1px solid #eee;}
</style>

<div class="dashboard">

<h2>Welcome, <?= htmlspecialchars($staff_name) ?> 👋</h2>

<div class="cards">
<div class="card"><h2><?= $total ?></h2>Total Parcels</div>
<div class="card"><h2><?= $pending ?></h2>Pending</div>
<div class="card"><h2><?= $transit ?></h2>In Transit</div>
<div class="card"><h2><?= $delivered ?></h2>Delivered</div>
</div>

<div class="smart-box">
<b>🧠 Smart Suggestion</b><br>
Next delivery stop: <b><?= $nextStop ?></b><br>
Estimated total travel today: <b><?= number_format($totalDistance,2) ?> KM</b>
</div>

<!-- LIVE MAP -->
<div id="map" style="height:400px;border-radius:12px;margin-bottom:20px;"></div>

<table>
<tr>
<th>Tracking</th>
<th>Sender</th>
<th>Receiver</th>
<th>Weight</th>
<th>Date</th>
<th>Status</th>
<th>Route</th>
</tr>

<?php while($row=$parcelTable->fetch_assoc()): ?>
<tr>
<td><?= $row['tracking_number'] ?></td>
<td><?= $row['sender_name'] ?></td>
<td><?= $row['receiver_name'] ?></td>
<td><?= $row['parcel_weight'] ?> kg</td>
<td><?= $row['booking_date'] ?></td>
<td><?= $row['status'] ?></td>
<td><a href="delivery.php">Open Route</a></td>
</tr>
<?php endwhile; ?>

</table>

</div>

<script>

var warehouse = [<?= $warehouse_lat ?>, <?= $warehouse_lon ?>];

var points = [
<?php
$result = $conn->query("
SELECT sender_name, receiver_name, pickup_lat, pickup_lng, delivery_lat, delivery_lng
FROM bookings
WHERE staff_id=$staff_id AND status!='Delivered'
");

while($row=$result->fetch_assoc()){
    echo "[{$row['pickup_lat']}, {$row['pickup_lng']}, 'Pickup: {$row['sender_name']}'],";
    echo "[{$row['delivery_lat']}, {$row['delivery_lng']}, 'Delivery: {$row['receiver_name']}'],";
}
?>
];

// Initialize map
var map = L.map('map').setView(warehouse, 12);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: 'Parcel Delivery System'
}).addTo(map);

// Warehouse marker
L.marker(warehouse).addTo(map)
    .bindPopup("Warehouse")
    .openPopup();

// Add pickup & delivery markers
points.forEach(function(p){
    L.marker([p[0], p[1]]).addTo(map)
        .bindPopup(p[2]);
});

// Draw route lines
var routePoints = [warehouse];
points.forEach(p => routePoints.push([p[0], p[1]]));

L.polyline(routePoints).addTo(map);

</script>
