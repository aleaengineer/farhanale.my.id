<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$slug = isset($_GET['slug']) ? sanitize($_GET['slug']) : '';

if (!$slug) {
    redirect('index.php');
}

$article = $pdo->prepare("
    SELECT a.*, c.name as category_name, c.slug as category_slug, u.full_name as author_name, u.username as author_username
    FROM articles a
    LEFT JOIN categories c ON a.category_id = c.id
    LEFT JOIN users u ON a.author_id = u.id
    WHERE a.slug = ? AND a.status = 'published'
");
$article->execute([$slug]);
$article = $article->fetch();

if (!$article) {
    redirect('index.php');
}

incrementViews($article['id']);

$tags = getTags($article['id']);
$relatedArticles = getRelatedArticles($article['id'], $article['category_id'], 3);
$popularArticles = getPopularArticles(5);

$pageTitle = $article['title'];
$metaDescription = $article['meta_description'] ?: getExcerpt($article['content'], 150);
$canonicalUrl = SITE_URL . '/article.php?slug=' . $article['slug'];

$ogImage = $article['featured_image'] ? SITE_URL . '/assets/img/articles/' . $article['featured_image'] : '';

include 'includes/header.php';
?>

<div class="section-padding" style="padding-top: 40px;">
    <div class="container">
        <nav aria-label="breadcrumb" data-aos="fade-up">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>/">Home</a></li>
                <li class="breadcrumb-item">
                    <a href="categories.php?slug=<?php echo htmlspecialchars($article['category_slug']); ?>">
                        <?php echo htmlspecialchars($article['category_name'] ?? 'Uncategorized'); ?>
                    </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">
                    <?php echo htmlspecialchars($article['title']); ?>
                </li>
            </ol>
        </nav>
    </div>
</div>

<div class="section-padding">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <article data-aos="fade-up">
                    <div class="article-detail">
                        <div class="article-detail-image">
                            <?php if ($article['featured_image']): ?>
                            <img src="<?php echo SITE_URL; ?>/assets/img/articles/<?php echo htmlspecialchars($article['featured_image']); ?>" alt="<?php echo htmlspecialchars($article['title']); ?>">
                            <?php else: ?>
                            <i class="fas fa-server"></i>
                            <?php endif; ?>
                        </div>
                        
                        <div class="article-detail-content">
                            <span class="article-category">
                                <i class="fas fa-folder me-2"></i><?php echo htmlspecialchars($article['category_name'] ?? 'Uncategorized'); ?>
                            </span>
                            
                            <h1 class="article-detail-title">
                                <?php echo htmlspecialchars($article['title']); ?>
                            </h1>
                            
                            <div class="article-detail-meta">
                                <span>
                                    <i class="fas fa-user-circle me-2"></i>
                                    <a href="#" class="author-link">
                                        <?php echo htmlspecialchars($article['author_name'] ?? $article['author_username']); ?>
                                    </a>
                                </span>
                                <span>
                                    <i class="far fa-calendar-alt me-2"></i>
                                    <?php echo formatDate($article['published_at']); ?>
                                </span>
                                <span>
                                    <i class="far fa-clock me-2"></i>
                                    <?php echo $article['read_time']; ?> min read
                                </span>
                                <span>
                                    <i class="far fa-eye me-2"></i>
                                    <?php echo number_format($article['views']); ?> views
                                </span>
                            </div>
                            
                            <?php if ($tags): ?>
                            <div class="article-tags mb-4">
                                <?php foreach ($tags as $tag): ?>
                                <a href="tags.php?slug=<?php echo htmlspecialchars($tag['slug']); ?>" class="tag-item">
                                    <i class="fas fa-tag me-1"></i><?php echo htmlspecialchars($tag['name']); ?>
                                </a>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                            
                            <div class="article-body">
                                <?php echo $article['content']; ?>
                            </div>
                            
                            <div class="share-buttons">
                                <span class="share-label">
                                    <i class="fas fa-share-alt me-2"></i>Bagikan:
                                </span>
                                <a href="#" class="share-btn share-facebook" data-platform="facebook" title="Share on Facebook">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                                <a href="#" class="share-btn share-twitter" data-platform="twitter" title="Share on Twitter">
                                    <i class="fab fa-twitter"></i>
                                </a>
                                <a href="#" class="share-btn share-linkedin" data-platform="linkedin" title="Share on LinkedIn">
                                    <i class="fab fa-linkedin-in"></i>
                                </a>
                                <a href="#" class="share-btn share-whatsapp" data-platform="whatsapp" title="Share on WhatsApp">
                                    <i class="fab fa-whatsapp"></i>
                                </a>
                                <a href="#" class="share-btn copy-link-btn" title="Copy Link">
                                    <i class="fas fa-link"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </article>
                
                <?php if ($relatedArticles): ?>
                <div class="section-title mt-5" data-aos="fade-up">
                    <h3>Artikel <span class="text-primary">Terkait</span></h3>
                </div>
                
                <div class="row g-4" data-aos="fade-up">
                    <?php foreach ($relatedArticles as $related): ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="article-card">
                            <div class="article-image">
                                <?php if ($related['featured_image']): ?>
                                <img src="<?php echo SITE_URL; ?>/assets/img/articles/<?php echo htmlspecialchars($related['featured_image']); ?>" alt="<?php echo htmlspecialchars($related['title']); ?>">
                                <?php else: ?>
                                <i class="fas fa-file-alt"></i>
                                <?php endif; ?>
                            </div>
                            <div class="article-content">
                                <span class="article-category">
                                    <i class="fas fa-folder me-1"></i><?php echo htmlspecialchars($related['category_name'] ?? 'Uncategorized'); ?>
                                </span>
                                <h5 class="article-title">
                                    <a href="article.php?slug=<?php echo htmlspecialchars($related['slug']); ?>">
                                        <?php echo htmlspecialchars($related['title']); ?>
                                    </a>
                                </h5>
                                <p class="article-excerpt">
                                    <?php echo getExcerpt($related['content'], 100); ?>
                                </p>
                                <div class="article-meta">
                                    <span>
                                        <i class="far fa-calendar-alt"></i> <?php echo formatDate($related['published_at']); ?>
                                    </span>
                                    <span>
                                        <i class="far fa-eye"></i> <?php echo number_format($related['views']); ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="col-lg-4">
                <div class="sidebar" data-aos="fade-up" data-aos-delay="100">
                    <h4 class="sidebar-title">
                        <i class="fas fa-fire me-2"></i>Artikel Populer
                    </h4>
                    
                    <div class="sidebar-articles">
                        <?php foreach ($popularArticles as $popular): ?>
                        <?php if ($popular['id'] == $article['id']) continue; ?>
                        <div class="sidebar-article">
                            <div class="sidebar-article-thumb">
                                <?php if ($popular['featured_image']): ?>
                                <img src="<?php echo SITE_URL; ?>/assets/img/articles/<?php echo htmlspecialchars($popular['featured_image']); ?>" alt="<?php echo htmlspecialchars($popular['title']); ?>">
                                <?php else: ?>
                                <i class="fas fa-file-alt"></i>
                                <?php endif; ?>
                            </div>
                            <div class="sidebar-article-content">
                                <h5 class="sidebar-article-title">
                                    <a href="article.php?slug=<?php echo htmlspecialchars($popular['slug']); ?>">
                                        <?php echo htmlspecialchars($popular['title']); ?>
                                    </a>
                                </h5>
                                <div class="sidebar-article-meta">
                                    <span><i class="far fa-eye me-1"></i><?php echo number_format($popular['views']); ?> views</span>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <?php if ($tags): ?>
                <div class="sidebar" data-aos="fade-up" data-aos-delay="200">
                    <h4 class="sidebar-title">
                        <i class="fas fa-tags me-2"></i>Tags
                    </h4>
                    
                    <div class="tag-cloud">
                        <?php foreach ($tags as $tag): ?>
                        <a href="tags.php?slug=<?php echo htmlspecialchars($tag['slug']); ?>" class="tag-item">
                            <?php echo htmlspecialchars($tag['name']); ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>