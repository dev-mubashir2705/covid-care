<?php
session_start();
require_once '../config.php';
require_once '../includes/functions.php';

// Check if already logged in
if (isset($_SESSION['admin_id'])) {
    redirect(SITE_URL . 'admin/dashboard.php');
}

$page_title = 'Admin Login - COVID Care System';
require_once '../includes/header.php';
require_once '../includes/navbar.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = sanitize($_POST['username']);
    $password = md5($_POST['password']);
    
    $query = "SELECT * FROM admin WHERE username = '$username' AND password = '$password'";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) == 1) {
        $admin = mysqli_fetch_assoc($result);
        $_SESSION['admin_id'] = $admin['admin_id'];
        $_SESSION['admin_name'] = $admin['username'];
        
        setFlashMessage('Login successful! Welcome back ' . $admin['username'], 'success');
        redirect(SITE_URL . 'admin/dashboard.php');
    } else {
        $error = "Invalid username or password!";
    }
}
?>

<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Segoe UI', sans-serif; background: #f8f9fa; min-height: 100vh; display: flex; flex-direction: column; }
    .main-content { flex: 1; display: flex; align-items: center; padding: 40px 0; }
    .container { max-width: 1200px; margin: 0 auto; padding: 0 20px; width: 100%; }
    .login-container { max-width: 450px; margin: 0 auto; width: 100%; }
    .login-header { text-align: center; margin-bottom: 30px; }
    .login-header i { font-size: 70px; color: #0d6efd; background: #e7f1ff; padding: 20px; border-radius: 50%; margin-bottom: 20px; }
    .login-header h2 { font-size: 32px; font-weight: 700; color: #333; }
    .login-card { background: white; border-radius: 20px; padding: 40px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); border: 1px solid #eee; }
    .form-group { margin-bottom: 25px; }
    .form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #333; }
    .form-control { width: 100%; padding: 12px 15px; border: 2px solid #e9ecef; border-radius: 8px; font-size: 15px; }
    .form-control:focus { outline: none; border-color: #0d6efd; }
    .btn-login { width: 100%; padding: 14px; background: #0d6efd; color: white; border: none; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; }
    .btn-login:hover { background: #0b5ed7; }
    .alert-danger { background: #f8d7da; color: #842029; padding: 12px; border-radius: 8px; margin-bottom: 20px; }
    .back-link { text-align: center; margin-top: 20px; }
    .back-link a { color: #6c757d; text-decoration: none; }
</style>

<main class="main-content">
    <div class="container">
        <div class="login-container">
            <div class="login-header">
                <i class="fas fa-user-shield"></i>
                <h2>Admin Login</h2>
            </div>
            
            <div class="login-card">
                <?php if ($error): ?>
                    <div class="alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <?php echo showFlashMessage(); ?>
                
                <form method="POST">
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" class="form-control" name="username" required>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                    <button type="submit" class="btn-login">Login</button>
                </form>
            </div>
            
            <div class="back-link">
                <a href="<?php echo SITE_URL; ?>">← Back to Home</a>
            </div>
        </div>
    </div>
</main>

<?php require_once '../includes/footer.php'; ?>