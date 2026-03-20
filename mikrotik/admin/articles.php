<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'add') {
        $title = sanitize($_POST['title']);
        $slug = generateSlug($title);
        $content = $_POST['content'];
        $excerpt = sanitize($_POST['excerpt']);
        $category_id = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null;
        $author_id = $_SESSION['user_id'];
        $status = sanitize($_POST['status']);
        $is_featured = isset($_POST['is_featured']) ? 1 : 0;
        $meta_title = sanitize($_POST['meta_title']);
        $meta_description = sanitize($_POST['meta_description']);
        $meta_keywords = sanitize($_POST['meta_keywords']);
        
        $featured_image = '';
        if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === 0) {
            $upload = uploadImage($_FILES['featured_image'], '../assets/img/articles/');
            if ($upload['success']) {
                $featured_image = $upload['filename'];
            }
        }
        
        $read_time = readingTime($content);
        $published_at = ($status === 'published') ? date('Y-m-d H:i:s') : null;
        
        $stmt = $pdo->prepare("INSERT INTO articles (title, slug, content, excerpt, featured_image, category_id, author_id, status, is_featured, read_time, meta_title, meta_description, meta_keywords, published_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$title, $slug, $content, $excerpt, $featured_image, $category_id, $author_id, $status, $is_featured, $read_time, $meta_title, $meta_description, $meta_keywords, $published_at])) {
            
            if (!empty($_POST['tags'])) {
                $articleId = $pdo->lastInsertId();
                foreach ($_POST['tags'] as $tagId) {
                    $stmt = $pdo->prepare("INSERT INTO article_tags (article_id, tag_id) VALUES (?, ?)");
                    $stmt->execute([$articleId, $tagId]);
                    
                    $stmt = $pdo->prepare("UPDATE tags SET article_count = article_count + 1 WHERE id = ?");
                    $stmt->execute([$tagId]);
                }
            }
            
            if ($category_id) {
                $stmt = $pdo->prepare("UPDATE categories SET article_count = article_count + 1 WHERE id = ?");
                $stmt->execute([$category_id]);
            }
            
            $message = 'Artikel berhasil ditambahkan!';
            $messageType = 'success';
        } else {
            $message = 'Gagal menambahkan artikel.';
            $messageType = 'error';
        }
    }
    
    if (isset($_POST['action']) && $_POST['action'] === 'update') {
        $id = (int)$_POST['id'];
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
        
        $current = $pdo->prepare("SELECT * FROM articles WHERE id = ?");
        $current->execute([$id]);
        $current = $current->fetch();
        
        $featured_image = $current['featured_image'];
        if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === 0) {
            $upload = uploadImage($_FILES['featured_image'], '../assets/img/articles/');
            if ($upload['success']) {
                if ($current['featured_image'] && file_exists('../assets/img/articles/' . $current['featured_image'])) {
                    unlink('../assets/img/articles/' . $current['featured_image']);
                }
                $featured_image = $upload['filename'];
            }
        }
        
        $read_time = readingTime($content);
        $published_at = ($status === 'published' && !$current['published_at']) ? date('Y-m-d H:i:s') : $current['published_at'];
        
        $oldCategoryId = $current['category_id'];
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
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
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

if (isset($_GET['action']) && $_GET['action'] === 'publish') {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare("UPDATE articles SET status = 'published', published_at = NOW() WHERE id = ?");
    $stmt->execute([$id]);
    redirect('articles.php');
}

if (isset($_GET['action']) && $_GET['action'] === 'unpublish') {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare("UPDATE articles SET status = 'draft', published_at = NULL WHERE id = ?");
    $stmt->execute([$id]);
    redirect('articles.php');
}

if (isset($_GET['action']) && $_GET['action'] === 'feature') {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare("UPDATE articles SET is_featured = 1 WHERE id = ?");
    $stmt->execute([$id]);
    redirect('articles.php');
}

if (isset($_GET['action']) && $_GET['action'] === 'unfeature') {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare("UPDATE articles SET is_featured = 0 WHERE id = ?");
    $stmt->execute([$id]);
    redirect('articles.php');
}

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$statusFilter = isset($_GET['status']) ? sanitize($_GET['status']) : '';
$categoryFilter = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$sortBy = isset($_GET['sort']) ? sanitize($_GET['sort']) : 'created_at';
$searchQuery = isset($_GET['search']) ? sanitize($_GET['search']) : '';

$where = ["1=1"];
$params = [];

if ($statusFilter) {
    $where[] = "status = ?";
    $params[] = $statusFilter;
}

if ($categoryFilter) {
    $where[] = "category_id = ?";
    $params[] = $categoryFilter;
}

if ($searchQuery) {
    $where[] = "(title LIKE ? OR content LIKE ? OR excerpt LIKE ?)";
    $params[] = "%$searchQuery%";
    $params[] = "%$searchQuery%";
    $params[] = "%$searchQuery%";
}

$whereClause = implode(' AND ', $where);

$totalArticles = $pdo->prepare("SELECT COUNT(*) FROM articles WHERE $whereClause");
$totalArticles->execute($params);
$totalArticles = $totalArticles->fetchColumn();
$totalPages = ceil($totalArticles / $limit);

switch ($sortBy) {
    case 'views':
        $orderClause = 'views DESC';
        break;
    case 'title':
        $orderClause = 'title ASC';
        break;
    case 'published':
        $orderClause = 'published_at DESC';
        break;
    default:
        $orderClause = 'created_at DESC';
        break;
}

$articlesQuery = "SELECT a.*, c.name as category_name, u.username as author_name 
    FROM articles a 
    LEFT JOIN categories c ON a.category_id = c.id 
    LEFT JOIN users u ON a.author_id = u.id 
    WHERE $whereClause 
    ORDER BY $orderClause 
    LIMIT :limit OFFSET :offset";

$articles = $pdo->prepare($articlesQuery);
foreach ($params as $i => $param) {
    $articles->bindValue($i + 1, $param);
}
$articles->bindValue(':limit', $limit, PDO::PARAM_INT);
$articles->bindValue(':offset', $offset, PDO::PARAM_INT);
$articles->execute();
$articles = $articles->fetchAll();

$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
$tags = $pdo->query("SELECT * FROM tags ORDER BY name")->fetchAll();

$pageTitle = 'Manajemen Artikel';
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
                <h2 class="header-title">Manajemen Artikel</h2>
            </div>
            <div class="header-right">
                <div class="user-info">
                    <img src="<?php echo getAvatar($_SESSION['full_name'] ?? $_SESSION['username']); ?>" alt="User" class="user-avatar">
                    <div class="user-details">
                        <span class="user-name"><?php echo htmlspecialchars($_SESSION['full_name'] ?? $_SESSION['username']); ?></span>
                        <span class="user-role"><?php echo htmlspecialchars(ucfirst($_SESSION['role'])); ?></span>
                    </div>
                </div>
                <a href="logout.php" class="btn btn-logout">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#articleModal">
                    <i class="fas fa-plus me-2"></i>Tambah Artikel
                </button>
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
                <div class="filters-row">
                    <div class="search-box">
                        <input type="text" class="form-control" id="searchInput" placeholder="Cari artikel..." value="<?php echo htmlspecialchars($searchQuery); ?>">
                    </div>
                    
                    <div class="filter-selects">
                        <select class="form-select" id="statusFilter">
                            <option value="">Semua Status</option>
                            <option value="published" <?php echo $statusFilter === 'published' ? 'selected' : ''; ?>>Published</option>
                            <option value="draft" <?php echo $statusFilter === 'draft' ? 'selected' : ''; ?>>Draft</option>
                            <option value="archived" <?php echo $statusFilter === 'archived' ? 'selected' : ''; ?>>Archived</option>
                        </select>
                        
                        <select class="form-select" id="categoryFilter">
                            <option value="0">Semua Kategori</option>
                            <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>" <?php echo $categoryFilter == $cat['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        
                        <select class="form-select" id="sortBy">
                            <option value="created_at" <?php echo $sortBy === 'created_at' ? 'selected' : ''; ?>>Terbaru</option>
                            <option value="published" <?php echo $sortBy === 'published' ? 'selected' : ''; ?>>Tanggal Publish</option>
                            <option value="views" <?php echo $sortBy === 'views' ? 'selected' : ''; ?>>Terpopuler</option>
                            <option value="title" <?php echo $sortBy === 'title' ? 'selected' : ''; ?>>Judul A-Z</option>
                        </select>
                    </div>
                </div>
                
                <table class="table table-hover">
                        <thead>
                            <tr>
                                <th width="80">Image</th>
                                <th>Title</th>
                                <th>Kategori</th>
                                <th>Author</th>
                                <th>Status</th>
                                <th>Views</th>
                                <th>Date</th>
                                <th width="200">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($articles as $article): ?>
                            <tr>
                                <td>
                                    <?php if ($article['featured_image']): ?>
                                    <img src="<?php echo SITE_URL; ?>/assets/img/articles/<?php echo htmlspecialchars($article['featured_image']); ?>" alt="<?php echo htmlspecialchars($article['title']); ?>" class="article-thumb">
                                    <?php else: ?>
                                    <div class="article-placeholder">
                                        <i class="fas fa-image"></i>
                                    </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="article-title">
                                        <?php echo htmlspecialchars($article['title']); ?>
                                        <?php if ($article['is_featured']): ?>
                                        <i class="fas fa-star text-warning ms-1" title="Featured"></i>
                                        <?php endif; ?>
                                    </div>
                                    <small class="text-muted"><?php echo htmlspecialchars(substr(strip_tags($article['excerpt']), 0, 60)); ?>...</small>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">
                                        <?php echo htmlspecialchars($article['category_name'] ?? 'Uncategorized'); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($article['author_name']); ?></td>
                                <td>
                                    <?php if ($article['status'] == 'published'): ?>
                                    <span class="badge bg-success">Published</span>
                                    <?php elseif ($article['status'] == 'draft'): ?>
                                    <span class="badge bg-warning">Draft</span>
                                    <?php else: ?>
                                    <span class="badge bg-secondary">Archived</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo number_format($article['views']); ?></td>
                                <td>
                                    <small><?php echo formatDateTime($article['created_at']); ?></small>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="<?php echo SITE_URL; ?>/article.php?slug=<?php echo htmlspecialchars($article['slug']); ?>" target="_blank" class="btn btn-sm btn-outline-primary" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="edit-article.php?id=<?php echo $article['id']; ?>" class="btn btn-sm btn-outline-secondary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <?php if ($article['status'] == 'published'): ?>
                                        <a href="?action=unpublish&id=<?php echo $article['id']; ?>" class="btn btn-sm btn-outline-warning" title="Unpublish" onclick="return confirm('Unpublish artikel ini?')">
                                            <i class="fas fa-eye-slash"></i>
                                        </a>
                                        <?php else: ?>
                                        <a href="?action=publish&id=<?php echo $article['id']; ?>" class="btn btn-sm btn-outline-success" title="Publish" onclick="return confirm('Publish artikel ini?')">
                                            <i class="fas fa-check"></i>
                                        </a>
                                        <?php endif; ?>
                                        <?php if ($article['is_featured']): ?>
                                        <a href="?action=unfeature&id=<?php echo $article['id']; ?>" class="btn btn-sm btn-outline-secondary" title="Unfeature">
                                            <i class="fas fa-star"></i>
                                        </a>
                                        <?php else: ?>
                                        <a href="?action=feature&id=<?php echo $article['id']; ?>" class="btn btn-sm btn-outline-warning" title="Feature">
                                            <i class="far fa-star"></i>
                                        </a>
                                        <?php endif; ?>
                                        <a href="?delete=<?php echo $article['id']; ?>" class="btn btn-sm btn-outline-danger" title="Delete" onclick="return confirm('Hapus artikel ini?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                
                <?php if ($totalPages > 1): ?>
                <nav>
                    <ul class="pagination justify-content-center">
                        <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $page - 1; ?><?php echo $statusFilter ? '&status=' . $statusFilter : ''; ?><?php echo $categoryFilter ? '&category=' . $categoryFilter : ''; ?><?php echo $sortBy ? '&sort=' . $sortBy : ''; ?><?php echo $searchQuery ? '&search=' . urlencode($searchQuery) : ''; ?>">Previous</a>
                        </li>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?><?php echo $statusFilter ? '&status=' . $statusFilter : ''; ?><?php echo $categoryFilter ? '&category=' . $categoryFilter : ''; ?><?php echo $sortBy ? '&sort=' . $sortBy : ''; ?><?php echo $searchQuery ? '&search=' . urlencode($searchQuery) : ''; ?>"><?php echo $i; ?></a>
                        </li>
                        <?php endfor; ?>
                        
                        <?php if ($page < $totalPages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $page + 1; ?><?php echo $statusFilter ? '&status=' . $statusFilter : ''; ?><?php echo $categoryFilter ? '&category=' . $categoryFilter : ''; ?><?php echo $sortBy ? '&sort=' . $sortBy : ''; ?><?php echo $searchQuery ? '&search=' . urlencode($searchQuery) : ''; ?>">Next</a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="articleModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Artikel Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="add">
                    
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="form-group mb-3">
                                <label class="form-label">Title *</label>
                                <input type="text" class="form-control" name="title" required>
                            </div>
                            
                            <div class="form-group mb-3">
                                <label class="form-label">Excerpt</label>
                                <textarea class="form-control" name="excerpt" rows="2"></textarea>
                            </div>
                            
                            <div class="form-group mb-3">
                                <label class="form-label">Content *</label>
                                <textarea class="form-control" name="content" id="contentEditor" rows="15" required></textarea>
                            </div>
                        </div>
                        
                        <div class="col-lg-4">
                            <div class="form-group mb-3">
                                <label class="form-label">Kategori</label>
                                <select class="form-select" name="category_id">
                                    <option value="">Pilih Kategori</option>
                                    <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>">
                                        <?php echo htmlspecialchars($cat['name']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="form-group mb-3">
                                <label class="form-label">Tags</label>
                                <select class="form-select" name="tags[]" multiple size="5">
                                    <?php foreach ($tags as $tag): ?>
                                    <option value="<?php echo $tag['id']; ?>">
                                        <?php echo htmlspecialchars($tag['name']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="text-muted">Hold Ctrl/Cmd untuk multi-select</small>
                            </div>
                            
                            <div class="form-group mb-3">
                                <label class="form-label">Status</label>
                                <select class="form-select" name="status">
                                    <option value="draft">Draft</option>
                                    <option value="published">Published</option>
                                    <option value="archived">Archived</option>
                                </select>
                            </div>
                            
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured">
                                <label class="form-check-label" for="is_featured">
                                    Featured Article
                                </label>
                            </div>
                            
                            <div class="form-group mb-3">
                                <label class="form-label">Featured Image</label>
                                <input type="file" class="form-control" name="featured_image" accept="image/*">
                            </div>
                            
                            <hr>
                            
                            <h6>SEO Settings</h6>
                            
                            <div class="form-group mb-3">
                                <label class="form-label">Meta Title</label>
                                <input type="text" class="form-control" name="meta_title">
                            </div>
                            
                            <div class="form-group mb-3">
                                <label class="form-label">Meta Description</label>
                                <textarea class="form-control" name="meta_description" rows="3"></textarea>
                            </div>
                            
                            <div class="form-group mb-3">
                                <label class="form-label">Meta Keywords</label>
                                <input type="text" class="form-control" name="meta_keywords">
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Simpan Artikel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('searchInput').addEventListener('keyup', function(e) {
    if (e.key === 'Enter') {
        window.location.href = '?search=' + encodeURIComponent(this.value);
    }
});

document.getElementById('statusFilter').addEventListener('change', function() {
    window.location.href = '?status=' + this.value;
});

document.getElementById('categoryFilter').addEventListener('change', function() {
    window.location.href = '?category=' + this.value;
});

document.getElementById('sortBy').addEventListener('change', function() {
    window.location.href = '?sort=' + this.value;
});
</script>

<?php include 'includes/footer.php'; ?>