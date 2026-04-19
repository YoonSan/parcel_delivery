<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SwiftParcel - Fast & Reliable Parcel Delivery Service</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <?php include 'nav.php'; ?>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container hero-container">
            <div class="hero-content">
                <h1 class="hero-title">Fast, Reliable Parcel Delivery at Your Fingertips</h1>
                <p class="hero-subtitle">Track your parcels in real-time, schedule deliveries, and enjoy seamless logistics management with SwiftParcel.</p>
                <div class="hero-buttons">
                    <a href="book.php" class="btn btn-primary btn-lg">Get Started</a>
                    <a href="#how-it-works" class="btn btn-secondary btn-lg">Learn More</a>
                </div>
                <div class="hero-stats">
                    <div class="stat">
                        <h3>50K+</h3>
                        <p>Deliveries Made</p>
                    </div>
                    <div class="stat">
                        <h3>10K+</h3>
                        <p>Happy Customers</p>
                    </div>
                    <div class="stat">
                        <h3>99.8%</h3>
                        <p>Success Rate</p>
                    </div>
                </div>
            </div>
            <div class="hero-image">
                <div class="hero-card">
                    <i class="fas fa-shipping-fast"></i>
                </div>
            </div>
        </div>
    </section>

    <!-- Track Parcel Section -->
<section id="track" class="track-section">
    <div class="container">
        <div class="track-card">
            <h2>Track Your Parcel</h2>
            <p>Enter your tracking number to get real-time updates</p>
            <div class="track-form">
                <form action="track.php" method="GET">
                    <input type="text" name="tracking" placeholder="Enter tracking number (e.g. SP5F8C9B12D)" required>
                    <button class="btn btn-primary"><i class="fas fa-search"></i> Track</button>
                </form>
            </div>
        </div>
    </div>
</section>

    <!-- Features Section -->
    <section class="features-section">
        <div class="container">
            <div class="section-header">
                <h2>Why Choose SwiftParcel?</h2>
                <p>Experience the best in parcel delivery services</p>
            </div>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h3>Real-Time Tracking</h3>
                    <p>Monitor your parcel's journey from pickup to delivery with live GPS tracking and instant notifications.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <h3>Fast Delivery</h3>
                    <p>Same-day and next-day delivery options available. Get your parcels delivered when you need them.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3>Secure & Safe</h3>
                    <p>Your parcels are insured and handled with care. We guarantee safe delivery every time.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <h3>Affordable Rates</h3>
                    <p>Competitive pricing with no hidden fees. Get the best value for your money.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h3>24/7 Support</h3>
                    <p>Our customer support team is always ready to assist you with any questions or concerns.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <h3>Mobile Friendly</h3>
                    <p>Manage your deliveries on the go with our responsive platform accessible from any device.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section class="services-section">
        <div class="container">
            <div class="section-header">
                <h2>Our Services</h2>
                <p>Comprehensive delivery solutions for all your needs</p>
            </div>
            <div class="services-grid">
                <div class="service-card">
                    <div class="service-image">
                        <i class="fas fa-box-open"></i>
                    </div>
                    <h3>Standard Delivery</h3>
                    <p>Reliable delivery within 3-5 business days at the most affordable rates.</p>
                    <ul>
                        <li><i class="fas fa-check"></i> Up to 30kg</li>
                        <li><i class="fas fa-check"></i> Insurance included</li>
                        <li><i class="fas fa-check"></i> Tracking available</li>
                    </ul>
                </div>
                <div class="service-card featured">
                    <div class="badge">Popular</div>
                    <div class="service-image">
                        <i class="fas fa-shipping-fast"></i>
                    </div>
                    <h3>Express Delivery</h3>
                    <p>Next-day delivery for urgent parcels. Fast, reliable, and guaranteed.</p>
                    <ul>
                        <li><i class="fas fa-check"></i> Next-day delivery</li>
                        <li><i class="fas fa-check"></i> Priority handling</li>
                        <li><i class="fas fa-check"></i> Real-time GPS tracking</li>
                    </ul>
                </div>
                <div class="service-card">
                    <div class="service-image">
                        <i class="fas fa-rocket"></i>
                    </div>
                    <h3>Same-Day Delivery</h3>
                    <p>Ultra-fast delivery within hours. Perfect for time-sensitive shipments.</p>
                    <ul>
                        <li><i class="fas fa-check"></i> Delivered same day</li>
                        <li><i class="fas fa-check"></i> Dedicated courier</li>
                        <li><i class="fas fa-check"></i> Live tracking</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="how-it-works" id="how-it-works">
        <div class="container">
            <div class="section-header">
                <h2>How It Works</h2>
                <p>Simple steps to get your parcel delivered</p>
            </div>
            <div class="steps-grid">
                <div class="step-card">
                    <div class="step-number">1</div>
                    <div class="step-icon">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <h3>Create Account</h3>
                    <p>Sign up in seconds and access our comprehensive delivery platform.</p>
                </div>
                <div class="step-arrow">
                    <i class="fas fa-arrow-right"></i>
                </div>
                <div class="step-card">
                    <div class="step-number">2</div>
                    <div class="step-icon">
                        <i class="fas fa-edit"></i>
                    </div>
                    <h3>Book Delivery</h3>
                    <p>Enter parcel details, pickup and delivery addresses, and choose your service.</p>
                </div>
                <div class="step-arrow">
                    <i class="fas fa-arrow-right"></i>
                </div>
                <div class="step-card">
                    <div class="step-number">3</div>
                    <div class="step-icon">
                        <i class="fas fa-truck"></i>
                    </div>
                    <h3>Track Delivery</h3>
                    <p>Monitor your parcel in real-time as it makes its way to the destination.</p>
                </div>
                <div class="step-arrow">
                    <i class="fas fa-arrow-right"></i>
                </div>
                <div class="step-card">
                    <div class="step-number">4</div>
                    <div class="step-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h3>Receive Parcel</h3>
                    <p>Get your parcel delivered safely with signature confirmation.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-content">
                <h2>Ready to Send Your Parcel?</h2>
                <p>Join thousands of satisfied customers who trust SwiftParcel for their delivery needs.</p>
                <a href="book.php" class="btn btn-primary btn-lg">Get Started Now</a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include 'footer.php'; ?>

    <script src="assets/js/main.js"></script>
</body>
</html>