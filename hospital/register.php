<?php
session_start();
require_once '../config.php';
require_once '../includes/functions.php';

// Redirect if already logged in
if (isset($_SESSION['hospital_id'])) {
    redirect(SITE_URL . 'hospital/dashboard.php');
}

$page_title = 'Hospital Registration - COVID Care System';
require_once '../includes/header.php';
require_once '../includes/navbar.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $phone = sanitize($_POST['phone']);
    $address = sanitize($_POST['address']);
    $city = sanitize($_POST['city']);
    $reg_no = sanitize($_POST['reg_no']);
    $password = md5($_POST['password']);
    $confirm = $_POST['confirm'];
    
    // Validation
    $check = mysqli_query($conn, "SELECT hospital_id FROM hospitals WHERE email = '$email'");
    if (mysqli_num_rows($check) > 0) {
        $error = "Email already registered!";
    } elseif ($_POST['password'] != $confirm) {
        $error = "Passwords do not match!";
    } else {
        $query = "INSERT INTO hospitals (name, email, phone, address, city, registration_no, password, status, created_at) 
                  VALUES ('$name', '$email', '$phone', '$address', '$city', '$reg_no', '$password', 'pending', NOW())";
        
        if (mysqli_query($conn, $query)) {
            $success = "Registration successful! Please wait for admin approval.";
        } else {
            $error = "Registration failed. Please try again.";
        }
    }
}
?>

<style>
    .register-section { padding: 60px 0; background: #f8f9fa; }
    .register-container { max-width: 600px; margin: 0 auto; }
    .register-header { text-align: center; margin-bottom: 30px; }
    .register-header i { font-size: 60px; color: #0d6efd; background: #e7f1ff; padding: 20px; border-radius: 50%; margin-bottom: 20px; }
    .register-header h2 { font-size: 32px; font-weight: 700; color: #333; }
    .register-card { background: white; border-radius: 15px; padding: 40px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); border: 1px solid #eee; }
    .form-group { margin-bottom: 20px; }
    .form-group label { display: block; margin-bottom: 5px; font-weight: 600; color: #333; }
    .form-control { width: 100%; padding: 12px 15px; border: 2px solid #e9ecef; border-radius: 8px; font-size: 15px; }
    .form-control:focus { outline: none; border-color: #0d6efd; }
    .btn-register { width: 100%; padding: 14px; background: #0d6efd; color: white; border: none; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; margin-top: 20px; }
    .btn-register:hover { background: #0b5ed7; }
    .login-link { text-align: center; margin-top: 25px; }
    .login-link a { color: #0d6efd; text-decoration: none; font-weight: 600; }
    .alert-danger { padding: 12px 15px; border-radius: 8px; margin-bottom: 20px; background: #f8d7da; color: #842029; border: 1px solid #f5c2c7; }
    .alert-success { padding: 12px 15px; border-radius: 8px; margin-bottom: 20px; background: #d1e7dd; color: #0f5132; border: 1px solid #badbcc; }
</style>

<section class="register-section">
    <div class="container">
        <div class="register-container">
            <div class="register-header">
                <i class="fas fa-hospital"></i>
                <h2>Hospital Registration</h2>
            </div>
            <div class="register-card">
                <?php if ($error): ?>
                    <div class="alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="alert-success"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="form-group">
                        <label>Hospital Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="text" class="form-control" name="phone" required>
                    </div>
                    <div class="form-group">
                        <label>Address</label>
                        <input type="text" class="form-control" name="address" required>
                    </div>
                    <div class="form-group">
                        <label>City</label>
                        <input type="text" class="form-control" name="city" required>
                    </div>
                    <div class="form-group">
                        <label>Registration Number</label>
                        <input type="text" class="form-control" name="reg_no" required>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                    <div class="form-group">
                        <label>Confirm Password</label>
                        <input type="password" class="form-control" name="confirm" required>
                    </div>
                    <button type="submit" class="btn-register">Register Hospital</button>
                </form>
                
                <div class="login-link">
                    <p>Already have an account? <a href="<?php echo SITE_URL; ?>hospital/login.php">Login here</a></p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once '../includes/footer.php'; ?>