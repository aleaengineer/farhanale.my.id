<?php
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin';
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function getSetting($key) {
    global $pdo;
    static $settings = [];
    
    if (empty($settings)) {
        $stmt = $pdo->query("SELECT setting_key, setting_value FROM settings");
        while ($row = $stmt->fetch()) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
    }
    
    return isset($settings[$key]) ? $settings[$key] : '';
}

function generateSlug($title) {
    $slug = strtolower($title);
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
    $slug = trim($slug, '-');
    return $slug;
}

function formatDate($date) {
    $indonesianMonths = [
        'January' => 'Januari',
        'February' => 'Februari',
        'March' => 'Maret',
        'April' => 'April',
        'May' => 'Mei',
        'June' => 'Juni',
        'July' => 'Juli',
        'August' => 'Agustus',
        'September' => 'September',
        'October' => 'Oktober',
        'November' => 'November',
        'December' => 'Desember'
    ];
    
    $formatted = date('d F Y', strtotime($date));
    foreach ($indonesianMonths as $en => $id) {
        $formatted = str_replace($en, $id, $formatted);
    }
    return $formatted;
}

function formatDateTime($date) {
    $indonesianMonths = [
        'January' => 'Januari',
        'February' => 'Februari',
        'March' => 'Maret',
        'April' => 'April',
        'May' => 'Mei',
        'June' => 'Juni',
        'July' => 'Juli',
        'August' => 'Agustus',
        'September' => 'September',
        'October' => 'Oktober',
        'November' => 'November',
        'December' => 'Desember'
    ];
    
    $formatted = date('d F Y H:i', strtotime($date));
    foreach ($indonesianMonths as $en => $id) {
        $formatted = str_replace($en, $id, $formatted);
    }
    return $formatted;
}

function uploadImage($file, $destination) {
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $filename = $file['name'];
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    
    if (!in_array($ext, $allowed)) {
        return ['success' => false, 'message' => 'Invalid file type. Only JPG, PNG, GIF, and WEBP allowed.'];
    }
    
    if ($file['size'] > 2097152) {
        return ['success' => false, 'message' => 'File too large. Maximum size is 2MB.'];
    }
    
    $newFilename = uniqid() . '.' . $ext;
    $target = $destination . $newFilename;
    
    if (!is_dir($destination)) {
        mkdir($destination, 0777, true);
    }
    
    if (move_uploaded_file($file['tmp_name'], $target)) {
        return ['success' => true, 'filename' => $newFilename];
    }
    
    return ['success' => false, 'message' => 'Upload failed. Please try again.'];
}

function readingTime($content) {
    $wordCount = str_word_count(strip_tags($content));
    $minutes = ceil($wordCount / 200);
    return $minutes;
}

function truncateText($text, $length = 150) {
    $text = strip_tags($text);
    if (strlen($text) > $length) {
        $text = substr($text, 0, $length) . '...';
    }
    return $text;
}

function getExcerpt($content, $length = 200) {
    $content = strip_tags($content);
    $content = preg_replace('/\s+/', ' ', $content);
    if (strlen($content) > $length) {
        $content = substr($content, 0, $length) . '...';
    }
    return $content;
}

function paginate($total, $perPage, $currentPage, $url) {
    $totalPages = ceil($total / $perPage);
    
    if ($totalPages <= 1) return '';
    
    $html = '<nav><ul class="pagination justify-content-center">';
    
    if ($currentPage > 1) {
        $html .= '<li class="page-item"><a class="page-link" href="' . $url . '?page=' . ($currentPage - 1) . '">Previous</a></li>';
    }
    
    for ($i = 1; $i <= $totalPages; $i++) {
        $active = $i == $currentPage ? 'active' : '';
        $html .= '<li class="page-item ' . $active . '"><a class="page-link" href="' . $url . '?page=' . $i . '">' . $i . '</a></li>';
    }
    
    if ($currentPage < $totalPages) {
        $html .= '<li class="page-item"><a class="page-link" href="' . $url . '?page=' . ($currentPage + 1) . '">Next</a></li>';
    }
    
    $html .= '</ul></nav>';
    
    return $html;
}

function getCategory($id) {
    global $pdo;
    static $categories = [];
    
    if (empty($categories)) {
        $stmt = $pdo->query("SELECT * FROM categories");
        while ($row = $stmt->fetch()) {
            $categories[$row['id']] = $row;
        }
    }
    
    return isset($categories[$id]) ? $categories[$id] : null;
}

function getTags($articleId) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT t.* FROM tags t
        INNER JOIN article_tags at ON t.id = at.tag_id
        WHERE at.article_id = ?
    ");
    $stmt->execute([$articleId]);
    return $stmt->fetchAll();
}

function getAuthor($id) {
    global $pdo;
    static $authors = [];
    
    if (empty($authors)) {
        $stmt = $pdo->query("SELECT id, username, full_name FROM users");
        while ($row = $stmt->fetch()) {
            $authors[$row['id']] = $row;
        }
    }
    
    return isset($authors[$id]) ? $authors[$id] : null;
}

function getRelatedArticles($articleId, $categoryId, $limit = 3) {
    global $pdo;
    $limit = (int)$limit;
    $stmt = $pdo->prepare("
        SELECT * FROM articles
        WHERE id != ? AND category_id = ? AND status = 'published'
        ORDER BY views DESC, published_at DESC
        LIMIT $limit
    ");
    $stmt->execute([$articleId, $categoryId]);
    return $stmt->fetchAll();
}

function getPopularArticles($limit = 5) {
    global $pdo;
    $limit = (int)$limit;
    $stmt = $pdo->query("
        SELECT a.*, c.name as category_name, c.slug as category_slug
        FROM articles a
        LEFT JOIN categories c ON a.category_id = c.id
        WHERE a.status = 'published'
        ORDER BY a.views DESC, a.published_at DESC
        LIMIT $limit
    ");
    return $stmt->fetchAll();
}

function incrementViews($articleId) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE articles SET views = views + 1 WHERE id = ?");
    $stmt->execute([$articleId]);
    
    $stmt = $pdo->prepare("INSERT INTO analytics (article_id, ip_address, user_agent) VALUES (?, ?, ?)");
    $stmt->execute([
        $articleId,
        $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
        $_SERVER['HTTP_USER_AGENT'] ?? ''
    ]);
}

function csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_verify($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function getSiteUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $path = dirname($_SERVER['PHP_SELF']);
    return $protocol . '://' . $host . $path;
}

function getCurrentUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    return $protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}

function isAdminUrl() {
    return strpos($_SERVER['REQUEST_URI'], '/admin/') !== false;
}

function getAvatar($name, $size = 40) {
    $initials = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $name), 0, 2));
    $colors = ['#8B5CF6', '#EC4899', '#10B981', '#F59E0B', '#3B82F6', '#EF4444', '#6366F1', '#14B8A6'];
    $color = $colors[abs(crc32($name)) % count($colors)];
    
    $radius = $size / 2;
    $fontSize = $size / 2.5;
    
    return "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='{$size}' height='{$size}' viewBox='0 0 {$size} {$size}'%3E%3Crect fill='{$color}' width='{$size}' height='{$size}' rx='{$radius}'/%3E%3Ctext x='50%25' y='50%25' dy='.35em' fill='white' font-family='Arial' font-size='{$fontSize}' text-anchor='middle'%3E{$initials}%3C/text%3E%3C/svg%3E";
}

function generateTableOfContents($content) {
    preg_match_all('/<h([1-6])>(.*?)<\/h[1-6]>/i', $content, $matches, PREG_SET_ORDER);
    
    if (empty($matches)) return '';
    
    $html = '<div class="toc-container">
        <h4><i class="fas fa-list-ul me-2"></i>Daftar Isi</h4>
        <ul class="toc-list">';
    
    foreach ($matches as $match) {
        $level = $match[1];
        $text = strip_tags($match[2]);
        $slug = generateSlug($text);
        $indent = ($level - 2) * 20;
        
        $html .= '<li style="margin-left: ' . $indent . 'px;">
            <a href="#' . $slug . '" class="toc-link">' . $text . '</a>
        </li>';
        
        $content = preg_replace('/<' . $match[0] . '/', '<h' . $level . ' id="' . $slug . '"', $content, 1);
    }
    
    $html .= '</ul></div>';
    
    return ['toc' => $html, 'content' => $content];
}

function getFeaturedImage($image, $default = 'network') {
    if ($image && file_exists(__DIR__ . '/../assets/img/articles/' . $image)) {
        return 'assets/img/articles/' . $image;
    }
    return "https://via.placeholder.com/800x600/" . urlencode(getCategoryColor($default)) . "/ffffff?text=No+Image";
}

function getCategoryColor($slug) {
    $colors = [
        'hotspot-management' => '2563EB',
        'routing-bgp' => '10B981',
        'firewall-security' => 'EF4444',
        'wireless-networking' => '8B5CF6',
        'vpn-configuration' => 'F59E0B',
        'bandwidth-management' => 'EC4899',
        'load-balancing' => '06B6D4',
        'scripting-automation' => '6366F1',
        'user-manager' => '8B5CF6',
        'troubleshooting-tips' => '6B7280',
        'default' => '8B5CF6'
    ];
    
    return $colors[$slug] ?? $colors['default'];
}

function sendJson($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit();
}