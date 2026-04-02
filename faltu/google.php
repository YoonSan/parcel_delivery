<?php
session_start();
include 'config.php';
include 'nav.php';

$success = $error = "";
$tracking_number = "";
$apiKey = "AIzaSyCSs6prZ-xszo08xDrAo2i2px9lsavLXk4"; // Replace with your key

if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
}

// Helper function: geocode address only on form submission
function geocodeAddress($address, $apiKey) {
    $address = urlencode($address);
    $url = "https://maps.googleapis.com/maps/api/geocode/json?address={$address}&key={$apiKey}";
    $resp = json_decode(file_get_contents($url), true);
    if ($resp['status'] === 'OK') {
        return [
            $resp['results'][0]['geometry']['location']['lat'],
            $resp['results'][0]['geometry']['location']['lng']
        ];
    }
    return [null, null];
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $sender_name      = $_POST['sender_name'] ?? '';
    $sender_address   = $_POST['sender_address'] ?? '';
    $receiver_name    = $_POST['receiver_name'] ?? '';
    $receiver_address = $_POST['receiver_address'] ?? '';
    $parcel_weight    = $_POST['parcel_weight'] ?? '';
    $package_type     = $_POST['package_type'] ?? '';

    $pickup_lat   = $_POST['pickup_lat'] ?: null;
    $pickup_lng   = $_POST['pickup_lng'] ?: null;
    $delivery_lat = $_POST['delivery_lat'] ?: null;
    $delivery_lng = $_POST['delivery_lng'] ?: null;

    if (empty($sender_name) || empty($sender_address) || empty($receiver_name) || empty($receiver_address) || empty($parcel_weight) || empty($package_type)) {
        $error = "Please fill in all required fields.";
    } else {
        // Only geocode if the user hasn't selected from suggestions
        if (!$pickup_lat || !$pickup_lng) {
            list($pickup_lat, $pickup_lng) = geocodeAddress($sender_address, $apiKey);
        }
        if (!$delivery_lat || !$delivery_lng) {
            list($delivery_lat, $delivery_lng) = geocodeAddress($receiver_address, $apiKey);
        }

        // Use default coordinates if still missing
        if (!$pickup_lat) $pickup_lat = 28.3949;
        if (!$pickup_lng) $pickup_lng = 84.1240;
        if (!$delivery_lat) $delivery_lat = 28.3949;
        if (!$delivery_lng) $delivery_lng = 84.1240;

        $tracking_number = 'SP'.strtoupper(substr(md5(uniqid()), 0, 10));
        $status = 'Pending';

        $stmt = $conn->prepare("
            INSERT INTO bookings
            (user_id, tracking_number, sender_name, sender_address,
             receiver_name, receiver_address, parcel_weight, package_type,
             status, booking_date, pickup_lat, pickup_lng, delivery_lat, delivery_lng)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?, ?, ?)
        ");
        $stmt->bind_param(
            "isssssdssdddd",
            $_SESSION['user_id'],
            $tracking_number,
            $sender_name,
            $sender_address,
            $receiver_name,
            $receiver_address,
            $parcel_weight,
            $package_type,
            $status,
            $pickup_lat,
            $pickup_lng,
            $delivery_lat,
            $delivery_lng
        );

        if ($stmt->execute()) {
            $success = "Parcel booked successfully!";
        } else {
            $error = "Database error: ".$stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>New Shipment | SwiftParcel</title>
<style>
.containers{max-width:900px;margin:3rem auto;padding:1rem;}
.card{background:#fff;padding:2rem;border-radius:10px;box-shadow:0 10px 25px rgba(0,0,0,0.1);}
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:1.2rem;}
.form-group{display:flex;flex-direction:column;}
.form-group.full{grid-column:1 / -1;}
input, select{padding:0.7rem;border-radius:6px;border:1px solid #ccc;font-size:0.95rem;}
input[type=text]{height:60px;}
button{margin-top:1.8rem;padding:1rem;background:#2A3EB1;color:#fff;border:none;border-radius:10px;font-size:1.05rem;font-weight:600;cursor:pointer;}
.notify-box{position:fixed;top:20px;right:20px;min-width:280px;padding:1rem;border-radius:12px;color:#fff;z-index:9999;}
.notify-box.success{background:#22c55e;}
.notify-box.error{background:#ef4444;}
.tracking-card{background:#f7f9fc;border-left:5px solid #2A3EB1;padding:1.5rem 2rem;margin:1.5rem 0;border-radius:8px;}
</style>
</head>
<body>

<?php if($success || $error): ?>
<div class="notify-box <?= $success ? 'success' : 'error' ?>">
    <?= htmlspecialchars($success ?: $error) ?>
</div>
<?php endif; ?>

<div class="containers">
<?php if($success): ?>
<div class="tracking-card">
<h3>✅ Parcel Booked Successfully!</h3>
<p><b>Tracking Number:</b> <?= htmlspecialchars($tracking_number) ?></p>
<p>Use this number to <a href="track.php?tracking=<?= htmlspecialchars($tracking_number) ?>">track your parcel</a>.</p>
</div>
<?php endif; ?>

<div class="card">
<form method="POST" id="bookingForm" novalidate>
<div class="form-grid">

<div class="form-group">
<label>Sender Name</label>
<input type="text" name="sender_name" required>
</div>

<div class="form-group">
<label>Receiver Name</label>
<input type="text" name="receiver_name" required>
</div>

<div class="form-group full">
<label>Pickup Address</label>
<input type="text" id="pickup_address" name="sender_address" placeholder="Start typing pickup address">
</div>

<div class="form-group full">
<label>Delivery Address</label>
<input type="text" id="delivery_address" name="receiver_address" placeholder="Start typing delivery address">
</div>

<div class="form-group">
<label>Parcel Weight (kg)</label>
<input type="number" step="0.01" name="parcel_weight" required>
</div>

<div class="form-group">
<label>Package Type</label>
<select name="package_type" required>
<option value="">Select</option>
<option value="Document">Document</option>
<option value="Box">Box</option>
<option value="Fragile">Fragile</option>
<option value="Electronics">Electronics</option>
</select>
</div>

</div>

<input type="hidden" name="pickup_lat" id="pickup_lat">
<input type="hidden" name="pickup_lng" id="pickup_lng">
<input type="hidden" name="delivery_lat" id="delivery_lat">
<input type="hidden" name="delivery_lng" id="delivery_lng">

<button type="submit">Book Service</button>
</form>
</div>
</div>

<?php include 'footer.php'; ?>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCSs6prZ-xszo08xDrAo2i2px9lsavLXk4"></script>
<script>
function initAutocomplete(){
    const pickup = new google.maps.places.Autocomplete(document.getElementById('pickup_address'));
    const delivery = new google.maps.places.Autocomplete(document.getElementById('delivery_address'));

    pickup.addListener('place_changed', () => {
        const place = pickup.getPlace();
        if(place && place.geometry){
            document.getElementById('pickup_lat').value = place.geometry.location.lat();
            document.getElementById('pickup_lng').value = place.geometry.location.lng();
        }
    });

    delivery.addListener('place_changed', () => {
        const place = delivery.getPlace();
        if(place && place.geometry){
            document.getElementById('delivery_lat').value = place.geometry.location.lat();
            document.getElementById('delivery_lng').value = place.geometry.location.lng();
        }
    });
}
window.onload = initAutocomplete;

// Hide notifications
setTimeout(()=>{
    const notify=document.querySelector('.notify-box');
    if(notify){notify.style.opacity='0';notify.style.transform='translateX(40px)';}
},4000);
</script>

</body>
</html>
