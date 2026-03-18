<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

header('Content-Type: application/xml');

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc><?php echo SITE_URL; ?>/</loc>
        <lastmod><?php echo date('Y-m-d'); ?></lastmod>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>
    <url>
        <loc><?php echo SITE_URL; ?>/categories.php</loc>
        <lastmod><?php echo date('Y-m-d'); ?></lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
    <url>
        <loc><?php echo SITE_URL; ?>/tags.php</loc>
        <lastmod><?php echo date('Y-m-d'); ?></lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.7</priority>
    </url>
    
    <?php
    $articles = $pdo->query("SELECT slug, updated_at FROM articles WHERE status = 'published'")->fetchAll();
    foreach ($articles as $article):
    ?>
    <url>
        <loc><?php echo SITE_URL; ?>/article.php?slug=<?php echo htmlspecialchars($article['slug']); ?></loc>
        <lastmod><?php echo date('Y-m-d', strtotime($article['updated_at'])); ?></lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.9</priority>
    </url>
    <?php endforeach; ?>
    
    <?php
    $categories = $pdo->query("SELECT slug FROM categories")->fetchAll();
    foreach ($categories as $category):
    ?>
    <url>
        <loc><?php echo SITE_URL; ?>/categories.php?slug=<?php echo htmlspecialchars($category['slug']); ?></loc>
        <lastmod><?php echo date('Y-m-d'); ?></lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
    <?php endforeach; ?>
    
    <?php
    $tags = $pdo->query("SELECT slug FROM tags")->fetchAll();
    foreach ($tags as $tag):
    ?>
    <url>
        <loc><?php echo SITE_URL; ?>/tags.php?slug=<?php echo htmlspecialchars($tag['slug']); ?></loc>
        <lastmod><?php echo date('Y-m-d'); ?></lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.6</priority>
    </url>
    <?php endforeach; ?>
</urlset>