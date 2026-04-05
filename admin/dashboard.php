<?php
session_start();
require_once '../config.php';
require_once '../includes/functions.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    setFlashMessage('Please login to access dashboard', 'danger');
    redirect(SITE_URL . 'admin/login.php');
}

$page_title = 'Admin Dashboard - COVID Care System';
require_once '../includes/header.php';
require_once '../includes/navbar.php';

// Get statistics
$stats = getAdminStats($conn);

// Get recent hospitals
$hospitals_query = "SELECT * FROM hospitals ORDER BY created_at DESC LIMIT 5";
$hospitals = mysqli_query($conn, $hospitals_query);

// Get recent patients
$patients_query = "SELECT * FROM patients ORDER BY created_at DESC LIMIT 5";
$patients = mysqli_query($conn, $patients_query);

// Get recent appointments
$appointments_query = "SELECT a.*, p.name as patient_name, h.name as hospital_name 
                      FROM appointments a
                      JOIN patients p ON a.patient_id = p.patient_id
                      JOIN hospitals h ON a.hospital_id = h.hospital_id
                      ORDER BY a.appointment_date DESC, a.appointment_time DESC
                      LIMIT 5";
$appointments = mysqli_query($conn, $appointments_query);

// Get counts for quick stats
$total_patients = $stats['total_patients'];
$total_hospitals = $stats['total_hospitals'];
$pending_hospitals = $stats['pending_hospitals'];
$total_appointments = $stats['total_appointments'];
$total_vaccines = $stats['total_vaccines'];
$total_tests = $stats['total_tests'];
$total_vaccinations = $stats['total_vaccinations'];
$approved_hospitals = $stats['approved_hospitals'];
?>

<style>
    /* ===== ADMIN DASHBOARD STYLES ===== */
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
        width: 100%;
    }
    
    /* Page Header */
    .page-header {
        background: linear-gradient(135deg, #0d6efd, #0b5ed7);
        padding: 60px 0;
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
        font-size: 42px;
        font-weight: 700;
        margin-bottom: 10px;
    }
    
    .welcome-text p {
        font-size: 18px;
        opacity: 0.9;
    }
    
    .date-badge {
        background: rgba(255,255,255,0.2);
        padding: 12px 25px;
        border-radius: 50px;
        font-size: 15px;
        display: flex;
        align-items: center;
        gap: 10px;
        backdrop-filter: blur(5px);
        border: 1px solid rgba(255,255,255,0.3);
    }
    
    .date-badge i {
        color: #ffc107;
    }
    
    /* Stats Cards */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 25px;
        margin-bottom: 40px;
    }
    
    .stat-card {
        background: white;
        border-radius: 20px;
        padding: 25px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        border: 1px solid #eee;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 5px;
        background: linear-gradient(90deg, #0d6efd, #0b5ed7);
    }
    
    .stat-card:hover {
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
        margin-bottom: 15px;
        color: #0d6efd;
        font-size: 28px;
        transition: all 0.3s ease;
    }
    
    .stat-card:hover .stat-icon {
        background: #0d6efd;
        color: white;
    }
    
    .stat-value {
        font-size: 32px;
        font-weight: 700;
        color: #333;
        margin-bottom: 5px;
    }
    
    .stat-label {
        color: #6c757d;
        font-size: 14px;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    /* Section Title */
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
    
    /* Quick Actions */
    .quick-actions-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 25px;
        margin-bottom: 40px;
    }
    
    .quick-action-card {
        background: white;
        border-radius: 15px;
        padding: 30px 20px;
        text-align: center;
        text-decoration: none;
        transition: all 0.3s ease;
        border: 1px solid #eee;
        border-bottom: 4px solid #0d6efd;
        display: block;
    }
    
    .quick-action-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(13,110,253,0.15);
        background: #f8f9fa;
    }
    
    .quick-action-card i {
        font-size: 40px;
        color: #0d6efd;
        margin-bottom: 15px;
        transition: all 0.3s ease;
    }
    
    .quick-action-card:hover i {
        transform: scale(1.1);
    }
    
    .quick-action-card h4 {
        font-size: 16px;
        font-weight: 600;
        color: #333;
        margin-bottom: 5px;
    }
    
    .quick-action-card p {
        color: #6c757d;
        font-size: 13px;
        margin: 0;
    }
    
    /* Content Cards */
    .content-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 25px;
        margin-bottom: 40px;
    }
    
    .content-card {
        background: white;
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        border: 1px solid #eee;
    }
    
    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid #f0f0f0;
    }
    
    .card-header h3 {
        font-size: 18px;
        font-weight: 700;
        color: #333;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .card-header h3 i {
        color: #0d6efd;
    }
    
    .view-link {
        color: #0d6efd;
        text-decoration: none;
        font-size: 13px;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 5px;
        transition: all 0.3s ease;
        padding: 5px 12px;
        background: #e7f1ff;
        border-radius: 50px;
    }
    
    .view-link:hover {
        background: #0d6efd;
        color: white;
        gap: 8px;
    }
    
    .view-link:hover i {
        color: white;
    }
    
    /* Tables */
    .table-responsive {
        overflow-x: auto;
    }
    
    table {
        width: 100%;
        border-collapse: collapse;
    }
    
    th {
        text-align: left;
        padding: 12px;
        background: #f8f9fa;
        color: #333;
        font-size: 13px;
        font-weight: 600;
        border-bottom: 2px solid #dee2e6;
    }
    
    td {
        padding: 12px;
        color: #6c757d;
        font-size: 13px;
        border-bottom: 1px solid #eee;
    }
    
    tr:hover td {
        background: #f8f9fa;
    }
    
    .badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 50px;
        font-size: 11px;
        font-weight: 600;
    }
    
    .badge-pending {
        background: #fff3cd;
        color: #856404;
    }
    
    .badge-approved {
        background: #d1e7dd;
        color: #0f5132;
    }
    
    .badge-rejected {
        background: #f8d7da;
        color: #842029;
    }
    
    .badge-info {
        background: #e7f1ff;
        color: #0d6efd;
    }
    
    .badge-success {
        background: #d1e7dd;
        color: #0f5132;
    }
    
    /* Responsive */
    @media (max-width: 992px) {
        .stats-grid,
        .quick-actions-grid {
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
        
        .welcome-text h1 {
            font-size: 32px;
        }
        
        .stats-grid,
        .quick-actions-grid {
            grid-template-columns: 1fr;
        }
        
        .card-header {
            flex-direction: column;
            gap: 10px;
            align-items: flex-start;
        }
    }
    
</style>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <div class="welcome-text">
            <h1>Admin Dashboard</h1>
            <p>Welcome back, <?php echo htmlspecialchars($_SESSION['admin_name']); ?>!</p>
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
        
        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-value"><?php echo $total_patients; ?></div>
                <div class="stat-label">Total Patients</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-hospital"></i>
                </div>
                <div class="stat-value"><?php echo $total_hospitals; ?></div>
                <div class="stat-label">Total Hospitals</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-value"><?php echo $pending_hospitals; ?></div>
                <div class="stat-label">Pending Approval</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-value"><?php echo $total_appointments; ?></div>
                <div class="stat-label">Appointments</div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="section-title">
            <div class="section-icon">
                <i class="fas fa-bolt"></i>
            </div>
            <div class="section-text">
                <h2>Quick Actions</h2>
                <p>Manage your system with these quick options</p>
            </div>
        </div>
        
        <div class="quick-actions-grid">
            <a href="hospitals.php" class="quick-action-card">
                <i class="fas fa-hospital"></i>
                <h4>Manage Hospitals</h4>
                <p>View & manage all hospitals</p>
            </a>
            
            <a href="approve_hospital.php" class="quick-action-card">
                <i class="fas fa-check-circle"></i>
                <h4>Approve Hospitals</h4>
                <p><?php echo $pending_hospitals; ?> pending approvals</p>
            </a>
            
            <a href="patients.php" class="quick-action-card">
                <i class="fas fa-users"></i>
                <h4>View Patients</h4>
                <p>All registered patients</p>
            </a>
            
            <a href="vaccines.php" class="quick-action-card">
                <i class="fas fa-syringe"></i>
                <h4>Manage Vaccines</h4>
                <p><?php echo $total_vaccines; ?> vaccine types</p>
            </a>
        </div>
        
        <!-- Recent Data Row -->
        <div class="content-row">
            <!-- Recent Hospitals -->
            <div class="content-card">
                <div class="card-header">
                    <h3><i class="fas fa-hospital"></i> Recent Hospitals</h3>
                    <a href="hospitals.php" class="view-link">
                        View All <i class="fas fa-arrow-right"></i>
                    </a>
<a href="manage_hospitals.php" class="btn btn-danger">
    <i class="fas fa-hospital"></i> Manage Hospitals
</a>

                </div>
                
                <?php if (mysqli_num_rows($hospitals) > 0): ?>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>City</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($h = mysqli_fetch_assoc($hospitals)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($h['name']); ?></td>
                                <td><?php echo htmlspecialchars($h['city']); ?></td>
                                <td>
                                    <?php
                                    $class = '';
                                    if ($h['status'] == 'pending') $class = 'badge-pending';
                                    elseif ($h['status'] == 'approved') $class = 'badge-approved';
                                    elseif ($h['status'] == 'rejected') $class = 'badge-rejected';
                                    ?>
                                    <span class="badge <?php echo $class; ?>"><?php echo ucfirst($h['status']); ?></span>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                    <p class="text-muted">No hospitals found.</p>
                <?php endif; ?>
            </div>
            
            <!-- Recent Patients -->
            <div class="content-card">
                <div class="card-header">
                    <h3><i class="fas fa-users"></i> Recent Patients</h3>
                    <a href="patients.php" class="view-link">
                        View All <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                
                <?php if (mysqli_num_rows($patients) > 0): ?>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>City</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($p = mysqli_fetch_assoc($patients)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($p['name']); ?></td>
                                <td><?php echo htmlspecialchars($p['email']); ?></td>
                                <td><?php echo htmlspecialchars($p['city']); ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                    <p class="text-muted">No patients found.</p>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Recent Appointments -->
        <div class="content-card" style="margin-bottom: 0;">
            <div class="card-header">
                <h3><i class="fas fa-calendar-alt"></i> Recent Appointments</h3>
                <a href="reports.php" class="view-link">
                    View All <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            
            <?php if (mysqli_num_rows($appointments) > 0): ?>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Patient</th>
                            <th>Hospital</th>
                            <th>Type</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($a = mysqli_fetch_assoc($appointments)): ?>
                        <tr>
                            <td><?php echo date('d M Y', strtotime($a['appointment_date'])); ?></td>
                            <td><?php echo htmlspecialchars($a['patient_name']); ?></td>
                            <td><?php echo htmlspecialchars($a['hospital_name']); ?></td>
                            <td><?php echo ucfirst($a['appointment_type']); ?></td>
                            <td>
                                <?php
                                $class = '';
                                if ($a['status'] == 'pending') $class = 'badge-pending';
                                elseif ($a['status'] == 'approved') $class = 'badge-approved';
                                elseif ($a['status'] == 'completed') $class = 'badge-success';
                                ?>
                                <span class="badge <?php echo $class; ?>"><?php echo ucfirst($a['status']); ?></span>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
                <p class="text-muted">No appointments found.</p>
            <?php endif; ?>
        </div>
        
    </div>
</main>

<?php require_once '../includes/footer.php'; ?>