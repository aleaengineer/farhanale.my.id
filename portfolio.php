<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$portfolio = $pdo->query("SELECT * FROM portfolio ORDER BY created_at DESC")->fetchAll();

$categories = $pdo->query("SELECT DISTINCT category FROM portfolio")->fetchAll(PDO::FETCH_COLUMN);

$pageTitle = 'Portfolio';
include 'includes/header.php';
?>

<div class="section-padding" style="padding-top: 120px;">
    <div class="container">
        <div class="section-title" data-aos="fade-up">
            <h2>My <span class="gradient-text">Portfolio</span></h2>
            <p>Explore my projects in networking, MikroTik configuration, and automation.</p>
        </div>
        
        <div class="text-center mb-5" data-aos="fade-up">
            <button class="filter-btn active" data-filter="all">All</button>
            <?php foreach ($categories as $category): ?>
            <button class="filter-btn" data-filter="<?php echo htmlspecialchars($category); ?>"><?php echo htmlspecialchars($category); ?></button>
            <?php endforeach; ?>
        </div>
        
        <div class="row g-4">
            <?php foreach ($portfolio as $project): ?>
            <div class="col-md-4" data-aos="fade-up">
                <div class="portfolio-card" data-category="<?php echo htmlspecialchars($project['category']); ?>">
                    <div class="portfolio-image">
                        <i class="fas fa-network-wired"></i>
                    </div>
                    <div class="portfolio-content">
                        <span class="portfolio-category"><?php echo htmlspecialchars($project['category']); ?></span>
                        <h3 class="portfolio-title"><?php echo htmlspecialchars($project['title']); ?></h3>
                        <p class="portfolio-description"><?php echo htmlspecialchars($project['description']); ?></p>
                        <?php if (!empty($project['project_url']) && $project['project_url'] != '#'): ?>
                        <a href="<?php echo htmlspecialchars($project['project_url']); ?>" class="portfolio-link" target="_blank">
                            View Project <i class="fas fa-external-link-alt"></i>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
