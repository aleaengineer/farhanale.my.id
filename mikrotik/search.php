<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$query = isset($_GET['q']) ? sanitize($_GET['q']) : '';
$isAjax = isset($_GET['ajax']) && $_GET['ajax'] == '1';

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = $isAjax ? 5 : 9;
$offset = ($page - 1) * $limit;

$results = [];

if ($query) {
    $where = [
        "a.status = 'published'",
        "(a.title LIKE ? OR a.content LIKE ? OR a.excerpt LIKE ? OR a.meta_keywords LIKE ?)"
    ];
    $params = ["%$query%", "%$query%", "%$query%", "%$query%"];
    
    $whereClause = implode(' AND ', $where);
    
    $totalArticles = $pdo->prepare("SELECT COUNT(*) FROM articles a WHERE $whereClause");
    $totalArticles->execute($params);
    $totalArticles = $totalArticles->fetchColumn();
    
    $articlesQuery = $pdo->prepare("
        SELECT a.*, c.name as category_name, c.slug as category_slug, u.full_name as author_name
        FROM articles a
        LEFT JOIN categories c ON a.category_id = c.id
        LEFT JOIN users u ON a.author_id = u.id
        WHERE $whereClause
        ORDER BY a.published_at DESC
        LIMIT :limit OFFSET :offset
    ");
    foreach ($params as $i => $param) {
        $articlesQuery->bindValue($i + 1, $param);
    }
    $articlesQuery->bindValue(':limit', $limit, PDO::PARAM_INT);
    $articlesQuery->bindValue(':offset', $offset, PDO::PARAM_INT);
    $articlesQuery->execute();
    $results = $articlesQuery->fetchAll();
}

if ($isAjax) {
    ob_start();
    
    if (empty($results)): ?>
    <div class="text-center py-4">
        <i class="fas fa-search" style="font-size: 3rem; color: var(--gray); margin-bottom: 15px;"></i>
        <p class="text-muted">Tidak ditemukan artikel dengan kata kunci "<?php echo htmlspecialchars($query); ?>"</p>
    </div>
    <?php else: ?>
    <div class="search-results">
        <?php foreach ($results as $article): ?>
        <div class="search-result-item">
            <a href="article.php?slug=<?php echo htmlspecialchars($article['slug']); ?>" class="search-result-link">
                <h5 class="search-result-title">
                    <?php echo htmlspecialchars($article['title']); ?>
                </h5>
            </a>
            <p class="search-result-excerpt">
                <?php echo getExcerpt($article['content'], 150); ?>
            </p>
            <div class="search-result-meta">
                <span class="badge bg-light text-dark">
                    <i class="fas fa-folder me-1"></i><?php echo htmlspecialchars($article['category_name'] ?? 'Uncategorized'); ?>
                </span>
                <span class="text-muted ms-2">
                    <i class="far fa-calendar-alt me-1"></i><?php echo formatDate($article['published_at']); ?>
                </span>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif;
    
    $output = ob_get_clean();
    echo $output;
    exit;
}

$totalPages = ceil($totalArticles ?? 0 / $limit);

$pageTitle = 'Search';
$metaDescription = 'Cari artikel blog MikroTik';
include 'includes/header.php';
?>

<section class="section-padding" style="padding-top: 40px;">
    <div class="container">
        <div class="section-title" data-aos="fade-up">
            <h1>
                <i class="fas fa-search me-2"></i>
                <?php echo $query ? 'Hasil Pencarian: "' . htmlspecialchars($query) . '"' : 'Cari Artikel'; ?>
            </h1>
            <p>
                <?php echo $query ? 'Ditemukan ' . number_format($totalArticles) . ' artikel' : 'Ketik kata kunci untuk mencari artikel'; ?>
            </p>
        </div>
        
        <div class="row mb-5" data-aos="fade-up" data-aos-delay="100">
            <div class="col-12">
                <div class="search-box-large">
                    <form action="search.php" method="GET">
                        <div class="input-group">
                            <input type="text" class="form-control" name="q" placeholder="Cari artikel..." value="<?php echo htmlspecialchars($query); ?>" autofocus>
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-search me-2"></i>Cari
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <?php if (!$query): ?>
        <div class="text-center py-5" data-aos="fade-up">
            <i class="fas fa-search" style="font-size: 5rem; color: var(--gray); margin-bottom: 30px;"></i>
            <h3 class="mb-3">Cari Artikel Blog MikroTik</h3>
            <p class="text-muted mb-4">Ketik kata kunci di atas untuk mencari artikel yang Anda butuhkan</p>
            <div class="search-suggestions">
                <p class="text-muted mb-2">Pencarian populer:</p>
                <div class="suggestion-tags">
                    <a href="search.php?q=load+balancing" class="tag-item">load balancing</a>
                    <a href="search.php?q=vpn" class="tag-item">VPN</a>
                    <a href="search.php?q=firewall" class="tag-item">firewall</a>
                    <a href="search.php?q=hotspot" class="tag-item">hotspot</a>
                    <a href="search.php?q=routing" class="tag-item">routing</a>
                </div>
            </div>
        </div>
        
        <?php elseif (!empty($results)): ?>
        <div class="row g-4">
            <?php foreach ($results as $article): ?>
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="<?php echo array_search($article, $results) * 100; ?>">
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
                    <a class="page-link" href="?page=<?php echo $page - 1; ?>&q=<?php echo urlencode($query); ?>">
                        <i class="fas fa-chevron-left"></i> Previous
                    </a>
                </li>
                <?php endif; ?>
                
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <?php if ($i == 1 || $i == $totalPages || ($i >= $page - 2 && $i <= $page + 2)): ?>
                <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $i; ?>&q=<?php echo urlencode($query); ?>"><?php echo $i; ?></a>
                </li>
                <?php elseif ($i == $page - 3 || $i == $page + 3): ?>
                <li class="page-item disabled"><span class="page-link">...</span></li>
                <?php endif; ?>
                <?php endfor; ?>
                
                <?php if ($page < $totalPages): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?php echo $page + 1; ?>&q=<?php echo urlencode($query); ?>">
                        Next <i class="fas fa-chevron-right"></i>
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </nav>
        <?php endif; ?>
        
        <?php else: ?>
        <div class="text-center py-5" data-aos="fade-up">
            <i class="fas fa-search" style="font-size: 4rem; color: var(--gray); margin-bottom: 20px;"></i>
            <h4 class="text-muted">Tidak ditemukan artikel dengan kata kunci "<?php echo htmlspecialchars($query); ?>"</h4>
            <p class="text-muted mb-4">Coba kata kunci lain atau lihat artikel populer kami</p>
            <a href="<?php echo SITE_URL; ?>/" class="btn btn-primary">
                <i class="fas fa-home me-2"></i>Kembali ke Home
            </a>
        </div>
        <?php endif; ?>
    </div>
</section>

<style>
.search-box-large {
    max-width: 800px;
    margin: 0 auto;
}

.search-box-large .input-group {
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    border-radius: 50px;
    overflow: hidden;
}

.search-box-large .form-control {
    border: none;
    padding: 20px 25px;
    font-size: 1.1rem;
}

.search-box-large .btn-primary {
    padding: 20px 40px;
    border-radius: 0 50px 50px 0;
    font-weight: 600;
}

#searchResults {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: #fff;
    border-radius: 15px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
    z-index: 1000;
    max-height: 500px;
    overflow-y: auto;
    display: none;
    margin-top: 10px;
}

#searchResults.show {
    display: block;
}

.search-result-item {
    padding: 15px 20px;
    border-bottom: 1px solid #E5E7EB;
    transition: all 0.3s ease;
}

.search-result-item:last-child {
    border-bottom: none;
}

.search-result-item:hover {
    background: var(--light);
}

.search-result-link {
    text-decoration: none;
    color: var(--dark);
}

.search-result-title {
    margin: 0 0 10px 0;
    font-weight: 600;
    color: var(--primary);
}

.search-result-excerpt {
    color: var(--gray);
    font-size: 0.9rem;
    margin: 0 0 10px 0;
}

.search-result-meta {
    display: flex;
    align-items: center;
    font-size: 0.85rem;
}

.search-suggestions {
    max-width: 600px;
    margin: 0 auto;
}

.suggestion-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    justify-content: center;
}

.suggestion-tags .tag-item {
    padding: 8px 15px;
    background: #fff;
    border-radius: 20px;
    color: var(--dark);
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.suggestion-tags .tag-item:hover {
    background: var(--primary);
    color: #fff;
    transform: translateY(-3px);
}
</style>

<?php include 'includes/footer.php'; ?>