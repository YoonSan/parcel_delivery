<?php
header('Content-Type: application/json');
include 'config.php'; // your DB connection file

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if($name && $email && $message){
        $stmt = $conn->prepare("INSERT INTO contact_messages (name,email,message,created_at) VALUES (?,?,?,NOW())");
        $stmt->bind_param("sss", $name, $email, $message);
        if($stmt->execute()){
            echo json_encode(['success' => true]);
            exit;
        } else {
            echo json_encode(['success' => false, 'error' => 'Database error']);
            exit;
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'All fields are required']);
        exit;
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
    exit;
}
?>
