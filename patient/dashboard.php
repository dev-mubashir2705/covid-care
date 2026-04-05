<?php
require_once '../config.php';
require_once '../includes/functions.php';

// Check if patient is logged in
if (!isset($_SESSION['patient_id'])) {
    setFlashMessage('Please login to access dashboard', 'danger');
    redirect(SITE_URL . 'patient/login.php');
}

$page_title = 'Patient Dashboard - COVID Care System';
require_once '../includes/header.php';
require_once '../includes/navbar.php';

$patient_id = $_SESSION['patient_id'];

// Get patient details
$patient = getRecord($conn, 'patients', 'patient_id', $patient_id);

// Get patient statistics
$stats = getPatientStats($conn, $patient_id);

// Get recent appointments
$appointments = getPatientAppointments($conn, $patient_id, 5);

// Get test results
$test_results = getPatientTestResults($conn, $patient_id);

// Get vaccination records
$vaccinations = getPatientVaccinations($conn, $patient_id);
?>

<style>
    /* ===== RESET EVERYTHING ===== */
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
    
    /* MAIN CONTENT - YAHI IMPORTANT HAI */
    .main-content {
        flex: 1;
        width: 100%;
        padding: 30px 0 50px 0;
    }
    
    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
        width: 100%;
    }
    
    /* ===== PAGE HEADER ===== */
    .page-header {
        background: linear-gradient(135deg, #0d6efd, #0b5ed7);
        padding: 50px 0;
        margin-bottom: 40px;
        color: white;
        width: 100%;
    }
    
    .page-header .container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
    }
    
    .welcome-text h1 {
        font-size: 36px;
        font-weight: 700;
        margin-bottom: 10px;
    }
    
    .welcome-text p {
        font-size: 16px;
        opacity: 0.9;
    }
    
    .date-badge {
        background: rgba(255,255,255,0.2);
        padding: 10px 20px;
        border-radius: 50px;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .date-badge i {
        color: #ffc107;
    }
    
    /* ===== STATS CARDS ===== */
    .stats-row {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 25px;
        margin-bottom: 40px;
    }
    
    .stat-box {
        background: white;
        border-radius: 15px;
        padding: 25px 20px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        border: 1px solid #eee;
        transition: all 0.3s ease;
        text-align: center;
    }
    
    .stat-box:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(13,110,253,0.15);
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
        margin: 0 auto 15px;
        color: #0d6efd;
        font-size: 28px;
    }
    
    .stat-number {
        font-size: 30px;
        font-weight: 700;
        color: #333;
        margin-bottom: 5px;
    }
    
    .stat-label {
        color: #6c757d;
        font-size: 14px;
        font-weight: 500;
    }
    
    /* ===== SECTION TITLE ===== */
    .section-title {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 25px;
    }
    
    .section-icon {
        width: 45px;
        height: 45px;
        background: #e7f1ff;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #0d6efd;
        font-size: 22px;
    }
    
    .section-text h2 {
        font-size: 24px;
        font-weight: 700;
        color: #333;
        margin-bottom: 5px;
    }
    
    .section-text p {
        color: #6c757d;
        font-size: 14px;
    }
    
    /* ===== QUICK ACTIONS ===== */
    .actions-row {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 25px;
        margin-bottom: 40px;
    }
    
    .action-box {
        background: white;
        border-radius: 15px;
        padding: 25px 20px;
        text-align: center;
        text-decoration: none;
        transition: all 0.3s ease;
        border: 1px solid #eee;
        border-bottom: 3px solid #0d6efd;
        display: block;
    }
    
    .action-box:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(13,110,253,0.15);
        background: #f8f9fa;
    }
    
    .action-box i {
        font-size: 35px;
        color: #0d6efd;
        margin-bottom: 12px;
    }
    
    .action-box h4 {
        font-size: 16px;
        font-weight: 600;
        color: #333;
        margin-bottom: 5px;
    }
    
    .action-box p {
        color: #6c757d;
        font-size: 12px;
        margin: 0;
    }
    
    /* ===== CONTENT CARDS ===== */
    .content-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 25px;
        margin-bottom: 30px;
    }
    
    .content-card {
        background: white;
        border-radius: 15px;
        padding: 20px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        border: 1px solid #eee;
    }
    
    .card-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 2px solid #f0f0f0;
    }
    
    .card-head h3 {
        font-size: 18px;
        font-weight: 600;
        color: #333;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .card-head h3 i {
        color: #0d6efd;
    }
    
    .view-link {
        color: #0d6efd;
        text-decoration: none;
        font-size: 13px;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 3px;
    }
    
    .view-link:hover {
        color: #0b5ed7;
        gap: 5px;
    }
    
    /* ===== TABLES ===== */
    .table-wrap {
        overflow-x: auto;
    }
    
    table {
        width: 100%;
        border-collapse: collapse;
    }
    
    th {
        text-align: left;
        padding: 10px;
        background: #f8f9fa;
        color: #333;
        font-size: 13px;
        font-weight: 600;
        border-bottom: 2px solid #dee2e6;
    }
    
    td {
        padding: 10px;
        color: #6c757d;
        font-size: 13px;
        border-bottom: 1px solid #eee;
    }
    
    .status-badge {
        display: inline-block;
        padding: 3px 8px;
        border-radius: 50px;
        font-size: 11px;
        font-weight: 600;
    }
    
    .status-pending { background: #fff3cd; color: #856404; }
    .status-approved { background: #d1e7dd; color: #0f5132; }
    .status-completed { background: #cfe2ff; color: #084298; }
    .status-cancelled { background: #f8d7da; color: #842029; }
    .status-info { background: #e7f1ff; color: #0d6efd; }
    .status-success { background: #d1e7dd; color: #0f5132; }
    .status-danger { background: #f8d7da; color: #842029; }
    
    /* ===== EMPTY STATE ===== */
    .empty-box {
        text-align: center;
        padding: 30px 20px;
    }
    
    .empty-box i {
        font-size: 40px;
        color: #dee2e6;
        margin-bottom: 10px;
    }
    
    .empty-box p {
        color: #6c757d;
        margin-bottom: 15px;
        font-size: 14px;
    }
    
    .empty-btn {
        display: inline-block;
        padding: 6px 15px;
        background: #0d6efd;
        color: white;
        text-decoration: none;
        border-radius: 50px;
        font-size: 12px;
        font-weight: 600;
    }
    
    .empty-btn:hover {
        background: #0b5ed7;
    }
    
    /* ===== PROFILE CARD ===== */
    .profile-box {
        background: white;
        border-radius: 15px;
        padding: 20px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        border: 1px solid #eee;
        height: 100%;
    }
    
    .profile-flex {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 15px;
    }
    
    .profile-avatar {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #0d6efd, #0b5ed7);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 28px;
    }
    
    .profile-info h3 {
        font-size: 18px;
        font-weight: 700;
        color: #333;
        margin-bottom: 5px;
    }
    
    .profile-info p {
        color: #6c757d;
        font-size: 13px;
        margin: 2px 0;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    
    .profile-info i {
        color: #0d6efd;
        width: 16px;
    }
    
    .profile-link {
        text-align: right;
        padding-top: 10px;
        border-top: 1px solid #eee;
    }
    
    .profile-link a {
        color: #0d6efd;
        text-decoration: none;
        font-size: 13px;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    
    .profile-link a:hover {
        color: #0b5ed7;
        gap: 8px;
    }
    
    /* ===== RESPONSIVE ===== */
    @media (max-width: 992px) {
        .stats-row,
        .actions-row {
            grid-template-columns: repeat(2, 1fr);
        }
        
        .content-row {
            grid-template-columns: 1fr;
        }
    }
    
    @media (max-width: 768px) {
        .page-header .container {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .stats-row,
        .actions-row {
            grid-template-columns: 1fr;
        }
        
        .profile-flex {
            flex-direction: column;
            text-align: center;
        }
        
        .profile-info p {
            justify-content: center;
        }
    }
</style>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <div class="welcome-text">
            <h1>Welcome, <?php echo htmlspecialchars($patient['name']); ?>!</h1>
            <p>Here's what's happening with your health dashboard</p>
        </div>
        <div class="date-badge">
            <i class="fas fa-calendar-alt"></i>
            <?php echo date('l, d M Y'); ?>
        </div>
    </div>
</section>

<!-- Main Content -->
<main class="main-content">
    <div class="container">
        
        <!-- Statistics Row -->
        <div class="stats-row">
            <div class="stat-box">
                <div class="stat-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-number"><?php echo $stats['total_appointments']; ?></div>
                <div class="stat-label">Total Appointments</div>
            </div>
            
            <div class="stat-box">
                <div class="stat-icon">
                    <i class="fas fa-hourglass-half"></i>
                </div>
                <div class="stat-number"><?php echo $stats['pending_appointments']; ?></div>
                <div class="stat-label">Pending</div>
            </div>
            
            <div class="stat-box">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-number"><?php echo $stats['approved_appointments']; ?></div>
                <div class="stat-label">Approved</div>
            </div>
            
            <div class="stat-box">
                <div class="stat-icon">
                    <i class="fas fa-check-double"></i>
                </div>
                <div class="stat-number"><?php echo $stats['completed_appointments']; ?></div>
                <div class="stat-label">Completed</div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="section-title">
            <div class="section-icon">
                <i class="fas fa-bolt"></i>
            </div>
            <div class="section-text">
                <h2>Quick Actions</h2>
                <p>What would you like to do today?</p>
            </div>
        </div>
        
        <div class="actions-row">
            <a href="<?php echo SITE_URL; ?>patient/book_appointment.php" class="action-box">
                <i class="fas fa-calendar-plus"></i>
                <h4>Book Appointment</h4>
                <p>Schedule test or vaccination</p>
            </a>
            
            <a href="<?php echo SITE_URL; ?>patient/my_appointments.php" class="action-box">
                <i class="fas fa-list"></i>
                <h4>My Appointments</h4>
                <p>View all appointments</p>
            </a>
            
            <a href="<?php echo SITE_URL; ?>patient/view_results.php" class="action-box">
                <i class="fas fa-flask"></i>
                <h4>Test Results</h4>
                <p>View COVID-19 test results</p>
            </a>
            
            <a href="<?php echo SITE_URL; ?>patient/vaccination_history.php" class="action-box">
                <i class="fas fa-syringe"></i>
                <h4>Vaccination</h4>
                <p>Track vaccination history</p>
            </a>
        </div>
        
        <!-- Recent Appointments & Test Results -->
        <div class="content-row">
            <!-- Recent Appointments -->
            <div class="content-card">
                <div class="card-head">
                    <h3><i class="fas fa-calendar-alt"></i> Recent Appointments</h3>
                    <a href="<?php echo SITE_URL; ?>patient/my_appointments.php" class="view-link">
                        View All <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                
                <?php if (!empty($appointments)): ?>
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Hospital</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_slice($appointments, 0, 3) as $apt): ?>
                                <tr>
                                    <td><?php echo formatDate($apt['appointment_date']); ?></td>
                                    <td><?php echo htmlspecialchars($apt['hospital_name']); ?></td>
                                    <td>
                                        <?php if ($apt['appointment_type'] == 'test'): ?>
                                            <span class="status-badge status-info">Test</span>
                                        <?php else: ?>
                                            <span class="status-badge status-success">Vaccine</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php
                                        $status_class = '';
                                        if ($apt['status'] == 'pending') $status_class = 'status-pending';
                                        elseif ($apt['status'] == 'approved') $status_class = 'status-approved';
                                        elseif ($apt['status'] == 'completed') $status_class = 'status-completed';
                                        else $status_class = 'status-cancelled';
                                        ?>
                                        <span class="status-badge <?php echo $status_class; ?>">
                                            <?php echo ucfirst($apt['status']); ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty-box">
                        <i class="fas fa-calendar-times"></i>
                        <p>No appointments found</p>
                        <a href="<?php echo SITE_URL; ?>patient/book_appointment.php" class="empty-btn">Book Now</a>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Recent Test Results -->
            <div class="content-card">
                <div class="card-head">
                    <h3><i class="fas fa-flask"></i> Recent Test Results</h3>
                    <a href="<?php echo SITE_URL; ?>patient/view_results.php" class="view-link">
                        View All <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                
                <?php if (!empty($test_results)): ?>
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Hospital</th>
                                    <th>Result</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_slice($test_results, 0, 3) as $test): ?>
                                <tr>
                                    <td><?php echo formatDate($test['test_date']); ?></td>
                                    <td><?php echo htmlspecialchars($test['hospital_name']); ?></td>
                                    <td>
                                        <?php if ($test['result'] == 'positive'): ?>
                                            <span class="status-badge status-danger">Positive</span>
                                        <?php else: ?>
                                            <span class="status-badge status-success">Negative</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty-box">
                        <i class="fas fa-flask"></i>
                        <p>No test results yet</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Vaccination History & Profile -->
        <div class="content-row">
            <!-- Vaccination History -->
            <div class="content-card">
                <div class="card-head">
                    <h3><i class="fas fa-syringe"></i> Vaccination History</h3>
                    <a href="<?php echo SITE_URL; ?>patient/vaccination_history.php" class="view-link">
                        View All <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                
                <?php if (!empty($vaccinations)): ?>
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Vaccine</th>
                                    <th>Dose</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_slice($vaccinations, 0, 3) as $vac): ?>
                                <tr>
                                    <td><?php echo formatDate($vac['vaccination_date']); ?></td>
                                    <td><?php echo htmlspecialchars($vac['vaccine_name']); ?></td>
                                    <td>Dose <?php echo $vac['dose_number']; ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty-box">
                        <i class="fas fa-syringe"></i>
                        <p>No vaccination records yet</p>
                        <a href="<?php echo SITE_URL; ?>patient/book_appointment.php?type=vaccination" class="empty-btn">Book Now</a>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Profile Summary -->
            <div class="profile-box">
                <div class="profile-flex">
                    <div class="profile-avatar">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <div class="profile-info">
                        <h3><?php echo htmlspecialchars($patient['name']); ?></h3>
                        <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($patient['email']); ?></p>
                        <p><i class="fas fa-phone"></i> <?php echo formatPhone($patient['phone']); ?></p>
                        <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($patient['city'] ?: 'Not specified'); ?></p>
                    </div>
                </div>
                <div class="profile-link">
                    <a href="<?php echo SITE_URL; ?>patient/profile.php">
                        View Full Profile <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
        
    </div>
</main>

<?php
require_once '../includes/footer.php';
?>