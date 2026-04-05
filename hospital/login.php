<?php
session_start();
require_once '../config.php';
require_once '../includes/functions.php';

// Redirect if already logged in
if (isset($_SESSION['hospital_id'])) {
    redirect(SITE_URL . 'hospital/dashboard.php');
}

$page_title = 'Hospital Login - COVID Care System';
require_once '../includes/header.php';
require_once '../includes/navbar.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = sanitize($_POST['email']);
    $password = md5($_POST['password']);
    
    $query = "SELECT * FROM hospitals WHERE email = '$email' AND password = '$password'";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) == 1) {
        $hospital = mysqli_fetch_assoc($result);
        
        if ($hospital['status'] != 'approved') {
            $error = "Your account is not approved yet. Please wait for admin approval.";
        } else {
            $_SESSION['hospital_id'] = $hospital['hospital_id'];
            $_SESSION['hospital_name'] = $hospital['name'];
            $_SESSION['hospital_email'] = $hospital['email'];
            
            setFlashMessage('Login successful! Welcome ' . $hospital['name'], 'success');
            redirect(SITE_URL . 'hospital/dashboard.php');
        }
    } else {
        $error = "Invalid email or password!";
    }
}
?>

<style>
    .login-section { padding: 60px 0; min-height: calc(100vh - 400px); display: flex; align-items: center; background: #f8f9fa; }
    .login-container { max-width: 450px; margin: 0 auto; width: 100%; }
    .login-header { text-align: center; margin-bottom: 30px; }
    .login-header i { font-size: 60px; color: #0d6efd; background: #e7f1ff; padding: 20px; border-radius: 50%; margin-bottom: 20px; }
    .login-header h2 { font-size: 32px; font-weight: 700; color: #333; }
    .login-card { background: white; border-radius: 15px; padding: 40px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); border: 1px solid #eee; }
    .form-group { margin-bottom: 25px; }
    .form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #333; }
    .form-control { width: 100%; padding: 12px 15px; border: 2px solid #e9ecef; border-radius: 8px; font-size: 15px; }
    .form-control:focus { outline: none; border-color: #0d6efd; }
    .btn-login { width: 100%; padding: 14px; background: #0d6efd; color: white; border: none; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; }
    .btn-login:hover { background: #0b5ed7; }
    .register-link { text-align: center; margin-top: 25px; padding-top: 25px; border-top: 1px solid #eee; }
    .register-link a { color: #0d6efd; text-decoration: none; font-weight: 600; }
    .alert { padding: 12px 15px; border-radius: 8px; margin-bottom: 20px; background: #f8d7da; color: #842029; border: 1px solid #f5c2c7; }
</style>

<section class="login-section">
    <div class="container">
        <div class="login-container">
            <div class="login-header">
                <i class="fas fa-hospital"></i>
                <h2>Hospital Login</h2>
            </div>
            <div class="login-card">
                <?php if ($error): ?>
                    <div class="alert"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                    <button type="submit" class="btn-login">Login</button>
                </form>
                
                <div class="register-link">
                    <p>Don't have an account? <a href="<?php echo SITE_URL; ?>hospital/register.php">Register here</a></p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once '../includes/footer.php'; ?>