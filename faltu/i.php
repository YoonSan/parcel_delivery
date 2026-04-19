<?php session_start(); include 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SwiftParcel Premium Delivery</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"/>
<style>
/* ===== Global ===== */
*{margin:0;padding:0;box-sizing:border-box;font-family:'Poppins',sans-serif;}
body{scroll-behavior:smooth; overflow-x:hidden; color:#fff; background:#5563DE; position:relative;}
@keyframes gradientBG {0%{background-position:0% 50%;}50%{background-position:100% 50%;}100%{background-position:0% 50%;}}
body{background:linear-gradient(135deg,#5563DE,#6D83F2,#FFD166,#F6AE2D); background-size:300% 300%; animation:gradientBG 15s ease infinite;}

/* Navbar */
nav{display:flex;justify-content:space-between;align-items:center;padding:1rem 2rem;background: rgba(0,0,0,0.2);backdrop-filter:blur(10px);position:fixed;width:100%;top:0;z-index:100;border-radius:0 0 15px 15px;transition:0.3s;}
nav h1{font-size:1.8rem;}
nav ul{display:flex;list-style:none;gap:1.5rem;}
nav ul li a{position:relative;color:#fff;font-weight:500;transition:0.3s;text-decoration:none;}
nav ul li a.active::after{content:'';position:absolute;bottom:-5px;left:0;width:100%;height:2px;background:#FFD166;}

/* Hero */
#home{height:100vh; display:flex; flex-direction:column; justify-content:center; align-items:center; text-align:center; position:relative; overflow:hidden;}
#home::after{content:''; position:absolute; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.4);}
#home h2,#home p,#home button{position:relative; z-index:1;}
#home h2{font-size:3rem; margin-bottom:1rem; animation:floatText 2s ease-in-out infinite alternate;}
#home p{font-size:1.2rem; margin-bottom:2rem;}
#home button{padding:0.8rem 2rem; border:none; border-radius:2rem; background:#FFD166; color:#333; cursor:pointer; font-weight:600; transition:0.3s; animation:pulseBtn 2s infinite;}
#home button:hover{background:#F6AE2D; transform:scale(1.05);}
@keyframes floatText{0%{transform:translateY(0);}100%{transform:translateY(-10px);}}
@keyframes pulseBtn{0%{transform:scale(1);}50%{transform:scale(1.05);}100%{transform:scale(1);}}

.package {position:absolute;width:40px;height:40px;opacity:0.7;background:#FFD166;border-radius:0.5rem;animation:floatUp linear infinite;}
.package1{left:10%;animation-duration:12s;top:90%;}
.package2{left:30%;animation-duration:15s;top:95%;}
.package3{left:50%;animation-duration:18s;top:100%;}
.package4{left:70%;animation-duration:14s;top:85%;}
.package5{left:90%;animation-duration:16s;top:92%;}
@keyframes floatUp {0%{transform:translateY(0) rotate(0deg);}100%{transform:translateY(-120vh) rotate(360deg);}}

/* Sections */
section{padding:6rem 2rem 3rem; max-width:800px; margin:auto; opacity:0; transform:translateY(50px); transition:all 1s;}
section.visible{opacity:1; transform:translateY(0);}
.glass-box{background: rgba(255,255,255,0.15); padding:2rem; border-radius:1rem; margin-top:2rem; box-shadow:0 5px 25px rgba(0,0,0,0.2); transition:0.3s, background 3s linear; background:linear-gradient(135deg, rgba(255,255,255,0.15), rgba(255,255,255,0.05));}
.glass-box:hover{transform:translateY(-5px); box-shadow:0 8px 30px rgba(0,0,0,0.25);}
/* ===== HOW IT WORKS (PREMIUM) ===== */
.how-it-works{
    padding:6rem 2rem;
    text-align:center;
}

.section-title{
    font-size:2.5rem;
    margin-bottom:0.5rem;
}

.section-subtitle{
    font-size:1rem;
    opacity:0.85;
    margin-bottom:4rem;
}

.timeline{
    position:relative;
    max-width:900px;
    margin:auto;
}

.timeline::before{
    content:'';
    position:absolute;
    left:50%;
    top:0;
    width:4px;
    height:100%;
    background:linear-gradient(#FFD166,#F6AE2D);
    transform:translateX(-50%);
    border-radius:2px;
}

.timeline-step{
    display:flex;
    align-items:center;
    justify-content:space-between;
    margin-bottom:4rem;
    position:relative;
}

.timeline-step:nth-child(even){
    flex-direction:row-reverse;
}

.timeline-step .icon{
    width:70px;
    height:70px;
    background:#FFD166;
    border-radius:50%;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:1.8rem;
    color:#333;
    z-index:2;
    box-shadow:0 0 0 8px rgba(255,209,102,0.3);
    transition:0.4s;
}

.timeline-step:hover .icon{
    transform:scale(1.2) rotate(10deg);
    box-shadow:0 0 25px #FFD166;
}

.timeline-step .content{
    width:42%;
    background:rgba(255,255,255,0.15);
    backdrop-filter:blur(10px);
    padding:1.8rem;
    border-radius:1rem;
    text-align:left;
    box-shadow:0 5px 25px rgba(0,0,0,0.25);
    transition:0.4s;
}

.timeline-step:hover .content{
    transform:translateY(-8px);
    box-shadow:0 10px 35px rgba(0,0,0,0.35);
}

.timeline-step h3{
    margin-bottom:0.5rem;
    color: #000; /* changed from #FFD166 to black */
}


.timeline-step p{
    font-size:0.95rem;
    opacity:0.9;
}

/* Responsive */
@media(max-width:768px){
    .timeline::before{left:30px;}
    .timeline-step{
        flex-direction:column;
        align-items:flex-start;
        padding-left:60px;
    }
    .timeline-step:nth-child(even){
        flex-direction:column;
    }
    .timeline-step .content{
        width:100%;
        margin-top:1rem;
    }
}


/* Forms */
input,select,textarea,button{width:100%; padding:0.8rem; border-radius:0.5rem; border:none; margin-top:1rem; font-size:1rem;}
input,select,textarea{background:rgba(255,255,255,0.85); color:#333;}
button{background:#FFD166; color:#333; cursor:pointer; transition:0.3s; position:relative; overflow:hidden;}
button:hover{background:#F6AE2D;}
button span.ripple{position:absolute;border-radius:50%;transform:scale(0);animation:ripple 0.6s linear;background: rgba(255,255,255,0.5);}
@keyframes ripple{to{transform:scale(4);opacity:0;}}
button.success{background:#06D6A0 !important;color:#fff;transform:scale(1.05);transition:all 0.3s;}

.progress-bar{width:100%; height:10px; background:rgba(255,255,255,0.3); border-radius:5px; margin-top:1rem; overflow:hidden;}
.progress-fill{height:10px; background:#FFD166; width:0%; transition:width 1s;}

/
/* Chat */
#chatIcon{position:fixed; bottom:30px; right:30px; width:60px; height:60px; background:#FFD166; border-radius:50%; display:flex; align-items:center; justify-content:center; cursor:pointer; z-index:300; box-shadow:0 5px 25px rgba(0,0,0,0.3); transition:0.3s; font-size:1.5rem;}
#chatIcon.pulse{animation:pulse 1s infinite;}
@keyframes pulse{0%{transform:scale(1);}50%{transform:scale(1.2);}100%{transform:scale(1);}}

#chatWindow{position:fixed; bottom:100px; right:30px; width:320px; max-height:450px; background:rgba(255,255,255,0.1); border-radius:1rem; display:none; flex-direction:column; backdrop-filter:blur(10px); padding:0.5rem; box-shadow:0 10px 40px rgba(0,0,0,0.3); z-index:300; overflow:hidden;}
#chatHeader{background:#FFD166; color:#333; padding:0.5rem 1rem; border-radius:0.8rem 0.8rem 0 0; font-weight:600; text-align:center;}
#chatMessages{flex:1; overflow-y:auto; padding:0.5rem; color:#fff; font-size:0.95rem; display:flex; flex-direction:column; gap:0.3rem;}
.message{padding:0.5rem 0.8rem; border-radius:0.7rem; max-width:75%; word-wrap:break-word; animation:fadeIn 0.3s;}
.userMsg{align-self:flex-end; background:#FFD166; color:#333;}
.botMsg{align-self:flex-start; background:rgba(255,255,255,0.2); color:#fff;}
.typing{font-style:italic; opacity:0.6;}
.quickReplies{display:flex; gap:0.5rem; flex-wrap:wrap; margin-top:0.3rem;}
.quickReplies button{padding:0.3rem 0.6rem; font-size:0.85rem; border-radius:0.5rem; border:none; background:#06D6A0; color:#fff; cursor:pointer; transition:0.3s;}
.quickReplies button:hover{background:#04A386;}
@keyframes fadeIn{from{opacity:0;}to{opacity:1;}}

/* Scroll Floating Parcels */
#scrollParcels{position:fixed;top:0;left:0;width:100%;height:100%;pointer-events:none;z-index:10;}
.scroll-package{position:absolute;background:#06D6A0;border-radius:0.3rem;opacity:0.7;transition:transform 0.3s;}
.scroll-package:hover{transform:scale(1.5) rotate(20deg);}

/* Services Section */
.services-container{display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:2rem; margin-top:3rem; margin-bottom:3rem;}
.service-card{background:rgba(255,255,255,0.15); padding:2rem; border-radius:1rem; text-align:center; transition:0.3s; box-shadow:0 5px 25px rgba(0,0,0,0.2);}
.service-card h3{margin:1rem 0 0.5rem;}
.service-card p{font-size:0.9rem; color:#fff;}
.service-card i{font-size:2.5rem; margin-bottom:1rem; color:#FFD166; transition:0.3s, text-shadow 0.3s;}
.service-card:hover i{color:#F6AE2D; text-shadow: 0 0 10px #FFD166, 0 0 20px #FFD166, 0 0 30px #FFD166; transform: scale(1.2) rotate(-5deg); animation: glowPulse 1.5s infinite alternate;}
@keyframes glowPulse{0%{text-shadow:0 0 10px #FFD166,0 0 20px #FFD166,0 0 30px #FFD166;}100%{text-shadow:0 0 20px #FFD166,0 0 30px #FFD166,0 0 40px #FFD166;}}

/* Responsive */
@media(max-width:768px){nav{flex-direction:column;gap:1rem; padding:1rem;} nav ul{flex-direction:column;gap:0.8rem;} #home h2{font-size:2rem;} #home p{font-size:1rem;} #home button{padding:0.7rem 1.5rem;} .glass-box{padding:1.5rem;} #chatWindow{width:250px; bottom:90px;}}
</style>
</head>
<body>

<!-- Navbar -->
<nav>
<h1>🚚 SwiftParcel</h1>
<ul>
<li><a href="index.php" class="active">Home</a></li>
<li><a href="about.php" >About</a></li>
<li><a href="book.php" >Book</a></li>
<?php if(isset($_SESSION['user_id'])): ?>
<li><a href="profile.php" >My Profile</a></li>
<li><a href="logout.php" >Logout</a></li>
<?php else: ?>
<li><a href="login.php" id="loginBtn">Login</a></li>
<?php endif; ?>
</ul>
</nav>

<!-- Hero -->
<section id="home">
<h2>Delivering Smiles Across Nepal</h2>
<p>SwiftParcel ensures your packages arrive safely and on time.</p>
<button onclick="window.location.href='book.php'">Book a Delivery</button>
<div class="package package1"></div>
<div class="package package2"></div>
<div class="package package3"></div>
<div class="package package4"></div>
<div class="package package5"></div>
</section>

<!-- About Section -->
<section id="about">
<h2>About SwiftParcel</h2>
<div class="glass-box">
<p>SwiftParcel is Nepal’s fastest and most reliable parcel delivery service. We specialize in secure, on-time deliveries, offering real-time tracking and exceptional customer support. Our mission is to make sending parcels easy, fast, and trustworthy, connecting communities across Nepal.</p>
<button onclick="window.location.href='about.php'">Learn More</button>
</div>
</section>

<!-- Services Section -->
<section id="services">
<h2>Our Services</h2>
<div class="services-container">
<div class="service-card"><i class="fas fa-shipping-fast"></i><h3>Fast Delivery</h3><p>Get your parcels delivered quickly across Nepal.</p></div>
<div class="service-card"><i class="fas fa-lock"></i><h3>Secure Handling</h3><p>Your packages are handled safely and carefully.</p></div>
<div class="service-card"><i class="fas fa-map-marker-alt"></i><h3>Online Tracking</h3><p>Track your parcel in real-time from anywhere.</p></div>
<div class="service-card"><i class="fas fa-wallet"></i><h3>Affordable Rates</h3><p>Reliable delivery at prices that suit your budget.</p></div>
<div class="service-card"><i class="fas fa-headset"></i><h3>24/7 Customer Support</h3><p>We’re always here to assist you with any queries.</p></div>
<div class="service-card"><i class="fas fa-bell"></i><h3>Real-Time Notifications</h3><p>Receive instant updates on your delivery status.</p></div>
</div>
</section>

<!-- Track Section -->
<section id="track">
<h2>Track Your Parcel</h2>
<div class="glass-box">
<form id="trackForm">
<input type="text" id="trackingId" placeholder="Enter Tracking ID" required>
<button type="submit">Track</button>
</form>
<div class="result" id="result"></div>
<div class="progress-bar"><div class="progress-fill" id="progress"></div></div>
</div>
</section>

<!-- How It Works Section -->
<section id="how-it-works" class="how-it-works">
    <h2 class="section-title">How It Works</h2>
    <p class="section-subtitle">Fast, simple & secure parcel delivery</p>

    <div class="timeline">
        <div class="timeline-step">
            <div class="icon">
                <i class="fas fa-user-plus"></i>
            </div>
            <div class="content">
                <h3>Create Account</h3>
                <p>Sign up quickly and access your personal SwiftParcel dashboard.</p>
            </div>
        </div>

        <div class="timeline-step">
            <div class="icon">
                <i class="fas fa-edit"></i>
            </div>
            <div class="content">
                <h3>Book Delivery</h3>
                <p>Enter pickup & delivery details and choose your service.</p>
            </div>
        </div>

        <div class="timeline-step">
            <div class="icon">
                <i class="fas fa-truck"></i>
            </div>
            <div class="content">
                <h3>Track in Real Time</h3>
                <p>Monitor your parcel live with instant status updates.</p>
            </div>
        </div>

        <div class="timeline-step">
            <div class="icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="content">
                <h3>Receive Safely</h3>
                <p>Your parcel is delivered securely with confirmation.</p>
            </div>
        </div>
    </div>
</section>


<!-- Chat -->
<div id="chatIcon">💬</div>
<div id="chatWindow">
<div id="chatHeader">SwiftParcel Chat</div>
<div id="chatMessages"><div class="botMsg">Hi! How can I help you today?</div></div>
<div id="chatInput">
<input type="text" placeholder="Type a message">
<button>Send</button>
</div>
</div>

<!-- Scroll Parcels Container -->
<div id="scrollParcels"></div>

<!-- Audio effects -->
<audio id="ding" src="https://www.soundjay.com/buttons/sounds/button-16.mp3"></audio>
<audio id="chime" src="https://www.soundjay.com/buttons/sounds/button-3.mp3"></audio>

 <!-- Footer -->
 <?php include 'footer.php'; ?>

<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
<script>
// ===== JavaScript (Tracking, Chat, Forms, Animations) =====

// Parcel Data
const parcelData={
PKG001:{status:'In Transit',location:'Kathmandu Hub',progress:60},
PKG002:{status:'Delivered',location:'Pokhara',progress:100},
PKG003:{status:'Out for Delivery',location:'Lalitpur',progress:85}
};

// Track Parcel
document.getElementById('trackForm').addEventListener('submit', e=>{
e.preventDefault();
let id=document.getElementById('trackingId').value.trim().toUpperCase();
const result=document.getElementById('result');
const progressBar=document.getElementById('progress');
progressBar.style.width='0%';
if(parcelData[id]){
let {status, location, progress}=parcelData[id];
result.innerHTML=`Status: <strong>${status}</strong><br>Location: <strong>${location}</strong>`;
let width=0;
let interval=setInterval(()=>{
if(width>=progress){clearInterval(interval);}
else{width++;progressBar.style.width=width+'%';}
},15);
document.getElementById('ding').play();
}else{
result.textContent='Tracking ID not found!';
progressBar.style.width='0%';
}
});



// Chat
const chatIcon=document.getElementById('chatIcon');
const chatWindow=document.getElementById('chatWindow');
const chatMessages=document.getElementById('chatMessages');
const chatInput=chatWindow.querySelector('input');
const chatSend=chatWindow.querySelector('button');
chatIcon.onclick=()=>chatWindow.style.display=chatWindow.style.display==='flex'?'none':'flex';

function addMessage(text, sender='bot'){
const msgDiv=document.createElement('div');
msgDiv.className=sender==='bot'?'botMsg message':'userMsg message';
msgDiv.innerHTML=text;
chatMessages.appendChild(msgDiv);
chatMessages.scrollTop=chatMessages.scrollHeight;
if(sender==='bot') chatIcon.classList.remove('pulse');
}

function botReply(msg){
chatIcon.classList.add('pulse');
addMessage('<span class="typing">SwiftParcel is typing...</span>', 'bot');
setTimeout(()=>{
chatMessages.querySelector('.typing')?.remove();
let reply="I'm here to help! Type 'track parcel' or 'book parcel'.";
let input=msg.toLowerCase();
if(input.includes('track')){ window.location.href='track.php'; return; }
if(input.includes('book')){ window.location.href='book.php'; return; }
if(input.includes('hi')||input.includes('hello')) reply='Hello! Welcome to SwiftParcel!';
addMessage(reply,'bot');
document.getElementById('ding').play();
},1000);
}

chatSend.onclick=()=>{
let msg=chatInput.value.trim();
if(!msg)return;
addMessage(msg,'user');
chatInput.value='';
botReply(msg);
};
chatInput.addEventListener('keypress', e=>{ if(e.key==='Enter') chatSend.click(); });

// Scroll Reveal
const sections=document.querySelectorAll('section');
const revealOnScroll=()=>{const trigger=window.innerHeight*0.85;sections.forEach(sec=>{if(sec.getBoundingClientRect().top<trigger)sec.classList.add('visible');});};
window.addEventListener('scroll',revealOnScroll);revealOnScroll();

// Ripple & Success
document.querySelectorAll('button').forEach(btn=>{
btn.addEventListener('click', function(e){
const ripple=document.createElement('span');ripple.classList.add('ripple');this.appendChild(ripple);
const rect=this.getBoundingClientRect();
ripple.style.left=(e.clientX-rect.left)+'px';
ripple.style.top=(e.clientY-rect.top)+'px';
setTimeout(()=>{ripple.remove();},600);
});
});





// Floating Scroll Parcels
const scrollContainer=document.getElementById('scrollParcels');
let parcels=[];
for(let i=0;i<10;i++){
const p=document.createElement('div');p.className='scroll-package';
p.style.width=20+Math.random()*20+'px';
p.style.height=20+Math.random()*20+'px';
p.style.left=Math.random()*100+'vw';
p.style.top=Math.random()*100+'vh';
scrollContainer.appendChild(p);
parcels.push({el:p,x:parseFloat(p.style.left),y:parseFloat(p.style.top),speed:0.1+Math.random()*0.3,angle:Math.random()*Math.PI*2});
}
function animateParcels(){
parcels.forEach(p=>{
p.angle+=0.01;p.x+=p.speed*Math.cos(p.angle);p.y+=p.speed*Math.sin(p.angle);
if(p.x>100)p.x=0;if(p.x<0)p.x=100;if(p.y>100)p.y=0;if(p.y<0)p.y=100;
p.el.style.left=p.x+'vw';p.el.style.top=p.y+'vh';
});
requestAnimationFrame(animateParcels);
}animateParcels();
</script>
</body>
</html>
