</main>

    <footer class="footer">
        <div class="footer-container">
            <div class="footer-section">
                <h3>📚 BookNest</h3>
                <p>Your trusted online bookstore for quality books at affordable prices.</p>
                <div class="social-links">
                    <a href="#">📘 Facebook</a>
                    <a href="#">🐦 Twitter</a>
                    <a href="#">📷 Instagram</a>
                </div>
            </div>

            <div class="footer-section">
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="about.php">About Us</a></li>
                    <li><a href="contact.php">Contact Us</a></li>
                    <li><a href="user/cart.php">Shopping Cart</a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h4>Categories</h4>
                <ul>
                    <li><a href="index.php?category=Fiction">Fiction</a></li>
                    <li><a href="index.php?category=Science Fiction">Science Fiction</a></li>
                    <li><a href="index.php?category=Education">Education</a></li>
                    <li><a href="index.php?category=Romance">Romance</a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h4>Customer Service</h4>
                <ul>
                    <li><a href="#">Shipping Info</a></li>
                    <li><a href="#">Returns & Refunds</a></li>
                    <li><a href="#">Payment Methods</a></li>
                    <li><a href="contact.php">Contact Support</a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h4>Admin</h4>
                <ul>
                    <li><a href="admin/login.php">🔐 Admin Login</a></li>
                    <?php if (is_logged_in() && is_admin()): ?>
                        <li><a href="admin/dashboard.php">📊 Admin Dashboard</a></li>
                    <?php endif; ?>
                </ul>
            </div>

            <div class="footer-section">
                <h4>Contact Info</h4>
                <p>📍 123 Book Street, Kathmandu, Nepal</p>
                <p>📞 +977-9876543210</p>
                <p>✉️ info@booknest.com</p>
                <p>⏰ Mon-Sat: 9AM-6PM</p>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> BookNest. All rights reserved. | Developed with ❤️ in Nepal</p>
        </div>
    </footer>

    <script src="assets/js/script.js"></script>
</body>
</html>
