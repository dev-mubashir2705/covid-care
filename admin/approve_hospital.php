<?php
session_start();
require_once '../config.php';
require_once '../includes/functions.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    setFlashMessage('Please login to access this page', 'danger');
    redirect(SITE_URL . 'admin/login.php');
}

$page_title = 'Approve Hospitals - Admin Panel';
require_once '../includes/header.php';
require_once '../includes/navbar.php';

// Handle approval/rejection
if (isset($_GET['approve'])) {
    $id = (int)$_GET['approve'];
    mysqli_query($conn, "UPDATE hospitals SET status = 'approved' WHERE hospital_id = $id");
    setFlashMessage('Hospital approved successfully!', 'success');
    redirect('approve_hospital.php');
}

if (isset($_GET['reject'])) {
    $id = (int)$_GET['reject'];
    mysqli_query($conn, "UPDATE hospitals SET status = 'rejected' WHERE hospital_id = $id");
    setFlashMessage('Hospital rejected!', 'warning');
    redirect('approve_hospital.php');
}

// Get pending hospitals
$hospitals = getRecords($conn, 'hospitals', "status = 'pending'", 'created_at ASC');
?>

<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Segoe UI', sans-serif; background: #f8f9fa; min-height: 100vh; display: flex; flex-direction: column; }
    .main-content { flex: 1; padding: 40px 0; }
    .container { max-width: 1200px; margin: 0 auto; padding: 0 20px; width: 100%; }
    
    .page-header { background: linear-gradient(135deg, #0d6efd, #0b5ed7); padding: 60px 0; margin-bottom: 40px; color: white; text-align: center; }
    .page-header h1 { font-size: 42px; font-weight: 700; margin-bottom: 10px; }
    
    .stats-card { background: white; border-radius: 15px; padding: 30px; margin-bottom: 30px; border: 1px solid #eee; display: flex; align-items: center; gap: 20px; }
    .stats-icon { width: 60px; height: 60px; background: #fff3cd; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #856404; font-size: 24px; }
    .stats-text h3 { font-size: 36px; font-weight: 700; color: #333; }
    .stats-text p { color: #6c757d; }
    
    .hospital-card { background: white; border-radius: 15px; padding: 25px; margin-bottom: 20px; border: 1px solid #eee; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
    .hospital-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
    .hospital-header h3 { font-size: 22px; font-weight: 700; color: #333; }
    .hospital-details { display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; margin-bottom: 20px; }
    .detail-item .label { font-size: 12px; color: #6c757d; text-transform: uppercase; }
    .detail-item .value { font-size: 16px; font-weight: 600; color: #333; }
    .actions { display: flex; gap: 15px; }
    .btn-approve { padding: 10px 25px; background: #28a745; color: white; border: none; border-radius: 8px; text-decoration: none; display: inline-block; }
    .btn-reject { padding: 10px 25px; background: #dc3545; color: white; border: none; border-radius: 8px; text-decoration: none; display: inline-block; }
    .empty-state { text-align: center; padding: 60px; background: white; border-radius: 15px; border: 1px solid #eee; }
    .empty-state i { font-size: 60px; color: #dee2e6; margin-bottom: 20px; }
</style>

<section class="page-header">
    <div class="container">
        <h1>Approve Hospitals</h1>
        <p>Review and approve hospital registrations</p>
    </div>
</section>

<main class="main-content">
    <div class="container">
        <?php echo showFlashMessage(); ?>
        
        <div class="stats-card">
            <div class="stats-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stats-text">
                <h3><?php echo count($hospitals); ?></h3>
                <p>Hospitals pending approval</p>
            </div>
        </div>
        
        <?php if (!empty($hospitals)): ?>
            <?php foreach ($hospitals as $h): ?>
            <div class="hospital-card">
                <div class="hospital-header">
                    <h3><?php echo $h['name']; ?></h3>
                    <span class="badge badge-pending">Pending</span>
                </div>
                
                <div class="hospital-details">
                    <div class="detail-item">
                        <div class="label">Email</div>
                        <div class="value"><?php echo $h['email']; ?></div>
                    </div>
                    <div class="detail-item">
                        <div class="label">Phone</div>
                        <div class="value"><?php echo $h['phone']; ?></div>
                    </div>
                    <div class="detail-item">
                        <div class="label">Address</div>
                        <div class="value"><?php echo $h['address']; ?></div>
                    </div>
                    <div class="detail-item">
                        <div class="label">City</div>
                        <div class="value"><?php echo $h['city']; ?></div>
                    </div>
                    <div class="detail-item">
                        <div class="label">Registration No.</div>
                        <div class="value"><?php echo $h['registration_no']; ?></div>
                    </div>
                    <div class="detail-item">
                        <div class="label">Registered On</div>
                        <div class="value"><?php echo date('d M Y', strtotime($h['created_at'])); ?></div>
                    </div>
                </div>
                
                <div class="actions">
                    <a href="?approve=<?php echo $h['hospital_id']; ?>" class="btn-approve" onclick="return confirm('Approve this hospital?')">
                        <i class="fas fa-check"></i> Approve
                    </a>
                    <a href="?reject=<?php echo $h['hospital_id']; ?>" class="btn-reject" onclick="return confirm('Reject this hospital?')">
                        <i class="fas fa-times"></i> Reject
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-check-circle"></i>
                <h3>No Pending Approvals</h3>
                <p>All hospitals have been reviewed.</p>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php require_once '../includes/footer.php'; ?>