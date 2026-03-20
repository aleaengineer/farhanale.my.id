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
        $description = sanitize($_POST['description']);
        $icon = sanitize($_POST['icon']);
        $color = sanitize($_POST['color']);
        
        $stmt = $pdo->prepare("INSERT INTO categories (name, slug, description, icon, color) VALUES (?, ?, ?, ?, ?)");
        if ($stmt->execute([$name, $slug, $description, $icon, $color])) {
            $message = 'Kategori berhasil ditambahkan!';
            $messageType = 'success';
        } else {
            $message = 'Gagal menambahkan kategori.';
            $messageType = 'error';
        }
    }
    
    if (isset($_POST['action']) && $_POST['action'] === 'update') {
        $id = (int)$_POST['id'];
        $name = sanitize($_POST['name']);
        $slug = generateSlug($name);
        $description = sanitize($_POST['description']);
        $icon = sanitize($_POST['icon']);
        $color = sanitize($_POST['color']);
        
        $stmt = $pdo->prepare("UPDATE categories SET name = ?, slug = ?, description = ?, icon = ?, color = ? WHERE id = ?");
        if ($stmt->execute([$name, $slug, $description, $icon, $color, $id])) {
            $message = 'Kategori berhasil diperbarui!';
            $messageType = 'success';
        } else {
            $message = 'Gagal memperbarui kategori.';
            $messageType = 'error';
        }
    }
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM articles WHERE category_id = ?");
    $stmt->execute([$id]);
    $articleCount = $stmt->fetchColumn();
    
    if ($articleCount > 0) {
        $message = 'Tidak dapat menghapus kategori ini karena masih memiliki artikel!';
        $messageType = 'error';
    } else {
        $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        redirect('categories.php');
    }
}

$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();

$editId = isset($_GET['edit']) ? (int)$_GET['edit'] : null;
$editData = null;
if ($editId) {
    $editStmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
    $editStmt->execute([$editId]);
    $editData = $editStmt->fetch();
}

$pageTitle = 'Manajemen Kategori';
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
            <a href="categories.php" class="sidebar-menu-item active">
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
                <h2 class="header-title">Manajemen Kategori</h2>
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
                <a href="?add=1" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#categoryModal">
                    <i class="fas fa-plus me-2"></i>Tambah Kategori
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
                                <th width="80">Icon</th>
                                <th>Nama</th>
                                <th>Slug</th>
                                <th>Warna</th>
                                <th>Artikel</th>
                                <th width="150">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categories as $category): ?>
                            <tr>
                                <td>
                                    <div class="category-icon-small" style="background: <?php echo htmlspecialchars($category['color']); ?>;">
                                        <i class="fas <?php echo htmlspecialchars($category['icon']); ?>"></i>
                                    </div>
                                </td>
                                <td>
                                    <strong><?php echo htmlspecialchars($category['name']); ?></strong>
                                    <?php if ($category['description']): ?>
                                    <br><small class="text-muted"><?php echo htmlspecialchars(substr($category['description'], 0, 50)); ?>...</small>
                                    <?php endif; ?>
                                </td>
                                <td><code><?php echo htmlspecialchars($category['slug']); ?></code></td>
                                <td>
                                    <span class="badge" style="background: <?php echo htmlspecialchars($category['color']); ?>; color: white;">
                                        <?php echo htmlspecialchars($category['color']); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-primary"><?php echo number_format($category['article_count']); ?></span>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="?edit=<?php echo $category['id']; ?>" class="btn btn-sm btn-outline-secondary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="?delete=<?php echo $category['id']; ?>" class="btn btn-sm btn-outline-danger" title="Delete" onclick="return confirm('Hapus kategori ini?')" <?php echo $category['article_count'] > 0 ? 'disabled' : ''; ?>>
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

<div class="modal fade" id="categoryModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo $editId ? 'Edit Kategori' : 'Tambah Kategori Baru'; ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="" id="categoryForm">
                    <input type="hidden" name="action" value="<?php echo $editId ? 'update' : 'add'; ?>">
                    <?php if ($editId): ?>
                    <input type="hidden" name="id" value="<?php echo $editId; ?>">
                    <?php endif; ?>
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group mb-3">
                                <label class="form-label">Nama Kategori *</label>
                                <input type="text" class="form-control" name="name" required value="<?php echo $editData ? htmlspecialchars($editData['name']) : ''; ?>">
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label class="form-label">Icon (FontAwesome)</label>
                                <input type="text" class="form-control" name="icon" value="<?php echo $editData ? htmlspecialchars($editData['icon']) : 'fa-folder'; ?>" placeholder="fa-folder">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label class="form-label">Slug</label>
                        <input type="text" class="form-control" name="slug" value="<?php echo $editData ? htmlspecialchars($editData['slug']) : ''; ?>" readonly>
                        <small class="text-muted">Slug akan di-generate otomatis dari nama kategori</small>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea class="form-control" name="description" rows="3"><?php echo $editData ? htmlspecialchars($editData['description']) : ''; ?></textarea>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label class="form-label">Warna Kategori</label>
                        <input type="color" class="form-control form-control-color" name="color" value="<?php echo $editData ? htmlspecialchars($editData['color']) : '#2563EB'; ?>">
                    </div>
                    
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i><?php echo $editId ? 'Update Kategori' : 'Simpan Kategori'; ?>
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
var myModal = new bootstrap.Modal(document.getElementById('categoryModal'));
myModal.show();
</script>
<?php endif; ?>

<style>
.category-icon-small {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 1.2rem;
}
</style>

<?php include 'includes/footer.php'; ?>