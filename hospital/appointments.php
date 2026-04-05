<?php
session_start();
require_once '../config.php';
require_once '../includes/functions.php';

// Check if hospital is logged in
if (!isset($_SESSION['hospital_id'])) {
    setFlashMessage('Please login to view appointments', 'danger');
    redirect(SITE_URL . 'hospital/login.php');
}

$page_title = 'Appointments - Hospital Panel';
require_once '../includes/header.php';
require_once '../includes/navbar.php';

$hospital_id = $_SESSION['hospital_id'];
$status = isset($_GET['status']) ? $_GET['status'] : '';

// Get appointments
$appointments = getHospitalAppointments($conn, $hospital_id, $status);
?>

<style>
    .page-header { background: linear-gradient(135deg, #0d6efd, #0b5ed7); padding: 60px 0; margin-bottom: 40px; color: white; text-align: center; }
    .page-header h1 { font-size: 42px; font-weight: 700; margin-bottom: 10px; }
    .filter-bar { display: flex; gap: 15px; margin-bottom: 30px; flex-wrap: wrap; }
    .filter-bar a { padding: 10px 25px; background: white; border: 1px solid #eee; border-radius: 50px; color: #6c757d; text-decoration: none; }
    .filter-bar a.active { background: #0d6efd; color: white; border-color: #0d6efd; }
    .card { background: white; border-radius: 15px; padding: 25px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); border: 1px solid #eee; }
    table { width: 100%; border-collapse: collapse; }
    th { text-align: left; padding: 12px; background: #f8f9fa; color: #333; font-weight: 600; border-bottom: 2px solid #dee2e6; }
    td { padding: 12px; color: #6c757d; border-bottom: 1px solid #eee; }
    .badge { display: inline-block; padding: 5px 12px; border-radius: 50px; font-size: 12px; font-weight: 600; }
    .badge-pending { background: #fff3cd; color: #856404; }
    .badge-approved { background: #d1e7dd; color: #0f5132; }
    .badge-completed { background: #cfe2ff; color: #084298; }
    .badge-cancelled { background: #f8d7da; color: #842029; }
    .btn { padding: 6px 15px; border-radius: 5px; text-decoration: none; font-size: 13px; display: inline-block; }
    .btn-primary { background: #0d6efd; color: white; }
    .btn-success { background: #28a745; color: white; }
    .btn-warning { background: #ffc107; color: #333; }
    @media (max-width: 768px) { table { font-size: 14px; } }
</style>

<section class="page-header">
    <div class="container">
        <h1>Appointments</h1>
        <p>Manage all patient appointments</p>
    </div>
</section>

<main class="container" style="padding-bottom: 50px;">
    <div class="filter-bar">
        <a href="?status=" class="<?php echo empty($status) ? 'active' : ''; ?>">All</a>
        <a href="?status=pending" class="<?php echo $status == 'pending' ? 'active' : ''; ?>">Pending</a>
        <a href="?status=approved" class="<?php echo $status == 'approved' ? 'active' : ''; ?>">Approved</a>
        <a href="?status=completed" class="<?php echo $status == 'completed' ? 'active' : ''; ?>">Completed</a>
        <a href="?status=cancelled" class="<?php echo $status == 'cancelled' ? 'active' : ''; ?>">Cancelled</a>
    </div>
    
    <div class="card">
        <?php if (!empty($appointments)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Patient</th>
                        <th>Phone</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($appointments as $apt): ?>
                    <tr>
                        <td><?php echo date('d M Y', strtotime($apt['appointment_date'])); ?></td>
                        <td><?php echo date('h:i A', strtotime($apt['appointment_time'])); ?></td>
                        <td><?php echo htmlspecialchars($apt['patient_name']); ?></td>
                        <td><?php echo htmlspecialchars($apt['phone']); ?></td>
                        <td><?php echo ucfirst($apt['appointment_type']); ?></td>
                        <td>
                            <?php
                            $class = '';
                            if ($apt['status'] == 'pending') $class = 'badge-pending';
                            elseif ($apt['status'] == 'approved') $class = 'badge-approved';
                            elseif ($apt['status'] == 'completed') $class = 'badge-completed';
                            elseif ($apt['status'] == 'cancelled') $class = 'badge-cancelled';
                            ?>
                            <span class="badge <?php echo $class; ?>"><?php echo ucfirst($apt['status']); ?></span>
                        </td>
                        <td>
                            <?php if ($apt['status'] == 'pending'): ?>
                                <a href="update_test.php?appointment_id=<?php echo $apt['appointment_id']; ?>" class="btn btn-primary">Update</a>
                            <?php elseif ($apt['status'] == 'approved'): ?>
                                <a href="#" class="btn btn-success">Complete</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-center text-muted">No appointments found.</p>
        <?php endif; ?>
    </div>
</main>

<?php require_once '../includes/footer.php'; ?>