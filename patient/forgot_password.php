<?php
require_once '../config.php';
require_once '../includes/functions.php';

// Redirect if already logged in
if (isset($_SESSION['patient_id'])) {
    redirect(SITE_URL . 'patient/dashboard.php');
}

$page_title = 'Forgot Password - COVID Care System';
require_once '../includes/header.php';
require_once '../includes/navbar.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = sanitize($_POST['email']);
    
    $check = mysqli_query($conn, "SELECT patient_id FROM patients WHERE email = '$email'");
    
    if (mysqli_num_rows($check) > 0) {
        $success = "Password reset instructions have been sent to your email.";
    } else {
        $error = "Email address not found.";
    }
}
?>

<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Segoe UI', sans-serif; background: #f8f9fa; min-height: 100vh; display: flex; flex-direction: column; }
    .main-content { flex: 1; display: flex; align-items: center; padding: 40px 0; }
    .container { max-width: 1200px; margin: 0 auto; padding: 0 20px; width: 100%; }
    .forgot-container { max-width: 450px; margin: 0 auto; width: 100%; }
    .forgot-header { text-align: center; margin-bottom: 30px; }
    .forgot-header i { font-size: 60px; color: #0d6efd; background: #e7f1ff; padding: 20px; border-radius: 50%; margin-bottom: 20px; }
    .forgot-header h2 { font-size: 32px; font-weight: 700; color: #333; }
    .forgot-card { background: white; border-radius: 20px; padding: 40px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); border: 1px solid #eee; }
    .info-box { background: #e7f1ff; padding: 15px; border-radius: 10px; margin-bottom: 25px; color: #0d6efd; display: flex; align-items: center; gap: 10px; }
    .form-group { margin-bottom: 25px; }
    .form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #333; }
    .form-control { width: 100%; padding: 12px 15px; border: 2px solid #e9ecef; border-radius: 8px; font-size: 15px; }
    .form-control:focus { outline: none; border-color: #0d6efd; }
    .btn-reset { width: 100%; padding: 14px; background: #0d6efd; color: white; border: none; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; }
    .btn-reset:hover { background: #0b5ed7; }
    .back-link { text-align: center; margin-top: 25px; }
    .back-link a { color: #6c757d; text-decoration: none; }
    .alert-danger { background: #f8d7da; color: #842029; padding: 12px; border-radius: 8px; margin-bottom: 20px; }
    .alert-success { background: #d1e7dd; color: #0f5132; padding: 12px; border-radius: 8px; margin-bottom: 20px; }
</style>

<main class="main-content">
    <div class="container">
        <div class="forgot-container">
            <div class="forgot-header">
                <i class="fas fa-key"></i>
                <h2>Forgot Password?</h2>
            </div>
            
            <div class="forgot-card">
                <?php if ($error): ?>
                    <div class="alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="alert-success"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <div class="info-box">
                    <i class="fas fa-info-circle"></i>
                    <span>Enter your email to reset password</span>
                </div>
                
                <form method="POST">
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <button type="submit" class="btn-reset">Send Reset Instructions</button>
                </form>
                
                <div class="back-link">
                    <a href="<?php echo SITE_URL; ?>patient/login.php">← Back to Login</a>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once '../includes/footer.php'; ?>