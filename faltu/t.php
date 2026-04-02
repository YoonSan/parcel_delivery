<?php
session_start();
include 'config.php';

$tracking_number = $_GET['tracking'] ?? '';
$parcel = null;

if ($tracking_number) {
    $stmt = $conn->prepare("SELECT * FROM bookings WHERE tracking_number=?");
    $stmt->bind_param("s", $tracking_number);
    $stmt->execute();
    $result = $stmt->get_result();
    $parcel = $result->fetch_assoc();
}

// Default coordinates (Nepal center) if any missing
$default_lat = 28.3949;
$default_lng = 84.1240;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Track Parcel | SwiftParcel</title>
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
<style>
body {font-family:sans-serif;padding:20px;background:#f5f5f5;}
h2 {color:#2A3EB1;}
#map {height:500px;width:100%;margin-top:20px;border-radius:10px;box-shadow:0 4px 12px rgba(0,0,0,0.1);}
form {margin-bottom:20px;}
input[type=text] {padding:8px;width:250px;border-radius:6px;border:1px solid #ccc;}
button {padding:8px 16px;border:none;background:#2A3EB1;color:#fff;border-radius:6px;cursor:pointer;}
button:hover {background:#5563DE;}
.info {background:#fff;padding:15px;border-radius:10px;box-shadow:0 4px 12px rgba(0,0,0,0.05);margin-bottom:20px;}
</style>
</head>
<body>

<h2>Parcel Tracking</h2>

<form method="GET">
    <input type="text" name="tracking" placeholder="Enter Tracking Number" value="<?= htmlspecialchars($tracking_number) ?>" required>
    <button type="submit">Track</button>
</form>

<?php if ($parcel): 
    $pickup_lat = $parcel['pickup_lat'] ?: $default_lat;
    $pickup_lng = $parcel['pickup_lng'] ?: $default_lng;
    $delivery_lat = $parcel['delivery_lat'] ?: $default_lat;
    $delivery_lng = $parcel['delivery_lng'] ?: $default_lng;
?>
    <div class="info">
        <p><strong>Tracking Number:</strong> <?= htmlspecialchars($parcel['tracking_number']) ?></p>
        <p><strong>Sender:</strong> <?= htmlspecialchars($parcel['sender_name']) ?> (<em><?= htmlspecialchars($parcel['sender_address']) ?></em>)</p>
        <p><strong>Receiver:</strong> <?= htmlspecialchars($parcel['receiver_name']) ?> (<em><?= htmlspecialchars($parcel['receiver_address']) ?></em>)</p>
        <p><strong>Status:</strong> <?= htmlspecialchars($parcel['status']) ?></p>
    </div>

    <div id="map"></div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
    // Initialize map without center/zoom
    const map = L.map('map');

    // Pickup & Delivery coordinates
    const pickup = [<?= $pickup_lat ?>, <?= $pickup_lng ?>];
    const delivery = [<?= $delivery_lat ?>, <?= $delivery_lng ?>];

    // Add OpenStreetMap tile layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
    }).addTo(map);

    // Add markers
    const pickupMarker = L.marker(pickup).addTo(map).bindPopup("Pickup: <?= htmlspecialchars($parcel['sender_address']) ?>").openPopup();
    const deliveryMarker = L.marker(delivery).addTo(map).bindPopup("Delivery: <?= htmlspecialchars($parcel['receiver_address']) ?>");

    // Draw route line
    const routeLine = L.polyline([pickup, delivery], {color:'#2A3EB1', weight:5}).addTo(map);

    // Fit map bounds to route
    map.fitBounds(routeLine.getBounds());
    </script>

<?php elseif($tracking_number): ?>
    <p style="color:red;">Tracking number not found!</p>
<?php endif; ?>

</body>
</html>
