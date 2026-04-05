<?php
require_once '../config.php';
require_once '../includes/functions.php';

// Check if patient is logged in
if (!isset($_SESSION['patient_id'])) {
    setFlashMessage('Please login to change password', 'danger');
    redirect(SITE_URL . 'patient/login.php');
}

$page_title = 'Change Password - COVID Care System';
require_once '../includes/header.php';
require_once '../includes/navbar.php';

$patient_id = $_SESSION['patient_id'];
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $current_password = md5($_POST['current_password']);
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Verify current password
    $check_query = "SELECT patient_id FROM patients WHERE patient_id = $patient_id AND password = '$current_password'";
    $check_result = mysqli_query($conn, $check_query);
    
    if (mysqli_num_rows($check_result) == 0) {
        $error = "Current password is incorrect!";
    } elseif ($new_password !== $confirm_password) {
        $error = "New passwords do not match!";
    } elseif (strlen($new_password) < 6) {
        $error = "Password must be at least 6 characters long!";
    } else {
        $new_password_md5 = md5($new_password);
        $update_query = "UPDATE patients SET password = '$new_password_md5' WHERE patient_id = $patient_id";
        
        if (mysqli_query($conn, $update_query)) {
            setFlashMessage('Password changed successfully!', 'success');
            redirect(SITE_URL . 'patient/profile.php');
        } else {
            $error = "Failed to change password. Please try again.";
        }
    }
}
?>

<style>
    /* ===== CHANGE PASSWORD STYLES ===== */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: #f8f9fa;
    }
    
    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }
    
    /* Page Header */
    .page-header {
        background: linear-gradient(135deg, #0d6efd, #0b5ed7);
        padding: 60px 0;
        margin-bottom: 40px;
        color: white;
        text-align: center;
    }
    
    .page-header h1 {
        font-size: 36px;
        font-weight: 700;
        margin-bottom: 10px;
    }
    
    .page-header p {
        font-size: 16px;
        opacity: 0.9;
        max-width: 600px;
        margin: 0 auto;
    }
    
    /* Password Container */
    .password-container {
        max-width: 500px;
        margin: 0 auto 60px;
    }
    
    /* Alert Messages */
    .alert {
        padding: 16px 20px;
        border-radius: 12px;
        margin-bottom: 25px;
        display: flex;
        align-items: center;
        gap: 12px;
        animation: slideInDown 0.5s ease;
    }
    
    @keyframes slideInDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .alert-danger {
        background: #f8d7da;
        color: #842029;
        border: 1px solid #f5c2c7;
    }
    
    .alert-success {
        background: #d1e7dd;
        color: #0f5132;
        border: 1px solid #badbcc;
    }
    
    .alert i {
        font-size: 20px;
    }
    
    /* Password Card */
    .password-card {
        background: white;
        border-radius: 20px;
        padding: 40px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        border: 1px solid #eee;
    }
    
    /* Info Box */
    .info-box {
        background: #e7f1ff;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 30px;
        display: flex;
        align-items: flex-start;
        gap: 15px;
        border-left: 4px solid #0d6efd;
    }
    
    .info-box i {
        font-size: 24px;
        color: #0d6efd;
    }
    
    .info-box p {
        color: #333;
        font-size: 14px;
        line-height: 1.6;
        margin: 0;
    }
    
    .info-box strong {
        color: #0d6efd;
    }
    
    /* Form Groups */
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
    
    .form-label i {
        color: #0d6efd;
        margin-right: 5px;
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
    
    /* Password Toggle */
    .password-toggle {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #6c757d;
        cursor: pointer;
        font-size: 16px;
        z-index: 10;
        transition: all 0.3s ease;
    }
    
    .password-toggle:hover {
        color: #0d6efd;
    }
    
    /* Password Strength Meter */
    .password-strength {
        margin-top: 8px;
        height: 4px;
        background: #e9ecef;
        border-radius: 2px;
        overflow: hidden;
    }
    
    .strength-bar {
        height: 100%;
        width: 0;
        transition: all 0.3s ease;
    }
    
    .strength-bar.weak {
        width: 33.33%;
        background: #dc3545;
    }
    
    .strength-bar.medium {
        width: 66.66%;
        background: #ffc107;
    }
    
    .strength-bar.strong {
        width: 100%;
        background: #28a745;
    }
    
    .strength-text {
        font-size: 12px;
        margin-top: 5px;
        color: #6c757d;
    }
    
    /* Password Requirements */
    .requirements-box {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 20px;
        margin: 25px 0;
        border: 1px solid #eee;
    }
    
    .requirements-title {
        font-size: 14px;
        font-weight: 600;
        color: #333;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .requirements-title i {
        color: #0d6efd;
    }
    
    .requirements-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .requirements-list li {
        color: #6c757d;
        font-size: 13px;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .requirements-list li i {
        color: #28a745;
        font-size: 12px;
    }
    
    .requirements-list li i.fa-times-circle {
        color: #dc3545;
    }
    
    /* Button Group */
    .button-group {
        display: flex;
        gap: 15px;
        margin-top: 30px;
    }
    
    .btn-update {
        flex: 2;
        padding: 15px 20px;
        background: linear-gradient(135deg, #0d6efd, #0b5ed7);
        color: white;
        border: none;
        border-radius: 12px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        box-shadow: 0 5px 15px rgba(13,110,253,0.2);
    }
    
    .btn-update:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(13,110,253,0.3);
    }
    
    .btn-update:active {
        transform: translateY(-1px);
    }
    
    .btn-cancel {
        flex: 1;
        padding: 15px 20px;
        background: #6c757d;
        color: white;
        border: none;
        border-radius: 12px;
        font-size: 16px;
        font-weight: 600;
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        transition: all 0.3s ease;
        box-shadow: 0 5px 15px rgba(108,117,125,0.2);
    }
    
    .btn-cancel:hover {
        background: #5a6268;
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(108,117,125,0.3);
    }
    
    .btn-cancel:active {
        transform: translateY(-1px);
    }
    
    /* Links */
    .back-link {
        text-align: center;
        margin-top: 25px;
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
    
    /* Responsive */
    @media (max-width: 768px) {
        .page-header {
            padding: 40px 0;
        }
        
        .page-header h1 {
            font-size: 28px;
        }
        
        .password-card {
            padding: 25px;
        }
        
        .button-group {
            flex-direction: column;
        }
        
        .info-box {
            flex-direction: column;
            text-align: center;
        }
        
        .info-box i {
            margin: 0 auto;
        }
    }
</style>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <h1>Change Password</h1>
        <p>Update your account password to keep your account secure</p>
    </div>
</section>

<!-- Password Form -->
<section class="container">
    <div class="password-container">
        
        <?php if ($error): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                <div><?php echo $error; ?></div>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <div><?php echo $success; ?></div>
            </div>
        <?php endif; ?>
        
        <div class="password-card">
            <!-- Info Box -->
            <div class="info-box">
                <i class="fas fa-shield-alt"></i>
                <p>
                    <strong>Security Tip:</strong> Choose a strong password that you don't use elsewhere. 
                    A strong password should be at least 6 characters long and include a mix of letters and numbers.
                </p>
            </div>
            
            <form method="POST" action="" id="passwordForm">
                <!-- Current Password -->
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-lock"></i> Current Password
                    </label>
                    <div class="input-group">
                        <span class="input-icon"><i class="fas fa-key"></i></span>
                        <input type="password" class="form-control" name="current_password" id="current_password" 
                               placeholder="Enter your current password" required>
                        <span class="password-toggle" onclick="togglePassword('current_password', this)">
                            <i class="fas fa-eye"></i>
                        </span>
                    </div>
                </div>
                
                <!-- New Password -->
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-lock"></i> New Password
                    </label>
                    <div class="input-group">
                        <span class="input-icon"><i class="fas fa-key"></i></span>
                        <input type="password" class="form-control" name="new_password" id="new_password" 
                               placeholder="Enter new password" required onkeyup="checkPasswordStrength()">
                        <span class="password-toggle" onclick="togglePassword('new_password', this)">
                            <i class="fas fa-eye"></i>
                        </span>
                    </div>
                    
                    <!-- Password Strength Meter -->
                    <div class="password-strength">
                        <div class="strength-bar" id="strengthBar"></div>
                    </div>
                    <div class="strength-text" id="strengthText">Enter a password</div>
                </div>
                
                <!-- Confirm New Password -->
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-lock"></i> Confirm New Password
                    </label>
                    <div class="input-group">
                        <span class="input-icon"><i class="fas fa-key"></i></span>
                        <input type="password" class="form-control" name="confirm_password" id="confirm_password" 
                               placeholder="Confirm new password" required onkeyup="checkPasswordMatch()">
                        <span class="password-toggle" onclick="togglePassword('confirm_password', this)">
                            <i class="fas fa-eye"></i>
                        </span>
                    </div>
                    <div class="strength-text" id="matchText"></div>
                </div>
                
                <!-- Password Requirements -->
                <div class="requirements-box">
                    <div class="requirements-title">
                        <i class="fas fa-list-check"></i>
                        Password Requirements
                    </div>
                    <ul class="requirements-list" id="requirementsList">
                        <li id="reqLength">
                            <i class="fas fa-times-circle"></i> At least 6 characters
                        </li>
                        <li id="reqNumber">
                            <i class="fas fa-times-circle"></i> Contains at least one number
                        </li>
                        <li id="reqLetter">
                            <i class="fas fa-times-circle"></i> Contains at least one letter
                        </li>
                        <li id="reqMatch">
                            <i class="fas fa-times-circle"></i> Passwords match
                        </li>
                    </ul>
                </div>
                
                <!-- Action Buttons -->
                <div class="button-group">
                    <button type="submit" class="btn-update" id="submitBtn">
                        <i class="fas fa-key"></i>
                        Update Password
                    </button>
                    <a href="<?php echo SITE_URL; ?>patient/profile.php" class="btn-cancel">
                        <i class="fas fa-times"></i>
                        Cancel
                    </a>
                </div>
            </form>
            
            <!-- Back Link -->
            <div class="back-link">
                <a href="<?php echo SITE_URL; ?>patient/profile.php">
                    <i class="fas fa-arrow-left"></i>
                    Back to Profile
                </a>
            </div>
        </div>
    </div>
</section>

<script>
    // Toggle password visibility
    function togglePassword(fieldId, element) {
        const field = document.getElementById(fieldId);
        const icon = element.querySelector('i');
        
        if (field.type === 'password') {
            field.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            field.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
    
    // Check password strength
    function checkPasswordStrength() {
        const password = document.getElementById('new_password').value;
        const strengthBar = document.getElementById('strengthBar');
        const strengthText = document.getElementById('strengthText');
        
        // Remove all classes
        strengthBar.classList.remove('weak', 'medium', 'strong');
        
        if (password.length === 0) {
            strengthBar.style.width = '0';
            strengthText.textContent = 'Enter a password';
            updateRequirements(password);
            return;
        }
        
        let strength = 0;
        
        // Check length
        if (password.length >= 6) strength++;
        if (password.length >= 8) strength++;
        
        // Check for numbers
        if (/\d/.test(password)) strength++;
        
        // Check for letters
        if (/[a-zA-Z]/.test(password)) strength++;
        
        // Check for special characters
        if (/[!@#$%^&*(),.?":{}|<>]/.test(password)) strength++;
        
        // Update UI based on strength
        if (strength <= 2) {
            strengthBar.classList.add('weak');
            strengthText.textContent = 'Weak password';
        } else if (strength <= 4) {
            strengthBar.classList.add('medium');
            strengthText.textContent = 'Medium password';
        } else {
            strengthBar.classList.add('strong');
            strengthText.textContent = 'Strong password';
        }
        
        updateRequirements(password);
        checkPasswordMatch();
    }
    
    // Check password requirements
    function updateRequirements(password) {
        const reqLength = document.getElementById('reqLength');
        const reqNumber = document.getElementById('reqNumber');
        const reqLetter = document.getElementById('reqLetter');
        
        // Length check
        if (password.length >= 6) {
            reqLength.innerHTML = '<i class="fas fa-check-circle"></i> At least 6 characters';
            reqLength.style.color = '#28a745';
        } else {
            reqLength.innerHTML = '<i class="fas fa-times-circle"></i> At least 6 characters';
            reqLength.style.color = '#dc3545';
        }
        
        // Number check
        if (/\d/.test(password)) {
            reqNumber.innerHTML = '<i class="fas fa-check-circle"></i> Contains at least one number';
            reqNumber.style.color = '#28a745';
        } else {
            reqNumber.innerHTML = '<i class="fas fa-times-circle"></i> Contains at least one number';
            reqNumber.style.color = '#dc3545';
        }
        
        // Letter check
        if (/[a-zA-Z]/.test(password)) {
            reqLetter.innerHTML = '<i class="fas fa-check-circle"></i> Contains at least one letter';
            reqLetter.style.color = '#28a745';
        } else {
            reqLetter.innerHTML = '<i class="fas fa-times-circle"></i> Contains at least one letter';
            reqLetter.style.color = '#dc3545';
        }
    }
    
    // Check if passwords match
    function checkPasswordMatch() {
        const newPass = document.getElementById('new_password').value;
        const confirmPass = document.getElementById('confirm_password').value;
        const matchText = document.getElementById('matchText');
        const reqMatch = document.getElementById('reqMatch');
        const submitBtn = document.getElementById('submitBtn');
        
        if (confirmPass.length > 0) {
            if (newPass === confirmPass) {
                matchText.textContent = '✓ Passwords match';
                matchText.style.color = '#28a745';
                reqMatch.innerHTML = '<i class="fas fa-check-circle"></i> Passwords match';
                reqMatch.style.color = '#28a745';
            } else {
                matchText.textContent = '✗ Passwords do not match';
                matchText.style.color = '#dc3545';
                reqMatch.innerHTML = '<i class="fas fa-times-circle"></i> Passwords match';
                reqMatch.style.color = '#dc3545';
            }
        } else {
            matchText.textContent = '';
        }
        
        // Enable/disable submit button based on all requirements
        const allRequirements = checkAllRequirements();
        if (allRequirements) {
            submitBtn.disabled = false;
            submitBtn.style.opacity = '1';
        } else {
            submitBtn.disabled = true;
            submitBtn.style.opacity = '0.6';
        }
    }
    
    // Check all requirements
    function checkAllRequirements() {
        const newPass = document.getElementById('new_password').value;
        const confirmPass = document.getElementById('confirm_password').value;
        
        return newPass.length >= 6 && 
               /\d/.test(newPass) && 
               /[a-zA-Z]/.test(newPass) && 
               newPass === confirmPass;
    }
    
    // Form submission validation
    document.getElementById('passwordForm').addEventListener('submit', function(e) {
        if (!checkAllRequirements()) {
            e.preventDefault();
            alert('Please meet all password requirements before submitting.');
        }
    });
    
    // Initialize on page load
    window.onload = function() {
        document.getElementById('submitBtn').disabled = true;
        document.getElementById('submitBtn').style.opacity = '0.6';
    };
</script>

<?php
require_once '../includes/footer.php';
?>