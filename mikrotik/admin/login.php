<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (isLoggedIn()) {
    redirect('index.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username']);
    $password = $_POST['password'];
    $remember = isset($_POST['remember']) ? true : false;
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['full_name'] = $user['full_name'];
        
        if ($remember) {
            $token = bin2hex(random_bytes(32));
            $hashedToken = hash('sha256', $token);
            $expires = date('Y-m-d H:i:s', strtotime('+30 days'));
            
            $stmt = $pdo->prepare("UPDATE users SET remember_token = ?, token_expires = ? WHERE id = ?");
            $stmt->execute([$hashedToken, $expires, $user['id']]);
            
            setcookie('remember_me', $user['id'] . ':' . $token, time() + (86400 * 30), '/', '', false, true);
        }
        
        $stmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
        $stmt->execute([$user['id']]);
        
        redirect('index.php');
    } else {
        $error = 'Username atau password salah!';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Admin MikroTik Blog</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #8B5CF6;
            --secondary: #EC4899;
            --mikrotik: #2563EB;
            --dark: #1F2937;
            --light: #F9FAFB;
        }
        
        * {
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            overflow-x: hidden;
        }
        
        .login-container {
            width: 100%;
            max-width: 450px;
        }
        
        .login-card {
            background: var(--white);
            border-radius: 20px;
            padding: 50px 40px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }
        
        .login-logo {
            font-size: 4rem;
            color: var(--mikrotik);
            text-align: center;
            margin-bottom: 10px;
            animation: float 3s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        .login-title {
            text-align: center;
            font-weight: 700;
            font-size: 1.8rem;
            color: var(--dark);
            margin-bottom: 10px;
        }
        
        .login-subtitle {
            text-align: center;
            color: var(--gray);
            margin-bottom: 30px;
            font-size: 0.95rem;
        }
        
        .form-label {
            font-weight: 600;
            color: var(--dark);
            font-size: 0.9rem;
            margin-bottom: 8px;
        }
        
        .input-group-text {
            background: var(--light);
            border: 2px solid #E5E7EB;
            border-right: none;
            border-radius: 10px 0 0 10px;
        }
        
        .input-group-text i {
            color: var(--primary);
        }
        
        .form-control {
            border: 2px solid #E5E7EB;
            border-left: none;
            border-radius: 0 10px 10px 0;
            padding: 12px 15px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: var(--primary);
            box-shadow: none;
        }
        
        .input-group:focus-within .input-group-text {
            border-color: var(--primary);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            border: none;
            color: white;
            font-weight: 600;
            padding: 14px;
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(139, 92, 246, 0.4);
            color: white;
        }
        
        .alert {
            border-radius: 10px;
            font-size: 0.9rem;
            margin-bottom: 25px;
        }
        
        .alert-danger {
            background: #FEF2F2;
            border: 1px solid #FECACA;
            color: #DC2626;
        }
        
        .form-check {
            margin-bottom: 20px;
        }
        
        .form-check-input:checked {
            background-color: var(--primary);
            border-color: var(--primary);
        }
        
        .back-link {
            display: inline-block;
            text-align: center;
            width: 100%;
            color: var(--primary);
            font-weight: 600;
            text-decoration: none;
            padding: 10px 20px;
            border: 2px solid var(--primary);
            border-radius: 50px;
            transition: all 0.3s ease;
        }
        
        .back-link:hover {
            background: var(--primary);
            color: white;
            transform: translateY(-2px);
        }
        
        .back-link i {
            margin-right: 8px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-logo">
                <i class="fas fa-server"></i>
            </div>
            <h3 class="login-title">Admin Login</h3>
            <p class="login-subtitle">Masuk untuk mengelola blog MikroTik</p>
            
            <?php if ($error): ?>
            <div class="alert alert-danger" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
            </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="username" class="form-label">
                        <i class="fas fa-user me-2"></i>Username
                    </label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" class="form-control" id="username" name="username" placeholder="Masukkan username" required autofocus>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="password" class="form-label">
                        <i class="fas fa-lock me-2"></i>Password
                    </label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan password" required>
                    </div>
                </div>
                
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="remember" name="remember">
                    <label class="form-check-label" for="remember">
                        Ingat saya
                    </label>
                </div>
                
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-sign-in-alt me-2"></i>Login
                </button>
            </form>
            
            <div class="mt-4">
                <a href="<?php echo SITE_URL; ?>/" class="back-link">
                    <i class="fas fa-arrow-left"></i> Kembali ke Website
                </a>
            </div>
        </div>
    </div>
</body>
</html>