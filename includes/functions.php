<?php
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['user_id']) && $_SESSION['user_id'] == 1;
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function getSetting($key) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = ?");
    $stmt->execute([$key]);
    $result = $stmt->fetch();
    return $result ? $result['setting_value'] : '';
}

function generateSlug($title) {
    $slug = strtolower($title);
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
    $slug = trim($slug, '-');
    return $slug;
}

function formatDate($date) {
    return date('d F Y', strtotime($date));
}

function uploadImage($file, $destination) {
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $filename = $file['name'];
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    
    if (!in_array($ext, $allowed)) {
        return ['success' => false, 'message' => 'Invalid file type'];
    }
    
    if ($file['size'] > 2097152) {
        return ['success' => false, 'message' => 'File too large. Max 2MB'];
    }
    
    $newFilename = uniqid() . '.' . $ext;
    $target = $destination . $newFilename;
    
    if (move_uploaded_file($file['tmp_name'], $target)) {
        return ['success' => true, 'filename' => $newFilename];
    }
    
    return ['success' => false, 'message' => 'Upload failed'];
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

function readingTime($content) {
    $wordCount = str_word_count(strip_tags($content));
    $minutes = ceil($wordCount / 200);
    return $minutes . ' min read';
}
