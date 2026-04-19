<?php
session_start();
include 'config.php';
include 'navbar.php';

if(!isset($_SESSION['user_id'])){
    header("Location: dashboard.php");
    exit();
}

$success = $error = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $sender_name = $_POST['sender_name'];
    $sender_address = $_POST['sender_address'];
    $receiver_name = $_POST['receiver_name'];
    $receiver_address = $_POST['receiver_address'];
    $parcel_weight = (float)$_POST['parcel_weight'];
    $package_type = $_POST['package_type'];

    // Hidden fields
    $dist_km = (float)$_POST['distance_km'] ?? 0;
    $total_fare = (float)$_POST['total_fare'] ?? 0;
    $p_lat = (float)$_POST['pickup_lat'] ?? 0;
    $p_lng = (float)$_POST['pickup_lng'] ?? 0;
    $d_lat = (float)$_POST['delivery_lat'] ?? 0;
    $d_lng = (float)$_POST['delivery_lng'] ?? 0;
    $delivery_days = (int)$_POST['delivery_days'] ?? 7;

    $tracking_number = 'SP'.strtoupper(substr(md5(uniqid()),0,10));

    $stmt = $conn->prepare("
        INSERT INTO bookings 
        (user_id, tracking_number, sender_name, sender_address, receiver_name, receiver_address, parcel_weight, package_type, status, booking_date, pickup_lat, pickup_lng, delivery_lat, delivery_lng, distance_km, total_fare, delivery_days) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Pending', NOW(), ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("isssssdsddddddi", $_SESSION['user_id'], $tracking_number, $sender_name, $sender_address, $receiver_name, $receiver_address, $parcel_weight, $package_type, $p_lat, $p_lng, $d_lat, $d_lng, $dist_km, $total_fare, $delivery_days);

    if($stmt->execute()) {
        $success = "Booking Successful! Tracking ID: " . $tracking_number . ". Estimated Delivery: ".$delivery_days." Days.";
    } else {
        $error = "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Book Shipment | SwiftParcel</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f7f6; margin: 0; }
        .main-wrapper { max-width: 1200px; margin: 90px auto 20px auto; padding: 20px; display: grid; grid-template-columns: 1.2fr 0.8fr; gap: 25px; }
        .card { background: #fff; padding: 25px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .full { grid-column: 1 / -1; }
        input, select { width: 100%; padding: 12px; margin-top: 5px; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; }
        #map { height: 450px; width: 100%; border-radius: 12px; border: 1px solid #ddd; z-index: 1; }
        .fare-box { background: #ffffff; padding: 20px; border-radius: 12px; border: 2px solid #2A3EB1; text-align: center; margin-top: 15px; }
        .fare-price { font-size: 2rem; font-weight: bold; color: #2A3EB1; display: block; }
        #status-msg { font-size: 0.85rem; color: #666; font-style: italic; height: 20px; }
        button { width: 100%; margin-top: 20px; padding: 15px; background: #2A3EB1; color: #fff; border: none; border-radius: 8px; cursor: pointer; font-weight: bold; }
        .notify-box { padding: 15px; margin-bottom: 20px; border-radius: 8px; color: #fff; text-align: center; grid-column: 1/-1; }
        .success { background: #2ecc71; } 
        .error { background: #e74c3c; }
    </style>
</head>
<body>

<div class="main-wrapper">
    <?php if($success): ?><div class="notify-box success"><?= $success ?></div><?php endif; ?>
    <?php if($error): ?><div class="notify-box error"><?= $error ?></div><?php endif; ?>

    <div class="left-col">
        <div class="card">
            <h2>Shipment Details</h2>
            <form method="POST">
                <div class="form-grid">
                    <div class="form-group"><label>Sender Name</label><input type="text" name="sender_name" required></div>
                    <div class="form-group"><label>Receiver Name</label><input type="text" name="receiver_name" required></div>
                    
                    <div class="form-group full">
                        <label>Pickup Address</label>
                        <input type="text" name="sender_address" id="s_addr" placeholder="Pickup Address" required>
                    </div>
                    
                    <div class="form-group full">
                        <label>Delivery Address</label>
                        <input type="text" name="receiver_address" id="r_addr" placeholder="Delivery Address" required>
                    </div>
                    
                    <div class="form-group"><label>Weight (kg)</label><input type="number" step="0.1" name="parcel_weight" id="p_weight" value="1" required></div>
                    <div class="form-group">
                        <label>Type</label>
                        <select name="package_type">
                            <option>Document</option>
                            <option>Box</option>
                            <option>Fragile</option>
                        </select>
                    </div>
                </div>

                <input type="hidden" name="pickup_lat" id="p_lat">
                <input type="hidden" name="pickup_lng" id="p_lng">
                <input type="hidden" name="delivery_lat" id="d_lat">
                <input type="hidden" name="delivery_lng" id="d_lng">
                <input type="hidden" name="distance_km" id="dist_val">
                <input type="hidden" name="total_fare" id="fare_val">
                <input type="hidden" name="delivery_days" id="delivery_days_val">

                <button type="submit">Confirm & Book Parcel</button>
            </form>
        </div>
    </div>

    <div class="right-col">
        <div id="map"></div>
        <div class="fare-box">
            <div id="status-msg">Enter addresses to calculate...</div>
            <span style="color: #666;">Distance: <b id="dist-ui">0.00</b> km</span><br>
            <span style="color: #666;">Estimated Delivery: <b id="delivery-days">-</b></span>
            <span class="fare-price">Rs <span id="fare-ui">0.00</span></span>
        </div>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
var map = L.map('map').setView([27.7172, 85.3240], 7);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '© OpenStreetMap' }).addTo(map);

var pMarker, dMarker, routeLine;

function calculateDistance(lat1, lon1, lat2, lon2) {
    const R = 6371; // km
    const dLat = (lat2 - lat1) * Math.PI / 180;
    const dLon = (lon2 - lon1) * Math.PI / 180;
    const a = 0.5 - Math.cos(dLat)/2 + Math.cos(lat1*Math.PI/180) * Math.cos(lat2*Math.PI/180) * (1 - Math.cos(dLon))/2;
    return R * 2 * Math.asin(Math.sqrt(a));
}

async function getCoords(address) {
    if (address.length < 3) return null;
    try {
        const res = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address + ", Nepal")}`);
        const data = await res.json();
        return data.length > 0 ? { lat: parseFloat(data[0].lat), lon: parseFloat(data[0].lon) } : null;
    } catch (e) { console.error(e); return null; }
}

// Check if inside Kathmandu Valley
function isInsideValley(lat, lon) {
    const minLat = 27.55, maxLat = 27.80;
    const minLon = 85.20, maxLon = 85.45;
    return lat >= minLat && lat <= maxLat && lon >= minLon && lon <= maxLon;
}

async function autoCalculate() {
    const start = document.getElementById('s_addr').value;
    const end = document.getElementById('r_addr').value;
    const weight = parseFloat(document.getElementById('p_weight').value) || 1;
    const statusMsg = document.getElementById('status-msg');

    if (start.length > 3 && end.length > 3) {
        statusMsg.innerText = "Searching locations...";
        const p1 = await getCoords(start);
        const p2 = await getCoords(end);

        if (p1 && p2) {
            if (pMarker) map.removeLayer(pMarker);
            if (dMarker) map.removeLayer(dMarker);
            if (routeLine) map.removeLayer(routeLine);

            pMarker = L.marker([p1.lat, p1.lon]).addTo(map);
            dMarker = L.marker([p2.lat, p2.lon]).addTo(map);
            routeLine = L.polyline([[p1.lat, p1.lon], [p2.lat, p2.lon]], {color: '#2A3EB1'}).addTo(map);
            map.fitBounds(routeLine.getBounds(), {padding: [40, 40]});

            const dist = calculateDistance(p1.lat, p1.lon, p2.lat, p2.lon).toFixed(2);
            const fare = Math.round(150 + (dist * 15) + (weight * 20));

            document.getElementById('dist-ui').innerText = dist;
            document.getElementById('fare-ui').innerText = fare.toLocaleString();

            // Calculate delivery days
            let deliveryDays = isInsideValley(p2.lat, p2.lon) ? 3 : 7;
            document.getElementById('delivery-days').innerText = deliveryDays + " Days";
            document.getElementById('delivery_days_val').value = deliveryDays;

            // Fill hidden fields
            document.getElementById('p_lat').value = p1.lat;
            document.getElementById('p_lng').value = p1.lon;
            document.getElementById('d_lat').value = p2.lat;
            document.getElementById('d_lng').value = p2.lon;
            document.getElementById('dist_val').value = dist;
            document.getElementById('fare_val').value = fare;

            statusMsg.innerText = "Location found!";
        } else {
            statusMsg.innerText = "Could not find locations. Try again.";
        }
    }
}

document.getElementById('s_addr').addEventListener('input', autoCalculate);
document.getElementById('r_addr').addEventListener('input', autoCalculate);
document.getElementById('p_weight').addEventListener('input', autoCalculate);
</script>

</body>
</html>