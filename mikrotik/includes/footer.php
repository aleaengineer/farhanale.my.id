    <footer class="footer bg-dark text-white mt-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="footer-brand">
                        <i class="fas fa-server me-2" style="color: var(--mikrotik);"></i>
                        <span>MikroTik Blog</span>
                    </div>
                    <p class="footer-description mt-3">
                        <?php echo htmlspecialchars(getSetting('site_description')); ?>
                    </p>
                    <div class="social-links mt-4">
                        <?php if ($facebook = getSetting('facebook')): ?>
                        <a href="<?php echo htmlspecialchars($facebook); ?>" target="_blank" class="social-link" title="Facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <?php endif; ?>
                        <?php if ($twitter = getSetting('twitter')): ?>
                        <a href="<?php echo htmlspecialchars($twitter); ?>" target="_blank" class="social-link" title="Twitter">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <?php endif; ?>
                        <?php if ($instagram = getSetting('instagram')): ?>
                        <a href="<?php echo htmlspecialchars($instagram); ?>" target="_blank" class="social-link" title="Instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <?php endif; ?>
                        <?php if ($linkedin = getSetting('linkedin')): ?>
                        <a href="<?php echo htmlspecialchars($linkedin); ?>" target="_blank" class="social-link" title="LinkedIn">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <?php endif; ?>
                        <?php if ($github = getSetting('github')): ?>
                        <a href="<?php echo htmlspecialchars($github); ?>" target="_blank" class="social-link" title="GitHub">
                            <i class="fab fa-github"></i>
                        </a>
                        <?php endif; ?>
                        <?php if ($youtube = getSetting('youtube')): ?>
                        <a href="<?php echo htmlspecialchars($youtube); ?>" target="_blank" class="social-link" title="YouTube">
                            <i class="fab fa-youtube"></i>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-6">
                    <h5 class="footer-title">Navigasi</h5>
                    <ul class="footer-links">
                        <li><a href="<?php echo SITE_URL; ?>/"><i class="fas fa-angle-right me-2"></i>Home</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/categories.php"><i class="fas fa-angle-right me-2"></i>Kategori</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/tags.php"><i class="fas fa-angle-right me-2"></i>Tags</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/search.php"><i class="fas fa-angle-right me-2"></i>Search</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <h5 class="footer-title">Kategori Populer</h5>
                    <ul class="footer-links">
                        <?php
                        $popularCats = $pdo->query("SELECT * FROM categories ORDER BY article_count DESC LIMIT 5")->fetchAll();
                        foreach ($popularCats as $cat):
                        ?>
                        <li>
                            <a href="<?php echo SITE_URL; ?>/categories.php?slug=<?php echo htmlspecialchars($cat['slug']); ?>">
                                <i class="fas <?php echo htmlspecialchars($cat['icon']); ?> me-2"></i>
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                
                <div class="col-lg-3">
                    <h5 class="footer-title">Kontak</h5>
                    <ul class="footer-contact">
                        <?php if ($email = getSetting('contact_email')): ?>
                        <li>
                            <i class="fas fa-envelope me-2"></i>
                            <a href="mailto:<?php echo htmlspecialchars($email); ?>"><?php echo htmlspecialchars($email); ?></a>
                        </li>
                        <?php endif; ?>
                        <?php if ($whatsapp = getSetting('whatsapp')): ?>
                        <li>
                            <i class="fab fa-whatsapp me-2"></i>
                            <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $whatsapp); ?>" target="_blank">
                                <?php echo htmlspecialchars($whatsapp); ?>
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
            
            <hr class="footer-hr">
            
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start">
                    <p class="footer-copyright mb-0">
                        &copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars(getSetting('site_name')); ?>. All rights reserved.
                    </p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <p class="footer-credits mb-0">
                        Made with <i class="fas fa-heart text-danger"></i> by 
                        <a href="<?php echo SITE_URL; ?>/../" target="_blank">Farhan Ale</a>
                    </p>
                </div>
            </div>
        </div>
    </footer>
    
    <a href="#" class="scroll-top" id="scrollTop">
        <i class="fas fa-arrow-up"></i>
    </a>
    
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="<?php echo SITE_URL; ?>/assets/js/main.js"></script>
    
    <?php if (isset($additionalJs)) echo $additionalJs; ?>
    
    <script>
    AOS.init({
        duration: 800,
        easing: 'ease-in-out',
        once: true
    });
    </script>
</body>
</html>