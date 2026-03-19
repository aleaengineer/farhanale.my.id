<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$query = isset($_GET['q']) ? sanitize($_GET['q']) : '';
$isAjax = isset($_GET['ajax']) && $_GET['ajax'] == '1';

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = $isAjax ? 5 : 9;
$offset = ($page - 1) * $limit;

$results = [];

$suggestions = [];

if ($query) {
    $keywords = preg_split('/\s+/', $query);
    
    $titleConditions = [];
    $contentConditions = [];
    $excerptConditions = [];
    $keywordConditions = [];
    
    foreach ($keywords as $index => $keyword) {
        $paramNum = $index + 1;
        $titleConditions[] = "a.title LIKE :t$paramNum";
        $contentConditions[] = "a.content LIKE :c$paramNum";
        $excerptConditions[] = "a.excerpt LIKE :e$paramNum";
        $keywordConditions[] = "a.meta_keywords LIKE :k$paramNum";
    }
    
    $where = [
        "a.status = 'published'",
        "(" . implode(" OR ", $titleConditions) . " OR " . implode(" OR ", $contentConditions) . " OR " . implode(" OR ", $excerptConditions) . " OR " . implode(" OR ", $keywordConditions) . ")"
    ];
    $whereClause = implode(' AND ', $where);
    
    $totalArticles = $pdo->prepare("SELECT COUNT(*) FROM articles a WHERE $whereClause");
    foreach ($keywords as $index => $keyword) {
        $paramNum = $index + 1;
        $searchTerm = "%$keyword%";
        $totalArticles->bindValue(":t$paramNum", $searchTerm);
        $totalArticles->bindValue(":c$paramNum", $searchTerm);
        $totalArticles->bindValue(":e$paramNum", $searchTerm);
        $totalArticles->bindValue(":k$paramNum", $searchTerm);
    }
    $totalArticles->execute();
    $totalArticles = $totalArticles->fetchColumn();
    
    $articlesQuery = $pdo->prepare("
        SELECT a.*, c.name as category_name, c.slug as category_slug, u.full_name as author_name,
            CASE 
                WHEN a.title LIKE :exact_title THEN 1
                WHEN a.title LIKE :start_title THEN 2
                WHEN a.title LIKE :any_title THEN 3
                ELSE 4
            END as relevance_score
        FROM articles a
        LEFT JOIN categories c ON a.category_id = c.id
        LEFT JOIN users u ON a.author_id = u.id
        WHERE $whereClause
        ORDER BY relevance_score ASC, a.published_at DESC
        LIMIT :limit OFFSET :offset
    ");
    foreach ($keywords as $index => $keyword) {
        $paramNum = $index + 1;
        $searchTerm = "%$keyword%";
        $articlesQuery->bindValue(":t$paramNum", $searchTerm);
        $articlesQuery->bindValue(":c$paramNum", $searchTerm);
        $articlesQuery->bindValue(":e$paramNum", $searchTerm);
        $articlesQuery->bindValue(":k$paramNum", $searchTerm);
    }
    $articlesQuery->bindValue(':exact_title', "%$query%");
    $articlesQuery->bindValue(':start_title', "$query%");
    $articlesQuery->bindValue(':any_title', "%$query%");
    $articlesQuery->bindValue(':limit', $limit, PDO::PARAM_INT);
    $articlesQuery->bindValue(':offset', $offset, PDO::PARAM_INT);
    $articlesQuery->execute();
    $results = $articlesQuery->fetchAll();
    
    if (empty($results) || count($results) < 3) {
        $suggestionQuery = "
            SELECT DISTINCT 
                a.title,
                a.slug,
                CASE 
                    WHEN a.title LIKE :exact_title THEN 1
                    WHEN SOUNDEX(a.title) = SOUNDEX(:soundex_title) THEN 2
                    WHEN a.title LIKE :start_title THEN 3
                    ELSE 4
                END as score
            FROM articles a
            WHERE a.status = 'published'
                AND (a.title LIKE :any_title 
                    OR SOUNDEX(a.title) = SOUNDEX(:soundex_any)
                    OR a.meta_keywords LIKE :keywords)
            ORDER BY score ASC
            LIMIT 5
        ";
        $stmt = $pdo->prepare($suggestionQuery);
        $stmt->bindValue(':exact_title', "$query");
        $stmt->bindValue(':soundex_title', $query);
        $stmt->bindValue(':start_title', "$query%");
        $stmt->bindValue(':any_title', "%$query%");
        $stmt->bindValue(':soundex_any', $query);
        $stmt->bindValue(':keywords', "%$query%");
        $stmt->execute();
        $suggestions = $stmt->fetchAll();
    }
}

if ($isAjax) {
    ob_start();
    
    if (empty($results)): ?>
    <div class="text-center py-4">
        <i class="fas fa-search" style="font-size: 3rem; color: var(--gray); margin-bottom: 15px;"></i>
        <p class="text-muted">Tidak ditemukan artikel dengan kata kunci "<?php echo htmlspecialchars($query); ?>"</p>
        <?php if (!empty($suggestions)): ?>
        <p class="text-muted mt-3">Mungkin maksud Anda:</p>
        <div class="suggestions-container" style="max-width: 100%; margin-top: 15px;">
            <?php foreach ($suggestions as $suggestion): ?>
            <a href="article.php?slug=<?php echo htmlspecialchars($suggestion['slug']); ?>" class="suggestion-item">
                <i class="fas fa-file-alt me-2"></i><?php echo htmlspecialchars($suggestion['title']); ?>
            </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
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

$totalPages = isset($totalArticles) ? ceil($totalArticles / $limit) : 1;

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
                <?php echo $query && isset($totalArticles) ? 'Ditemukan ' . number_format($totalArticles) . ' artikel' : 'Ketik kata kunci untuk mencari artikel'; ?>
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
        <?php if (!empty($suggestions) && count($results) < 3): ?>
        <div class="row mb-4" data-aos="fade-up">
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="fas fa-lightbulb me-2"></i>
                    <strong>Saran Pencarian:</strong> Mungkin Anda mencari artikel ini?
                    <div class="mt-3 suggestions-container" style="max-width: 100%;">
                        <?php foreach ($suggestions as $suggestion): ?>
                        <a href="article.php?slug=<?php echo htmlspecialchars($suggestion['slug']); ?>" class="suggestion-item">
                            <i class="fas fa-file-alt me-2"></i><?php echo htmlspecialchars($suggestion['title']); ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
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
        
        <?php if ($totalPages > 1 && !empty($results)): ?>
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
        
        <?php elseif (!empty($suggestions)): ?>
        <div class="text-center py-5" data-aos="fade-up">
            <i class="fas fa-search" style="font-size: 4rem; color: var(--gray); margin-bottom: 20px;"></i>
            <h4 class="text-muted">Tidak ditemukan artikel dengan kata kunci "<?php echo htmlspecialchars($query); ?>"</h4>
            <p class="text-muted mb-4">Mungkin maksud Anda:</p>
            <div class="suggestions-container">
                <?php foreach ($suggestions as $suggestion): ?>
                <a href="article.php?slug=<?php echo htmlspecialchars($suggestion['slug']); ?>" class="suggestion-item">
                    <i class="fas fa-file-alt me-2"></i><?php echo htmlspecialchars($suggestion['title']); ?>
                </a>
                <?php endforeach; ?>
            </div>
            <a href="<?php echo SITE_URL; ?>/" class="btn btn-primary mt-4">
                <i class="fas fa-home me-2"></i>Kembali ke Home
            </a>
        </div>
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

.suggestions-container {
    max-width: 600px;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.suggestion-item {
    display: block;
    padding: 15px 20px;
    background: #fff;
    border-radius: 10px;
    color: var(--dark);
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    text-align: left;
}

.suggestion-item:hover {
    background: var(--primary);
    color: #fff;
    transform: translateX(10px);
    box-shadow: 0 5px 20px rgba(139, 92, 246, 0.3);
}
</style>

<?php include 'includes/footer.php'; ?>