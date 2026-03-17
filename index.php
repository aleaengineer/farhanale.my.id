<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$portfolioCount = $pdo->query("SELECT COUNT(*) FROM portfolio")->fetchColumn();
$blogCount = $pdo->query("SELECT COUNT(*) FROM blogs")->fetchColumn();
$certCount = $pdo->query("SELECT COUNT(*) FROM certificates")->fetchColumn();

$featuredPortfolio = $pdo->query("SELECT * FROM portfolio ORDER BY created_at DESC LIMIT 3")->fetchAll();

$skills = $pdo->query("SELECT * FROM skills LIMIT 6")->fetchAll();

$pageTitle = 'Home';
include 'includes/header.php';
?>

<div class="hero-section">
    <div class="container">
        <div class="hero-content" data-aos="fade-up">
            <h1 class="hero-title">Hi, I'm <span class="gradient-text">Farhan Ale</span></h1>
            <p class="hero-subtitle">Network & Automation Engineer</p>
            <p class="hero-description">Expert in MikroTik, Network Automation, and Cloud Technologies. Building robust and efficient network infrastructure with modern automation solutions.</p>
            <div class="hero-buttons">
                <a href="portfolio.php" class="btn btn-hero">
                    <i class="fas fa-folder-open me-2"></i>View Portfolio
                </a>
                <a href="contact.php" class="btn btn-hero-outline">
                    <i class="fas fa-paper-plane me-2"></i>Contact Me
                </a>
            </div>
        </div>
    </div>
</div>

<section class="stats-section">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-project-diagram"></i>
                    </div>
                    <h2 class="stat-number" data-count="<?php echo $portfolioCount; ?>">0</h2>
                    <p class="stat-label">Projects Completed</p>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-blog"></i>
                    </div>
                    <h2 class="stat-number" data-count="<?php echo $blogCount; ?>">0</h2>
                    <p class="stat-label">Blog Articles</p>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-certificate"></i>
                    </div>
                    <h2 class="stat-number" data-count="<?php echo $certCount; ?>">0</h2>
                    <p class="stat-label">Certifications</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section-padding">
    <div class="container">
        <div class="section-title" data-aos="fade-up">
            <h2>Featured <span class="gradient-text">Portfolio</span></h2>
            <p>Check out some of my recent projects in networking, MikroTik configuration, and automation.</p>
        </div>
        <div class="row g-4">
            <?php foreach ($featuredPortfolio as $project): ?>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="<?php echo array_search($project, $featuredPortfolio) * 100; ?>">
                <div class="portfolio-card">
                    <div class="portfolio-image">
                        <i class="fas fa-network-wired"></i>
                    </div>
                    <div class="portfolio-content">
                        <span class="portfolio-category"><?php echo htmlspecialchars($project['category']); ?></span>
                        <h3 class="portfolio-title"><?php echo htmlspecialchars($project['title']); ?></h3>
                        <p class="portfolio-description"><?php echo substr(htmlspecialchars($project['description']), 0, 100); ?>...</p>
                        <a href="portfolio.php" class="portfolio-link">
                            View Details <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-5" data-aos="fade-up">
            <a href="portfolio.php" class="btn btn-gradient">
                <i class="fas fa-th-large me-2"></i>View All Projects
            </a>
        </div>
    </div>
</section>

<section class="section-padding" style="background: var(--light);">
    <div class="container">
        <div class="section-title" data-aos="fade-up">
            <h2>My <span class="gradient-text">Skills</span></h2>
            <p>Here are some of my technical skills and expertise in networking and automation.</p>
        </div>
        <div class="row g-4">
            <?php foreach ($skills as $skill): ?>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="<?php echo array_search($skill, $skills) * 100; ?>">
                <div class="skill-card">
                    <div class="skill-icon">
                        <i class="fas <?php echo htmlspecialchars($skill['icon']); ?>"></i>
                    </div>
                    <h3 class="skill-name"><?php echo htmlspecialchars($skill['name']); ?></h3>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width: 0%" aria-valuenow="<?php echo $skill['percentage']; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <p class="skill-percentage"><?php echo $skill['percentage']; ?>%</p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-5" data-aos="fade-up">
            <a href="about.php" class="btn btn-outline-gradient">
                <i class="fas fa-graduation-cap me-2"></i>View All Skills
            </a>
        </div>
    </div>
</section>

<section class="section-padding contact-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="contact-content text-center text-white" data-aos="fade-up">
                    <h2>Let's Work <span class="text-warning">Together</span></h2>
                    <p class="mb-4">Have a project in mind? Let's discuss how I can help you build robust network infrastructure and automation solutions.</p>
                    <a href="contact.php" class="btn btn-hero">
                        <i class="fas fa-envelope me-2"></i>Get In Touch
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
