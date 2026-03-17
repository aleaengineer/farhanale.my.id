<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'send') {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $subject = sanitize($_POST['subject']);
    $message = sanitize($_POST['message']);
    
    if (empty($name) || empty($email) || empty($message)) {
        echo json_encode(['success' => false, 'message' => 'Please fill in all required fields.']);
        exit;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
        exit;
    }
    
    $stmt = $pdo->prepare("INSERT INTO messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
    
    if ($stmt->execute([$name, $email, $subject, $message])) {
        echo json_encode(['success' => true, 'message' => 'Message sent successfully! I will get back to you soon.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to send message. Please try again.']);
    }
    exit;
}

$pageTitle = 'Contact';
include 'includes/header.php';
?>

<div class="contact-section section-padding" style="padding-top: 150px;">
    <div class="container">
        <div class="contact-content">
            <div class="text-center text-white mb-5" data-aos="fade-up">
                <h2 class="mb-3">Get In <span class="text-warning">Touch</span></h2>
                <p class="mb-4">Have a question or want to work together? Feel free to contact me!</p>
            </div>
            
            <div class="row">
                <div class="col-lg-5 mb-4" data-aos="fade-right">
                    <div class="contact-info">
                        <div class="contact-info-item">
                            <div class="contact-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="contact-text">
                                <h4>Email</h4>
                                <p><?php echo getSetting('contact_email'); ?></p>
                            </div>
                        </div>
                        
                        <div class="contact-info-item">
                            <div class="contact-icon">
                                <i class="fab fa-whatsapp"></i>
                            </div>
                            <div class="contact-text">
                                <h4>WhatsApp</h4>
                                <p><?php echo getSetting('whatsapp'); ?></p>
                            </div>
                        </div>
                        
                        <div class="contact-info-item">
                            <div class="contact-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div class="contact-text">
                                <h4>Location</h4>
                                <p><?php echo getSetting('address'); ?></p>
                            </div>
                        </div>
                        
                        <div class="contact-info-item">
                            <div class="contact-icon">
                                <i class="fas fa-share-alt"></i>
                            </div>
                            <div class="contact-text">
                                <h4>Social Media</h4>
                                <div class="mt-2">
                                    <a href="<?php echo getSetting('linkedin'); ?>" target="_blank" class="btn btn-social btn-linkedin me-2">
                                        <i class="fab fa-linkedin-in"></i>
                                    </a>
                                    <a href="<?php echo getSetting('github'); ?>" target="_blank" class="btn btn-social btn-github me-2">
                                        <i class="fab fa-github"></i>
                                    </a>
                                    <a href="<?php echo getSetting('instagram'); ?>" target="_blank" class="btn btn-social btn-instagram">
                                        <i class="fab fa-instagram"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-7" data-aos="fade-left">
                    <div class="contact-card">
                        <h3>Send Me a Message</h3>
                        <form id="contactForm">
                            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                            
                            <div class="mb-3">
                                <label for="name" class="form-label">Your Name *</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Your Email *</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="subject" class="form-label">Subject</label>
                                <input type="text" class="form-control" id="subject" name="subject">
                            </div>
                            
                            <div class="mb-4">
                                <label for="message" class="form-label">Message *</label>
                                <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-paper-plane me-2"></i>Send Message
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
