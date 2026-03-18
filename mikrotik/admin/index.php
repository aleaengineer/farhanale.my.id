<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$totalArticles = $pdo->query("SELECT COUNT(*) FROM articles")->fetchColumn();
$publishedArticles = $pdo->query("SELECT COUNT(*) FROM articles WHERE status = 'published'")->fetchColumn();
$draftArticles = $pdo->query("SELECT COUNT(*) FROM articles WHERE status = 'draft'")->fetchColumn();
$totalViews = $pdo->query("SELECT COALESCE(SUM(views), 0) FROM articles")->fetchColumn();
$totalCategories = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
$totalTags = $pdo->query("SELECT COUNT(*) FROM tags")->fetchColumn();

$recentArticles = $pdo->query("SELECT a.*, c.name as category_name, u.username as author_name 
    FROM articles a 
    LEFT JOIN categories c ON a.category_id = c.id 
    LEFT JOIN users u ON a.author_id = u.id 
    ORDER BY a.created_at DESC LIMIT 5")->fetchAll();

$popularArticles = $pdo->query("SELECT a.*, c.name as category_name 
    FROM articles a 
    LEFT JOIN categories c ON a.category_id = c.id 
    WHERE a.status = 'published' 
    ORDER BY a.views DESC LIMIT 5")->fetchAll();

$todayViews = $pdo->query("SELECT COUNT(*) FROM analytics WHERE DATE(viewed_at) = CURDATE()")->fetchColumn();
$weekViews = $pdo->query("SELECT COUNT(*) FROM analytics WHERE viewed_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetchColumn();

$pageTitle = 'Dashboard';
include 'includes/header.php';
?>

<div class="admin-wrapper">
    <div class="admin-sidebar">
        <div class="sidebar-header">
            <i class="fas fa-server sidebar-logo"></i>
            <h4>MikroTik Admin</h4>
        </div>
        <nav class="sidebar-menu">
            <a href="index.php" class="sidebar-menu-item active">
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
            <a href="settings.php" class="sidebar-menu-item">
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
                <h2 class="header-title">Dashboard</h2>
            </div>
            <div class="header-right">
                <div class="user-info">
                    <img src="<?php echo getAvatar($_SESSION['full_name'] ?? $_SESSION['username']); ?>" alt="User" class="user-avatar">
                    <div class="user-details">
                        <span class="user-name"><?php echo htmlspecialchars($_SESSION['full_name'] ?? $_SESSION['username']); ?></span>
                        <span class="user-role"><?php echo htmlspecialchars(ucfirst($_SESSION['role'])); ?></span>
                    </div>
                </div>
                <a href="logout.php" class="btn btn-logout">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </div>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card stat-purple">
                <div class="stat-icon">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div class="stat-info">
                    <h3 class="stat-value"><?php echo number_format($totalArticles); ?></h3>
                    <p class="stat-label">Total Artikel</p>
                </div>
                <div class="stat-detail">
                    <span class="stat-detail-item">
                        <i class="fas fa-check-circle text-success"></i> 
                        <?php echo number_format($publishedArticles); ?> Published
                    </span>
                    <span class="stat-detail-item">
                        <i class="fas fa-pencil-alt text-warning"></i> 
                        <?php echo number_format($draftArticles); ?> Draft
                    </span>
                </div>
            </div>
            
            <div class="stat-card stat-blue">
                <div class="stat-icon">
                    <i class="fas fa-eye"></i>
                </div>
                <div class="stat-info">
                    <h3 class="stat-value"><?php echo number_format($totalViews); ?></h3>
                    <p class="stat-label">Total Views</p>
                </div>
                <div class="stat-detail">
                    <span class="stat-detail-item">
                        <i class="fas fa-calendar-day"></i> 
                        <?php echo number_format($todayViews); ?> Hari ini
                    </span>
                    <span class="stat-detail-item">
                        <i class="fas fa-calendar-week"></i> 
                        <?php echo number_format($weekViews); ?> Minggu ini
                    </span>
                </div>
            </div>
            
            <div class="stat-card stat-pink">
                <div class="stat-icon">
                    <i class="fas fa-th-large"></i>
                </div>
                <div class="stat-info">
                    <h3 class="stat-value"><?php echo number_format($totalCategories); ?></h3>
                    <p class="stat-label">Kategori</p>
                </div>
                <div class="stat-detail">
                    <span class="stat-detail-item">
                        <i class="fas fa-tags"></i> 
                        <?php echo number_format($totalTags); ?> Tags
                    </span>
                </div>
            </div>
            
            <div class="stat-card stat-green">
                <div class="stat-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="stat-info">
                    <h3 class="stat-value"><?php echo number_format($weekViews); ?></h3>
                    <p class="stat-label">Views Minggu Ini</p>
                </div>
                <div class="stat-detail">
                    <span class="stat-detail-item text-success">
                        <i class="fas fa-arrow-up"></i> 
                        <?php echo $weekViews > 0 ? 'Aktif' : 'Tidak ada'; ?>
                    </span>
                </div>
            </div>
        </div>
        
        <div class="content-grid">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-clock me-2"></i>Artikel Terbaru</h4>
                    <a href="articles.php" class="btn btn-sm btn-view-all">Lihat Semua</a>
                </div>
                <div class="card-body">
                    <?php if ($recentArticles): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Kategori</th>
                                    <th>Author</th>
                                    <th>Status</th>
                                    <th>Views</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentArticles as $article): ?>
                                <tr>
                                    <td>
                                        <div class="article-title"><?php echo htmlspecialchars(substr($article['title'], 0, 40)); ?>...</div>
                                        <small class="text-muted"><?php echo formatDateTime($article['created_at']); ?></small>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">
                                            <?php echo htmlspecialchars($article['category_name'] ?? 'Uncategorized'); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($article['author_name'] ?? 'Unknown'); ?></td>
                                    <td>
                                        <?php if ($article['status'] == 'published'): ?>
                                        <span class="badge bg-success">Published</span>
                                        <?php elseif ($article['status'] == 'draft'): ?>
                                        <span class="badge bg-warning">Draft</span>
                                        <?php else: ?>
                                        <span class="badge bg-secondary">Archived</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo number_format($article['views']); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <p class="text-muted text-center py-4">Belum ada artikel</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-fire me-2"></i>Artikel Populer</h4>
                    <a href="articles.php?sort=views" class="btn btn-sm btn-view-all">Lihat Semua</a>
                </div>
                <div class="card-body">
                    <?php if ($popularArticles): ?>
                    <div class="popular-articles">
                        <?php foreach ($popularArticles as $index => $article): ?>
                        <div class="popular-article-item">
                            <div class="popular-rank"><?php echo $index + 1; ?></div>
                            <div class="popular-content">
                                <h5 class="popular-title">
                                    <a href="<?php echo SITE_URL; ?>/article.php?slug=<?php echo htmlspecialchars($article['slug']); ?>" target="_blank">
                                        <?php echo htmlspecialchars($article['title']); ?>
                                    </a>
                                </h5>
                                <div class="popular-meta">
                                    <span class="badge bg-light text-dark">
                                        <?php echo htmlspecialchars($article['category_name'] ?? 'Uncategorized'); ?>
                                    </span>
                                    <span class="text-muted ms-2">
                                        <i class="fas fa-eye me-1"></i><?php echo number_format($article['views']); ?> views
                                    </span>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <p class="text-muted text-center py-4">Belum ada data</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="quick-actions">
            <h4><i class="fas fa-bolt me-2"></i>Aksi Cepat</h4>
            <div class="action-buttons">
                <a href="articles.php?add=1" class="btn btn-action btn-primary">
                    <i class="fas fa-plus me-2"></i>Tambah Artikel Baru
                </a>
                <a href="categories.php" class="btn btn-action btn-secondary">
                    <i class="fas fa-th-large me-2"></i>Kelola Kategori
                </a>
                <a href="settings.php" class="btn btn-action btn-info">
                    <i class="fas fa-cog me-2"></i>Pengaturan Site
                </a>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>