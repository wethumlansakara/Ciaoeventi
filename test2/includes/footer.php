    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <div class="footer-logo gradient-text">CiaoEventi</div>
                    <p class="footer-description">
                        Your gateway to unforgettable experiences and amazing events. 
                        Discover the best parties, festivals, and exclusive gatherings.
                    </p>
                    <div class="footer-social">
                        <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-tiktok"></i></a>
                    </div>
                </div>
                
                <div>
                    <h3 class="footer-title">Quick Links</h3>
                    <ul class="footer-links">
                        <li><a href="index.php" class="footer-link">Home</a></li>
                        <li><a href="events.php" class="footer-link">Events</a></li>
                        <li><a href="create_event.php" class="footer-link">Create Event</a></li>
                        <li><a href="login.php" class="footer-link">Sign In</a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="footer-title">Categories</h3>
                    <ul class="footer-links">
                        <li><a href="events.php?category=Festival" class="footer-link">Festivals</a></li>
                        <li><a href="events.php?category=Party" class="footer-link">Parties</a></li>
                        <li><a href="events.php?category=Concert" class="footer-link">Concerts</a></li>
                        <li><a href="events.php?category=Nightlife" class="footer-link">Nightlife</a></li>
                        <li><a href="events.php?category=Social" class="footer-link">Social</a></li>
                        <li><a href="events.php?category=Music" class="footer-link">Music</a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="footer-title">Get Tickets</h3>
                    <p class="footer-description">
                        Book your tickets through our official partner
                    </p>
                    <a href="https://www.tickets.lk" target="_blank" class="btn btn-primary mt-4">
                        Visit tickets.lk
                    </a>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p class="footer-copyright">
                    © 2025 CiaoEventi. All rights reserved.
                </p>
                <p class="footer-copyright">
                    Made with <i class="fas fa-heart text-pink"></i> for party lovers
                </p>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script src="js/script.js"></script>
    
    <script>
        // Initialize additional features
        document.addEventListener('DOMContentLoaded', function() {
            // Event card animations
            const eventCards = document.querySelectorAll('.event-card');
            eventCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.classList.add('pulse-animation');
                });
                card.addEventListener('mouseleave', function() {
                    this.classList.remove('pulse-animation');
                });
            });
            
            // Smooth loading of images
            const images = document.querySelectorAll('img');
            images.forEach(img => {
                img.addEventListener('load', function() {
                    this.classList.add('loaded');
                });
            });
        });
    </script>

        <!-- Footer code remains the same -->
    
    <!-- JavaScript -->
    <script src="js/script.js"></script>
    
    <script>
        // Initialize additional features
        document.addEventListener('DOMContentLoaded', function() {
            // Event card animations
            const eventCards = document.querySelectorAll('.event-card');
            eventCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.classList.add('pulse-animation');
                });
                card.addEventListener('mouseleave', function() {
                    this.classList.remove('pulse-animation');
                });
            });
            
            // Smooth loading of images
            const images = document.querySelectorAll('img');
            images.forEach(img => {
                img.addEventListener('load', function() {
                    this.classList.add('loaded');
                });
            });
        });
    </script>
    
<!-- Simple Mobile Menu JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const menuBtn = document.querySelector('.mobile-menu-btn');
    const navLinks = document.querySelector('.nav-links');
    
    if (menuBtn && navLinks) {
        menuBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            navLinks.classList.toggle('active');
            
            // Change icon
            const icon = this.querySelector('i');
            if (icon) {
                if (navLinks.classList.contains('active')) {
                    icon.classList.replace('fa-bars', 'fa-times');
                } else {
                    icon.classList.replace('fa-times', 'fa-bars');
                }
            }
        });
        
        // Close menu when clicking outside
        document.addEventListener('click', function(e) {
            if (!navLinks.contains(e.target) && !menuBtn.contains(e.target)) {
                navLinks.classList.remove('active');
                const icon = menuBtn.querySelector('i');
                if (icon) icon.classList.replace('fa-times', 'fa-bars');
            }
        });
        
        // Close menu when clicking a link
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', () => {
                navLinks.classList.remove('active');
                const icon = menuBtn.querySelector('i');
                if (icon) icon.classList.replace('fa-times', 'fa-bars');
            });
        });
    }
});
</script>

</body>
</html>