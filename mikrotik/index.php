<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 9;
$offset = ($page - 1) * $limit;

$totalArticles = $pdo->query("SELECT COUNT(*) FROM articles WHERE status = 'published'")->fetchColumn();
$totalPages = ceil($totalArticles / $limit);

$articles = $pdo->prepare("
    SELECT a.*, c.name as category_name, c.slug as category_slug, u.full_name as author_name
    FROM articles a
    LEFT JOIN categories c ON a.category_id = c.id
    LEFT JOIN users u ON a.author_id = u.id
    WHERE a.status = 'published'
    ORDER BY a.is_featured DESC, a.published_at DESC
    LIMIT :limit OFFSET :offset
");
$articles->bindValue(':limit', $limit, PDO::PARAM_INT);
$articles->bindValue(':offset', $offset, PDO::PARAM_INT);
$articles->execute();
$articles = $articles->fetchAll();

$featuredArticle = $pdo->query("
    SELECT a.*, c.name as category_name, c.slug as category_slug, u.full_name as author_name
    FROM articles a
    LEFT JOIN categories c ON a.category_id = c.id
    LEFT JOIN users u ON a.author_id = u.id
    WHERE a.status = 'published' AND a.is_featured = 1
    ORDER BY a.published_at DESC
    LIMIT 1
")->fetch();

$popularArticles = getPopularArticles(5);
$categories = $pdo->query("SELECT * FROM categories WHERE article_count > 0 ORDER BY name LIMIT 8")->fetchAll();

$pageTitle = 'Home';
include 'includes/header.php';
?>

<section class="hero-section">
    <div class="container">
        <h1 class="hero-title" data-aos="fade-up">
            Blog <span class="text-white">MikroTik</span>
        </h1>
        <p class="hero-subtitle" data-aos="fade-up" data-aos-delay="100">
            Tutorial lengkap dan panduan konfigurasi MikroTik oleh Farhan Ale
        </p>
        <p class="hero-description" data-aos="fade-up" data-aos-delay="200">
            Pelajari konfigurasi MikroTik, networking, dan automation dengan tutorial yang mudah dipahami dan praktis
        </p>
        <div data-aos="fade-up" data-aos-delay="300">
            <a href="#articles" class="btn btn-hero">
                <i class="fas fa-book-reader me-2"></i>Mulai Belajar
            </a>
        </div>
    </div>
</section>

<section class="section-padding" id="articles">
    <div class="container">
        <?php if ($featuredArticle): ?>
        <div class="section-title" data-aos="fade-up">
            <h2>Artikel <span class="text-primary">Unggulan</span></h2>
            <p>Artikel pilihan untuk memulai belajar MikroTik</p>
        </div>
        
        <div class="featured-article" data-aos="fade-up" data-aos-delay="100">
            <div class="featured-image">
                <?php if ($featuredArticle['featured_image']): ?>
                <img src="<?php echo SITE_URL; ?>/assets/img/articles/<?php echo htmlspecialchars($featuredArticle['featured_image']); ?>" alt="<?php echo htmlspecialchars($featuredArticle['title']); ?>">
                <?php else: ?>
                <i class="fas fa-server"></i>
                <?php endif; ?>
                <span class="featured-badge"><i class="fas fa-star me-2"></i>Featured</span>
            </div>
            <div class="featured-content">
                <span class="article-category">
                    <i class="fas fa-folder me-2"></i><?php echo htmlspecialchars($featuredArticle['category_name'] ?? 'Uncategorized'); ?>
                </span>
                <h3 class="featured-title">
                    <a href="article.php?slug=<?php echo htmlspecialchars($featuredArticle['slug']); ?>">
                        <?php echo htmlspecialchars($featuredArticle['title']); ?>
                    </a>
                </h3>
                <p class="featured-excerpt">
                    <?php echo getExcerpt($featuredArticle['content'], 200); ?>
                </p>
                <div class="featured-meta">
                    <span>
                        <i class="fas fa-user me-2"></i><?php echo htmlspecialchars($featuredArticle['author_name'] ?? 'Admin'); ?>
                    </span>
                    <span>
                        <i class="far fa-calendar-alt me-2"></i><?php echo formatDate($featuredArticle['published_at']); ?>
                    </span>
                    <span>
                        <i class="far fa-clock me-2"></i><?php echo $featuredArticle['read_time']; ?> min read
                    </span>
                    <span>
                        <i class="far fa-eye me-2"></i><?php echo number_format($featuredArticle['views']); ?> views
                    </span>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="section-title mt-5" data-aos="fade-up">
            <h2>Artikel <span class="text-primary">Terbaru</span></h2>
            <p>Artikel terbaru tentang konfigurasi MikroTik</p>
        </div>
        
        <div class="row g-4">
            <?php foreach ($articles as $article): ?>
            <?php if ($featuredArticle && $article['id'] == $featuredArticle['id']) continue; ?>
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="<?php echo array_search($article, $articles) * 100; ?>">
                <div class="article-card">
                    <div class="article-image">
                        <?php if ($article['featured_image']): ?>
                        <img src="<?php echo SITE_URL; ?>/assets/img/articles/<?php echo htmlspecialchars($article['featured_image']); ?>" alt="<?php echo htmlspecialchars($article['title']); ?>">
                        <?php else: ?>
                        <i class="fas fa-server"></i>
                        <?php endif; ?>
                    </div>
                    <div class="article-content">
                        <span class="article-category">
                            <i class="fas fa-folder me-1"></i><?php echo htmlspecialchars($article['category_name'] ?? 'Uncategorized'); ?>
                        </span>
                        <h4 class="article-title">
                            <a href="article.php?slug=<?php echo htmlspecialchars($article['slug']); ?>">
                                <?php echo htmlspecialchars($article['title']); ?>
                            </a>
                        </h4>
                        <p class="article-excerpt">
                            <?php echo getExcerpt($article['content'], 120); ?>
                        </p>
                        <div class="article-meta">
                            <span>
                                <i class="far fa-calendar-alt"></i> <?php echo formatDate($article['published_at']); ?>
                            </span>
                            <span>
                                <i class="far fa-clock"></i> <?php echo $article['read_time']; ?> min
                            </span>
                            <span>
                                <i class="far fa-eye"></i> <?php echo number_format($article['views']); ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <?php if ($totalPages > 1): ?>
        <nav class="mt-5" data-aos="fade-up">
            <ul class="pagination justify-content-center">
                <?php if ($page > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?php echo $page - 1; ?>">
                        <i class="fas fa-chevron-left"></i> Previous
                    </a>
                </li>
                <?php endif; ?>
                
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <?php if ($i == 1 || $i == $totalPages || ($i >= $page - 2 && $i <= $page + 2)): ?>
                <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                </li>
                <?php elseif ($i == $page - 3 || $i == $page + 3): ?>
                <li class="page-item disabled"><span class="page-link">...</span></li>
                <?php endif; ?>
                <?php endfor; ?>
                
                <?php if ($page < $totalPages): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?php echo $page + 1; ?>">
                        Next <i class="fas fa-chevron-right"></i>
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </nav>
        <?php endif; ?>
    </div>
</section>

<section class="section-padding" style="background: var(--light);">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="section-title" data-aos="fade-up">
                    <h2>Kategori <span class="text-primary">Populer</span></h2>
                    <p>Jelajahi artikel berdasarkan kategori</p>
                </div>
                
                <div class="category-grid" data-aos="fade-up" data-aos-delay="100">
                    <?php foreach ($categories as $cat): ?>
                    <a href="categories.php?slug=<?php echo htmlspecialchars($cat['slug']); ?>" class="category-card">
                        <div class="category-icon">
                            <i class="fas <?php echo htmlspecialchars($cat['icon']); ?>"></i>
                        </div>
                        <h5 class="category-name"><?php echo htmlspecialchars($cat['name']); ?></h5>
                        <span class="category-count"><?php echo number_format($cat['article_count']); ?> artikel</span>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="sidebar" data-aos="fade-up" data-aos-delay="200">
                    <h4 class="sidebar-title">
                        <i class="fas fa-fire me-2"></i>Artikel Populer
                    </h4>
                    
                    <div class="sidebar-articles">
                        <?php foreach ($popularArticles as $index => $article): ?>
                        <div class="sidebar-article">
                            <div class="sidebar-article-thumb">
                                <?php if ($article['featured_image']): ?>
                                <img src="<?php echo SITE_URL; ?>/assets/img/articles/<?php echo htmlspecialchars($article['featured_image']); ?>" alt="<?php echo htmlspecialchars($article['title']); ?>">
                                <?php else: ?>
                                <i class="fas fa-file-alt"></i>
                                <?php endif; ?>
                            </div>
                            <div class="sidebar-article-content">
                                <h5 class="sidebar-article-title">
                                    <a href="article.php?slug=<?php echo htmlspecialchars($article['slug']); ?>">
                                        <?php echo htmlspecialchars($article['title']); ?>
                                    </a>
                                </h5>
                                <div class="sidebar-article-meta">
                                    <span><i class="far fa-eye me-1"></i><?php echo number_format($article['views']); ?> views</span>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>