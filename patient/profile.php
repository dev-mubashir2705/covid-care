<?php
require_once '../config.php';
require_once '../includes/functions.php';

// Check if patient is logged in
if (!isset($_SESSION['patient_id'])) {
    setFlashMessage('Please login to view profile', 'danger');
    redirect(SITE_URL . 'patient/login.php');
}

$page_title = 'My Profile - COVID Care System';
require_once '../includes/header.php';
require_once '../includes/navbar.php';

$patient_id = $_SESSION['patient_id'];

// Get patient details
$patient = getRecord($conn, 'patients', 'patient_id', $patient_id);

// Get statistics
$stats = getPatientStats($conn, $patient_id);

// Get recent appointments
$recent_appointments = getPatientAppointments($conn, $patient_id, 3);
?>

<style>
    /* ===== PROFILE PAGE STYLES ===== */
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
    }
    
    .page-header .container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
    }
    
    .page-header h1 {
        font-size: 36px;
        font-weight: 700;
        margin-bottom: 10px;
    }
    
    .page-header p {
        font-size: 16px;
        opacity: 0.9;
    }
    
    .header-badge {
        background: rgba(255,255,255,0.2);
        padding: 10px 20px;
        border-radius: 50px;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .header-badge i {
        color: #ffc107;
    }
    
    /* Profile Grid */
    .profile-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 25px;
        margin-bottom: 40px;
    }
    
    /* Profile Card */
    .profile-card {
        background: white;
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        border: 1px solid #eee;
    }
    
    .profile-header {
        display: flex;
        align-items: center;
        gap: 25px;
        margin-bottom: 30px;
        padding-bottom: 25px;
        border-bottom: 2px solid #f0f0f0;
    }
    
    .profile-avatar {
        width: 100px;
        height: 100px;
        background: linear-gradient(135deg, #0d6efd, #0b5ed7);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 45px;
        box-shadow: 0 10px 20px rgba(13,110,253,0.2);
    }
    
    .profile-title h2 {
        font-size: 28px;
        font-weight: 700;
        color: #333;
        margin-bottom: 5px;
    }
    
    .profile-title .member-since {
        color: #6c757d;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    
    .profile-title .member-since i {
        color: #0d6efd;
    }
    
    /* Info Grid */
    .info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
        margin-bottom: 30px;
    }
    
    .info-item {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 15px;
        transition: all 0.3s ease;
        border: 1px solid #eee;
    }
    
    .info-item:hover {
        background: #e7f1ff;
        border-color: #0d6efd;
    }
    
    .info-label {
        font-size: 12px;
        color: #6c757d;
        margin-bottom: 5px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    
    .info-label i {
        color: #0d6efd;
        font-size: 14px;
    }
    
    .info-value {
        font-size: 16px;
        font-weight: 600;
        color: #333;
    }
    
    /* Action Buttons */
    .action-buttons {
        display: flex;
        gap: 15px;
        margin-top: 20px;
    }
    
    .btn-action {
        flex: 1;
        padding: 12px 20px;
        border-radius: 10px;
        text-decoration: none;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        border: none;
        cursor: pointer;
    }
    
    .btn-edit {
        background: #0d6efd;
        color: white;
    }
    
    .btn-edit:hover {
        background: #0b5ed7;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(13,110,253,0.3);
    }
    
    .btn-password {
        background: #6c757d;
        color: white;
    }
    
    .btn-password:hover {
        background: #5a6268;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(108,117,125,0.3);
    }
    
    .btn-dashboard {
        background: #28a745;
        color: white;
    }
    
    .btn-dashboard:hover {
        background: #218838;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(40,167,69,0.3);
    }
    
    /* Stats Card */
    .stats-card {
        background: white;
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        border: 1px solid #eee;
    }
    
    .stats-card h3 {
        font-size: 20px;
        font-weight: 700;
        color: #333;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid #f0f0f0;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .stats-card h3 i {
        color: #0d6efd;
    }
    
    .stat-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px dashed #eee;
    }
    
    .stat-item:last-child {
        border-bottom: none;
    }
    
    .stat-name {
        color: #6c757d;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .stat-name i {
        color: #0d6efd;
        width: 20px;
    }
    
    .stat-number {
        font-size: 18px;
        font-weight: 700;
        color: #0d6efd;
    }
    
    /* Recent Activity Card */
    .recent-card {
        background: white;
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        border: 1px solid #eee;
        margin-bottom: 40px;
    }
    
    .recent-card h3 {
        font-size: 20px;
        font-weight: 700;
        color: #333;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid #f0f0f0;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .recent-card h3 i {
        color: #0d6efd;
    }
    
    .activity-list {
        list-style: none;
    }
    
    .activity-item {
        display: flex;
        align-items: center;
        padding: 15px 0;
        border-bottom: 1px solid #eee;
    }
    
    .activity-item:last-child {
        border-bottom: none;
    }
    
    .activity-icon {
        width: 45px;
        height: 45px;
        background: #e7f1ff;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        color: #0d6efd;
        font-size: 20px;
    }
    
    .activity-content {
        flex: 1;
    }
    
    .activity-title {
        font-weight: 600;
        color: #333;
        margin-bottom: 3px;
    }
    
    .activity-meta {
        color: #6c757d;
        font-size: 12px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .activity-meta i {
        color: #0d6efd;
        font-size: 10px;
    }
    
    .activity-status {
        padding: 3px 10px;
        border-radius: 50px;
        font-size: 11px;
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
    
    .view-all-link {
        text-align: center;
        margin-top: 20px;
        padding-top: 15px;
        border-top: 1px solid #eee;
    }
    
    .view-all-link a {
        color: #0d6efd;
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        transition: all 0.3s ease;
    }
    
    .view-all-link a:hover {
        gap: 8px;
    }
    
    /* Badge */
    .badge {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 50px;
        font-size: 11px;
        font-weight: 600;
    }
    
    .badge-primary {
        background: #0d6efd;
        color: white;
    }
    
    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 30px;
        color: #6c757d;
    }
    
    .empty-state i {
        font-size: 40px;
        color: #dee2e6;
        margin-bottom: 10px;
    }
    
    /* Responsive */
    @media (max-width: 992px) {
        .profile-grid {
            grid-template-columns: 1fr;
        }
    }
    
    @media (max-width: 768px) {
        .page-header h1 {
            font-size: 28px;
        }
        
        .profile-header {
            flex-direction: column;
            text-align: center;
        }
        
        .info-grid {
            grid-template-columns: 1fr;
        }
        
        .action-buttons {
            flex-direction: column;
        }
        
        .activity-item {
            flex-direction: column;
            text-align: center;
        }
        
        .activity-icon {
            margin: 0 0 10px 0;
        }
        
        .activity-meta {
            justify-content: center;
        }
    }
</style>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <div>
            <h1>My Profile</h1>
            <p>Manage your personal information and account settings</p>
        </div>
        <div class="header-badge">
            <i class="fas fa-user-circle"></i>
            Member since <?php echo date('M Y', strtotime($patient['created_at'])); ?>
        </div>
    </div>
</section>

<!-- Profile Grid -->
<section class="container">
    <div class="profile-grid">
        <!-- Profile Information -->
        <div class="profile-card">
            <div class="profile-header">
                <div class="profile-avatar">
                    <i class="fas fa-user-circle"></i>
                </div>
                <div class="profile-title">
                    <h2><?php echo $patient['name']; ?></h2>
                    <div class="member-since">
                        <i class="fas fa-calendar-alt"></i>
                        Joined: <?php echo date('d M Y', strtotime($patient['created_at'])); ?>
                    </div>
                </div>
            </div>
            
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">
                        <i class="fas fa-envelope"></i> Email Address
                    </div>
                    <div class="info-value"><?php echo $patient['email']; ?></div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">
                        <i class="fas fa-phone"></i> Phone Number
                    </div>
                    <div class="info-value"><?php echo formatPhone($patient['phone']); ?></div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">
                        <i class="fas fa-calendar"></i> Date of Birth
                    </div>
                    <div class="info-value"><?php echo $patient['dob'] ? formatDate($patient['dob']) : 'Not specified'; ?></div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">
                        <i class="fas fa-venus-mars"></i> Gender
                    </div>
                    <div class="info-value"><?php echo $patient['gender'] ?: 'Not specified'; ?></div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">
                        <i class="fas fa-city"></i> City
                    </div>
                    <div class="info-value"><?php echo $patient['city'] ?: 'Not specified'; ?></div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">
                        <i class="fas fa-id-card"></i> Patient ID
                    </div>
                    <div class="info-value">#<?php echo str_pad($patient['patient_id'], 5, '0', STR_PAD_LEFT); ?></div>
                </div>
            </div>
            
            <div class="info-item" style="grid-column: 1/-1; margin-top: 0;">
                <div class="info-label">
                    <i class="fas fa-map-marker-alt"></i> Address
                </div>
                <div class="info-value"><?php echo $patient['address'] ?: 'Not specified'; ?></div>
            </div>
            
            <div class="action-buttons">
                <a href="<?php echo SITE_URL; ?>patient/edit_profile.php" class="btn-action btn-edit">
                    <i class="fas fa-edit"></i> Edit Profile
                </a>
                <a href="<?php echo SITE_URL; ?>patient/change_password.php" class="btn-action btn-password">
                    <i class="fas fa-key"></i> Change Password
                </a>
                <a href="<?php echo SITE_URL; ?>patient/dashboard.php" class="btn-action btn-dashboard">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            </div>
        </div>
        
        <!-- Statistics Card -->
        <div class="stats-card">
            <h3><i class="fas fa-chart-bar"></i> Your Statistics</h3>
            
            <div class="stat-item">
                <span class="stat-name"><i class="fas fa-calendar-check"></i> Total Appointments</span>
                <span class="stat-number"><?php echo $stats['total_appointments']; ?></span>
            </div>
            
            <div class="stat-item">
                <span class="stat-name"><i class="fas fa-hourglass-half"></i> Pending Appointments</span>
                <span class="stat-number"><?php echo $stats['pending_appointments']; ?></span>
            </div>
            
            <div class="stat-item">
                <span class="stat-name"><i class="fas fa-check-circle"></i> Approved Appointments</span>
                <span class="stat-number"><?php echo $stats['approved_appointments']; ?></span>
            </div>
            
            <div class="stat-item">
                <span class="stat-name"><i class="fas fa-check-double"></i> Completed Appointments</span>
                <span class="stat-number"><?php echo $stats['completed_appointments']; ?></span>
            </div>
            
            <div class="stat-item">
                <span class="stat-name"><i class="fas fa-flask"></i> Tests Taken</span>
                <span class="stat-number"><?php echo $stats['total_tests']; ?></span>
            </div>
            
            <div class="stat-item">
                <span class="stat-name"><i class="fas fa-syringe"></i> Vaccinations Received</span>
                <span class="stat-number"><?php echo $stats['total_vaccinations']; ?></span>
            </div>
        </div>
    </div>
    
    <!-- Recent Activity -->
    <div class="recent-card">
        <h3><i class="fas fa-history"></i> Recent Activity</h3>
        
        <?php if (!empty($recent_appointments)): ?>
            <ul class="activity-list">
                <?php foreach ($recent_appointments as $apt): ?>
                <li class="activity-item">
                    <div class="activity-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="activity-content">
                        <div class="activity-title">
                            <?php echo ucfirst($apt['appointment_type']); ?> Appointment at <?php echo $apt['hospital_name']; ?>
                        </div>
                        <div class="activity-meta">
                            <span><i class="fas fa-calendar"></i> <?php echo formatDate($apt['appointment_date']); ?></span>
                            <span><i class="fas fa-clock"></i> <?php echo formatTime($apt['appointment_time']); ?></span>
                            <span class="activity-status 
                                <?php 
                                if ($apt['status'] == 'pending') echo 'status-pending';
                                elseif ($apt['status'] == 'approved') echo 'status-approved';
                                elseif ($apt['status'] == 'completed') echo 'status-completed';
                                ?>">
                                <?php echo ucfirst($apt['status']); ?>
                            </span>
                        </div>
                    </div>
                </li>
                <?php endforeach; ?>
            </ul>
            
            <div class="view-all-link">
                <a href="<?php echo SITE_URL; ?>patient/my_appointments.php">
                    View All Appointments <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-calendar-times"></i>
                <p>No recent activity found</p>
                <a href="<?php echo SITE_URL; ?>patient/book_appointment.php" class="badge badge-primary" style="text-decoration: none;">
                    Book Your First Appointment
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php
require_once '../includes/footer.php';
?>