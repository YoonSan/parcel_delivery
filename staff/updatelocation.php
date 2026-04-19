<?php
session_start();
include 'config.php';
if(isset($_SESSION['staff_id']) && isset($_POST['lat'])){
    $sid = $_SESSION['staff_id'];
    $lat = $_POST['lat'];
    $lng = $_POST['lng'];
    $heading = $_POST['heading'] ?? 0;
    $conn->query("UPDATE deliverystaff SET current_lat=$lat, current_lng=$lng WHERE id=$sid");
}
?>