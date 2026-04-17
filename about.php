<?php
$page_title = "About Us";
require_once 'includes/header.php';
?>

<div class="container">
    <div class="about-page">
        <div class="hero-section">
            <div class="hero-content">
                <h1>About BookNest</h1>
                <p>Your trusted partner in discovering amazing books</p>
            </div>
        </div>

        <div class="about-content">
            <div class="story-section">
                <h2>Our Story</h2>
                <div class="story-content">
                    <div class="story-text">
                        <p>BookNest was founded in 2020 with a simple mission: to make quality books accessible to everyone in Nepal. What started as a small bookstore in Kathmandu has now grown into one of the most trusted online bookstores in the country.</p>
                        
                        <p>We believe that books have the power to transform lives, broaden horizons, and create lasting change. Our passion for literature and commitment to customer satisfaction has helped us build a community of over 1000+ happy readers across Nepal.</p>
                        
                        <p>Whether you're looking for the latest bestsellers, classic literature, educational materials, or niche genres, BookNest is your one-stop destination for all your reading needs.</p>
                    </div>
                    <div class="story-image">
                        <img src="assets/images/about-store.jpg" alt="BookNest Store" onerror="this.src='assets/images/default-book.jpg'">
                    </div>
                </div>
            </div>

            <div class="mission-section">
                <h2>Our Mission & Vision</h2>
                <div class="mission-grid">
                    <div class="mission-card">
                        <div class="mission-icon">🎯</div>
                        <h3>Our Mission</h3>
                        <p>To provide a diverse collection of quality books at affordable prices while fostering a love for reading in our community.</p>
                    </div>
                    <div class="mission-card">
                        <div class="mission-icon">👁️</div>
                        <h3>Our Vision</h3>
                        <p>To become Nepal's leading bookstore, known for our exceptional customer service and extensive book collection.</p>
                    </div>
                    <div class="mission-card">
                        <div class="mission-icon">💎</div>
                        <h3>Our Values</h3>
                        <p>Quality, affordability, customer satisfaction, and a passion for literature guide everything we do.</p>
                    </div>
                </div>
            </div>

            <div class="features-section">
                <h2>Why Choose BookNest?</h2>
                <div class="features-grid">
                    <div class="feature-item">
                        <div class="feature-icon">📚</div>
                        <h3>Extensive Collection</h3>
                        <p>Over 10,000+ books across multiple genres including fiction, non-fiction, educational, and more.</p>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon">💰</div>
                        <h3>Best Prices</h3>
                        <p>Competitive pricing and regular discounts to make reading affordable for everyone.</p>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon">🚚</div>
                        <h3>Fast Delivery</h3>
                        <p>Quick and reliable delivery across Nepal within 2-3 business days.</p>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon">🔒</div>
                        <h3>Secure Shopping</h3>
                        <p>Safe and secure payment options including eSewa and cash on delivery.</p>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon">🎁</div>
                        <h3>Loyalty Rewards</h3>
                        <p>Join our loyalty program and earn points with every purchase.</p>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon">📞</div>
                        <h3>24/7 Support</h3>
                        <p>Dedicated customer support team ready to help you with any queries.</p>
                    </div>
                </div>
            </div>

            <div class="team-section">
                <h2>Meet Our Team</h2>
                <div class="team-grid">
                    <div class="team-member">
                        <div class="member-image">
                            <img src="assets/images/team-1.jpg" alt="Team Member" onerror="this.src='assets/images/default-book.jpg'">
                        </div>
                        <h3>Rajesh Sharma</h3>
                        <p class="member-role">Founder & CEO</p>
                        <p class="member-bio">A passionate book lover with over 15 years of experience in the publishing industry.</p>
                    </div>
                    <div class="team-member">
                        <div class="member-image">
                            <img src="assets/images/team-2.jpg" alt="Team Member" onerror="this.src='assets/images/default-book.jpg'">
                        </div>
                        <h3>Priya Koirala</h3>
                        <p class="member-role">Head of Operations</p>
                        <p class="member-bio">Ensuring smooth operations and exceptional customer service since day one.</p>
                    </div>
                    <div class="team-member">
                        <div class="member-image">
                            <img src="assets/images/team-3.jpg" alt="Team Member" onerror="this.src='assets/images/default-book.jpg'">
                        </div>
                        <h3>Amit Gurung</h3>
                        <p class="member-role">Marketing Manager</p>
                        <p class="member-bio">Creative mind behind our marketing campaigns and community engagement.</p>
                    </div>
                    <div class="team-member">
                        <div class="member-image">
                            <img src="assets/images/team-4.jpg" alt="Team Member" onerror="this.src='assets/images/default-book.jpg'">
                        </div>
                        <h3>Sushila Tamang</h3>
                        <p class="member-role">Customer Support Lead</p>
                        <p class="member-bio">Dedicated to ensuring every customer has a wonderful shopping experience.</p>
                    </div>
                </div>
            </div>

            <div class="stats-section">
                <h2>Our Achievements</h2>
                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="stat-number">10,000+</div>
                        <div class="stat-label">Books Available</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">1000+</div>
                        <div class="stat-label">Happy Customers</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">50+</div>
                        <div class="stat-label">Publishing Partners</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">4.8/5</div>
                        <div class="stat-label">Customer Rating</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.about-page {
    padding: 0;
}

.hero-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 80px 0;
    text-align: center;
    margin-bottom: 50px;
}

.hero-content h1 {
    font-size: 3em;
    margin-bottom: 15px;
}

.hero-content p {
    font-size: 1.2em;
    opacity: 0.9;
}

.about-content {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px 50px;
}

.story-section,
.mission-section,
.features-section,
.team-section,
.stats-section {
    margin-bottom: 60px;
}

.story-section h2,
.mission-section h2,
.features-section h2,
.team-section h2,
.stats-section h2 {
    text-align: center;
    font-size: 2.5em;
    margin-bottom: 40px;
    color: #333;
}

.story-content {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 40px;
    align-items: center;
}

.story-text p {
    font-size: 1.1em;
    line-height: 1.8;
    margin-bottom: 20px;
    color: #555;
}

.story-image img {
    width: 100%;
    border-radius: 10px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
}

.mission-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 30px;
}

.mission-card {
    background: white;
    padding: 30px;
    border-radius: 15px;
    text-align: center;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
}

.mission-card:hover {
    transform: translateY(-5px);
}

.mission-icon {
    font-size: 3em;
    margin-bottom: 20px;
}

.mission-card h3 {
    font-size: 1.5em;
    margin-bottom: 15px;
    color: #333;
}

.mission-card p {
    color: #666;
    line-height: 1.6;
}

.features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 30px;
}

.feature-item {
    display: flex;
    gap: 20px;
    background: #f8f9fa;
    padding: 25px;
    border-radius: 10px;
    transition: background 0.3s ease;
}

.feature-item:hover {
    background: #e9ecef;
}

.feature-icon {
    font-size: 2.5em;
    min-width: 60px;
    text-align: center;
}

.feature-item h3 {
    font-size: 1.3em;
    margin-bottom: 10px;
    color: #333;
}

.feature-item p {
    color: #666;
    line-height: 1.6;
}

.team-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 30px;
}

.team-member {
    background: white;
    padding: 30px;
    border-radius: 15px;
    text-align: center;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
}

.member-image {
    width: 120px;
    height: 120px;
    margin: 0 auto 20px;
    border-radius: 50%;
    overflow: hidden;
}

.member-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.team-member h3 {
    font-size: 1.3em;
    margin-bottom: 5px;
    color: #333;
}

.member-role {
    color: #007bff;
    font-weight: 600;
    margin-bottom: 15px;
}

.member-bio {
    color: #666;
    line-height: 1.6;
    font-size: 0.95em;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 30px;
}

.stat-item {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 40px 20px;
    border-radius: 15px;
    text-align: center;
}

.stat-number {
    font-size: 3em;
    font-weight: bold;
    margin-bottom: 10px;
}

.stat-label {
    font-size: 1.1em;
    opacity: 0.9;
}

@media (max-width: 768px) {
    .hero-content h1 {
        font-size: 2em;
    }
    
    .story-content {
        grid-template-columns: 1fr;
    }
    
    .features-grid {
        grid-template-columns: 1fr;
    }
    
    .feature-item {
        flex-direction: column;
        text-align: center;
    }
    
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
</style>

<?php require_once 'includes/footer.php'; ?>
