<?php
session_start();
require_once '../config.php';
require_once '../includes/functions.php';

// Check if hospital is logged in
if (!isset($_SESSION['hospital_id'])) {
    setFlashMessage('Please login to access dashboard', 'danger');
    redirect(SITE_URL . 'hospital/login.php');
}

$page_title = 'Hospital Dashboard - COVID Care System';
require_once '../includes/header.php';
require_once '../includes/navbar.php';

$hospital_id = $_SESSION['hospital_id'];

// Get hospital details
$hospital = getRecord($conn, 'hospitals', 'hospital_id', $hospital_id);

// Get statistics
$stats = getHospitalStats($conn, $hospital_id);

// Get recent appointments
$appointments = getHospitalAppointments($conn, $hospital_id, null, null, 5);
?>

<style>
    /* ===== NEW PERFECT HOSPITAL DASHBOARD ===== */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    
    body {
        font-family: 'Poppins', 'Segoe UI', sans-serif;
        background: #f4f7fc;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }
    
    .main-content {
        flex: 1;
        padding: 30px 0 50px;
    }
    
    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
        width: 100%;
    }
    
    /* ===== WELCOME CARD ===== */
    .welcome-card {
        background: linear-gradient(135deg, #0d6efd, #0b5ed7);
        border-radius: 20px;
        padding: 30px;
        margin-bottom: 30px;
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
        box-shadow: 0 10px 30px rgba(13,110,253,0.3);
    }
    
    .welcome-text h1 {
        font-size: 28px;
        font-weight: 600;
        margin-bottom: 8px;
    }
    
    .welcome-text p {
        font-size: 15px;
        opacity: 0.9;
    }
    
    .date-box {
        background: rgba(255,255,255,0.2);
        padding: 12px 25px;
        border-radius: 50px;
        font-size: 15px;
        display: flex;
        align-items: center;
        gap: 10px;
        backdrop-filter: blur(5px);
        border: 1px solid rgba(255,255,255,0.2);
    }
    
    .date-box i {
        color: #ffc107;
    }
    
    /* ===== STATS CARDS ===== */
    .stats-row {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-bottom: 40px;
    }
    
    .stat-item {
        background: white;
        border-radius: 16px;
        padding: 25px 20px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.03);
        border: 1px solid #eef2f6;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 15px;
    }
    
    .stat-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(13,110,253,0.1);
        border-color: #0d6efd;
    }
    
    .stat-icon {
        width: 60px;
        height: 60px;
        background: #e7f1ff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #0d6efd;
        font-size: 26px;
    }
    
    .stat-content h3 {
        font-size: 28px;
        font-weight: 700;
        color: #1a2639;
        margin-bottom: 5px;
    }
    
    .stat-content p {
        color: #64748b;
        font-size: 14px;
        font-weight: 500;
    }
    
    /* ===== SECTION TITLE ===== */
    .section-title {
        font-size: 22px;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .section-title i {
        color: #0d6efd;
        background: #e7f1ff;
        padding: 10px;
        border-radius: 12px;
        font-size: 18px;
    }
    
    /* ===== QUICK ACTIONS ===== */
    .actions-row {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-bottom: 40px;
    }
    
    .action-box {
        background: white;
        border-radius: 16px;
        padding: 25px 20px;
        text-align: center;
        text-decoration: none;
        transition: all 0.3s ease;
        border: 1px solid #eef2f6;
        box-shadow: 0 5px 15px rgba(0,0,0,0.02);
    }
    
    .action-box:hover {
        background: #0d6efd;
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(13,110,253,0.2);
    }
    
    .action-box i {
        font-size: 30px;
        color: #0d6efd;
        margin-bottom: 12px;
        transition: all 0.3s ease;
    }
    
    .action-box:hover i {
        color: white;
    }
    
    .action-box h4 {
        font-size: 16px;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 5px;
        transition: all 0.3s ease;
    }
    
    .action-box:hover h4 {
        color: white;
    }
    
    .action-box p {
        font-size: 13px;
        color: #64748b;
        margin: 0;
        transition: all 0.3s ease;
    }
    
    .action-box:hover p {
        color: rgba(255,255,255,0.9);
    }
    
    /* ===== CARDS ===== */
    .cards-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 25px;
        margin-bottom: 30px;
    }
    
    .info-card {
        background: white;
        border-radius: 20px;
        padding: 25px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.03);
        border: 1px solid #eef2f6;
    }
    
    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid #f1f5f9;
    }
    
    .card-header h3 {
        font-size: 18px;
        font-weight: 600;
        color: #1e293b;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .card-header h3 i {
        color: #0d6efd;
    }
    
    .view-link {
        color: #0d6efd;
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    
    .view-link:hover {
        color: #0b5ed7;
        gap: 8px;
    }
    
    /* ===== TABLES ===== */
    .table-responsive {
        overflow-x: auto;
    }
    
    table {
        width: 100%;
        border-collapse: collapse;
    }
    
    th {
        text-align: left;
        padding: 12px 10px;
        background: #f8fafc;
        color: #475569;
        font-size: 13px;
        font-weight: 600;
        border-bottom: 2px solid #e2e8f0;
    }
    
    td {
        padding: 12px 10px;
        color: #334155;
        font-size: 14px;
        border-bottom: 1px solid #eef2f6;
    }
    
    .badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 50px;
        font-size: 12px;
        font-weight: 500;
    }
    
    .badge-pending {
        background: #fff3cd;
        color: #856404;
    }
    
    .badge-approved {
        background: #d1e7dd;
        color: #0f5132;
    }
    
    .badge-completed {
        background: #cfe2ff;
        color: #084298;
    }
    
    .badge-success {
        background: #d1e7dd;
        color: #0f5132;
    }
    
    /* ===== HOSPITAL INFO ===== */
    .info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
        margin-bottom: 20px;
    }
    
    .info-row {
        padding: 12px;
        background: #f8fafc;
        border-radius: 12px;
    }
    
    .info-label {
        font-size: 12px;
        color: #64748b;
        margin-bottom: 5px;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    
    .info-label i {
        color: #0d6efd;
    }
    
    .info-value {
        font-size: 15px;
        font-weight: 600;
        color: #1e293b;
    }
    
    .status-badge {
        display: inline-block;
        padding: 4px 12px;
        background: #d1e7dd;
        color: #0f5132;
        border-radius: 50px;
        font-size: 12px;
    }
    
    /* ===== EMPTY STATE ===== */
    .empty-box {
        text-align: center;
        padding: 40px 20px;
    }
    
    .empty-box i {
        font-size: 45px;
        color: #cbd5e1;
        margin-bottom: 12px;
    }
    
    .empty-box p {
        color: #64748b;
        font-size: 14px;
    }
    
    /* ===== RESPONSIVE ===== */
    @media (max-width: 992px) {
        .stats-row,
        .actions-row {
            grid-template-columns: repeat(2, 1fr);
        }
        
        .cards-row {
            grid-template-columns: 1fr;
        }
    }
    
    @media (max-width: 768px) {
        .welcome-card {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .stats-row,
        .actions-row {
            grid-template-columns: 1fr;
        }
        
        .info-grid {
            grid-template-columns: 1fr;
        }
        
        .stat-item {
            padding: 20px;
        }
    }
</style>

<!-- Main Content -->
<main class="main-content">
    <div class="container">
        
        <!-- Welcome Card -->
        <div class="welcome-card">
            <div class="welcome-text">
                <h1>Welcome back, <?php echo htmlspecialchars($hospital['name']); ?>! 👋</h1>
                <p>Here's what's happening with your hospital today</p>
            </div>
            <div class="date-box">
                <i class="fas fa-calendar-alt"></i>
                <?php echo date('l, d M Y'); ?>
            </div>
        </div>
        
        <!-- Statistics Cards -->
        <div class="stats-row">
            <div class="stat-item">
                <div class="stat-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-content">
                    <h3><?php echo $stats['total_appointments']; ?></h3>
                    <p>Total Appointments</p>
                </div>
            </div>
            
            <div class="stat-item">
                <div class="stat-icon">
                    <i class="fas fa-hourglass-half"></i>
                </div>
                <div class="stat-content">
                    <h3><?php echo $stats['pending_appointments']; ?></h3>
                    <p>Pending</p>
                </div>
            </div>
            
            <div class="stat-item">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-content">
                    <h3><?php echo $stats['total_patients']; ?></h3>
                    <p>Total Patients</p>
                </div>
            </div>
            
            <div class="stat-item">
                <div class="stat-icon">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <div class="stat-content">
                    <h3><?php echo $stats['today_appointments']; ?></h3>
                    <p>Today's Appointments</p>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="section-title">
            <i class="fas fa-bolt"></i>
            <span>Quick Actions</span>
        </div>
        
        <div class="actions-row">
            <a href="<?php echo SITE_URL; ?>hospital/appointments.php" class="action-box">
                <i class="fas fa-calendar-alt"></i>
                <h4>All Appointments</h4>
                <p>View all appointments</p>
            </a>
            
    
    <!-- 🔥 NEW: PENDING APPOINTMENTS LINK 🔥 -->
    <a href="<?php echo SITE_URL; ?>hospital/pending_appointments.php" class="action-box">
        <i class="fas fa-hourglass-half"></i>
        <h4>Pending Appointments</h4>
        <p>Review & manage pending</p>
    </a>

    <a href="<?php echo SITE_URL; ?>hospital/patients.php" class="action-box">
        <i class="fas fa-users"></i>
        <h4>View Patients</h4>
        <p>Manage your patients</p>
    </a>
    
    <a href="<?php echo SITE_URL; ?>hospital/update_test.php" class="action-box">
        <i class="fas fa-flask"></i>
        <h4>Update Test</h4>
        <p>Add test results</p>
    </a>
    
    <a href="<?php echo SITE_URL; ?>hospital/update_vaccination.php" class="action-box">
        <i class="fas fa-syringe"></i>
        <h4>Update Vaccination</h4>
        <p>Record vaccination</p>
    </a>
</div>

        
        <!-- Recent Appointments & Hospital Info -->
        <div class="cards-row">
            <!-- Recent Appointments Card -->
            <div class="info-card">
                <div class="card-header">
                    <h3><i class="fas fa-clock"></i> Recent Appointments</h3>
                    <a href="<?php echo SITE_URL; ?>hospital/appointments.php" class="view-link">
                        View All <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                
                <?php if (!empty($appointments)): ?>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Patient</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_slice($appointments, 0, 4) as $apt): ?>
                                <tr>
                                    <td><?php echo date('d M', strtotime($apt['appointment_date'])); ?></td>
                                    <td><?php echo htmlspecialchars($apt['patient_name']); ?></td>
                                    <td><?php echo ucfirst($apt['appointment_type']); ?></td>
                                    <td>
                                        <?php
                                        $class = '';
                                        if ($apt['status'] == 'pending') $class = 'badge-pending';
                                        elseif ($apt['status'] == 'approved') $class = 'badge-approved';
                                        elseif ($apt['status'] == 'completed') $class = 'badge-completed';
                                        ?>
                                        <span class="badge <?php echo $class; ?>"><?php echo ucfirst($apt['status']); ?></span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty-box">
                        <i class="fas fa-calendar-times"></i>
                        <p>No recent appointments</p>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Hospital Info Card -->
            <div class="info-card">
                <div class="card-header">
                    <h3><i class="fas fa-hospital"></i> Hospital Information</h3>
                    <a href="<?php echo SITE_URL; ?>hospital/profile.php" class="view-link">
                        View Profile <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                
                <div class="info-grid">
                    <div class="info-row">
                        <div class="info-label">
                            <i class="fas fa-building"></i> Hospital Name
                        </div>
                        <div class="info-value"><?php echo htmlspecialchars($hospital['name']); ?></div>
                    </div>
                    
                    <div class="info-row">
                        <div class="info-label">
                            <i class="fas fa-envelope"></i> Email
                        </div>
                        <div class="info-value"><?php echo htmlspecialchars($hospital['email']); ?></div>
                    </div>
                    
                    <div class="info-row">
                        <div class="info-label">
                            <i class="fas fa-phone"></i> Phone
                        </div>
                        <div class="info-value"><?php echo htmlspecialchars($hospital['phone']); ?></div>
                    </div>
                    
                    <div class="info-row">
                        <div class="info-label">
                            <i class="fas fa-map-marker-alt"></i> City
                        </div>
                        <div class="info-value"><?php echo htmlspecialchars($hospital['city']); ?></div>
                    </div>
                    
                    <div class="info-row" style="grid-column: span 2;">
                        <div class="info-label">
                            <i class="fas fa-map-pin"></i> Address
                        </div>
                        <div class="info-value"><?php echo htmlspecialchars($hospital['address']); ?></div>
                    </div>
                    
                    <div class="info-row">
                        <div class="info-label">
                            <i class="fas fa-id-card"></i> Reg No.
                        </div>
                        <div class="info-value"><?php echo htmlspecialchars($hospital['registration_no']); ?></div>
                    </div>
                    
                    <div class="info-row">
                        <div class="info-label">
                            <i class="fas fa-check-circle"></i> Status
                        </div>
                        <div class="info-value">
                            <span class="status-badge"><?php echo ucfirst($hospital['status']); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        
        <!-- Quick Stats Summary -->
        <div class="info-card" style="margin-top: 10px;">
            <div class="card-header">
                <h3><i class="fas fa-chart-simple"></i> Appointment Summary</h3>
            </div>
            
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; padding: 10px 0;">
                <div style="text-align: center;">
                    <div style="font-size: 24px; font-weight: 700; color: #0d6efd;"><?php echo $stats['pending_appointments']; ?></div>
                    <div style="font-size: 13px; color: #64748b;">Pending</div>
                </div>
                <div style="text-align: center;">
                    <div style="font-size: 24px; font-weight: 700; color: #28a745;"><?php echo $stats['approved_appointments']; ?></div>
                    <div style="font-size: 13px; color: #64748b;">Approved</div>
                </div>
                <div style="text-align: center;">
                    <div style="font-size: 24px; font-weight: 700; color: #0d6efd;"><?php echo $stats['completed_appointments']; ?></div>
                    <div style="font-size: 13px; color: #64748b;">Completed</div>
                </div>
            </div>
        </div>
        
    </div>
</main>

<?php require_once '../includes/footer.php'; ?>