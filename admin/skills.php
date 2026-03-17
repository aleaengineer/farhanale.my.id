<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'add') {
        $name = sanitize($_POST['name']);
        $category = sanitize($_POST['category']);
        $percentage = (int)$_POST['percentage'];
        $icon = sanitize($_POST['icon']);
        
        $stmt = $pdo->prepare("INSERT INTO skills (name, category, percentage, icon) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$name, $category, $percentage, $icon])) {
            $message = 'Skill added successfully!';
        } else {
            $message = 'Failed to add skill.';
        }
    }
    
    if (isset($_POST['action']) && $_POST['action'] === 'update') {
        $id = $_POST['id'];
        $name = sanitize($_POST['name']);
        $category = sanitize($_POST['category']);
        $percentage = (int)$_POST['percentage'];
        $icon = sanitize($_POST['icon']);
        
        $stmt = $pdo->prepare("UPDATE skills SET name = ?, category = ?, percentage = ?, icon = ? WHERE id = ?");
        if ($stmt->execute([$name, $category, $percentage, $icon, $id])) {
            $message = 'Skill updated successfully!';
        } else {
            $message = 'Failed to update skill.';
        }
    }
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM skills WHERE id = ?");
    $stmt->execute([$id]);
    redirect('skills.php');
}

$skills = $pdo->query("SELECT * FROM skills ORDER BY category, percentage DESC")->fetchAll();
$editId = isset($_GET['edit']) ? $_GET['edit'] : null;
$editData = $editId ? $pdo->prepare("SELECT * FROM skills WHERE id = ?") : null;
if ($editId) { $editData->execute([$editId]); $editData = $editData->fetch(); }

$fontAwesomeIcons = [
    'fa-server', 'fa-network-wired', 'fa-code', 'fa-globe', 'fa-cogs',
    'fa-linux', 'fa-shield-halved', 'fa-infinity', 'fa-cloud', 'fa-docker',
    'fa-database', 'fa-brain', 'fa-microchip', 'fa-wifi', 'fa-lock'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Skill Management - Admin Panel</title>
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
                <a href="certificates.php" class="admin-menu-item">
                    <i class="fas fa-certificate"></i> Certificates
                </a>
                <a href="skills.php" class="admin-menu-item active">
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
                    <h3 class="d-inline-block ms-3">Skill Management</h3>
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
                <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#skillModal">
                    <i class="fas fa-plus me-2"></i>Add New Skill
                </button>
                
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Icon</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Percentage</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($skills as $skill): ?>
                            <tr>
                                <td><i class="fas <?php echo htmlspecialchars($skill['icon']); ?>" style="font-size: 1.5rem; color: var(--primary);"></i></td>
                                <td><?php echo htmlspecialchars($skill['name']); ?></td>
                                <td><span class="badge bg-primary"><?php echo htmlspecialchars($skill['category']); ?></span></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="progress" style="width: 100px; height: 10px; margin-right: 10px;">
                                            <div class="progress-bar" role="progressbar" style="width: <?php echo $skill['percentage']; ?>%; background: var(--gradient);" aria-valuenow="<?php echo $skill['percentage']; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <span><?php echo $skill['percentage']; ?>%</span>
                                    </div>
                                </td>
                                <td>
                                    <a href="?edit=<?php echo $skill['id']; ?>" class="btn btn-action btn-edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="?delete=<?php echo $skill['id']; ?>" class="btn btn-action btn-delete">
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
    
    <div class="modal fade" id="skillModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><?php echo $editId ? 'Edit' : 'Add New'; ?> Skill</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="<?php echo $editId ? 'update' : 'add'; ?>">
                        <?php if ($editId): ?>
                        <input type="hidden" name="id" value="<?php echo $editId; ?>">
                        <?php endif; ?>
                        
                        <div class="mb-3">
                            <label class="form-label">Skill Name *</label>
                            <input type="text" class="form-control" name="name" required value="<?php echo $editData ? htmlspecialchars($editData['name']) : ''; ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Category *</label>
                            <input type="text" class="form-control" name="category" required value="<?php echo $editData ? htmlspecialchars($editData['category']) : ''; ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Percentage *</label>
                            <input type="number" class="form-control" name="percentage" min="0" max="100" required value="<?php echo $editData ? htmlspecialchars($editData['percentage']) : ''; ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Icon *</label>
                            <select class="form-select" name="icon" required>
                                <option value="">Select Icon</option>
                                <?php foreach ($fontAwesomeIcons as $icon): ?>
                                <option value="<?php echo $icon; ?>" <?php echo ($editData && $editData['icon'] == $icon) ? 'selected' : ''; ?>>
                                    <?php echo $icon; ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
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
    var myModal = new bootstrap.Modal(document.getElementById('skillModal'));
    myModal.show();
    <?php endif; ?>
    </script>
</body>
</html>
