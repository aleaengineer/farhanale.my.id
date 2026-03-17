    <!-- Footer -->
    <footer class="footer mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h4 class="text-white mb-3">Farhan Ale</h4>
                    <p class="text-light">Network & Automation Engineer</p>
                    <p class="text-light">Expert in MikroTik, Network Automation, and Cloud Technologies.</p>
                </div>
                <div class="col-md-4">
                    <h4 class="text-white mb-3">Quick Links</h4>
                    <ul class="list-unstyled">
                        <li><a href="index.php" class="text-light">Home</a></li>
                        <li><a href="about.php" class="text-light">About</a></li>
                        <li><a href="portfolio.php" class="text-light">Portfolio</a></li>
                        <li><a href="blog.php" class="text-light">Blog</a></li>
                        <li><a href="contact.php" class="text-light">Contact</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h4 class="text-white mb-3">Connect With Me</h4>
                    <div class="social-links">
                        <a href="<?php echo getSetting('linkedin'); ?>" target="_blank" class="btn btn-social btn-linkedin me-2">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <a href="<?php echo getSetting('github'); ?>" target="_blank" class="btn btn-social btn-github me-2">
                            <i class="fab fa-github"></i>
                        </a>
                        <a href="<?php echo getSetting('instagram'); ?>" target="_blank" class="btn btn-social btn-instagram me-2">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="https://wa.me/<?php echo getSetting('whatsapp'); ?>" target="_blank" class="btn btn-social btn-whatsapp">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                    </div>
                    <p class="text-light mt-3">
                        <i class="fas fa-envelope me-2"></i>
                        <?php echo getSetting('contact_email'); ?>
                    </p>
                    <p class="text-light">
                        <i class="fas fa-map-marker-alt me-2"></i>
                        <?php echo getSetting('address'); ?>
                    </p>
                </div>
            </div>
            <hr class="bg-light">
            <div class="row">
                <div class="col-md-12 text-center">
                    <p class="text-light mb-0">&copy; <?php echo date('Y'); ?> Farhan Ale. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <!-- AOS Animation Library -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Custom JS -->
    <script src="assets/js/main.js"></script>
</body>
</html>
