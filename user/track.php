<?php
session_start();
include 'config.php';
include 'navbar.php';

$tracking_number = $_GET['tn'] ?? '';
$parcel = null;

if ($tracking_number) {
    // We join with deliverystaff to get the driver's current coordinates
    $stmt = $conn->prepare("
        SELECT b.*, s.current_lat AS driver_lat, s.current_lng AS driver_lng, s.name AS driver_name 
        FROM bookings b 
        LEFT JOIN deliverystaff s ON b.staff_id = s.id 
        WHERE b.tracking_number = ?");
    $stmt->bind_param("s", $tracking_number);
    $stmt->execute();
    $parcel = $stmt->get_result()->fetch_assoc();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Live Tracking | SwiftParcel</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        #live-map { height: 500px; width: 100%; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .track-box { max-width: 900px; margin: 30px auto; padding: 20px; font-family: sans-serif; }
        .info-panel { display: flex; justify-content: space-between; background: #fff; padding: 15px; border-radius: 10px; margin-bottom: 15px; border: 1px solid #ddd; }
    </style>
</head>
<body>

<div class="track-box">
    <h2>Track Your Parcel</h2>
    <form method="GET" style="margin-bottom: 20px;">
        <input type="text" name="tn" value="<?= htmlspecialchars($tracking_number) ?>" placeholder="Enter Tracking Number" style="padding:10px; width:300px; border-radius:5px; border:1px solid #ccc;">
        <button type="submit" style="padding:10px 20px; background:#2A3EB1; color:white; border:none; border-radius:5px; cursor:pointer;">Track Live</button>
    </form>

    <?php if ($parcel): ?>
        <div class="info-panel">
            <div>
                <strong>Status:</strong> <span style="color: #2A3EB1;"><?= $parcel['status'] ?></span><br>
                <strong>Driver:</strong> <?= $parcel['driver_name'] ?? 'Not assigned yet' ?>
            </div>
            <div>
                <strong>To:</strong> <?= htmlspecialchars($parcel['receiver_name']) ?><br>
                <strong>Estimate:</strong> <?= $parcel['distance_km'] ?> km away
            </div>
        </div>

        <div id="live-map"></div>

        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        <script>
            // Initialize Map
            var map = L.map('live-map').setView([<?= $parcel['pickup_lat'] ?>, <?= $parcel['pickup_lng'] ?>], 13);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

            // Icons
            var scooterIcon = L.icon({
                iconUrl: 'https://cdn-icons-png.flaticon.com/512/713/713437.png',
                iconSize: [40, 40],
                iconAnchor: [20, 20]
            });

            // Markers
            var pickup = L.marker([<?= $parcel['pickup_lat'] ?>, <?= $parcel['pickup_lng'] ?>]).addTo(map).bindPopup("Pickup Point");
            var delivery = L.marker([<?= $parcel['delivery_lat'] ?>, <?= $parcel['delivery_lng'] ?>]).addTo(map).bindPopup("Delivery Point");
            
            // The Moving Driver Marker
            var driverMarker = L.marker([<?= $parcel['driver_lat'] ?? 0 ?>, <?= $parcel['driver_lng'] ?? 0 ?>], {icon: scooterIcon}).addTo(map).bindPopup("Driver is here");

            // Live Update Function
            async function updateTracking() {
                try {
                    const response = await fetch('get_driver_location.php?tn=<?= $tracking_number ?>');
                    const data = await response.json();
                    
                    if (data.lat && data.lng) {
                        var newPos = new L.LatLng(data.lat, data.lng);
                        driverMarker.setLatLng(newPos); // Move the icon smoothly
                    }
                } catch (error) {
                    console.error("Error fetching driver location:", error);
                }
            }

            // Ask database for location every 5 seconds
            setInterval(updateTracking, 5000);
        </script>
    <?php elseif(isset($_GET['tn'])): ?>
        <p style="color:red;">Parcel not found. Please check your tracking number.</p>
    <?php endif; ?>
</div>

</body>
</html>