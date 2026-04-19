<?php
session_start();
include 'config.php';

if(!isset($_SESSION['role']) || $_SESSION['role'] != 'staff'){
    header("Location: stafflogin.php");
    exit();
}

$staff_id = $_SESSION['staff_id'];

// Warehouse coordinates
$warehouse_lat = 27.7172;
$warehouse_lon = 85.3240;

/* ---------------- Haversine Distance Function ---------------- */
function haversineDistance($lat1, $lon1, $lat2, $lon2) {
    $earthRadius = 6371; // KM
    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);

    $a = sin($dLat/2) * sin($dLat/2) +
         cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
         sin($dLon/2) * sin($dLon/2);
    $c = 2 * atan2(sqrt($a), sqrt(1-$a));

    return $earthRadius * $c;
}

/* ---------------- Fetch Parcels ---------------- */
$query = "
SELECT id, sender_name, receiver_name, parcel_weight, package_type, status,
       pickup_lat, pickup_lng, delivery_lat, delivery_lng
FROM bookings 
WHERE staff_id = $staff_id AND status != 'Delivered'
";

$result = $conn->query($query);

$parcels = [];

if($result->num_rows > 0){
    while($row = $result->fetch_assoc()){
        $parcels[] = $row;
    }
}

/* ---------------- Generate Delivery Order (Nearest Neighbor) ---------------- */
$delivery_order = [];
$visited = array_fill(0, count($parcels), false);
$current_lat = $warehouse_lat;
$current_lon = $warehouse_lon;
$total_distance_all = 0;

while(count($delivery_order) < count($parcels)){
    $nearest_idx = -1;
    $min_distance = PHP_INT_MAX;

    foreach($parcels as $i => $parcel){
        if(!$visited[$i]){
            $dist = haversineDistance($current_lat, $current_lon, $parcel['pickup_lat'], $parcel['pickup_lng']);
            if($dist < $min_distance){
                $min_distance = $dist;
                $nearest_idx = $i;
            }
        }
    }

    if($nearest_idx != -1){
        $parcel = $parcels[$nearest_idx];

        $distance_to_pickup = haversineDistance($current_lat, $current_lon, $parcel['pickup_lat'], $parcel['pickup_lng']);
        $distance_to_delivery = haversineDistance($parcel['pickup_lat'], $parcel['pickup_lng'], $parcel['delivery_lat'], $parcel['delivery_lng']);
        $total_distance = $distance_to_pickup + $distance_to_delivery;
        $total_distance_all += $total_distance;

        $delivery_order[] = [
            'id' => $parcel['id'],
            'pickup' => $parcel['sender_name'],
            'pickup_lat' => $parcel['pickup_lat'],
            'pickup_lng' => $parcel['pickup_lng'],
            'delivery' => $parcel['receiver_name'],
            'delivery_lat' => $parcel['delivery_lat'],
            'delivery_lng' => $parcel['delivery_lng'],
            'weight' => $parcel['parcel_weight'],
            'package' => $parcel['package_type'],
            'status' => $parcel['status'],
            'distance' => $total_distance
        ];

        $current_lat = $parcel['delivery_lat'];
        $current_lon = $parcel['delivery_lng'];

        $visited[$nearest_idx] = true;
    }
}

include 'navbar.php';
?>

<div class="container" style="padding:25px;">
    <h2 style="margin-bottom:20px; color:#333;">Optimized Delivery Order & Map</h2>

    <!-- Map -->
    <div id="map" style="height:500px; width:100%; margin-bottom:20px;"></div>

    <?php if(!empty($delivery_order)): ?>
    <table style="width:100%; border-collapse:collapse; background:#fff; border-radius:12px; overflow:hidden; box-shadow:0 4px 12px rgba(0,0,0,0.05);">
        <thead style="background:#f7f7f7;">
            <tr>
                <th>Step</th>
                <th>Parcel ID</th>
                <th>Pickup → Delivery</th>
                <th>Weight</th>
                <th>Package</th>
                <th>Status</th>
                <th>Total Distance (km)</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $highlighted = false;
            foreach($delivery_order as $index => $parcel):
                $highlight = '';
                if(!$highlighted && in_array($parcel['status'], ['Pending', 'Picked Up', 'In Transit'])){
                    $highlight = 'background-color:#d1f7d1; font-weight:bold;';
                    $highlighted = true;
                }
            ?>
            <tr style="<?= $highlight ?>">
                <td><?= $index+1 ?></td>
                <td><?= $parcel['id'] ?></td>
                <td><?= htmlspecialchars($parcel['pickup']) ?> → <?= htmlspecialchars($parcel['delivery']) ?></td>
                <td><?= $parcel['weight'] ?> kg</td>
                <td><?= htmlspecialchars($parcel['package']) ?></td>
                <td><?= $parcel['status'] ?></td>
                <td><?= number_format($parcel['distance'],2) ?> km</td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr style="font-weight:bold; background:#f0f0f0;">
                <td colspan="6" style="text-align:right;">Total Distance:</td>
                <td><?= number_format($total_distance_all,2) ?> km</td>
            </tr>
        </tfoot>
    </table>

    <?php else: ?>
        <p>No assigned deliveries.</p>
    <?php endif; ?>
</div>

<!-- Leaflet JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
var map = L.map('map').setView([<?= $warehouse_lat ?>, <?= $warehouse_lon ?>], 13);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: 'Map data © <a href="https://openstreetmap.org">OpenStreetMap</a>'
}).addTo(map);

// Warehouse marker
var warehouse = L.marker([<?= $warehouse_lat ?>, <?= $warehouse_lon ?>]).addTo(map).bindPopup("Warehouse");

var routeCoords = [[<?= $warehouse_lat ?>, <?= $warehouse_lon ?>]];

<?php foreach($delivery_order as $parcel): ?>
    // Pickup marker
    var pickup = [<?= $parcel['pickup_lat'] ?>, <?= $parcel['pickup_lng'] ?>];
    L.marker(pickup).addTo(map).bindPopup("Parcel <?= $parcel['id'] ?> Pickup: <?= htmlspecialchars($parcel['pickup']) ?>");
    routeCoords.push(pickup);

    // Delivery marker
    var delivery = [<?= $parcel['delivery_lat'] ?>, <?= $parcel['delivery_lng'] ?>];
    L.marker(delivery).addTo(map).bindPopup("Parcel <?= $parcel['id'] ?> Delivery: <?= htmlspecialchars($parcel['delivery']) ?>");
    routeCoords.push(delivery);
<?php endforeach; ?>

// Draw route polyline
L.polyline(routeCoords, {color: 'blue'}).addTo(map);
map.fitBounds(routeCoords);
</script>
