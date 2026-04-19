<?php
include 'config.php';
session_start();

if($_SERVER["REQUEST_METHOD"]=="POST"){
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email=? AND password=?");
    $stmt->bind_param("ss",$email,$password);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows>0){
        $user = $result->fetch_assoc();
        $_SESSION['user_id']=$user['id'];
        $_SESSION['user_name']=$user['name'];
        $_SESSION['user_email']=$user['email'];
        $_SESSION['user_phone']=$user['phone'];
        $_SESSION['user_address']=$user['address'];
        $_SESSION['role'] = 'user';
        header("Location: ./user/dashboard.php");
        exit();
    } else {
        $error = "Invalid Email or Password!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login | SwiftParcel</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
/* ===== Global ===== */
body{
    margin:0;
    padding:0;
    font-family:'Poppins',sans-serif;
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    background: linear-gradient(135deg,#5563DE,#6D83F2,#FFD166,#F6AE2D);
    background-size:300% 300%;
    animation:gradientBG 15s ease infinite;
    overflow:hidden;
}
@keyframes gradientBG {
    0%{background-position:0% 50%;}
    50%{background-position:100% 50%;}
    100%{background-position:0% 50%;}
}

/* Glass Modal Style */
.modal{
    display:flex;
    justify-content:center;
    align-items:center;
    position:fixed;
    top:0;
    left:0;
    width:100%;
    height:100%;
    background:rgba(0,0,0,0.5);
    z-index:100;
}
.modal-content{
    background: rgba(255,255,255,0.15);
    padding:2.5rem 2rem;
    border-radius:1rem;
    backdrop-filter: blur(10px);
    box-shadow:0 5px 25px rgba(0,0,0,0.2);
    width:360px;
    text-align:center;
    color:#fff;
    position:relative;
    overflow:hidden;
    animation:fadeIn 1s ease forwards;
}
.modal-content h2{
    margin-bottom:1.5rem;
    font-size:2rem;
}
.input-container{
    position:relative;
}
input{
    width:100%;
    padding:0.8rem;
    margin:0.8rem 0;
    border:none;
    border-radius:0.5rem;
    background: rgba(255,255,255,0.85);
    color:#333;
    font-size:1rem;
}
.show-password{
    position:absolute;
    right:10px;
    top:50%;
    transform:translateY(-50%);
    cursor:pointer;
    font-size:1.1rem;
    color:#333;
}
button{
    width:100%;
    padding:0.8rem;
    margin-top:1rem;
    border:none;
    border-radius:2rem;
    background:#FFD166;
    color:#333;
    font-weight:600;
    cursor:pointer;
    transition:0.3s;
    position:relative;
    overflow:hidden;
}
button:hover{background:#F6AE2D; transform:scale(1.05);}
button span.ripple{
    position:absolute;
    border-radius:50%;
    transform:scale(0);
    animation:ripple 0.6s linear;
    background: rgba(255,255,255,0.5);
}
@keyframes ripple{
    to{transform:scale(4);opacity:0;}
}
.close-btn{
    position:absolute;
    top:10px;
    right:15px;
    font-size:1.5rem;
    cursor:pointer;
    color:#FFD166;
}
.error{
    color:#FF6B6B;
    margin-top:0.8rem;
}
p.register-link{
    margin-top:1rem;
    font-size:0.9rem;
    color:#fff;
}
p.register-link a{
    color:#FFD166;
    text-decoration:none;
}
p.register-link a:hover{color:#F6AE2D;}
@keyframes fadeIn{
    from{opacity:0; transform:translateY(20px);}
    to{opacity:1; transform:translateY(0);}
}

/* Floating packages */
.package{
    position:absolute;
    width:30px;
    height:30px;
    background:#FFD166;
    border-radius:0.5rem;
    opacity:0.7;
    animation:floatUp linear infinite;
}
.package1{left:10%; animation-duration:12s; top:90%;}
.package2{left:30%; animation-duration:15s; top:95%;}
.package3{left:50%; animation-duration:18s; top:100%;}
.package4{left:70%; animation-duration:14s; top:85%;}
.package5{left:90%; animation-duration:16s; top:92%;}
@keyframes floatUp {
    0%{transform:translateY(0) rotate(0deg);}
    100%{transform:translateY(-120vh) rotate(360deg);}
}

/* Responsive */
@media(max-width:400px){.modal-content{width:90%;}}
</style>
</head>
<body>

<!-- Floating Hero Packages -->
<div class="package package1"></div>
<div class="package package2"></div>
<div class="package package3"></div>
<div class="package package4"></div>
<div class="package package5"></div>

<!-- Login Modal -->
<div class="modal" id="loginModal">
    <div class="modal-content">
        <span class="close-btn" id="closeModal">&times;</span>
        <h2>Login</h2>
        <form method="POST">
            <input type="email" name="email" placeholder="Email" required>
            <div class="input-container">
                <input type="password" id="password" name="password" placeholder="Password" required>
                <span class="show-password" id="togglePassword">&#128065;</span>
            </div>
            <button type="submit">Login</button>
            <?php if(isset($error)) echo "<p class='error'>$error</p>"; ?>
        </form>
        <p class="register-link">Don't have an account? <a href="register.php">Register</a></p>
    </div>
</div>

<script>
// Close Modal and redirect to index.php
const modal = document.getElementById('loginModal');
const closeModal = document.getElementById('closeModal');
closeModal.onclick = () => {
    window.location.href = 'index.php';
};
window.onclick = e => { if(e.target==modal) modal.style.display='none'; };

// Ripple Effect
document.querySelectorAll('button').forEach(btn=>{
    btn.addEventListener('click', function(e){
        const ripple=document.createElement('span');ripple.classList.add('ripple');this.appendChild(ripple);
        const rect=this.getBoundingClientRect();
        ripple.style.left=(e.clientX-rect.left)+'px';
        ripple.style.top=(e.clientY-rect.top)+'px';
        setTimeout(()=>{ripple.remove();},600);
    });
});

// Password toggle
const togglePassword = document.getElementById('togglePassword');
const password = document.getElementById('password');
togglePassword.addEventListener('click', () => {
    const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
    password.setAttribute('type', type);
    togglePassword.style.color = type === 'password' ? '#333' : '#FFD166';
});
</script>

</body>
</html>
