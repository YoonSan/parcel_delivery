<?php
session_start();
include 'config.php';
include 'nav.php'; // Your navbar

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$success = $error = "";
$tracking_number = "";

if($_SERVER["REQUEST_METHOD"]=="POST"){
    $sender_name = $_POST['sender_name'];
    $sender_address = $_POST['sender_address'];
    $receiver_name = $_POST['receiver_name'];
    $receiver_address = $_POST['receiver_address'];
    $parcel_weight = $_POST['parcel_weight'];
    $package_type = $_POST['package_type'];
    $status = 'Pending';

    // Google Maps API key
    $apiKey = "YOUR_GOOGLE_MAPS_API_KEY"; // Replace with your actual key

    // Function to get lat/lng from address
    function getLatLng($address, $apiKey){
        $address = urlencode($address);
        $url = "https://maps.googleapis.com/maps/api/geocode/json?address={$address}&key={$apiKey}";
        $resp_json = file_get_contents($url);
        $resp = json_decode($resp_json, true);
        if($resp['status']=='OK'){
            $lat = $resp['results'][0]['geometry']['location']['lat'];
            $lng = $resp['results'][0]['geometry']['location']['lng'];
            return [$lat, $lng];
        } else {
            return [NULL, NULL];
        }
    }

    // Get coordinates
    list($pickup_lat, $pickup_lng) = getLatLng($sender_address, $apiKey);
    list($delivery_lat, $delivery_lng) = getLatLng($receiver_address, $apiKey);

    // Generate unique tracking number
    $tracking_number = 'SP'.strtoupper(substr(md5(uniqid()),0,10));

    // Prepare insert statement
    $stmt = $conn->prepare("
        INSERT INTO bookings 
        (user_id, tracking_number, sender_name, sender_address, receiver_name, receiver_address, parcel_weight, package_type, status, booking_date, pickup_lat, pickup_lng, delivery_lat, delivery_lng) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?, ?, ?)
    ");

    // Correct bind_param with 13 types
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

    if($stmt->execute()){
        $success = "Parcel booked successfully!";
    } else {
        $error = "Error while booking parcel.";
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
/* ===== Form & Layout ===== */
.containers{max-width:900px;margin:3rem auto;padding:1rem;}
.card{background:#fff;padding:2rem;border-radius:10px;box-shadow:0 10px 25px rgba(0,0,0,0.1);}
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:1.2rem;}
.form-group{display:flex;flex-direction:column;}
.form-group.full{grid-column:1 / -1;}
input, textarea, select{padding:0.7rem;border-radius:6px;border:1px solid #ccc;font-size:0.95rem;}
textarea{resize:none;height:80px;}
button{margin-top:1.8rem;padding:1rem;background:linear-gradient(135deg,#2A3EB1,#5563DE);color:#fff;border:none;border-radius:10px;font-size:1.05rem;font-weight:600;cursor:pointer;transition:all 0.3s ease;box-shadow:0 8px 18px rgba(42,62,177,0.35);}
button:hover{transform:translateY(-2px);box-shadow:0 12px 25px rgba(42,62,177,0.45);}

/* Notification */
.notify-box{position:fixed;top:20px;right:20px;min-width:280px;max-width:360px;padding:1rem 1.2rem;border-radius:12px;color:#fff;font-size:0.95rem;font-weight:500;z-index:9999;box-shadow:0 15px 30px rgba(0,0,0,0.25);}
.notify-box.success{background:linear-gradient(135deg,#0f9d58,#34d399);}
.notify-box.error{background:linear-gradient(135deg,#d93025,#f87171);}

/* Tracking Card */
.tracking-card{
    background:#f7f9fc;
    border-left:5px solid #2A3EB1;
    padding:1.5rem 2rem;
    margin:1.5rem 0;
    border-radius:8px;
    box-shadow:0 5px 15px rgba(0,0,0,0.05);
}
.tracking-card h3{margin:0;color:#2A3EB1;}
.tracking-card p{margin:0.5rem 0 0;color:#333;font-weight:500;font-size:1rem;}
</style>
</head>
<body>

<?php if($success || $error): ?>
<div class="notify-box <?= $success ? 'success' : 'error' ?>">
  <?= $success ?: $error ?>
</div>
<?php endif; ?>

<section class="page-header">
  <h1>New Shipment</h1>
  <p>Create and manage your parcel delivery</p>
</section>

<div class="containers">

  <?php if($success): ?>
    <div class="tracking-card">
        <h3>✅ Parcel Booked Successfully!</h3>
        <p><b>Tracking Number:</b> <?= htmlspecialchars($tracking_number) ?></p>
        <p>Use this number to <a href="track.php?tracking=<?= htmlspecialchars($tracking_number) ?>">track your parcel</a> in real-time.</p>
    </div>
  <?php endif; ?>

  <div class="card">
    <form method="POST">
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
          <textarea name="sender_address" required></textarea>
        </div>

        <div class="form-group full">
          <label>Delivery Address</label>
          <textarea name="receiver_address" required></textarea>
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

      <button type="submit">Book Service</button>
    </form>
  </div>
</div>

<?php include 'footer.php'; ?>

<script>
setTimeout(() => {
  const notify = document.querySelector('.notify-box');
  if(notify){
    notify.style.opacity = '0';
    notify.style.transform = 'translateX(40px)';
  }
}, 4000);
</script>

</body>
</html>
AIzaSyCSs6prZ-xszo08xDrAo2i2px9lsavLXk4