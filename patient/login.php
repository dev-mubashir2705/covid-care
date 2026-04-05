<?php
require_once '../config.php';
require_once '../includes/functions.php';

// Redirect if already logged in
if (isset($_SESSION['patient_id'])) {
    redirect(SITE_URL . 'patient/dashboard.php');
}

$page_title = 'Patient Login - COVID Care System';
require_once '../includes/header.php';
require_once '../includes/navbar.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = sanitize($_POST['email']);
    $password = md5($_POST['password']);
    
    $query = "SELECT * FROM patients WHERE email = '$email' AND password = '$password'";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) == 1) {
        $patient = mysqli_fetch_assoc($result);
        $_SESSION['patient_id'] = $patient['patient_id'];
        $_SESSION['patient_name'] = $patient['name'];
        $_SESSION['patient_email'] = $patient['email'];
        
        setFlashMessage('Login successful! Welcome back ' . $patient['name'], 'success');
        redirect(SITE_URL . 'patient/dashboard.php');
    } else {
        $error = "Invalid email or password!";
    }
}
?>

<style>
    /* ===== PATIENT LOGIN STYLES ===== */
    .login-section {
        padding: 80px 0;
        min-height: calc(100vh - 400px);
        display: flex;
        align-items: center;
        background: linear-gradient(135deg, #f5f7fa 0%, #f8f9fa 100%);
    }
    
    .login-container {
        max-width: 450px;
        margin: 0 auto;
        width: 100%;
    }
    
    .login-header {
        text-align: center;
        margin-bottom: 30px;
    }
    
    .login-header i {
        font-size: 70px;
        color: #0d6efd;
        background: #e7f1ff;
        padding: 20px;
        border-radius: 50%;
        margin-bottom: 20px;
        box-shadow: 0 10px 20px rgba(13,110,253,0.1);
    }
    
    .login-header h2 {
        font-size: 32px;
        font-weight: 700;
        color: #333;
        margin-bottom: 10px;
    }
    
    .login-header p {
        color: #6c757d;
        font-size: 16px;
    }
    
    .login-card {
        background: white;
        border-radius: 20px;
        padding: 40px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        border: 1px solid #eee;
        animation: slideInUp 0.8s ease;
    }
    
    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .form-group {
        margin-bottom: 25px;
    }
    
    .form-label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #333;
        font-size: 14px;
    }
    
    .input-group {
        position: relative;
    }
    
    .input-icon {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #0d6efd;
        font-size: 16px;
        z-index: 10;
    }
    
    .form-control {
        width: 100%;
        padding: 15px 15px 15px 45px;
        border: 2px solid #e9ecef;
        border-radius: 12px;
        font-size: 15px;
        transition: all 0.3s ease;
        background: #f8f9fa;
    }
    
    .form-control:focus {
        outline: none;
        border-color: #0d6efd;
        background: white;
        box-shadow: 0 0 0 4px rgba(13,110,253,0.1);
    }
    
    .form-control::placeholder {
        color: #adb5bd;
        font-size: 14px;
    }
    
    .password-toggle {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #6c757d;
        cursor: pointer;
        font-size: 16px;
        z-index: 10;
    }
    
    .password-toggle:hover {
        color: #0d6efd;
    }
    
    .remember-forgot {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
    }
    
    .remember-checkbox {
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
    }
    
    .remember-checkbox input {
        width: 18px;
        height: 18px;
        cursor: pointer;
        accent-color: #0d6efd;
    }
    
    .remember-checkbox span {
        color: #6c757d;
        font-size: 14px;
    }
    
    .forgot-link {
        color: #0d6efd;
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .forgot-link:hover {
        color: #0b5ed7;
        text-decoration: underline;
    }
    
    .btn-login {
        width: 100%;
        padding: 15px;
        background: linear-gradient(135deg, #0d6efd, #0b5ed7);
        color: white;
        border: none;
        border-radius: 12px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 10px 20px rgba(13,110,253,0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }
    
    .btn-login:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 30px rgba(13,110,253,0.3);
    }
    
    .register-link {
        text-align: center;
        margin-top: 25px;
        padding-top: 25px;
        border-top: 1px solid #eee;
    }
    
    .register-link p {
        color: #6c757d;
        margin-bottom: 10px;
    }
    
    .register-link a {
        color: #0d6efd;
        text-decoration: none;
        font-weight: 600;
        font-size: 16px;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    
    .register-link a:hover {
        color: #0b5ed7;
        gap: 12px;
    }
    
    .alert {
        padding: 16px 20px;
        border-radius: 12px;
        margin-bottom: 25px;
        display: flex;
        align-items: center;
        gap: 10px;
        animation: slideInLeft 0.5s ease;
    }
    
    @keyframes slideInLeft {
        from {
            opacity: 0;
            transform: translateX(-30px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    
    .alert-danger {
        background: #f8d7da;
        color: #842029;
        border: 1px solid #f5c2c7;
    }
    
    .alert i {
        font-size: 20px;
    }
    
    .back-link {
        text-align: center;
        margin-top: 20px;
    }
    
    .back-link a {
        color: #6c757d;
        text-decoration: none;
        font-size: 14px;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    
    .back-link a:hover {
        color: #0d6efd;
        gap: 12px;
    }
    
    @media (max-width: 768px) {
        .login-section {
            padding: 60px 0;
        }
        
        .login-card {
            padding: 30px 20px;
        }
        
        .login-header i {
            font-size: 60px;
            padding: 15px;
        }
        
        .login-header h2 {
            font-size: 28px;
        }
    }
</style>

<!-- Login Section -->
<section class="login-section">
    <div class="container">
        <div class="login-container">
            <div class="login-header">
                <i class="fas fa-user-circle"></i>
                <h2>Patient Login</h2>
                <p>Welcome back! Please login to your account</p>
            </div>
            
            <div class="login-card">
                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <?php echo showFlashMessage(); ?>
                
                <form method="POST" action="">
                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <div class="input-group">
                            <span class="input-icon"><i class="fas fa-envelope"></i></span>
                            <input type="email" class="form-control" name="email" placeholder="Enter your email" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-icon"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control" name="password" id="password" placeholder="Enter your password" required>
                            <span class="password-toggle" onclick="togglePassword()">
                                <i class="fas fa-eye" id="toggleIcon"></i>
                            </span>
                        </div>
                    </div>
                    
                    <div class="remember-forgot">
                        <label class="remember-checkbox">
                            <input type="checkbox" name="remember">
                            <span>Remember me</span>
                        </label>
                        <a href="<?php echo SITE_URL; ?>patient/forgot_password.php" class="forgot-link">Forgot Password?</a>
                    </div>
                    
                    <button type="submit" class="btn-login">
                        <i class="fas fa-sign-in-alt"></i>
                        Login to Dashboard
                    </button>
                    
                    <div class="register-link">
                        <p>Don't have an account?</p>
                        <a href="<?php echo SITE_URL; ?>patient/register.php">
                            Create New Account <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </form>
            </div>
            
            <div class="back-link">
                <a href="<?php echo SITE_URL; ?>">
                    <i class="fas fa-arrow-left"></i>
                    Back to Home
                </a>
            </div>
        </div>
    </div>
</section>

<script>
    function togglePassword() {
        const password = document.getElementById('password');
        const toggleIcon = document.getElementById('toggleIcon');
        
        if (password.type === 'password') {
            password.type = 'text';
            toggleIcon.classList.remove('fa-eye');
            toggleIcon.classList.add('fa-eye-slash');
        } else {
            password.type = 'password';
            toggleIcon.classList.remove('fa-eye-slash');
            toggleIcon.classList.add('fa-eye');
        }
    }
</script>

<?php
require_once '../includes/footer.php';
?>