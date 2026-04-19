<?php
session_start();
include "db.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Function to get status color
function statusColor($status){
    $colors = [
        'Pending'=>'#f39c12',
        'Booked'=>'#6c757d',
        'Picked Up'=>'#3498db',
        'In Transit'=>'#17a2b8',
        'At Sorting Center'=>'#6610f2',
        'Out for Delivery'=>'#fd7e14',
        'Delivered'=>'#28a745',
        'Delayed'=>'#ffc107',
        'Returned'=>'#6f42c1',
        'Cancelled'=>'#dc3545'
    ];
    return $colors[$status] ?? '#6c757d';
}

// Function to get response color
function responseColor($response){
    $colors = [
        'Pending'=>'#f39c12',
        'Accepted'=>'#28a745',
        'Rejected'=>'#dc3545'
    ];
    return $colors[$response] ?? '#6c757d';
}

// Handle reassign via AJAX
if(isset($_POST['reassign'])){
    $parcel_id = intval($_POST['parcel_id']);
    $staff_id = intval($_POST['staff_id']);
    if($parcel_id && $staff_id){
        $conn->query("UPDATE bookings SET staff_id=$staff_id, status='Pending', staff_response='Pending' WHERE id=$parcel_id");
        exit('success');
    }
    exit('error');
}

// Fetch parcels for AJAX
if(isset($_GET['action']) && $_GET['action'] === 'fetch_parcels'){
    $parcels = $conn->query("
        SELECT b.*, u.name AS user_name, d.name AS staff_name 
        FROM bookings b
        LEFT JOIN users u ON b.user_id = u.id
        LEFT JOIN deliverystaff d ON b.staff_id = d.id
        ORDER BY b.id DESC
    ");
    $data = [];
    while($row = $parcels->fetch_assoc()){
        $data[] = $row;
    }
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

// Fetch dashboard stats
$totalUsers = $conn->query("SELECT COUNT(*) AS total FROM users")->fetch_assoc()['total'];
$totalParcels = $conn->query("SELECT COUNT(*) AS total FROM bookings")->fetch_assoc()['total'];
$delivered = $conn->query("SELECT COUNT(*) AS total FROM bookings WHERE status='Delivered'")->fetch_assoc()['total'];
$pending = $conn->query("SELECT COUNT(*) AS total FROM bookings WHERE status!='Delivered'")->fetch_assoc()['total'];

// Fetch all staff for dropdown
$staffs = $conn->query("SELECT id, name FROM deliverystaff");
$staffArray = [];
while($s = $staffs->fetch_assoc()){
    $staffArray[] = $s;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Poppins',sans-serif}
body{background:#f4f6f9}
.container{display:flex;min-height:100vh}
.sidebar{width:240px;background:#1e1e2f;color:#fff;padding:20px}
.sidebar h2{text-align:center;margin-bottom:30px;font-size:1.5rem}
.sidebar a{display:block;color:#ccc;text-decoration:none;padding:12px 15px;border-radius:6px;margin-bottom:8px;transition:0.3s}
.sidebar a:hover{background:#4f46e5;color:#fff}
.main{flex:1;padding:25px}
.topbar{display:flex;justify-content:space-between;align-items:center;margin-bottom:25px}
.topbar h1{font-size:24px;font-weight:600}
.admin-name{background:#fff;padding:10px 15px;border-radius:8px;box-shadow:0 2px 10px rgba(0,0,0,0.05)}
.cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:20px;margin-bottom:30px}
.card{background:#fff;padding:20px;border-radius:12px;box-shadow:0 4px 15px rgba(0,0,0,0.06)}
.card h3{font-size:15px;color:#777}
.card p{font-size:20px;font-weight:600;margin-top:10px;color:#333}
h2.section-title{margin:30px 0 15px;font-size:22px;color:#333}
.table-container{overflow-x:auto;}
table{width:100%;border-collapse:collapse;background:#fff;border-radius:8px;overflow:hidden}
th,td{padding:12px 10px;text-align:center;border-bottom:1px solid #eee;font-size:0.9rem}
th{background:#1e90ff;color:#fff}
tr:hover{background:#f1faff}
button, select{padding:5px 8px;border-radius:4px;border:1px solid #ccc;font-size:0.85rem;cursor:pointer}
button{background:#28a745;color:#fff;border:none;transition:0.3s}
button:hover{background:#218838}
.status-badge{padding:5px 8px;border-radius:12px;color:#fff;font-weight:600;font-size:0.85rem;}
/* Notification box */
#notifBox{position:fixed;top:20px;right:20px;background:#28a745;color:#fff;padding:12px 20px;border-radius:8px;box-shadow:0 2px 10px rgba(0,0,0,0.2);display:none;z-index:1000;}
</style>
</head>
<body>

<div class="container">
<?php include 'sidebar.php'; ?>

<div class="main">
<div class="topbar">
    <h1>Dashboard</h1>
    <div class="admin-name">
        Welcome, <strong><?= $_SESSION['admin'] ?? 'Admin' ?></strong>
    </div>
</div>

<div class="cards">
    <div class="card"><h3>Total Users</h3><p><?= $totalUsers ?></p></div>
    <div class="card"><h3>Total Parcels</h3><p><?= $totalParcels ?></p></div>
    <div class="card"><h3>Delivered</h3><p><?= $delivered ?></p></div>
    <div class="card"><h3>Pending</h3><p><?= $pending ?></p></div>
</div>

<h2 class="section-title">All Parcels</h2>
<div class="table-container">
<table id="parcelTable">
    <thead>
        <tr>
            <th>ID</th>
            <th>User</th>
            <th>Sender</th>
            <th>Receiver</th>
            <th>Weight</th>
            <th>Package Type</th>
            <th>Status</th>
            <th>Staff</th>
            <th>Staff Response</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>
</div>
</div>
</div>

<div id="notifBox"></div>

<script>
// Staff list from PHP
const staffList = <?= json_encode($staffArray) ?>;

// Show notification
function showNotification(msg,color='#28a745'){
    const box = document.getElementById('notifBox');
    box.textContent = msg;
    box.style.background = color;
    box.style.display = 'block';
    setTimeout(()=>{ box.style.display='none'; }, 3000);
}

// Load parcels
function loadParcels(){
    fetch('admin.php?action=fetch_parcels')
    .then(res=>res.json())
    .then(data=>{
        const tbody = document.querySelector('#parcelTable tbody');
        tbody.innerHTML = '';
        data.forEach(row=>{
            const statusColor = {
                'Pending':'#f39c12','Booked':'#6c757d','Picked Up':'#3498db',
                'In Transit':'#17a2b8','At Sorting Center':'#6610f2','Out for Delivery':'#fd7e14',
                'Delivered':'#28a745','Delayed':'#ffc107','Returned':'#6f42c1','Cancelled':'#dc3545'
            }[row.status]||'#6c757d';

            const responseColor = {
                'Pending':'#f39c12','Accepted':'#28a745','Rejected':'#dc3545'
            }[row.staff_response]||'#6c757d';

            const assignForm = (row.staff_response=='Rejected'||!row.staff_id) ? `
                <form class="assignForm" data-parcel-id="${row.id}" style="display:flex; gap:5px; justify-content:center; align-items:center;">
                    <select name="staff_id" required>
                        <option value="">Select Staff</option>
                        ${staffList.map(s=>`<option value="${s.id}">${s.name}</option>`).join('')}
                    </select>
                    <button type="submit">Assign</button>
                </form>` : '-';

            tbody.innerHTML += `
                <tr>
                    <td>${row.id}</td>
                    <td>${row.user_name||'-'}</td>
                    <td>${row.sender_name}</td>
                    <td>${row.receiver_name}</td>
                    <td>${row.parcel_weight} kg</td>
                    <td>${row.package_type}</td>
                    <td><span class="status-badge" style="background:${statusColor}">${row.status}</span></td>
                    <td>${row.staff_name||'-'}</td>
                    <td><span class="status-badge" style="background:${responseColor}">${row.staff_response||'Pending'}</span></td>
                    <td>${assignForm}</td>
                </tr>`;
        });
    });
}
loadParcels();
setInterval(loadParcels,5000);

// Handle assign via AJAX
document.addEventListener('submit',function(e){
    if(e.target.classList.contains('assignForm')){
        e.preventDefault();
        const form = e.target;
        const parcelId = form.dataset.parcelId;
        const staffId = form.querySelector('select[name="staff_id"]').value;
        if(!staffId) return;
        fetch('admin.php',{
            method:'POST',
            headers:{'Content-Type':'application/x-www-form-urlencoded'},
            body:`reassign=1&parcel_id=${parcelId}&staff_id=${staffId}`
        })
        .then(res=>res.text())
        .then(data=>{
            if(data.trim()==='success'){
                showNotification('Driver assigned successfully!');
                loadParcels();
            } else {
                showNotification('Error assigning driver','#dc3545');
            }
        });
    }
});
</script>

</body>
</html>
