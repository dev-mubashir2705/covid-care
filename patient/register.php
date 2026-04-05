<?php
require_once '../config.php';
require_once '../includes/functions.php';

// Redirect if already logged in
if (isset($_SESSION['patient_id'])) {
    redirect(SITE_URL . 'patient/dashboard.php');
}

$page_title = 'Patient Registration - COVID Care System';
require_once '../includes/header.php';
require_once '../includes/navbar.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $phone = sanitize($_POST['phone']);
    $password = md5($_POST['password']);
    $confirm_password = $_POST['confirm_password'];
    $address = sanitize($_POST['address']);
    $city = sanitize($_POST['city']);
    $dob = sanitize($_POST['dob']);
    $gender = sanitize($_POST['gender']);
    
    // Validation
    if ($_POST['password'] !== $confirm_password) {
        $error = "Passwords do not match!";
    } else {
        // Check if email already exists
        $check_query = "SELECT patient_id FROM patients WHERE email = '$email'";
        $check_result = mysqli_query($conn, $check_query);
        
        if (mysqli_num_rows($check_result) > 0) {
            $error = "Email already registered!";
        } else {
            $query = "INSERT INTO patients (name, email, phone, password, address, city, dob, gender, created_at) 
                      VALUES ('$name', '$email', '$phone', '$password', '$address', '$city', '$dob', '$gender', NOW())";
            
            if (mysqli_query($conn, $query)) {
                $patient_id = mysqli_insert_id($conn);
                $_SESSION['patient_id'] = $patient_id;
                $_SESSION['patient_name'] = $name;
                $_SESSION['patient_email'] = $email;
                
                setFlashMessage('Registration successful! Welcome ' . $name, 'success');
                redirect(SITE_URL . 'patient/dashboard.php');
            } else {
                $error = "Registration failed. Please try again.";
            }
        }
    }
}
?>

<style>
    /* ===== PATIENT REGISTRATION STYLES ===== */
    .register-section {
        padding: 60px 0;
        background: linear-gradient(135deg, #f5f7fa 0%, #f8f9fa 100%);
    }
    
    .register-container {
        max-width: 700px;
        margin: 0 auto;
    }
    
    .register-header {
        text-align: center;
        margin-bottom: 40px;
    }
    
    .register-header i {
        font-size: 70px;
        color: #0d6efd;
        background: #e7f1ff;
        padding: 20px;
        border-radius: 50%;
        margin-bottom: 20px;
        box-shadow: 0 10px 20px rgba(13,110,253,0.1);
    }
    
    .register-header h2 {
        font-size: 32px;
        font-weight: 700;
        color: #333;
        margin-bottom: 10px;
    }
    
    .register-header p {
        color: #6c757d;
        font-size: 16px;
    }
    
    .register-card {
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
    
    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 20px;
    }
    
    .form-group {
        margin-bottom: 20px;
    }
    
    .form-label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #333;
        font-size: 14px;
    }
    
    .form-label i {
        color: #0d6efd;
        margin-right: 5px;
    }
    
    .form-control {
        width: 100%;
        padding: 12px 15px;
        border: 2px solid #e9ecef;
        border-radius: 10px;
        font-size: 14px;
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
    }
    
    select.form-control {
        cursor: pointer;
    }
    
    .btn-register {
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
        margin-top: 20px;
    }
    
    .btn-register:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 30px rgba(13,110,253,0.3);
    }
    
    .login-link {
        text-align: center;
        margin-top: 25px;
        padding-top: 25px;
        border-top: 1px solid #eee;
    }
    
    .login-link p {
        color: #6c757d;
        margin-bottom: 10px;
    }
    
    .login-link a {
        color: #0d6efd;
        text-decoration: none;
        font-weight: 600;
        font-size: 16px;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    
    .login-link a:hover {
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
    
    .alert-danger {
        background: #f8d7da;
        color: #842029;
        border: 1px solid #f5c2c7;
    }
    
    .alert i {
        font-size: 20px;
    }
    
    .terms {
        margin-top: 20px;
        text-align: center;
        color: #6c757d;
        font-size: 13px;
    }
    
    .terms a {
        color: #0d6efd;
        text-decoration: none;
    }
    
    .terms a:hover {
        text-decoration: underline;
    }
    
    @media (max-width: 768px) {
        .register-section {
            padding: 40px 0;
        }
        
        .register-card {
            padding: 25px 20px;
        }
        
        .form-row {
            grid-template-columns: 1fr;
            gap: 0;
        }
        
        .register-header i {
            font-size: 60px;
            padding: 15px;
        }
        
        .register-header h2 {
            font-size: 28px;
        }
    }
</style>

<!-- Registration Section -->
<section class="register-section">
    <div class="container">
        <div class="register-container">
            <div class="register-header">
                <i class="fas fa-user-plus"></i>
                <h2>Patient Registration</h2>
                <p>Create your account to book appointments and track your health</p>
            </div>
            
            <div class="register-card">
                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <?php echo showFlashMessage(); ?>
                
                <form method="POST" action="">
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-user"></i> Full Name
                            </label>
                            <input type="text" class="form-control" name="name" placeholder="Enter your full name" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-envelope"></i> Email Address
                            </label>
                            <input type="email" class="form-control" name="email" placeholder="Enter your email" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-phone"></i> Phone Number
                            </label>
                            <input type="tel" class="form-control" name="phone" placeholder="03XXXXXXXXX" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-city"></i> City
                            </label>
                            <input type="text" class="form-control" name="city" placeholder="Enter your city" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-calendar"></i> Date of Birth
                            </label>
                            <input type="date" class="form-control" name="dob" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-venus-mars"></i> Gender
                            </label>
                            <select class="form-control" name="gender" required>
                                <option value="">Select Gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-map-marker-alt"></i> Address
                        </label>
                        <textarea class="form-control" name="address" rows="2" placeholder="Enter your complete address" required></textarea>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-lock"></i> Password
                            </label>
                            <input type="password" class="form-control" name="password" placeholder="Create password" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-lock"></i> Confirm Password
                            </label>
                            <input type="password" class="form-control" name="confirm_password" placeholder="Confirm password" required>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn-register">
                        <i class="fas fa-user-plus"></i>
                        Create Account
                    </button>
                    
                    <div class="terms">
                        By registering, you agree to our <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>.
                    </div>
                    
                    <div class="login-link">
                        <p>Already have an account?</p>
                        <a href="<?php echo SITE_URL; ?>patient/login.php">
                            Login Here <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<?php
require_once '../includes/footer.php';
?>