<?php
include 'config.php';

$tn = $_GET['tn'] ?? '';

if ($tn) {
    // Find the driver assigned to this tracking number and get their current location
    $query = "SELECT s.current_lat, s.current_lng 
              FROM bookings b 
              JOIN deliverystaff s ON b.staff_id = s.id 
              WHERE b.tracking_number = '$tn' LIMIT 1";
    
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);

    if ($row) {
        echo json_encode([
            'lat' => (float)$row['current_lat'],
            'lng' => (float)$row['current_lng']
        ]);
    } else {
        echo json_encode(['error' => 'No driver found']);
    }
}
?>