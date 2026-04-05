<?php
require_once '../config.php';
require_once '../includes/functions.php';

// Check if patient is logged in
if (!isset($_SESSION['patient_id'])) {
    setFlashMessage('Please login to view appointments', 'danger');
    redirect(SITE_URL . 'patient/login.php');
}

$page_title = 'My Appointments - COVID Care System';
require_once '../includes/header.php';
require_once '../includes/navbar.php';

$patient_id = $_SESSION['patient_id'];

// Handle cancellation
if (isset($_GET['cancel']) && is_numeric($_GET['cancel'])) {
    $appointment_id = (int)$_GET['cancel'];
    
    // Verify this appointment belongs to the patient
    $check_query = "SELECT appointment_id FROM appointments WHERE appointment_id = $appointment_id AND patient_id = $patient_id";
    $check_result = mysqli_query($conn, $check_query);
    
    if (mysqli_num_rows($check_result) > 0) {
        $update_query = "UPDATE appointments SET status = 'cancelled' WHERE appointment_id = $appointment_id";
        if (mysqli_query($conn, $update_query)) {
            setFlashMessage('Appointment cancelled successfully.', 'success');
        } else {
            setFlashMessage('Error cancelling appointment.', 'danger');
        }
    }
    redirect(SITE_URL . 'patient/my_appointments.php');
}

// Get filter
$status_filter = isset($_GET['status']) ? sanitize($_GET['status']) : '';

// Build query
$where = "a.patient_id = $patient_id";
if (!empty($status_filter)) {
    $where .= " AND a.status = '$status_filter'";
}

$query = "SELECT a.*, h.name as hospital_name, h.address, h.city, h.phone 
          FROM appointments a
          JOIN hospitals h ON a.hospital_id = h.hospital_id
          WHERE $where
          ORDER BY a.appointment_date DESC, a.appointment_time DESC";

$result = mysqli_query($conn, $query);
$appointments = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $appointments[] = $row;
    }
}

// Get counts for tabs
$counts_query = "SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
    SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved,
    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled
    FROM appointments WHERE patient_id = $patient_id";
$counts_result = mysqli_query($conn, $counts_query);
$counts = mysqli_fetch_assoc($counts_result);
?>

<style>
    /* ===== MY APPOINTMENTS STYLES ===== */
    .page-header {
        background: linear-gradient(135deg, #0d6efd, #0b5ed7);
        padding: 60px 0;
        margin-bottom: 50px;
        color: white;
        text-align: center;
    }
    
    .page-header h1 {
        font-size: 42px;
        font-weight: 700;
        margin-bottom: 15px;
    }
    
    .page-header p {
        font-size: 18px;
        max-width: 700px;
        margin: 0 auto;
        opacity: 0.9;
    }
    
    .filter-tabs {
        display: flex;
        gap: 10px;
        margin-bottom: 30px;
        flex-wrap: wrap;
        background: white;
        padding: 20px;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        border: 1px solid #eee;
    }
    
    .filter-tab {
        padding: 10px 25px;
        background: #f8f9fa;
        border: 1px solid #eee;
        border-radius: 50px;
        color: #6c757d;
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    
    .filter-tab:hover {
        background: #e9ecef;
        color: #333;
    }
    
    .filter-tab.active {
        background: #0d6efd;
        color: white;
        border-color: #0d6efd;
    }
    
    .filter-tab .count {
        background: rgba(0,0,0,0.1);
        padding: 2px 8px;
        border-radius: 50px;
        font-size: 12px;
    }
    
    .filter-tab.active .count {
        background: rgba(255,255,255,0.2);
    }
    
    .appointments-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 20px;
        margin-bottom: 40px;
    }
    
    .appointment-card {
        background: white;
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        border: 1px solid #eee;
        transition: all 0.3s ease;
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
    }
    
    .appointment-card:hover {
        box-shadow: 0 15px 30px rgba(13,110,253,0.15);
        border-color: #0d6efd;
    }
    
    .appointment-date {
        min-width: 120px;
        text-align: center;
        padding: 15px;
        background: #f8f9fa;
        border-radius: 12px;
        border: 1px solid #eee;
    }
    
    .appointment-date .day {
        font-size: 32px;
        font-weight: 800;
        color: #0d6efd;
        line-height: 1;
        margin-bottom: 5px;
    }
    
    .appointment-date .month {
        font-size: 16px;
        font-weight: 600;
        color: #6c757d;
        text-transform: uppercase;
    }
    
    .appointment-date .time {
        font-size: 14px;
        color: #6c757d;
        margin-top: 5px;
        padding-top: 5px;
        border-top: 1px solid #dee2e6;
    }
    
    .appointment-details {
        flex: 1;
    }
    
    .appointment-hospital {
        font-size: 20px;
        font-weight: 700;
        color: #333;
        margin-bottom: 10px;
    }
    
    .appointment-hospital i {
        color: #0d6efd;
        margin-right: 8px;
    }
    
    .appointment-info {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 10px;
        margin-bottom: 15px;
    }
    
    .info-item {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #6c757d;
        font-size: 14px;
    }
    
    .info-item i {
        color: #0d6efd;
        width: 16px;
    }
    
    .appointment-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
        padding-top: 15px;
        border-top: 1px solid #eee;
    }
    
    .status-badge {
        display: inline-block;
        padding: 8px 20px;
        border-radius: 50px;
        font-size: 14px;
        font-weight: 600;
    }
    
    .status-pending {
        background: #fff3cd;
        color: #856404;
    }
    
    .status-approved {
        background: #d1e7dd;
        color: #0f5132;
    }
    
    .status-completed {
        background: #cfe2ff;
        color: #084298;
    }
    
    .status-cancelled {
        background: #f8d7da;
        color: #842029;
    }
    
    .action-buttons {
        display: flex;
        gap: 10px;
    }
    
    .btn-action {
        padding: 8px 20px;
        border-radius: 50px;
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    
    .btn-view {
        background: #0d6efd;
        color: white;
    }
    
    .btn-view:hover {
        background: #0b5ed7;
        transform: translateY(-2px);
    }
    
    .btn-cancel {
        background: #dc3545;
        color: white;
    }
    
    .btn-cancel:hover {
        background: #c82333;
        transform: translateY(-2px);
    }
    
    .btn-reschedule {
        background: #ffc107;
        color: #333;
    }
    
    .btn-reschedule:hover {
        background: #e0a800;
        transform: translateY(-2px);
    }
    
    .empty-state {
        text-align: center;
        padding: 80px 20px;
        background: white;
        border-radius: 15px;
        border: 1px solid #eee;
    }
    
    .empty-state i {
        font-size: 80px;
        color: #dee2e6;
        margin-bottom: 20px;
    }
    
    .empty-state h3 {
        font-size: 24px;
        font-weight: 700;
        color: #333;
        margin-bottom: 10px;
    }
    
    .empty-state p {
        color: #6c757d;
        margin-bottom: 25px;
    }
    
    .empty-btn {
        display: inline-block;
        padding: 12px 35px;
        background: #0d6efd;
        color: white;
        text-decoration: none;
        border-radius: 50px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .empty-btn:hover {
        background: #0b5ed7;
        transform: translateY(-2px);
    }
    
    @media (max-width: 768px) {
        .appointment-card {
            flex-direction: column;
        }
        
        .appointment-date {
            width: 100%;
        }
        
        .appointment-footer {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .action-buttons {
            width: 100%;
        }
        
        .btn-action {
            flex: 1;
            text-align: center;
            justify-content: center;
        }
    }
</style>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <h1>My Appointments</h1>
        <p>View and manage all your appointments</p>
    </div>
</section>

<!-- Filter Tabs -->
<section class="container">
    <div class="filter-tabs">
        <a href="?status=" class="filter-tab <?php echo empty($status_filter) ? 'active' : ''; ?>">
            <i class="fas fa-list"></i> All
            <span class="count"><?php echo $counts['total']; ?></span>
        </a>
        <a href="?status=pending" class="filter-tab <?php echo $status_filter == 'pending' ? 'active' : ''; ?>">
            <i class="fas fa-hourglass-half"></i> Pending
            <span class="count"><?php echo $counts['pending']; ?></span>
        </a>
        <a href="?status=approved" class="filter-tab <?php echo $status_filter == 'approved' ? 'active' : ''; ?>">
            <i class="fas fa-check-circle"></i> Approved
            <span class="count"><?php echo $counts['approved']; ?></span>
        </a>
        <a href="?status=completed" class="filter-tab <?php echo $status_filter == 'completed' ? 'active' : ''; ?>">
            <i class="fas fa-check-double"></i> Completed
            <span class="count"><?php echo $counts['completed']; ?></span>
        </a>
        <a href="?status=cancelled" class="filter-tab <?php echo $status_filter == 'cancelled' ? 'active' : ''; ?>">
            <i class="fas fa-times-circle"></i> Cancelled
            <span class="count"><?php echo $counts['cancelled']; ?></span>
        </a>
    </div>
    
    <?php echo showFlashMessage(); ?>
    
    <?php if (!empty($appointments)): ?>
    <div class="appointments-grid">
        <?php foreach ($appointments as $apt): ?>
        <div class="appointment-card">
            <div class="appointment-date">
                <div class="day"><?php echo date('d', strtotime($apt['appointment_date'])); ?></div>
                <div class="month"><?php echo date('M Y', strtotime($apt['appointment_date'])); ?></div>
                <div class="time"><?php echo date('h:i A', strtotime($apt['appointment_time'])); ?></div>
            </div>
            
            <div class="appointment-details">
                <div class="appointment-hospital">
                    <i class="fas fa-hospital"></i>
                    <?php echo $apt['hospital_name']; ?>
                </div>
                
                <div class="appointment-info">
                    <div class="info-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <?php echo $apt['address']; ?>, <?php echo $apt['city']; ?>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-phone"></i>
                        <?php echo formatPhone($apt['phone']); ?>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-syringe"></i>
                        Type: <?php echo ucfirst($apt['appointment_type']); ?>
                    </div>
                </div>
                
                <div class="appointment-footer">
                    <div>
                        <?php 
                        $status_class = '';
                        if ($apt['status'] == 'pending') {
                            $status_class = 'status-pending';
                        } elseif ($apt['status'] == 'approved') {
                            $status_class = 'status-approved';
                        } elseif ($apt['status'] == 'completed') {
                            $status_class = 'status-completed';
                        } elseif ($apt['status'] == 'cancelled') {
                            $status_class = 'status-cancelled';
                        }
                        ?>
                        <span class="status-badge <?php echo $status_class; ?>">
                            <i class="fas fa-circle me-1" style="font-size: 8px;"></i>
                            <?php echo ucfirst($apt['status']); ?>
                        </span>
                    </div>
                    
                    <div class="action-buttons">
                        <a href="#" class="btn-action btn-view">
                            <i class="fas fa-eye"></i> View
                        </a>
                        
                        <?php if ($apt['status'] == 'pending'): ?>
                            <a href="?cancel=<?php echo $apt['appointment_id']; ?>" class="btn-action btn-cancel" 
                               onclick="return confirm('Are you sure you want to cancel this appointment?')">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        <?php endif; ?>
                        
                        <?php if ($apt['status'] == 'approved'): ?>
                            <a href="#" class="btn-action btn-reschedule">
                                <i class="fas fa-clock"></i> Reschedule
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    
    <?php else: ?>
    <div class="empty-state">
        <i class="fas fa-calendar-times"></i>
        <h3>No Appointments Found</h3>
        <p>You haven't booked any appointments yet.</p>
        <a href="<?php echo SITE_URL; ?>patient/book_appointment.php" class="empty-btn">
            <i class="fas fa-calendar-plus me-2"></i>Book Appointment
        </a>
    </div>
    <?php endif; ?>
</section>

<?php
require_once '../includes/footer.php';
?>