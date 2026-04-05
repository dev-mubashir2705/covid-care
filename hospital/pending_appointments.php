<?php
session_start();
require_once '../config.php';
require_once '../includes/functions.php';

// Check if hospital is logged in
if (!isset($_SESSION['hospital_id'])) {
    setFlashMessage('Please login to view pending appointments', 'danger');
    redirect(SITE_URL . 'hospital/login.php');
}

$page_title = 'Pending Appointments - Hospital Panel';
require_once '../includes/header.php';
require_once '../includes/navbar.php';

$hospital_id = $_SESSION['hospital_id'];

// Handle approve/reject actions
if (isset($_GET['approve'])) {
    $appointment_id = (int)$_GET['approve'];
    $query = "UPDATE appointments SET status = 'approved' WHERE appointment_id = $appointment_id AND hospital_id = $hospital_id";
    if (mysqli_query($conn, $query)) {
        setFlashMessage('Appointment approved successfully!', 'success');
    } else {
        setFlashMessage('Error approving appointment!', 'danger');
    }
    redirect('pending_appointments.php');
}

if (isset($_GET['reject'])) {
    $appointment_id = (int)$_GET['reject'];
    $query = "UPDATE appointments SET status = 'rejected' WHERE appointment_id = $appointment_id AND hospital_id = $hospital_id";
    if (mysqli_query($conn, $query)) {
        setFlashMessage('Appointment rejected!', 'warning');
    } else {
        setFlashMessage('Error rejecting appointment!', 'danger');
    }
    redirect('pending_appointments.php');
}

// Get pending appointments
$query = "SELECT a.*, p.name as patient_name, p.phone, p.email, p.dob, p.gender
          FROM appointments a
          JOIN patients p ON a.patient_id = p.patient_id
          WHERE a.hospital_id = $hospital_id 
          AND a.status = 'pending'
          ORDER BY a.appointment_date ASC, a.appointment_time ASC";

$result = mysqli_query($conn, $query);
$appointments = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $appointments[] = $row;
    }
}

// Get counts
$total_pending = count($appointments);
$today_pending = 0;
$tomorrow_pending = 0;

$today = date('Y-m-d');
$tomorrow = date('Y-m-d', strtotime('+1 day'));

foreach ($appointments as $apt) {
    if ($apt['appointment_date'] == $today) {
        $today_pending++;
    }
    if ($apt['appointment_date'] == $tomorrow) {
        $tomorrow_pending++;
    }
}
?>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <h1>Pending Appointments</h1>
        <p>Review and manage pending appointment requests</p>
    </div>
</section>

<!-- Main Content -->
<main class="main-content">
    <div class="container">
        
        <!-- Statistics -->
        <div class="stats-row">
            <div class="stat-box">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $total_pending; ?></h3>
                    <p>Total Pending</p>
                </div>
            </div>
            
            <div class="stat-box">
                <div class="stat-icon">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $today_pending; ?></h3>
                    <p>Today</p>
                </div>
            </div>
            
            <div class="stat-box">
                <div class="stat-icon">
                    <i class="fas fa-calendar-week"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $tomorrow_pending; ?></h3>
                    <p>Tomorrow</p>
                </div>
            </div>
        </div>
        
        <!-- Flash Messages -->
        <?php echo showFlashMessage(); ?>
        
        <!-- Pending Appointments List -->
        <div class="content-card">
            <div class="card-title">
                <i class="fas fa-hourglass-half"></i>
                <h3>Pending Appointment Requests</h3>
            </div>
            
            <?php if (!empty($appointments)): ?>
                <div class="appointments-list">
                    <?php foreach ($appointments as $apt): ?>
                    <div class="appointment-item">
                        <div class="appointment-header">
                            <div class="patient-info">
                                <div class="patient-avatar">
                                    <i class="fas fa-user-circle"></i>
                                </div>
                                <div class="patient-details">
                                    <h4><?php echo htmlspecialchars($apt['patient_name']); ?></h4>
                                    <p><i class="fas fa-phone"></i> <?php echo formatPhone($apt['phone']); ?></p>
                                    <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($apt['email']); ?></p>
                                </div>
                            </div>
                            <div class="appointment-type">
                                <span class="type-badge <?php echo $apt['appointment_type']; ?>">
                                    <?php echo ucfirst($apt['appointment_type']); ?>
                                </span>
                            </div>
                        </div>
                        
                        <div class="appointment-details">
                            <div class="detail-row">
                                <div class="detail-item">
                                    <i class="fas fa-calendar"></i>
                                    <span><?php echo date('l, d M Y', strtotime($apt['appointment_date'])); ?></span>
                                </div>
                                <div class="detail-item">
                                    <i class="fas fa-clock"></i>
                                    <span><?php echo date('h:i A', strtotime($apt['appointment_time'])); ?></span>
                                </div>
                            </div>
                            
                            <?php if ($apt['appointment_type'] == 'vaccination'): ?>
                            <div class="detail-item vaccine-note">
                                <i class="fas fa-syringe"></i>
                                <span>Vaccination appointment</span>
                            </div>
                            <?php else: ?>
                            <div class="detail-item test-note">
                                <i class="fas fa-flask"></i>
                                <span>COVID-19 Test appointment</span>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="action-buttons">
                            <a href="?approve=<?php echo $apt['appointment_id']; ?>" 
                               class="btn-approve" 
                               onclick="return confirm('Approve this appointment?')">
                                <i class="fas fa-check"></i> Approve
                            </a>
                            <a href="?reject=<?php echo $apt['appointment_id']; ?>" 
                               class="btn-reject" 
                               onclick="return confirm('Reject this appointment?')">
                                <i class="fas fa-times"></i> Reject
                            </a>
                            <a href="#" class="btn-view">
                                <i class="fas fa-eye"></i> View
                            </a>
                        </div>
                        
                        <?php if ($apt['appointment_date'] == $today): ?>
                        <div class="urgent-badge">
                            <i class="fas fa-exclamation-circle"></i> Today
                        </div>
                        <?php elseif ($apt['appointment_date'] == $tomorrow): ?>
                        <div class="soon-badge">
                            <i class="fas fa-clock"></i> Tomorrow
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-box">
                    <i class="fas fa-check-circle"></i>
                    <h4>No Pending Appointments</h4>
                    <p>All appointments have been processed.</p>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Quick Tips -->
        <div class="tips-card">
            <div class="tips-header">
                <i class="fas fa-lightbulb"></i>
                <h4>Quick Tips</h4>
            </div>
            <ul class="tips-list">
                <li><i class="fas fa-check-circle"></i> Approve appointments to confirm with patients</li>
                <li><i class="fas fa-times-circle"></i> Reject appointments if you cannot accommodate</li>
                <li><i class="fas fa-calendar-alt"></i> Check today's appointments marked with red badge</li>
                <li><i class="fas fa-clock"></i> Process appointments in a timely manner</li>
            </ul>
        </div>
        
    </div>
</main>

<style>
    /* ===== PENDING APPOINTMENTS STYLES ===== */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: #f8f9fa;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }
    
    .main-content {
        flex: 1;
        padding: 40px 0;
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
        font-size: 42px;
        font-weight: 700;
        margin-bottom: 10px;
    }
    
    .page-header p {
        font-size: 18px;
        opacity: 0.9;
    }
    
    /* Statistics */
    .stats-row {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 25px;
        margin-bottom: 40px;
    }
    
    .stat-box {
        background: white;
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        border: 1px solid #eee;
        display: flex;
        align-items: center;
        gap: 20px;
        transition: all 0.3s ease;
    }
    
    .stat-box:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(13,110,253,0.15);
    }
    
    .stat-icon {
        width: 60px;
        height: 60px;
        background: #e7f1ff;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #0d6efd;
        font-size: 28px;
    }
    
    .stat-info h3 {
        font-size: 32px;
        font-weight: 700;
        color: #333;
        margin-bottom: 5px;
    }
    
    .stat-info p {
        color: #6c757d;
        font-size: 14px;
    }
    
    /* Content Card */
    .content-card {
        background: white;
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        border: 1px solid #eee;
        margin-bottom: 30px;
    }
    
    .card-title {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 25px;
        padding-bottom: 15px;
        border-bottom: 2px solid #f0f0f0;
    }
    
    .card-title i {
        color: #0d6efd;
        font-size: 24px;
    }
    
    .card-title h3 {
        font-size: 22px;
        font-weight: 600;
        color: #333;
        margin: 0;
    }
    
    /* Appointments List */
    .appointments-list {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }
    
    .appointment-item {
        background: #f8f9fa;
        border: 1px solid #eee;
        border-radius: 15px;
        padding: 25px;
        position: relative;
        transition: all 0.3s ease;
    }
    
    .appointment-item:hover {
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        border-color: #0d6efd;
    }
    
    .appointment-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 15px;
    }
    
    .patient-info {
        display: flex;
        gap: 15px;
        flex: 1;
    }
    
    .patient-avatar {
        width: 50px;
        height: 50px;
        background: #e7f1ff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #0d6efd;
        font-size: 24px;
    }
    
    .patient-details h4 {
        font-size: 18px;
        font-weight: 600;
        color: #333;
        margin-bottom: 5px;
    }
    
    .patient-details p {
        color: #6c757d;
        font-size: 13px;
        margin: 2px 0;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    
    .patient-details i {
        color: #0d6efd;
        width: 16px;
    }
    
    .type-badge {
        display: inline-block;
        padding: 6px 15px;
        border-radius: 50px;
        font-size: 13px;
        font-weight: 600;
    }
    
    .type-badge.test {
        background: #e7f1ff;
        color: #0d6efd;
    }
    
    .type-badge.vaccination {
        background: #d1e7dd;
        color: #0f5132;
    }
    
    .appointment-details {
        margin-bottom: 20px;
        padding: 15px;
        background: white;
        border-radius: 10px;
        border: 1px solid #eee;
    }
    
    .detail-row {
        display: flex;
        gap: 30px;
        margin-bottom: 10px;
        flex-wrap: wrap;
    }
    
    .detail-item {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #6c757d;
        font-size: 14px;
    }
    
    .detail-item i {
        color: #0d6efd;
        width: 16px;
    }
    
    .vaccine-note {
        color: #0f5132;
        background: #d1e7dd;
        padding: 8px 12px;
        border-radius: 8px;
        margin-top: 5px;
    }
    
    .test-note {
        color: #0d6efd;
        background: #e7f1ff;
        padding: 8px 12px;
        border-radius: 8px;
        margin-top: 5px;
    }
    
    .action-buttons {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }
    
    .btn-approve,
    .btn-reject,
    .btn-view {
        padding: 10px 20px;
        border-radius: 8px;
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
    }
    
    .btn-approve {
        background: #28a745;
        color: white;
    }
    
    .btn-approve:hover {
        background: #218838;
        transform: translateY(-2px);
    }
    
    .btn-reject {
        background: #dc3545;
        color: white;
    }
    
    .btn-reject:hover {
        background: #c82333;
        transform: translateY(-2px);
    }
    
    .btn-view {
        background: #6c757d;
        color: white;
    }
    
    .btn-view:hover {
        background: #5a6268;
        transform: translateY(-2px);
    }
    
    .urgent-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        background: #dc3545;
        color: white;
        padding: 5px 15px;
        border-radius: 50px;
        font-size: 12px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    
    .soon-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        background: #ffc107;
        color: #333;
        padding: 5px 15px;
        border-radius: 50px;
        font-size: 12px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    
    /* Tips Card */
    .tips-card {
        background: white;
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        border: 1px solid #eee;
    }
    
    .tips-header {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 15px;
    }
    
    .tips-header i {
        color: #ffc107;
        font-size: 24px;
    }
    
    .tips-header h4 {
        font-size: 18px;
        font-weight: 600;
        color: #333;
        margin: 0;
    }
    
    .tips-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .tips-list li {
        color: #6c757d;
        font-size: 14px;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .tips-list li i {
        color: #0d6efd;
        font-size: 14px;
    }
    
    /* Empty Box */
    .empty-box {
        text-align: center;
        padding: 60px 20px;
    }
    
    .empty-box i {
        font-size: 60px;
        color: #28a745;
        margin-bottom: 20px;
    }
    
    .empty-box h4 {
        font-size: 22px;
        font-weight: 600;
        color: #333;
        margin-bottom: 10px;
    }
    
    .empty-box p {
        color: #6c757d;
        font-size: 16px;
    }
    
    /* Responsive */
    @media (max-width: 992px) {
        .stats-row {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    
    @media (max-width: 768px) {
        .stats-row {
            grid-template-columns: 1fr;
        }
        
        .appointment-header {
            flex-direction: column;
        }
        
        .patient-info {
            flex-wrap: wrap;
        }
        
        .detail-row {
            flex-direction: column;
            gap: 10px;
        }
        
        .action-buttons {
            flex-direction: column;
        }
        
        .btn-approve,
        .btn-reject,
        .btn-view {
            width: 100%;
            justify-content: center;
        }
        
        .urgent-badge,
        .soon-badge {
            position: static;
            margin-bottom: 15px;
            display: inline-block;
        }
    }
</style>

<?php require_once '../includes/footer.php'; ?>