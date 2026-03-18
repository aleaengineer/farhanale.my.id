<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle . ' | ' . SITE_NAME) : htmlspecialchars(SITE_NAME); ?></title>
    
    <meta name="description" content="<?php echo isset($metaDescription) ? htmlspecialchars($metaDescription) : htmlspecialchars(getSetting('site_description')); ?>">
    <meta name="keywords" content="MikroTik, networking, router, konfigurasi MikroTik, tutorial MikroTik">
    <meta name="author" content="<?php echo htmlspecialchars(getSetting('site_name')); ?>">
    
    <meta name="robots" content="index, follow">
    <meta name="googlebot" content="index, follow">
    
    <link rel="canonical" href="<?php echo isset($canonicalUrl) ? htmlspecialchars($canonicalUrl) : getCurrentUrl(); ?>">
    
    <meta property="og:title" content="<?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) : htmlspecialchars(SITE_NAME); ?>">
    <meta property="og:description" content="<?php echo isset($metaDescription) ? htmlspecialchars($metaDescription) : htmlspecialchars(getSetting('site_description')); ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo getCurrentUrl(); ?>">
    <meta property="og:image" content="<?php echo isset($ogImage) ? htmlspecialchars($ogImage) : SITE_URL . '/assets/img/logo/og-image.jpg'; ?>">
    <meta property="og:site_name" content="<?php echo htmlspecialchars(SITE_NAME); ?>">
    
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) : htmlspecialchars(SITE_NAME); ?>">
    <meta name="twitter:description" content="<?php echo isset($metaDescription) ? htmlspecialchars($metaDescription) : htmlspecialchars(getSetting('site_description')); ?>">
    <meta name="twitter:image" content="<?php echo isset($ogImage) ? htmlspecialchars($ogImage) : SITE_URL . '/assets/img/logo/og-image.jpg'; ?>">
    
    <link rel="icon" type="image/x-icon" href="<?php echo SITE_URL; ?>/assets/img/logo/favicon.ico">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo SITE_URL; ?>/assets/img/logo/apple-touch-icon.png">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <link href="<?php echo SITE_URL; ?>/assets/css/style.css" rel="stylesheet">
    
    <style>
        :root {
            --primary: #8B5CF6;
            --secondary: #EC4899;
            --mikrotik: #2563EB;
            --dark: #1F2937;
            --light: #F9FAFB;
            --gray: #6B7280;
            --success: #10B981;
            --warning: #F59E0B;
            --danger: #EF4444;
        }
    </style>
    
    <?php if (isset($additionalCss)) echo $additionalCss; ?>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="<?php echo SITE_URL; ?>/">
                <i class="fas fa-server me-2" style="color: var(--mikrotik);"></i>
                <span class="brand-text">MikroTik <span class="brand-highlight">Blog</span></span>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto ms-lg-4">
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/">
                            <i class="fas fa-home me-1"></i> Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'categories.php' ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/categories.php">
                            <i class="fas fa-th-large me-1"></i> Kategori
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'tags.php' ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/tags.php">
                            <i class="fas fa-tags me-1"></i> Tags
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo SITE_URL; ?>/../" target="_blank">
                            <i class="fas fa-external-link-alt me-1"></i> Website
                        </a>
                    </li>
                </ul>
                
                <form class="d-flex search-form me-3" action="<?php echo SITE_URL; ?>/search.php">
                    <div class="input-group">
                        <input class="form-control search-input" type="search" name="q" placeholder="Cari artikel..." value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>">
                        <button class="btn btn-search" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </nav>