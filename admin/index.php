<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$portfolioCount = $pdo->query("SELECT COUNT(*) FROM portfolio")->fetchColumn();
$blogCount = $pdo->query("SELECT COUNT(*) FROM blogs")->fetchColumn();
$certCount = $pdo->query("SELECT COUNT(*) FROM certificates")->fetchColumn();
$skillCount = $pdo->query("SELECT COUNT(*) FROM skills")->fetchColumn();

$recentMessages = $pdo->query("SELECT * FROM messages ORDER BY created_at DESC LIMIT 5")->fetchAll();

$recentBlogs = $pdo->query("SELECT * FROM blogs ORDER BY created_at DESC LIMIT 5")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Admin Panel</title>
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
                <a href="index.php" class="admin-menu-item active">
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
                <a href="settings.php" class="admin-menu-item">
                    <i class="fas fa-cog"></i> Settings
                </a>
                <a href="../index.php" class="admin-menu-item" target="_blank">
                    <i class="fas fa-external-link-alt"></i> View Site
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
                    <h3 class="d-inline-block ms-3">Dashboard</h3>
                </div>
                <div>
                    <span class="me-3">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    <a href="logout.php">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-3">
                    <div class="stat-card stat-card-purple">
                        <div class="stat-card-icon">
                            <i class="fas fa-briefcase"></i>
                        </div>
                        <p class="stat-card-title">Portfolio</p>
                        <h2 class="stat-card-value"><?php echo $portfolioCount; ?></h2>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card stat-card-pink">
                        <div class="stat-card-icon">
                            <i class="fas fa-blog"></i>
                        </div>
                        <p class="stat-card-title">Blogs</p>
                        <h2 class="stat-card-value"><?php echo $blogCount; ?></h2>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card stat-card-blue">
                        <div class="stat-card-icon">
                            <i class="fas fa-certificate"></i>
                        </div>
                        <p class="stat-card-title">Certificates</p>
                        <h2 class="stat-card-value"><?php echo $certCount; ?></h2>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card stat-card-green">
                        <div class="stat-card-icon">
                            <i class="fas fa-cogs"></i>
                        </div>
                        <p class="stat-card-title">Skills</p>
                        <h2 class="stat-card-value"><?php echo $skillCount; ?></h2>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="admin-card">
                        <h4>Recent Messages</h4>
                        <?php if ($recentMessages): ?>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentMessages as $msg): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($msg['name']); ?></td>
                                        <td><?php echo htmlspecialchars($msg['email']); ?></td>
                                        <td><?php echo date('M d', strtotime($msg['created_at'])); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php else: ?>
                        <p class="text-muted">No messages yet.</p>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="admin-card">
                        <h4>Recent Blogs</h4>
                        <?php if ($recentBlogs): ?>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Category</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentBlogs as $blog): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($blog['title']); ?></td>
                                        <td><?php echo htmlspecialchars($blog['category']); ?></td>
                                        <td><?php echo date('M d', strtotime($blog['created_at'])); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php else: ?>
                        <p class="text-muted">No blogs yet.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="../assets/js/admin.js"></script>
</body>
</html>
