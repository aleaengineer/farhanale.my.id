<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$slug = $_GET['slug'] ?? '';

$stmt = $pdo->prepare("SELECT * FROM blogs WHERE slug = ?");
$stmt->execute([$slug]);
$blog = $stmt->fetch();

if (!$blog) {
    redirect('blog.php');
}

$relatedBlogs = $pdo->prepare("SELECT * FROM blogs WHERE category = ? AND id != ? ORDER BY created_at DESC LIMIT 3");
$relatedBlogs->execute([$blog['category'], $blog['id']]);
$relatedBlogs = $relatedBlogs->fetchAll();

$pageTitle = htmlspecialchars($blog['title']);
include 'includes/header.php';
?>

<div class="section-padding" style="padding-top: 120px;">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <article data-aos="fade-up">
                    <div class="blog-image mb-4">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    
                    <span class="blog-category"><?php echo htmlspecialchars($blog['category']); ?></span>
                    
                    <h1 class="mb-4"><?php echo htmlspecialchars($blog['title']); ?></h1>
                    
                    <div class="blog-meta mb-4">
                        <span class="blog-date"><i class="far fa-calendar-alt me-2"></i><?php echo formatDate($blog['created_at']); ?></span>
                        <span class="blog-read-time"><i class="far fa-clock me-2"></i><?php echo readingTime($blog['content']); ?></span>
                    </div>
                    
                    <div class="blog-content text-muted mb-5">
                        <?php echo nl2br(htmlspecialchars($blog['content'])); ?>
                    </div>
                    
                    <div class="border-top pt-4">
                        <h5>Share this article:</h5>
                        <div class="social-links">
                            <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>" target="_blank" class="btn btn-social btn-linkedin me-2">
                                <i class="fab fa-linkedin-in"></i>
                            </a>
                            <a href="https://twitter.com/intent/tweet?text=<?php echo urlencode($blog['title']); ?>&url=<?php echo urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>" target="_blank" class="btn btn-social btn-linkedin me-2" style="background: #1DA1F2;">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>" target="_blank" class="btn btn-social btn-linkedin" style="background: #1877F2;">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                        </div>
                    </div>
                </article>
            </div>
            
            <div class="col-lg-4" data-aos="fade-left">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">About Author</h5>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <div style="width: 80px; height: 80px; background: var(--gradient); border-radius: 50%; margin: 0 auto 15px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-user" style="font-size: 2rem; color: white;"></i>
                            </div>
                            <h5>Farhan Ale</h5>
                            <p class="text-muted mb-0">Network & Automation Engineer</p>
                        </div>
                        <p class="text-muted small">Passionate about networking, automation, and building robust infrastructure solutions.</p>
                    </div>
                </div>
                
                <?php if ($relatedBlogs): ?>
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Related Posts</h5>
                    </div>
                    <div class="card-body">
                        <?php foreach ($relatedBlogs as $related): ?>
                        <div class="mb-3">
                            <h6 class="mb-1">
                                <a href="blog-detail.php?slug=<?php echo htmlspecialchars($related['slug']); ?>" style="color: var(--primary);">
                                    <?php echo htmlspecialchars($related['title']); ?>
                                </a>
                            </h6>
                            <small class="text-muted"><?php echo formatDate($related['created_at']); ?></small>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
