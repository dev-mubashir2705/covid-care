<?php
session_start();
require_once '../config.php';
require_once '../includes/functions.php';

// Check if hospital is logged in
if (!isset($_SESSION['hospital_id'])) {
    setFlashMessage('Please login to change password', 'danger');
    redirect(SITE_URL . 'hospital/login.php');
}

$page_title = 'Change Password - Hospital Panel';
require_once '../includes/header.php';
require_once '../includes/navbar.php';

$hospital_id = $_SESSION['hospital_id'];
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $current = md5($_POST['current']);
    $new = md5($_POST['new']);
    $confirm = $_POST['confirm'];
    
    // Check current password
    $check = mysqli_query($conn, "SELECT hospital_id FROM hospitals WHERE hospital_id = $hospital_id AND password = '$current'");
    
    if (mysqli_num_rows($check) == 0) {
        $error = "Current password is incorrect!";
    } elseif ($_POST['new'] != $confirm) {
        $error = "New passwords do not match!";
    } elseif (strlen($_POST['new']) < 6) {
        $error = "Password must be at least 6 characters!";
    } else {
        mysqli_query($conn, "UPDATE hospitals SET password = '$new' WHERE hospital_id = $hospital_id");
        $success = "Password changed successfully!";
    }
}
?>

<style>
    body { background: #f8f9fa; }
    .container { max-width: 500px; margin: 50px auto; }
    .card { background: white; padding: 30px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); }
    h2 { text-align: center; margin-bottom: 30px; color: #333; }
    .form-group { margin-bottom: 20px; }
    label { display: block; margin-bottom: 5px; font-weight: 600; color: #555; }
    input { width: 100%; padding: 12px; border: 2px solid #e9ecef; border-radius: 8px; font-size: 15px; }
    input:focus { outline: none; border-color: #0d6efd; }
    button { width: 100%; padding: 12px; background: #0d6efd; color: white; border: none; border-radius: 8px; font-size: 16px; cursor: pointer; margin-top: 10px; }
    button:hover { background: #0b5ed7; }
    .error { background: #f8d7da; color: #842029; padding: 12px; border-radius: 8px; margin-bottom: 20px; }
    .success { background: #d1e7dd; color: #0f5132; padding: 12px; border-radius: 8px; margin-bottom: 20px; }
    .back { text-align: center; margin-top: 20px; }
    .back a { color: #6c757d; text-decoration: none; }
    .back a:hover { color: #0d6efd; }
</style>

<section class="page-header" style="background: linear-gradient(135deg, #0d6efd, #0b5ed7); padding: 60px 0; color: white; text-align: center;">
    <div class="container">
        <h1 style="font-size: 42px; font-weight: 700;">Change Password</h1>
    </div>
</section>

<main class="container" style="padding: 50px 0;">
    <div class="card">
        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label>Current Password</label>
                <input type="password" name="current" required>
            </div>
            <div class="form-group">
                <label>New Password</label>
                <input type="password" name="new" required>
            </div>
            <div class="form-group">
                <label>Confirm New Password</label>
                <input type="password" name="confirm" required>
            </div>
            <button type="submit">Update Password</button>
        </form>
        
        <div class="back">
            <a href="<?php echo SITE_URL; ?>hospital/profile.php">← Back to Profile</a>
        </div>
    </div>
</main>

<?php require_once '../includes/footer.php'; ?>