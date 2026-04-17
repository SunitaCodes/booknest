<?php
$page_title = "Contact Us";
require_once 'includes/header.php';

// Handle contact form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = clean_input($_POST['name']);
    $email = clean_input($_POST['email']);
    $subject = clean_input($_POST['subject']);
    $message = clean_input($_POST['message']);
    
    $errors = [];
    
    if (empty($name) || !preg_match("/^[a-zA-Z\s]+$/", $name)) {
        $errors[] = "Name should contain only alphabets";
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email is required";
    }
    
    if (empty($subject)) {
        $errors[] = "Subject is required";
    }
    
    if (empty($message) || strlen($message) < 10) {
        $errors[] = "Message must be at least 10 characters long";
    }
    
    if (empty($errors)) {
        // In a real application, you would send an email here
        // For demo purposes, we'll just show a success message
        $_SESSION['message'] = "Thank you for contacting us! We'll get back to you within 24 hours.";
        $_SESSION['message_type'] = "success";
        header('Location: contact.php');
        exit();
    }
}
?>

<div class="container">
    <div class="contact-page">
        <div class="page-header">
            <h1>Contact Us</h1>
            <p>We're here to help and answer any questions you might have</p>
        </div>

        <div class="contact-content">
            <div class="contact-info">
                <h2>Get in Touch</h2>
                <p>We'd love to hear from you! Whether you have a question about our books, pricing, or anything else, our team is ready to answer all your questions.</p>
                
                <div class="contact-methods">
                    <div class="contact-method">
                        <div class="contact-icon">📍</div>
                        <div class="contact-details">
                            <h3>Visit Our Store</h3>
                            <p>123 Book Street<br>Kathmandu, Nepal<br>Opposite to City Hall</p>
                        </div>
                    </div>
                    
                    <div class="contact-method">
                        <div class="contact-icon">📞</div>
                        <div class="contact-details">
                            <h3>Call Us</h3>
                            <p>Main: +977-9876543210<br>Support: +977-9865432109<br>Mon-Sat: 9AM - 6PM</p>
                        </div>
                    </div>
                    
                    <div class="contact-method">
                        <div class="contact-icon">✉️</div>
                        <div class="contact-details">
                            <h3>Email Us</h3>
                            <p>General: info@booknest.com<br>Support: support@booknest.com<br>Orders: orders@booknest.com</p>
                        </div>
                    </div>
                </div>

                <div class="social-section">
                    <h3>Follow Us</h3>
                    <div class="social-links">
                        <a href="#" class="social-link">
                            <span class="social-icon">📘</span>
                            <span>Facebook</span>
                        </a>
                        <a href="#" class="social-link">
                            <span class="social-icon">🐦</span>
                            <span>Twitter</span>
                        </a>
                        <a href="#" class="social-link">
                            <span class="social-icon">📷</span>
                            <span>Instagram</span>
                        </a>
                        <a href="#" class="social-link">
                            <span class="social-icon">💼</span>
                            <span>LinkedIn</span>
                        </a>
                    </div>
                </div>
            </div>

            <div class="contact-form-section">
                <h2>Send Us a Message</h2>
                
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-error">
                        <?php foreach ($errors as $error): ?>
                            <p><?php echo $error; ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="" class="contact-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="name">Your Name *</label>
                            <input type="text" id="name" name="name" required 
                                   value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>"
                                   pattern="[A-Za-z\s]+" title="Only alphabets allowed">
                        </div>
                        <div class="form-group">
                            <label for="email">Your Email *</label>
                            <input type="email" id="email" name="email" required 
                                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="subject">Subject *</label>
                        <input type="text" id="subject" name="subject" required 
                               value="<?php echo isset($_POST['subject']) ? htmlspecialchars($_POST['subject']) : ''; ?>"
                               placeholder="What is this regarding?">
                    </div>
                    
                    <div class="form-group">
                        <label for="message">Message *</label>
                        <textarea id="message" name="message" required rows="6" 
                                  placeholder="Tell us more about your inquiry..."><?php echo isset($_POST['message']) ? htmlspecialchars($_POST['message']) : ''; ?></textarea>
                        <small>Minimum 10 characters required</small>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Send Message</button>
                        <button type="reset" class="btn btn-outline">Clear Form</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="faq-section">
            <h2>Frequently Asked Questions</h2>
            <div class="faq-grid">
                <div class="faq-item">
                    <h3>How long does delivery take?</h3>
                    <p>We deliver within 2-3 business days within Kathmandu Valley and 3-5 business days for other locations in Nepal.</p>
                </div>
                
                <div class="faq-item">
                    <h3>What payment methods do you accept?</h3>
                    <p>We accept Cash on Delivery, eSewa, and bank transfers for online orders.</p>
                </div>
                
                <div class="faq-item">
                    <h3>Can I return or exchange books?</h3>
                    <p>Yes, you can return books within 7 days of delivery if they are damaged or incorrect. Exchanges are also available.</p>
                </div>
                
                <div class="faq-item">
                    <h3>Do you offer discounts on bulk orders?</h3>
                    <p>Yes, we offer special discounts for bulk orders (10+ books). Please contact us for a custom quote.</p>
                </div>
                
                <div class="faq-item">
                    <h3>How can I track my order?</h3>
                    <p>Once your order is shipped, you'll receive a tracking number via email. You can also track your order from your account dashboard.</p>
                </div>
                
                <div class="faq-item">
                    <h3>Do you have a physical store?</h3>
                    <p>Yes! Our physical store is located at 123 Book Street, Kathmandu. You're welcome to visit us during business hours.</p>
                </div>
            </div>
        </div>

        <div class="map-section">
            <h2>Find Us</h2>
            <div class="map-container">
                <div class="map-placeholder">
                    <div class="map-icon">🗺️</div>
                    <h3>BookNest Store Location</h3>
                    <p>123 Book Street, Kathmandu, Nepal</p>
                    <p>Near City Hall, opposite to the main bus station</p>
                    <a href="https://maps.google.com" target="_blank" class="btn btn-outline">Get Directions</a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.contact-page {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px 0 50px;
}

.page-header {
    text-align: center;
    margin-bottom: 50px;
}

.page-header h1 {
    font-size: 2.5em;
    color: #333;
    margin-bottom: 10px;
}

.page-header p {
    color: #666;
    font-size: 1.1em;
}

.contact-content {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 50px;
    margin-bottom: 60px;
}

.contact-info h2,
.contact-form-section h2 {
    font-size: 1.8em;
    color: #333;
    margin-bottom: 20px;
}

.contact-info p {
    color: #666;
    line-height: 1.6;
    margin-bottom: 30px;
}

.contact-methods {
    margin-bottom: 40px;
}

.contact-method {
    display: flex;
    gap: 20px;
    margin-bottom: 25px;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 10px;
}

.contact-icon {
    font-size: 2.5em;
    min-width: 60px;
    text-align: center;
}

.contact-details h3 {
    font-size: 1.2em;
    color: #333;
    margin-bottom: 10px;
}

.contact-details p {
    margin: 0;
    line-height: 1.5;
}

.social-section h3 {
    font-size: 1.3em;
    color: #333;
    margin-bottom: 15px;
}

.social-links {
    display: flex;
    gap: 15px;
}

.social-link {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 15px;
    background: #f8f9fa;
    border-radius: 8px;
    text-decoration: none;
    color: #666;
    transition: all 0.3s ease;
}

.social-link:hover {
    background: #007bff;
    color: white;
}

.social-icon {
    font-size: 1.2em;
}

.contact-form {
    background: white;
    padding: 30px;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 20px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    color: #333;
}

.form-group input,
.form-group textarea {
    width: 100%;
    padding: 12px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 14px;
    transition: border-color 0.3s ease;
}

.form-group input:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #007bff;
}

.form-group small {
    display: block;
    margin-top: 5px;
    color: #666;
    font-size: 0.85em;
}

.form-actions {
    display: flex;
    gap: 15px;
    justify-content: flex-end;
}

.faq-section {
    margin-bottom: 60px;
}

.faq-section h2 {
    text-align: center;
    font-size: 2em;
    color: #333;
    margin-bottom: 40px;
}

.faq-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 25px;
}

.faq-item {
    background: white;
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.faq-item h3 {
    font-size: 1.2em;
    color: #333;
    margin-bottom: 10px;
}

.faq-item p {
    color: #666;
    line-height: 1.6;
}

.map-section h2 {
    text-align: center;
    font-size: 2em;
    color: #333;
    margin-bottom: 30px;
}

.map-container {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
}

.map-placeholder {
    padding: 60px 20px;
    text-align: center;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}

.map-icon {
    font-size: 4em;
    margin-bottom: 20px;
}

.map-placeholder h3 {
    font-size: 1.5em;
    color: #333;
    margin-bottom: 10px;
}

.map-placeholder p {
    color: #666;
    margin-bottom: 20px;
}

@media (max-width: 768px) {
    .contact-content {
        grid-template-columns: 1fr;
        gap: 30px;
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .faq-grid {
        grid-template-columns: 1fr;
    }
    
    .social-links {
        flex-wrap: wrap;
    }
}
</style>

<?php require_once 'includes/footer.php'; ?>
