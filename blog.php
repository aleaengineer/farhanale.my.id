<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 6;
$offset = ($page - 1) * $limit;

$totalBlogs = $pdo->query("SELECT COUNT(*) FROM blogs")->fetchColumn();
$totalPages = ceil($totalBlogs / $limit);

$blogs = $pdo->prepare("SELECT * FROM blogs ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
$blogs->bindValue(':limit', $limit, PDO::PARAM_INT);
$blogs->bindValue(':offset', $offset, PDO::PARAM_INT);
$blogs->execute();
$blogs = $blogs->fetchAll();

$pageTitle = 'Blog';
include 'includes/header.php';
?>

<div class="section-padding" style="padding-top: 120px;">
    <div class="container">
        <div class="section-title" data-aos="fade-up">
            <h2>My <span class="gradient-text">Blog</span></h2>
            <p>Read my articles about networking, MikroTik, automation, and technology.</p>
        </div>
        
        <div class="mb-5" data-aos="fade-up">
            <div class="input-group">
                <input type="text" id="blogSearch" class="form-control" placeholder="Search articles...">
                <span class="input-group-text bg-white"><i class="fas fa-search"></i></span>
            </div>
        </div>
        
        <div class="row g-4">
            <?php foreach ($blogs as $blog): ?>
            <div class="col-md-4" data-aos="fade-up">
                <div class="blog-card">
                    <div class="blog-image">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div class="blog-content">
                        <span class="blog-category"><?php echo htmlspecialchars($blog['category']); ?></span>
                        <h3 class="blog-title">
                            <a href="blog-detail.php?slug=<?php echo htmlspecialchars($blog['slug']); ?>">
                                <?php echo htmlspecialchars($blog['title']); ?>
                            </a>
                        </h3>
                        <p class="blog-excerpt"><?php echo substr(strip_tags($blog['content']), 0, 100); ?>...</p>
                        <div class="blog-meta">
                            <span class="blog-date"><i class="far fa-calendar-alt me-2"></i><?php echo formatDate($blog['created_at']); ?></span>
                            <span class="blog-read-time"><i class="far fa-clock me-2"></i><?php echo readingTime($blog['content']); ?></span>
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
                    <a class="page-link" href="?page=<?php echo $page - 1; ?>">Previous</a>
                </li>
                <?php endif; ?>
                
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                </li>
                <?php endfor; ?>
                
                <?php if ($page < $totalPages): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?php echo $page + 1; ?>">Next</a>
                </li>
                <?php endif; ?>
            </ul>
        </nav>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
