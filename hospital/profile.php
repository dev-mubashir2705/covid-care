<?php
session_start();
require_once '../config.php';
require_once '../includes/functions.php';

// Check if hospital is logged in
if (!isset($_SESSION['hospital_id'])) {
    setFlashMessage('Please login to view profile', 'danger');
    redirect(SITE_URL . 'hospital/login.php');
}

$page_title = 'Hospital Profile - COVID Care System';
require_once '../includes/header.php';
require_once '../includes/navbar.php';

$hospital_id = $_SESSION['hospital_id'];

// Get hospital details
$hospital = getRecord($conn, 'hospitals', 'hospital_id', $hospital_id);
?>

<style>
    .page-header { background: linear-gradient(135deg, #0d6efd, #0b5ed7); padding: 60px 0; margin-bottom: 40px; color: white; text-align: center; }
    .page-header h1 { font-size: 42px; font-weight: 700; margin-bottom: 10px; }
    .profile-card { background: white; border-radius: 15px; padding: 30px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); border: 1px solid #eee; max-width: 800px; margin: 0 auto; }
    .profile-header { display: flex; align-items: center; gap: 20px; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 2px solid #f0f0f0; }
    .profile-icon { width: 80px; height: 80px; background: linear-gradient(135deg, #0d6efd, #0b5ed7); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 35px; }
    .profile-title h2 { font-size: 28px; font-weight: 700; color: #333; margin-bottom: 5px; }
    .profile-title p { color: #6c757d; }
    .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    .info-item { padding: 15px; background: #f8f9fa; border-radius: 10px; }
    .info-label { font-size: 12px; color: #6c757d; margin-bottom: 5px; text-transform: uppercase; }
    .info-value { font-size: 16px; font-weight: 600; color: #333; }
    .badge { display: inline-block; padding: 5px 12px; border-radius: 50px; font-size: 12px; font-weight: 600; }
    .badge-success { background: #d1e7dd; color: #0f5132; }
    .badge-warning { background: #fff3cd; color: #856404; }
    .action-buttons { margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; display: flex; gap: 15px; }
    .btn { padding: 12px 25px; border-radius: 8px; text-decoration: none; font-weight: 600; display: inline-block; }
    .btn-primary { background: #0d6efd; color: white; }
    .btn-secondary { background: #6c757d; color: white; }
    @media (max-width: 768px) { .profile-header { flex-direction: column; text-align: center; } .info-grid { grid-template-columns: 1fr; } }
</style>

<section class="page-header">
    <div class="container">
        <h1>Hospital Profile</h1>
        <p>View and manage your hospital information</p>
    </div>
</section>

<main class="container" style="padding-bottom: 50px;">
    <div class="profile-card">
        <div class="profile-header">
            <div class="profile-icon">
                <i class="fas fa-hospital"></i>
            </div>
            <div class="profile-title">
                <h2><?php echo htmlspecialchars($hospital['name']); ?></h2>
                <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($hospital['email']); ?></p>
                <p><i class="fas fa-phone"></i> <?php echo htmlspecialchars($hospital['phone']); ?></p>
            </div>
        </div>
        
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Registration Number</div>
                <div class="info-value"><?php echo htmlspecialchars($hospital['registration_no']); ?></div>
            </div>
            <div class="info-item">
                <div class="info-label">Status</div>
                <div class="info-value">
                    <span class="badge <?php echo $hospital['status'] == 'approved' ? 'badge-success' : 'badge-warning'; ?>">
                        <?php echo ucfirst($hospital['status']); ?>
                    </span>
                </div>
            </div>
            <div class="info-item">
                <div class="info-label">Address</div>
                <div class="info-value"><?php echo htmlspecialchars($hospital['address']); ?></div>
            </div>
            <div class="info-item">
                <div class="info-label">City</div>
                <div class="info-value"><?php echo htmlspecialchars($hospital['city']); ?></div>
            </div>
            <div class="info-item">
                <div class="info-label">Member Since</div>
                <div class="info-value"><?php echo date('d M Y', strtotime($hospital['created_at'])); ?></div>
            </div>
        </div>
        
        <div class="action-buttons">
            <a href="#" class="btn btn-primary">Edit Profile</a>
            <a href="<?php echo SITE_URL; ?>hospital/change_password.php" class="btn btn-secondary">Change Password</a>
        </div>
    </div>
</main>

<?php require_once '../includes/footer.php'; ?>