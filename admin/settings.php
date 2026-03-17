<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $settings = [
        'site_name' => sanitize($_POST['site_name']),
        'site_description' => sanitize($_POST['site_description']),
        'contact_email' => sanitize($_POST['contact_email']),
        'whatsapp' => sanitize($_POST['whatsapp']),
        'linkedin' => sanitize($_POST['linkedin']),
        'instagram' => sanitize($_POST['instagram']),
        'github' => sanitize($_POST['github']),
        'address' => sanitize($_POST['address'])
    ];
    
    foreach ($settings as $key => $value) {
        $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?");
        $stmt->execute([$key, $value, $value]);
    }
    
    $message = 'Settings updated successfully!';
}

$siteName = getSetting('site_name');
$siteDescription = getSetting('site_description');
$contactEmail = getSetting('contact_email');
$whatsapp = getSetting('whatsapp');
$linkedin = getSetting('linkedin');
$instagram = getSetting('instagram');
$github = getSetting('github');
$address = getSetting('address');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="../assets/css/admin.css" rel="stylesheet">
</head>
<body>
    <div class="admin-layout">
        <div class="admin-sidebar">
            <div class="admin-sidebar-header">
                <h4>Admin Panel</h4>
            </div>
            <nav class="admin-sidebar-menu">
                <a href="index.php" class="admin-menu-item">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a href="portfolio.php" class="admin-menu-item">
                    <i class="fas fa-briefcase"></i> Portfolio
                </a>
                <a href="blogs.php" class="admin-menu-item">
                    <i class="fas fa-blog"></i> Blogs
                </a>
                <a href="certificates.php" class="admin-menu-item">
                    <i class="fas fa-certificate"></i> Certificates
                </a>
                <a href="skills.php" class="admin-menu-item">
                    <i class="fas fa-cogs"></i> Skills
                </a>
                <a href="settings.php" class="admin-menu-item active">
                    <i class="fas fa-cog"></i> Settings
                </a>
                <a href="logout.php" class="admin-menu-item">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </nav>
        </div>
        
        <div class="admin-content">
            <div class="admin-topbar">
                <div>
                    <button class="btn btn-light" id="sidebarToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h3 class="d-inline-block ms-3">Site Settings</h3>
                </div>
                <div>
                    <a href="index.php" class="btn btn-gradient">
                        <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                    </a>
                </div>
            </div>
            
            <?php if ($message): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
            <?php endif; ?>
            
            <div class="admin-card">
                <h4>General Settings</h4>
                <form method="POST" action="">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Site Name</label>
                                <input type="text" class="form-control" name="site_name" value="<?php echo htmlspecialchars($siteName); ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Site Description</label>
                                <input type="text" class="form-control" name="site_description" value="<?php echo htmlspecialchars($siteDescription); ?>">
                            </div>
                        </div>
                    </div>
                    
                    <h4 class="mt-4 mb-3">Contact Information</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Contact Email</label>
                                <input type="email" class="form-control" name="contact_email" value="<?php echo htmlspecialchars($contactEmail); ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">WhatsApp Number</label>
                                <input type="text" class="form-control" name="whatsapp" value="<?php echo htmlspecialchars($whatsapp); ?>">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Address</label>
                                <textarea class="form-control" name="address" rows="2"><?php echo htmlspecialchars($address); ?></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <h4 class="mt-4 mb-3">Social Media Links</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">LinkedIn URL</label>
                                <input type="url" class="form-control" name="linkedin" value="<?php echo htmlspecialchars($linkedin); ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">GitHub URL</label>
                                <input type="url" class="form-control" name="github" value="<?php echo htmlspecialchars($github); ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Instagram URL</label>
                                <input type="url" class="form-control" name="instagram" value="<?php echo htmlspecialchars($instagram); ?>">
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Save Settings
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="../assets/js/admin.js"></script>
</body>
</html>
