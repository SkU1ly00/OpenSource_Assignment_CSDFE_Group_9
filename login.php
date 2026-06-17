<?php
/**
 * Login Page
 * User authentication entry point
 */

session_start();
require_once 'config/config.php';
require_once 'src/classes/Database.php';
require_once 'src/classes/Auth.php';
require_once 'src/functions.php';

// Redirect if already logged in
if (isUserLoggedIn()) {
    redirect('index.php');
}

$login_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitizeInput($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $login_error = 'Please enter both username and password';
    } else {
        $auth = new Auth();
        $result = $auth->login($username, $password);
        
        if ($result['success']) {
            setFlashMessage('success', 'Welcome back!');
            redirect('index.php');
        } else {
            $login_error = $result['message'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Security Incident Reporting System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="public/css/style.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container {
            width: 100%;
            max-width: 450px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            padding: 40px;
        }
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-header h1 {
            color: #333;
            font-size: 28px;
            margin-bottom: 10px;
        }
        .login-header p {
            color: #666;
            font-size: 14px;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 10px 20px;
            font-weight: 600;
        }
        .btn-login:hover {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
            color: white;
        }
        .alert {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1>🔒 Security Incident</h1>
            <p>Reporting System - University of Dodoma</p>
        </div>

        <?php if (!empty($login_error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $login_error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" placeholder="Enter your username" required>
                <small class="text-muted">Default: admin</small>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                <small class="text-muted">Default: admin123</small>
            </div>

            <button type="submit" class="btn btn-login w-100 text-white mb-3">Login</button>
        </form>

        <div class="text-center">
            <p class="text-muted small">University of Dodoma | Cyber Security and Digital Forensics Engineering</p>
            <p class="text-muted small">Course: Open Source Technologies (CP 222) | Group 9</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>