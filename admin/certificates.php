<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'add') {
        $title = sanitize($_POST['title']);
        $issuer = sanitize($_POST['issuer']);
        $issue_date = $_POST['issue_date'];
        $image = '';
        
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $upload = uploadImage($_FILES['image'], '../assets/img/certificates/');
            if ($upload['success']) {
                $image = $upload['filename'];
            }
        }
        
        $stmt = $pdo->prepare("INSERT INTO certificates (title, issuer, issue_date, image) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$title, $issuer, $issue_date, $image])) {
            $message = 'Certificate added successfully!';
        } else {
            $message = 'Failed to add certificate.';
        }
    }
    
    if (isset($_POST['action']) && $_POST['action'] === 'update') {
        $id = $_POST['id'];
        $title = sanitize($_POST['title']);
        $issuer = sanitize($_POST['issuer']);
        $issue_date = $_POST['issue_date'];
        
        $current = $pdo->prepare("SELECT image FROM certificates WHERE id = ?");
        $current->execute([$id]);
        $current = $current->fetch();
        
        $image = $current['image'];
        
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $upload = uploadImage($_FILES['image'], '../assets/img/certificates/');
            if ($upload['success']) {
                if ($current['image'] && file_exists('../assets/img/certificates/' . $current['image'])) {
                    unlink('../assets/img/certificates/' . $current['image']);
                }
                $image = $upload['filename'];
            }
        }
        
        $stmt = $pdo->prepare("UPDATE certificates SET title = ?, issuer = ?, issue_date = ?, image = ? WHERE id = ?");
        if ($stmt->execute([$title, $issuer, $issue_date, $image, $id])) {
            $message = 'Certificate updated successfully!';
        } else {
            $message = 'Failed to update certificate.';
        }
    }
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $current = $pdo->prepare("SELECT image FROM certificates WHERE id = ?");
    $current->execute([$id]);
    $current = $current->fetch();
    
    if ($current && $current['image'] && file_exists('../assets/img/certificates/' . $current['image'])) {
        unlink('../assets/img/certificates/' . $current['image']);
    }
    
    $stmt = $pdo->prepare("DELETE FROM certificates WHERE id = ?");
    $stmt->execute([$id]);
    redirect('certificates.php');
}

$certificates = $pdo->query("SELECT * FROM certificates ORDER BY issue_date DESC")->fetchAll();
$editId = isset($_GET['edit']) ? $_GET['edit'] : null;
$editData = $editId ? $pdo->prepare("SELECT * FROM certificates WHERE id = ?") : null;
if ($editId) { $editData->execute([$editId]); $editData = $editData->fetch(); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate Management - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="../assets/css/admin.css" rel="stylesheet">
</head>
<body>
    <div class="admin-layout">
        <div class="admin-sidebar">
            <div class="admin-sidebar-header">
                <h4>Admin Panel</h4>
            </div>
            <nav class="admin-sidebar-menu">
                <a href="index.php" class="admin-menu-item">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a href="portfolio.php" class="admin-menu-item">
                    <i class="fas fa-briefcase"></i> Portfolio
                </a>
                <a href="blogs.php" class="admin-menu-item">
                    <i class="fas fa-blog"></i> Blogs
                </a>
                <a href="certificates.php" class="admin-menu-item active">
                    <i class="fas fa-certificate"></i> Certificates
                </a>
                <a href="skills.php" class="admin-menu-item">
                    <i class="fas fa-cogs"></i> Skills
                </a>
                <a href="settings.php" class="admin-menu-item">
                    <i class="fas fa-cog"></i> Settings
                </a>
                <a href="logout.php" class="admin-menu-item">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </nav>
        </div>
        
        <div class="admin-content">
            <div class="admin-topbar">
                <div>
                    <button class="btn btn-light" id="sidebarToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h3 class="d-inline-block ms-3">Certificate Management</h3>
                </div>
                <div>
                    <a href="index.php" class="btn btn-gradient">
                        <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                    </a>
                </div>
            </div>
            
            <?php if ($message): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
            <?php endif; ?>
            
            <div class="admin-card">
                <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#certModal">
                    <i class="fas fa-plus me-2"></i>Add New Certificate
                </button>
                
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Issuer</th>
                                <th>Issue Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($certificates as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['title']); ?></td>
                                <td><?php echo htmlspecialchars($item['issuer']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($item['issue_date'])); ?></td>
                                <td>
                                    <a href="?edit=<?php echo $item['id']; ?>" class="btn btn-action btn-edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="?delete=<?php echo $item['id']; ?>" class="btn btn-action btn-delete">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="certModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><?php echo $editId ? 'Edit' : 'Add New'; ?> Certificate</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="<?php echo $editId ? 'update' : 'add'; ?>">
                        <?php if ($editId): ?>
                        <input type="hidden" name="id" value="<?php echo $editId; ?>">
                        <?php endif; ?>
                        
                        <div class="mb-3">
                            <label class="form-label">Title *</label>
                            <input type="text" class="form-control" name="title" required value="<?php echo $editData ? htmlspecialchars($editData['title']) : ''; ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Issuer *</label>
                            <input type="text" class="form-control" name="issuer" required value="<?php echo $editData ? htmlspecialchars($editData['issuer']) : ''; ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Issue Date *</label>
                            <input type="date" class="form-control" name="issue_date" required value="<?php echo $editData ? htmlspecialchars($editData['issue_date']) : ''; ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Image</label>
                            <input type="file" class="form-control image-upload" name="image" accept="image/*">
                            <?php if ($editData && $editData['image']): ?>
                            <img src="../assets/img/certificates/<?php echo htmlspecialchars($editData['image']); ?>" class="preview-image">
                            <?php endif; ?>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-save me-2"></i><?php echo $editId ? 'Update' : 'Save'; ?>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../assets/js/admin.js"></script>
    <script>
    <?php if ($editId): ?>
    var myModal = new bootstrap.Modal(document.getElementById('certModal'));
    myModal.show();
    <?php endif; ?>
    </script>
</body>
</html>
