<?php
session_start();
include 'config.php'; // Adjust path if needed

$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = trim($_POST['firstName'] ?? '');
    $lastName  = trim($_POST['lastName'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $phone     = trim($_POST['phone'] ?? '');
    $subject   = trim($_POST['subject'] ?? '');
    $message   = trim($_POST['message'] ?? '');

    if ($firstName && $lastName && $email && $message) {
        $stmt = $conn->prepare("
            INSERT INTO contact_messages (name, email, phone, subject, message)
            VALUES (?, ?, ?, ?, ?)
        ");
        $fullName = $firstName . ' ' . $lastName;
        $stmt->bind_param("sssss", $fullName, $email, $phone, $subject, $message);

        if ($stmt->execute()) {
            $success = "Message sent successfully!";
        } else {
            $error = "Failed to send message: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $error = "Please fill in all required fields.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Contact Us - SwiftParcel</title>
<link rel="stylesheet" href="css/style.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
<?php include 'nav.php'; ?>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <h1>Contact Us</h1>
        <p>We're here to help and answer any questions you might have</p>
    </div>
</section>

<!-- Contact Section -->
<section class="contact-section">
    <div class="container">
        <div class="contact-grid">
            <!-- Contact Form -->
            <div class="contact-form-wrapper">
                <h2>Send Us a Message</h2>

                <!-- Success/Error Messages -->
                <?php if($success) echo "<p style='color:green; font-weight:bold;'>$success</p>"; ?>
                <?php if($error) echo "<p style='color:red; font-weight:bold;'>$error</p>"; ?>

                <form class="contact-form" id="contactForm" method="POST" action="contact.php">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="firstName">First Name</label>
                            <input type="text" id="firstName" name="firstName" required>
                        </div>
                        <div class="form-group">
                            <label for="lastName">Last Name</label>
                            <input type="text" id="lastName" name="lastName" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone">
                    </div>
                    <div class="form-group">
                        <label for="subject">Subject</label>
                        <select id="subject" name="subject" required>
                            <option value="">Select a subject</option>
                            <option value="general">General Inquiry</option>
                            <option value="support">Customer Support</option>
                            <option value="tracking">Tracking Issue</option>
                            <option value="billing">Billing Question</option>
                            <option value="partnership">Partnership Opportunity</option>
                            <option value="feedback">Feedback</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="message">Message</label>
                        <textarea id="message" name="message" rows="6" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-paper-plane"></i> Send Message
                    </button>
                </form>
            </div>

            <!-- Contact Information -->
            <div class="contact-info-wrapper">
                <h2>Get in Touch</h2>
                <p>Have a question or need assistance? Our team is ready to help you.</p>
                <div class="contact-info-cards">
                    <div class="info-card">
                        <div class="info-icon"><i class="fas fa-phone"></i></div>
                        <div class="info-content">
                            <h4>Phone</h4>
                            <p>+977 9867676767</p>
                            <p class="info-note">Mon-Fri, 8am-8pm EST</p>
                        </div>
                    </div>
                    <div class="info-card">
                        <div class="info-icon"><i class="fas fa-envelope"></i></div>
                        <div class="info-content">
                            <h4>Email</h4>
                            <p>info@swiftparcel.com</p>
                            <p class="info-note">We'll respond within 24 hours</p>
                        </div>
                    </div>
                    <div class="info-card">
                        <div class="info-icon"><i class="fas fa-map-marker-alt"></i></div>
                        <div class="info-content">
                            <h4>Office Address</h4>
                            <p>Sanepa, Lalitpur</p>
                        </div>
                    </div>
                    <div class="info-card">
                        <div class="info-icon"><i class="fas fa-clock"></i></div>
                        <div class="info-content">
                            <h4>Business Hours</h4>
                            <p>Monday - Friday: 8am - 8pm</p>
                            <p>Saturday - Sunday: 9am - 5pm</p>
                        </div>
                    </div>
                </div>

                <div class="social-section">
                    <h4>Follow Us</h4>
                    <div class="social-links-large">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>
<script src="js/main.js"></script>
</body>
</html>
