<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 9;
$offset = ($page - 1) * $limit;

$selectedCategory = isset($_GET['slug']) ? sanitize($_GET['slug']) : '';

$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();

$where = ["status = 'published'"];
$params = [];
$categoryName = 'Semua Kategori';

if ($selectedCategory) {
    $where[] = "category_id = (SELECT id FROM categories WHERE slug = ?)";
    $params[] = $selectedCategory;
    
    $catQuery = $pdo->prepare("SELECT * FROM categories WHERE slug = ?");
    $catQuery->execute([$selectedCategory]);
    $category = $catQuery->fetch();
    
    if ($category) {
        $categoryName = $category['name'];
    }
}

$whereClause = implode(' AND ', $where);

$totalArticles = $pdo->prepare("SELECT COUNT(*) FROM articles WHERE $whereClause");
$totalArticles->execute($params);
$totalArticles = $totalArticles->fetchColumn();
$totalPages = ceil($totalArticles / $limit);

$articles = $pdo->prepare("
    SELECT a.*, c.name as category_name, c.slug as category_slug, u.full_name as author_name
    FROM articles a
    LEFT JOIN categories c ON a.category_id = c.id
    LEFT JOIN users u ON a.author_id = u.id
    WHERE $whereClause
    ORDER BY a.published_at DESC
    LIMIT :limit OFFSET :offset
");
foreach ($params as $i => $param) {
    $articles->bindValue($i + 1, $param);
}
$articles->bindValue(':limit', $limit, PDO::PARAM_INT);
$articles->bindValue(':offset', $offset, PDO::PARAM_INT);
$articles->execute();
$articles = $articles->fetchAll();

$pageTitle = $categoryName;
$metaDescription = "Lihat semua artikel dalam kategori " . $categoryName;
include 'includes/header.php';
?>

<section class="section-padding" style="padding-top: 40px;">
    <div class="container">
        <div class="section-title" data-aos="fade-up">
            <h1>
                <i class="fas fa-th-large me-2"></i>
                <?php echo htmlspecialchars($categoryName); ?>
            </h1>
            <p>
                <?php echo $selectedCategory ? 'Artikel dalam kategori ' . htmlspecialchars($categoryName) : 'Jelajahi artikel berdasarkan kategori'; ?>
            </p>
        </div>
        
        <div class="row mb-5" data-aos="fade-up" data-aos-delay="100">
            <div class="col-12">
                <div class="category-filter">
                    <button class="btn btn-filter <?php echo !$selectedCategory ? 'active' : ''; ?>" onclick="filterCategory('')">
                        Semua
                    </button>
                    <?php foreach ($categories as $cat): ?>
                    <button class="btn btn-filter <?php echo $selectedCategory == $cat['slug'] ? 'active' : ''; ?>" onclick="filterCategory('<?php echo htmlspecialchars($cat['slug']); ?>')">
                        <i class="fas <?php echo htmlspecialchars($cat['icon']); ?> me-1"></i>
                        <?php echo htmlspecialchars($cat['name']); ?>
                        <span class="badge bg-light text-dark ms-1"><?php echo number_format($cat['article_count']); ?></span>
                    </button>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        
        <?php if ($articles): ?>
        <div class="row g-4">
            <?php foreach ($articles as $article): ?>
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
                    <a class="page-link" href="?page=<?php echo $page - 1; ?><?php echo $selectedCategory ? '&slug=' . $selectedCategory : ''; ?>">
                        <i class="fas fa-chevron-left"></i> Previous
                    </a>
                </li>
                <?php endif; ?>
                
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <?php if ($i == 1 || $i == $totalPages || ($i >= $page - 2 && $i <= $page + 2)): ?>
                <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $i; ?><?php echo $selectedCategory ? '&slug=' . $selectedCategory : ''; ?>"><?php echo $i; ?></a>
                </li>
                <?php elseif ($i == $page - 3 || $i == $page + 3): ?>
                <li class="page-item disabled"><span class="page-link">...</span></li>
                <?php endif; ?>
                <?php endfor; ?>
                
                <?php if ($page < $totalPages): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?php echo $page + 1; ?><?php echo $selectedCategory ? '&slug=' . $selectedCategory : ''; ?>">
                        Next <i class="fas fa-chevron-right"></i>
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </nav>
        <?php endif; ?>
        
        <?php else: ?>
        <div class="text-center py-5" data-aos="fade-up">
            <i class="fas fa-folder-open" style="font-size: 4rem; color: var(--gray); margin-bottom: 20px;"></i>
            <h4 class="text-muted">Belum ada artikel di kategori ini</h4>
            <a href="<?php echo SITE_URL; ?>/" class="btn btn-primary mt-3">
                <i class="fas fa-home me-2"></i>Kembali ke Home
            </a>
        </div>
        <?php endif; ?>
    </div>
</section>

<script>
function filterCategory(slug) {
    const url = new URL(window.location.href);
    if (slug) {
        url.searchParams.set('slug', slug);
        url.searchParams.set('page', '1');
    } else {
        url.searchParams.delete('slug');
        url.searchParams.set('page', '1');
    }
    window.location.href = url.toString();
}
</script>

<?php include 'includes/footer.php'; ?>