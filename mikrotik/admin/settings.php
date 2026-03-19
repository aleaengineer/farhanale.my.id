<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $settings = [
        'site_name' => sanitize($_POST['site_name']),
        'site_description' => sanitize($_POST['site_description']),
        'site_url' => sanitize($_POST['site_url']),
        'contact_email' => sanitize($_POST['contact_email']),
        'whatsapp' => sanitize($_POST['whatsapp']),
        'facebook' => sanitize($_POST['facebook']),
        'twitter' => sanitize($_POST['twitter']),
        'instagram' => sanitize($_POST['instagram']),
        'linkedin' => sanitize($_POST['linkedin']),
        'github' => sanitize($_POST['github']),
        'youtube' => sanitize($_POST['youtube']),
        'per_page_articles' => (int)$_POST['per_page_articles'],
        'per_page_search' => (int)$_POST['per_page_search'],
        'enable_analytics' => isset($_POST['enable_analytics']) ? 'true' : 'false',
        'google_analytics' => sanitize($_POST['google_analytics']),
        'enable_comments' => isset($_POST['enable_comments']) ? 'true' : 'false',
        'disqus_shortname' => sanitize($_POST['disqus_shortname'])
    ];
    
    $success = true;
    foreach ($settings as $key => $value) {
        $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?");
        if (!$stmt->execute([$key, $value, $value])) {
            $success = false;
        }
    }
    
    if ($success) {
        $message = 'Pengaturan berhasil disimpan!';
        $messageType = 'success';
    } else {
        $message = 'Gagal menyimpan pengaturan.';
        $messageType = 'error';
    }
}

$pageTitle = 'Pengaturan';
include 'includes/header.php';
?>

<div class="admin-wrapper">
    <div class="admin-sidebar">
        <div class="sidebar-header">
            <i class="fas fa-server sidebar-logo"></i>
            <h4>MikroTik Admin</h4>
        </div>
        <nav class="sidebar-menu">
            <a href="index.php" class="sidebar-menu-item">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            <a href="articles.php" class="sidebar-menu-item">
                <i class="fas fa-file-alt"></i> Artikel
            </a>
            <a href="categories.php" class="sidebar-menu-item">
                <i class="fas fa-th-large"></i> Kategori
            </a>
            <a href="tags.php" class="sidebar-menu-item">
                <i class="fas fa-tags"></i> Tags
            </a>
            <a href="settings.php" class="sidebar-menu-item active">
                <i class="fas fa-cog"></i> Settings
            </a>
            <a href="<?php echo SITE_URL; ?>/" class="sidebar-menu-item" target="_blank">
                <i class="fas fa-external-link-alt"></i> View Site
            </a>
            <a href="logout.php" class="sidebar-menu-item sidebar-logout">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </nav>
    </div>
    
    <div class="admin-content">
        <div class="admin-header">
            <div class="header-left">
                <button class="btn btn-toggle-sidebar" id="toggleSidebar">
                    <i class="fas fa-bars"></i>
                </button>
                <h2 class="header-title">Pengaturan</h2>
            </div>
        </div>
        
        <?php if ($message): ?>
        <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show">
            <?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
        
        <div class="card">
            <div class="card-body">
                <form method="POST" action="">
                    <ul class="nav nav-tabs mb-4" id="settingsTabs" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active" type="button" data-tab="general">
                                <i class="fas fa-cog me-2"></i>General
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" type="button" data-tab="social">
                                <i class="fas fa-share-alt me-2"></i>Social Media
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" type="button" data-tab="display">
                                <i class="fas fa-th-large me-2"></i>Display
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" type="button" data-tab="analytics">
                                <i class="fas fa-chart-line me-2"></i>Analytics
                            </button>
                        </li>
                    </ul>
                    
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="general">
                            <h5 class="mb-4">General Settings</h5>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label">Site Name *</label>
                                        <input type="text" class="form-control" name="site_name" value="<?php echo htmlspecialchars(getSetting('site_name')); ?>" required>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label">Site URL *</label>
                                        <input type="url" class="form-control" name="site_url" value="<?php echo htmlspecialchars(getSetting('site_url')); ?>" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group mb-3">
                                <label class="form-label">Site Description</label>
                                <textarea class="form-control" name="site_description" rows="3"><?php echo htmlspecialchars(getSetting('site_description')); ?></textarea>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label">Contact Email</label>
                                        <input type="email" class="form-control" name="contact_email" value="<?php echo htmlspecialchars(getSetting('contact_email')); ?>">
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label">WhatsApp</label>
                                        <input type="text" class="form-control" name="whatsapp" value="<?php echo htmlspecialchars(getSetting('whatsapp')); ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="tab-pane fade" id="social">
                            <h5 class="mb-4">Social Media Settings</h5>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label">Facebook</label>
                                        <input type="url" class="form-control" name="facebook" value="<?php echo htmlspecialchars(getSetting('facebook')); ?>">
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label">Twitter</label>
                                        <input type="url" class="form-control" name="twitter" value="<?php echo htmlspecialchars(getSetting('twitter')); ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label">Instagram</label>
                                        <input type="url" class="form-control" name="instagram" value="<?php echo htmlspecialchars(getSetting('instagram')); ?>">
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label">LinkedIn</label>
                                        <input type="url" class="form-control" name="linkedin" value="<?php echo htmlspecialchars(getSetting('linkedin')); ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label">GitHub</label>
                                        <input type="url" class="form-control" name="github" value="<?php echo htmlspecialchars(getSetting('github')); ?>">
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label">YouTube</label>
                                        <input type="url" class="form-control" name="youtube" value="<?php echo htmlspecialchars(getSetting('youtube')); ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="tab-pane fade" id="display">
                            <h5 class="mb-4">Display Settings</h5>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label">Articles Per Page</label>
                                        <input type="number" class="form-control" name="per_page_articles" value="<?php echo getSetting('per_page_articles') ?: 9; ?>" min="1" max="50">
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label">Search Results Per Page</label>
                                        <input type="number" class="form-control" name="per_page_search" value="<?php echo getSetting('per_page_search') ?: 12; ?>" min="1" max="50">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="enable_comments" name="enable_comments" <?php echo getSetting('enable_comments') == 'true' ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="enable_comments">
                                    Enable Comments
                                </label>
                            </div>
                            
                            <div class="form-group mb-3">
                                <label class="form-label">Disqus Shortname</label>
                                <input type="text" class="form-control" name="disqus_shortname" value="<?php echo htmlspecialchars(getSetting('disqus_shortname')); ?>" placeholder="example">
                            </div>
                        </div>
                        
                        <div class="tab-pane fade" id="analytics">
                            <h5 class="mb-4">Analytics Settings</h5>
                            
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="enable_analytics" name="enable_analytics" <?php echo getSetting('enable_analytics') == 'true' ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="enable_analytics">
                                    Enable Analytics
                                </label>
                            </div>
                            
                            <div class="form-group mb-3">
                                <label class="form-label">Google Analytics Tracking ID</label>
                                <input type="text" class="form-control" name="google_analytics" value="<?php echo htmlspecialchars(getSetting('google_analytics')); ?>" placeholder="G-XXXXXXXXXX">
                                <small class="text-muted">Format: G-XXXXXXXXXX atau UA-XXXXXXXX-X</small>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Simpan Pengaturan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var tabButtons = document.querySelectorAll('#settingsTabs .nav-link');
    var tabContent = document.querySelectorAll('.tab-pane');
    
    function switchTab(tabId) {
        tabButtons.forEach(function(button) {
            button.classList.remove('active');
        });
        tabContent.forEach(function(content) {
            content.classList.remove('show', 'active');
        });
        
        var targetButton = document.querySelector('#settingsTabs .nav-link[data-tab="' + tabId + '"]');
        var targetContent = document.querySelector('#' + tabId);
        
        if (targetButton && targetContent) {
            targetButton.classList.add('active');
            targetContent.classList.add('show', 'active');
            localStorage.setItem('activeSettingsTab', tabId);
        }
    }
    
    tabButtons.forEach(function(button) {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            var tabId = this.getAttribute('data-tab');
            switchTab(tabId);
        });
    });
    
    var savedTab = localStorage.getItem('activeSettingsTab');
    if (savedTab) {
        switchTab(savedTab);
    }
});
</script>

<?php include 'includes/footer.php'; ?>
