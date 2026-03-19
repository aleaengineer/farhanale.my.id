<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) {
    redirect('articles.php');
}

$stmt = $pdo->prepare("SELECT * FROM articles WHERE id = ?");
$stmt->execute([$id]);
$article = $stmt->fetch();

if (!$article) {
    redirect('articles.php');
}

$tagStmt = $pdo->prepare("SELECT tag_id FROM article_tags WHERE article_id = ?");
$tagStmt->execute([$id]);
$article['tags'] = array_column($tagStmt->fetchAll(), 'tag_id');

$message = '';
$messageType = '';

if (isset($_GET['delete'])) {
    $deleteId = (int)$_GET['delete'];
    if ($deleteId === $id) {
        $current = $pdo->prepare("SELECT featured_image, category_id FROM articles WHERE id = ?");
        $current->execute([$id]);
        $current = $current->fetch();
        
        if ($current['featured_image'] && file_exists('../assets/img/articles/' . $current['featured_image'])) {
            unlink('../assets/img/articles/' . $current['featured_image']);
        }
        
        $stmt = $pdo->prepare("DELETE FROM article_tags WHERE article_id = ?");
        $stmt->execute([$id]);
        
        $stmt = $pdo->prepare("DELETE FROM articles WHERE id = ?");
        $stmt->execute([$id]);
        
        if ($current['category_id']) {
            $stmt = $pdo->prepare("UPDATE categories SET article_count = article_count - 1 WHERE id = ?");
            $stmt->execute([$current['category_id']]);
        }
        
        redirect('articles.php');
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitize($_POST['title']);
    $slug = generateSlug($title);
    $content = $_POST['content'];
    $excerpt = sanitize($_POST['excerpt']);
    $category_id = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null;
    $status = sanitize($_POST['status']);
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $meta_title = sanitize($_POST['meta_title']);
    $meta_description = sanitize($_POST['meta_description']);
    $meta_keywords = sanitize($_POST['meta_keywords']);
    
    $featured_image = $article['featured_image'];
    if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === 0) {
        $upload = uploadImage($_FILES['featured_image'], '../assets/img/articles/');
        if ($upload['success']) {
            if ($article['featured_image'] && file_exists('../assets/img/articles/' . $article['featured_image'])) {
                unlink('../assets/img/articles/' . $article['featured_image']);
            }
            $featured_image = $upload['filename'];
        }
    }
    
    $read_time = readingTime($content);
    $published_at = ($status === 'published' && !$article['published_at']) ? date('Y-m-d H:i:s') : $article['published_at'];
    
    $oldCategoryId = $article['category_id'];
    if ($oldCategoryId != $category_id) {
        if ($oldCategoryId) {
            $stmt = $pdo->prepare("UPDATE categories SET article_count = article_count - 1 WHERE id = ?");
            $stmt->execute([$oldCategoryId]);
        }
        if ($category_id) {
            $stmt = $pdo->prepare("UPDATE categories SET article_count = article_count + 1 WHERE id = ?");
            $stmt->execute([$category_id]);
        }
    }
    
    $stmt = $pdo->prepare("UPDATE articles SET title = ?, slug = ?, content = ?, excerpt = ?, featured_image = ?, category_id = ?, status = ?, is_featured = ?, read_time = ?, meta_title = ?, meta_description = ?, meta_keywords = ?, published_at = ? WHERE id = ?");
    if ($stmt->execute([$title, $slug, $content, $excerpt, $featured_image, $category_id, $status, $is_featured, $read_time, $meta_title, $meta_description, $meta_keywords, $published_at, $id])) {
        
        $stmt = $pdo->prepare("DELETE FROM article_tags WHERE article_id = ?");
        $stmt->execute([$id]);
        
        if (!empty($_POST['tags'])) {
            $currentTags = $pdo->prepare("SELECT tag_id FROM article_tags WHERE article_id = ?");
            $currentTags->execute([$id]);
            $currentTags = array_column($currentTags->fetchAll(), 'tag_id');
            
            foreach ($_POST['tags'] as $tagId) {
                if (!in_array($tagId, $currentTags)) {
                    $stmt = $pdo->prepare("INSERT INTO article_tags (article_id, tag_id) VALUES (?, ?)");
                    $stmt->execute([$id, $tagId]);
                    
                    $stmt = $pdo->prepare("UPDATE tags SET article_count = article_count + 1 WHERE id = ?");
                    $stmt->execute([$tagId]);
                }
            }
        }
        
        $message = 'Artikel berhasil diperbarui!';
        $messageType = 'success';
    } else {
        $message = 'Gagal memperbarui artikel.';
        $messageType = 'error';
    }
}

$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
$tags = $pdo->query("SELECT * FROM tags ORDER BY name")->fetchAll();

$pageTitle = 'Edit Artikel';
include 'includes/header.php';
?>

<div class="admin-wrapper">
    <div class="admin-sidebar">
        <div class="sidebar-header">
            <i class="fas fa-server sidebar-logo"></i>
            <h4>MikroTik Admin</h4>
        </div>
        <nav class="sidebar-menu">
            <a href="index.php" class="sidebar-menu-item">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            <a href="articles.php" class="sidebar-menu-item active">
                <i class="fas fa-file-alt"></i> Artikel
            </a>
            <a href="categories.php" class="sidebar-menu-item">
                <i class="fas fa-th-large"></i> Kategori
            </a>
            <a href="tags.php" class="sidebar-menu-item">
                <i class="fas fa-tags"></i> Tags
            </a>
            <a href="settings.php" class="sidebar-menu-item">
                <i class="fas fa-cog"></i> Settings
            </a>
            <a href="<?php echo SITE_URL; ?>/" class="sidebar-menu-item" target="_blank">
                <i class="fas fa-external-link-alt"></i> View Site
            </a>
            <a href="logout.php" class="sidebar-menu-item sidebar-logout">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </nav>
    </div>
    
    <div class="admin-content">
        <div class="admin-header">
            <div class="header-left">
                <button class="btn btn-toggle-sidebar" id="toggleSidebar">
                    <i class="fas fa-bars"></i>
                </button>
                <h2 class="header-title">Edit Artikel</h2>
            </div>
            <div class="header-right">
                <a href="articles.php" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Kembali
                </a>
                <a href="<?php echo SITE_URL; ?>/article.php?slug=<?php echo htmlspecialchars($article['slug']); ?>" target="_blank" class="btn btn-outline-primary">
                    <i class="fas fa-eye me-2"></i>Lihat Artikel
                </a>
            </div>
        </div>
        
        <?php if ($message): ?>
        <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show">
            <?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
        
        <div class="card">
            <div class="card-body">
                <form method="POST" action="" enctype="multipart/form-data" id="articleForm">
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="form-group mb-3">
                                <label class="form-label">Judul *</label>
                                <input type="text" class="form-control" name="title" required value="<?php echo htmlspecialchars($article['title']); ?>">
                            </div>
                            
                            <div class="form-group mb-3">
                                <label class="form-label">Excerpt</label>
                                <textarea class="form-control" name="excerpt" rows="2"><?php echo htmlspecialchars($article['excerpt']); ?></textarea>
                            </div>
                            
                            <div class="form-group mb-3">
                                <label class="form-label">Konten *</label>
                                <textarea class="form-control" name="content" id="contentEditor" rows="20" required><?php echo htmlspecialchars($article['content']); ?></textarea>
                            </div>
                        </div>
                        
                        <div class="col-lg-4">
                            <div class="form-group mb-3">
                                <label class="form-label">Kategori</label>
                                <select class="form-select" name="category_id">
                                    <option value="">Pilih Kategori</option>
                                    <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>" <?php echo $article['category_id'] == $cat['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($cat['name']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="form-group mb-3">
                                <label class="form-label">Tags</label>
                                <select class="form-select" name="tags[]" multiple size="5">
                                    <?php foreach ($tags as $tag): ?>
                                    <option value="<?php echo $tag['id']; ?>" <?php echo in_array($tag['id'], $article['tags'] ?? []) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($tag['name']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="text-muted">Tahan Ctrl/Cmd untuk multi-select</small>
                            </div>
                            
                            <div class="form-group mb-3">
                                <label class="form-label">Status</label>
                                <select class="form-select" name="status">
                                    <option value="draft" <?php echo $article['status'] == 'draft' ? 'selected' : ''; ?>>Draft</option>
                                    <option value="published" <?php echo $article['status'] == 'published' ? 'selected' : ''; ?>>Published</option>
                                    <option value="archived" <?php echo $article['status'] == 'archived' ? 'selected' : ''; ?>>Archived</option>
                                </select>
                            </div>
                            
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" <?php echo $article['is_featured'] ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="is_featured">
                                    Featured Article
                                </label>
                            </div>
                            
                            <div class="form-group mb-3">
                                <label class="form-label">Featured Image</label>
                                <input type="file" class="form-control" name="featured_image" accept="image/*">
                                <?php if ($article['featured_image']): ?>
                                <img src="<?php echo SITE_URL; ?>/assets/img/articles/<?php echo htmlspecialchars($article['featured_image']); ?>" class="img-thumbnail mt-2" style="max-width: 200px;">
                                <?php endif; ?>
                            </div>
                            
                            <hr>
                            
                            <h6>SEO Settings</h6>
                            
                            <div class="form-group mb-3">
                                <label class="form-label">Meta Title</label>
                                <input type="text" class="form-control" name="meta_title" value="<?php echo htmlspecialchars($article['meta_title'] ?? ''); ?>">
                            </div>
                            
                            <div class="form-group mb-3">
                                <label class="form-label">Meta Description</label>
                                <textarea class="form-control" name="meta_description" rows="3"><?php echo htmlspecialchars($article['meta_description'] ?? ''); ?></textarea>
                            </div>
                            
                            <div class="form-group mb-3">
                                <label class="form-label">Meta Keywords</label>
                                <input type="text" class="form-control" name="meta_keywords" value="<?php echo htmlspecialchars($article['meta_keywords'] ?? ''); ?>">
                            </div>
                            
                            <div class="card bg-light mt-3">
                                <div class="card-body">
                                    <h6 class="card-title mb-3">Informasi Artikel</h6>
                                    <div class="row">
                                        <div class="col-6 mb-2">
                                            <small class="text-muted">Dibuat:</small><br>
                                            <strong><?php echo formatDateTime($article['created_at']); ?></strong>
                                        </div>
                                        <div class="col-6 mb-2">
                                            <small class="text-muted">Dilihat:</small><br>
                                            <strong><?php echo number_format($article['views']); ?></strong>
                                        </div>
                                        <div class="col-6 mb-2">
                                            <small class="text-muted">Waktu Baca:</small><br>
                                            <strong><?php echo $article['read_time']; ?> menit</strong>
                                        </div>
                                        <div class="col-6 mb-2">
                                            <small class="text-muted">Slug:</small><br>
                                            <strong><?php echo htmlspecialchars($article['slug']); ?></strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <hr>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Update Artikel
                                </button>
                                <a href="?delete=<?php echo $article['id']; ?>" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus artikel ini? Tindakan ini tidak bisa dibatalkan!')">
                                    <i class="fas fa-trash me-2"></i>Hapus Artikel
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
