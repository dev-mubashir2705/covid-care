<?php
session_start();
require_once '../config.php';
require_once '../includes/functions.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    setFlashMessage('Please login to access this page', 'danger');
    redirect(SITE_URL . 'admin/login.php');
}

$page_title = 'Manage Patients - Admin Panel';
require_once '../includes/header.php';
require_once '../includes/navbar.php';

// Get all patients
$patients = getRecords($conn, 'patients', '', 'created_at DESC');
?>

<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Segoe UI', sans-serif; background: #f8f9fa; min-height: 100vh; display: flex; flex-direction: column; }
    .main-content { flex: 1; padding: 40px 0; }
    .container { max-width: 1200px; margin: 0 auto; padding: 0 20px; width: 100%; }
    
    .page-header { background: linear-gradient(135deg, #0d6efd, #0b5ed7); padding: 60px 0; margin-bottom: 40px; color: white; text-align: center; }
    .page-header h1 { font-size: 42px; font-weight: 700; margin-bottom: 10px; }
    
    .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 25px; margin-bottom: 30px; }
    .stat-card { background: white; border-radius: 15px; padding: 25px; text-align: center; border: 1px solid #eee; }
    .stat-number { font-size: 36px; font-weight: 700; color: #0d6efd; }
    
    .card { background: white; border-radius: 15px; padding: 25px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); border: 1px solid #eee; }
    table { width: 100%; border-collapse: collapse; }
    th { text-align: left; padding: 12px; background: #f8f9fa; color: #333; font-weight: 600; border-bottom: 2px solid #dee2e6; }
    td { padding: 12px; color: #6c757d; border-bottom: 1px solid #eee; }
    .btn-view { padding: 5px 12px; background: #0d6efd; color: white; text-decoration: none; border-radius: 5px; font-size: 12px; }
</style>

<section class="page-header">
    <div class="container">
        <h1>Manage Patients</h1>
        <p>View all registered patients</p>
    </div>
</section>

<main class="main-content">
    <div class="container">
        <?php 
        $total = count($patients);
        $male = countRecords($conn, 'patients', "gender = 'Male'");
        $female = countRecords($conn, 'patients', "gender = 'Female'");
        ?>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo $total; ?></div>
                <div>Total Patients</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $male; ?></div>
                <div>Male</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $female; ?></div>
                <div>Female</div>
            </div>
        </div>
        
        <div class="card">
            <?php if (!empty($patients)): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>City</th>
                        <th>Gender</th>
                        <th>Registered</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($patients as $p): ?>
                    <tr>
                        <td>#<?php echo $p['patient_id']; ?></td>
                        <td><strong><?php echo $p['name']; ?></strong></td>
                        <td><?php echo $p['email']; ?></td>
                        <td><?php echo $p['phone']; ?></td>
                        <td><?php echo $p['city']; ?></td>
                        <td><?php echo $p['gender']; ?></td>
                        <td><?php echo date('d M Y', strtotime($p['created_at'])); ?></td>
                        <td>
                            <a href="#" class="btn-view">View</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
                <p class="text-center text-muted">No patients found.</p>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php require_once '../includes/footer.php'; ?>