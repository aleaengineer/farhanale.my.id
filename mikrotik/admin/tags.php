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
        $name = sanitize($_POST['name']);
        $slug = generateSlug($name);
        
        $stmt = $pdo->prepare("INSERT INTO tags (name, slug) VALUES (?, ?)");
        if ($stmt->execute([$name, $slug])) {
            $message = 'Tag berhasil ditambahkan!';
            $messageType = 'success';
        } else {
            $message = 'Gagal menambahkan tag.';
            $messageType = 'error';
        }
    }
    
    if (isset($_POST['action']) && $_POST['action'] === 'update') {
        $id = (int)$_POST['id'];
        $name = sanitize($_POST['name']);
        $slug = generateSlug($name);
        
        $stmt = $pdo->prepare("UPDATE tags SET name = ?, slug = ? WHERE id = ?");
        if ($stmt->execute([$name, $slug, $id])) {
            $message = 'Tag berhasil diperbarui!';
            $messageType = 'success';
        } else {
            $message = 'Gagal memperbarui tag.';
            $messageType = 'error';
        }
    }
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    $stmt = $pdo->prepare("UPDATE tags SET article_count = article_count - (SELECT COUNT(*) FROM article_tags WHERE tag_id = ?) WHERE id = ?");
    $stmt->execute([$id, $id]);
    
    $stmt = $pdo->prepare("DELETE FROM article_tags WHERE tag_id = ?");
    $stmt->execute([$id]);
    
    $stmt = $pdo->prepare("DELETE FROM tags WHERE id = ?");
    $stmt->execute([$id]);
    
    redirect('tags.php');
}

$tags = $pdo->query("SELECT * FROM tags ORDER BY article_count DESC, name")->fetchAll();

$editId = isset($_GET['edit']) ? (int)$_GET['edit'] : null;
$editData = null;
if ($editId) {
    $editStmt = $pdo->prepare("SELECT * FROM tags WHERE id = ?");
    $editStmt->execute([$editId]);
    $editData = $editStmt->fetch();
}

$pageTitle = 'Manajemen Tags';
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
            <a href="articles.php" class="sidebar-menu-item">
                <i class="fas fa-file-alt"></i> Artikel
            </a>
            <a href="categories.php" class="sidebar-menu-item">
                <i class="fas fa-th-large"></i> Kategori
            </a>
            <a href="tags.php" class="sidebar-menu-item active">
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
                <h2 class="header-title">Manajemen Tags</h2>
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
                <a href="?add=1" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tagModal">
                    <i class="fas fa-plus me-2"></i>Tambah Tag
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
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nama Tag</th>
                                <th>Slug</th>
                                <th>Artikel</th>
                                <th width="150">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tags as $tag): ?>
                            <tr>
                                <td>
                                    <span class="badge bg-light text-dark" style="font-size: 0.95rem;">
                                        <i class="fas fa-tag me-1"></i><?php echo htmlspecialchars($tag['name']); ?>
                                    </span>
                                </td>
                                <td><code><?php echo htmlspecialchars($tag['slug']); ?></code></td>
                                <td>
                                    <span class="badge bg-primary"><?php echo number_format($tag['article_count']); ?> artikel</span>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="?edit=<?php echo $tag['id']; ?>" class="btn btn-sm btn-outline-secondary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="?delete=<?php echo $tag['id']; ?>" class="btn btn-sm btn-outline-danger" title="Delete" onclick="return confirm('Hapus tag ini?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="tagModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo $editId ? 'Edit Tag' : 'Tambah Tag Baru'; ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="" id="tagForm">
                    <input type="hidden" name="action" value="<?php echo $editId ? 'update' : 'add'; ?>">
                    <?php if ($editId): ?>
                    <input type="hidden" name="id" value="<?php echo $editId; ?>">
                    <?php endif; ?>
                    
                    <div class="form-group mb-3">
                        <label class="form-label">Nama Tag *</label>
                        <input type="text" class="form-control" name="name" required value="<?php echo $editData ? htmlspecialchars($editData['name']) : ''; ?>">
                    </div>
                    
                    <div class="form-group mb-3">
                        <label class="form-label">Slug</label>
                        <input type="text" class="form-control" name="slug" value="<?php echo $editData ? htmlspecialchars($editData['slug']) : ''; ?>" readonly>
                        <small class="text-muted">Slug akan di-generate otomatis dari nama tag</small>
                    </div>
                    
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i><?php echo $editId ? 'Update Tag' : 'Simpan Tag'; ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$('input[name="name"]').on('keyup', function() {
    const slug = $(this).val().toLowerCase().replace(/[^a-z0-9]+/g, '-');
    $('input[name="slug"]').val(slug);
});
</script>

<?php if ($editId): ?>
<script>
var myModal = new bootstrap.Modal(document.getElementById('tagModal'));
myModal.show();
</script>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>