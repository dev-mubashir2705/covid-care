<?php
session_start();
require_once '../config.php';
require_once '../includes/functions.php';

// Check if hospital is logged in
if (!isset($_SESSION['hospital_id'])) {
    setFlashMessage('Please login to view patients', 'danger');
    redirect(SITE_URL . 'hospital/login.php');
}

$page_title = 'Patients - Hospital Panel';
require_once '../includes/header.php';
require_once '../includes/navbar.php';

$hospital_id = $_SESSION['hospital_id'];

// Get search parameter
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';

// Get patients who have appointments with this hospital
$query = "SELECT DISTINCT p.*, 
          (SELECT COUNT(*) FROM appointments WHERE patient_id = p.patient_id AND hospital_id = $hospital_id) as total_appointments,
          (SELECT MAX(appointment_date) FROM appointments WHERE patient_id = p.patient_id AND hospital_id = $hospital_id) as last_visit
          FROM patients p 
          JOIN appointments a ON p.patient_id = a.patient_id 
          WHERE a.hospital_id = $hospital_id";

if (!empty($search)) {
    $query .= " AND (p.name LIKE '%$search%' OR p.email LIKE '%$search%' OR p.phone LIKE '%$search%' OR p.city LIKE '%$search%')";
}

$query .= " ORDER BY p.name ASC";

$result = mysqli_query($conn, $query);
$patients = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $patients[] = $row;
    }
}

// Get statistics
$total_patients = count($patients);
$new_this_month = 0;
$active_patients = 0;

foreach ($patients as $p) {
    if (isset($p['created_at']) && date('Y-m', strtotime($p['created_at'])) == date('Y-m')) {
        $new_this_month++;
    }
    if (isset($p['last_visit']) && strtotime($p['last_visit']) > strtotime('-30 days')) {
        $active_patients++;
    }
}
?>

<style>
    /* ===== PATIENTS PAGE STYLES ===== */
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
    
    .page-header h1 {
        font-size: 42px;
        font-weight: 700;
        margin-bottom: 10px;
    }
    
    .page-header p {
        font-size: 18px;
        opacity: 0.9;
    }
    
    .header-badge {
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
    
    .header-badge i {
        color: #ffc107;
    }
    
    /* Stats Cards */
    .stats-row {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
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
    }
    
    .stat-small {
        font-size: 13px;
        color: #28a745;
        margin-top: 5px;
    }
    
    /* Search Bar */
    .search-section {
        margin-bottom: 30px;
    }
    
    .search-card {
        background: white;
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        border: 1px solid #eee;
    }
    
    .search-form {
        display: flex;
        gap: 15px;
        align-items: center;
        flex-wrap: wrap;
    }
    
    .search-group {
        flex: 3;
        min-width: 250px;
        position: relative;
    }
    
    .search-group i {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #0d6efd;
        font-size: 16px;
    }
    
    .search-group input {
        width: 100%;
        padding: 15px 15px 15px 45px;
        border: 2px solid #e9ecef;
        border-radius: 12px;
        font-size: 15px;
        transition: all 0.3s ease;
    }
    
    .search-group input:focus {
        outline: none;
        border-color: #0d6efd;
        box-shadow: 0 0 0 4px rgba(13,110,253,0.1);
    }
    
    .btn-search {
        flex: 1;
        min-width: 120px;
        padding: 15px 25px;
        background: #0d6efd;
        color: white;
        border: none;
        border-radius: 12px;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }
    
    .btn-search:hover {
        background: #0b5ed7;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(13,110,253,0.3);
    }
    
    .btn-reset {
        flex: 0.5;
        min-width: 80px;
        padding: 15px;
        background: #f8f9fa;
        color: #6c757d;
        border: 2px solid #e9ecef;
        border-radius: 12px;
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        transition: all 0.3s ease;
    }
    
    .btn-reset:hover {
        background: #e9ecef;
        color: #333;
    }
    
    /* Results Header */
    .results-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 15px;
    }
    
    .results-count {
        background: white;
        padding: 10px 20px;
        border-radius: 50px;
        border: 1px solid #eee;
    }
    
    .results-count i {
        color: #0d6efd;
        margin-right: 5px;
    }
    
    .results-count strong {
        color: #0d6efd;
        font-size: 18px;
    }
    
    /* Table Card */
    .table-card {
        background: white;
        border-radius: 20px;
        padding: 25px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        border: 1px solid #eee;
        margin-bottom: 30px;
    }
    
    .table-responsive {
        overflow-x: auto;
    }
    
    table {
        width: 100%;
        border-collapse: collapse;
    }
    
    th {
        text-align: left;
        padding: 15px;
        background: #f8f9fa;
        color: #333;
        font-size: 14px;
        font-weight: 600;
        border-bottom: 2px solid #dee2e6;
    }
    
    td {
        padding: 15px;
        color: #6c757d;
        font-size: 14px;
        border-bottom: 1px solid #eee;
    }
    
    tr:hover td {
        background: #f8f9fa;
    }
    
    .patient-name {
        font-weight: 600;
        color: #333;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .patient-name i {
        color: #0d6efd;
    }
    
    .badge {
        display: inline-block;
        padding: 5px 12px;
        border-radius: 50px;
        font-size: 12px;
        font-weight: 600;
    }
    
    .badge-active {
        background: #d1e7dd;
        color: #0f5132;
    }
    
    .badge-inactive {
        background: #f8d7da;
        color: #842029;
    }
    
    .badge-new {
        background: #cfe2ff;
        color: #084298;
    }
    
    .action-buttons {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }
    
    .btn-action {
        padding: 6px 12px;
        border-radius: 5px;
        text-decoration: none;
        font-size: 12px;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        transition: all 0.3s ease;
    }
    
    .btn-view {
        background: #0d6efd;
        color: white;
    }
    
    .btn-view:hover {
        background: #0b5ed7;
        transform: translateY(-2px);
    }
    
    .btn-test {
        background: #28a745;
        color: white;
    }
    
    .btn-test:hover {
        background: #218838;
        transform: translateY(-2px);
    }
    
    .btn-vaccine {
        background: #ffc107;
        color: #333;
    }
    
    .btn-vaccine:hover {
        background: #e0a800;
        transform: translateY(-2px);
    }
    
    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
    }
    
    .empty-state i {
        font-size: 60px;
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
        margin-bottom: 20px;
    }
    
    .empty-btn {
        display: inline-block;
        padding: 12px 30px;
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
        
        .page-header .container {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .page-header h1 {
            font-size: 32px;
        }
        
        .search-form {
            flex-direction: column;
        }
        
        .search-group,
        .btn-search,
        .btn-reset {
            width: 100%;
        }
        
        .action-buttons {
            flex-direction: column;
        }
        
        .btn-action {
            width: 100%;
            justify-content: center;
        }
        
        .results-header {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <div>
            <h1>Patients</h1>
            <p>View all patients who have appointments with your hospital</p>
        </div>
        <div class="header-badge">
            <i class="fas fa-users"></i>
            <?php echo $total_patients; ?> Total Patients
        </div>
    </div>
</section>

<!-- Main Content -->
<main class="main-content">
    <div class="container">
        
        <!-- Statistics Cards -->
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-value"><?php echo $total_patients; ?></div>
                <div class="stat-label">Total Patients</div>
                <div class="stat-small">
                    <i class="fas fa-user-plus"></i> All time
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-user-plus"></i>
                </div>
                <div class="stat-value"><?php echo $new_this_month; ?></div>
                <div class="stat-label">New This Month</div>
                <div class="stat-small">
                    <i class="fas fa-calendar"></i> <?php echo date('F Y'); ?>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-user-check"></i>
                </div>
                <div class="stat-value"><?php echo $active_patients; ?></div>
                <div class="stat-label">Active Patients</div>
                <div class="stat-small">
                    <i class="fas fa-clock"></i> Last 30 days
                </div>
            </div>
        </div>
        
        <!-- Search Section -->
        <div class="search-section">
            <div class="search-card">
                <form method="GET" action="" class="search-form">
                    <div class="search-group">
                        <i class="fas fa-search"></i>
                        <input type="text" name="search" placeholder="Search by name, email, phone or city..." 
                               value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    
                    <button type="submit" class="btn-search">
                        <i class="fas fa-search"></i> Search
                    </button>
                    
                    <?php if (!empty($search)): ?>
                        <a href="patients.php" class="btn-reset">
                            <i class="fas fa-times"></i> Reset
                        </a>
                    <?php endif; ?>
                </form>
            </div>
        </div>
        
        <!-- Results Header -->
        <div class="results-header">
            <div class="results-count">
                <i class="fas fa-users"></i>
                <strong><?php echo $total_patients; ?></strong> patients found
                <?php if (!empty($search)): ?>
                    matching "<strong><?php echo htmlspecialchars($search); ?></strong>"
                <?php endif; ?>
            </div>
            
            <div class="results-sort">
                <select onchange="window.location.href='?sort='+this.value<?php echo !empty($search) ? '+\'&search='.$search.'\'' : ''; ?>" class="form-select">
                    <option value="name">Sort by Name</option>
                    <option value="newest">Sort by Newest</option>
                    <option value="oldest">Sort by Oldest</option>
                    <option value="visits">Sort by Most Visits</option>
                </select>
            </div>
        </div>
        
        <!-- Patients Table -->
        <div class="table-card">
            <?php if (!empty($patients)): ?>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Patient</th>
                                <th>Contact</th>
                                <th>Location</th>
                                <th>Visits</th>
                                <th>Last Visit</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($patients as $patient): 
                                $is_new = (isset($patient['created_at']) && date('Y-m', strtotime($patient['created_at'])) == date('Y-m'));
                                $is_active = (isset($patient['last_visit']) && strtotime($patient['last_visit']) > strtotime('-30 days'));
                            ?>
                            <tr>
                                <td>
                                    <div class="patient-name">
                                        <i class="fas fa-user-circle"></i>
                                        <?php echo htmlspecialchars($patient['name']); ?>
                                    </div>
                                    <?php if ($is_new): ?>
                                        <span class="badge badge-new" style="margin-top: 5px;">New</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($patient['email']); ?></div>
                                    <div><i class="fas fa-phone"></i> <?php echo formatPhone($patient['phone']); ?></div>
                                </td>
                                <td>
                                    <div><i class="fas fa-city"></i> <?php echo htmlspecialchars($patient['city'] ?: 'N/A'); ?></div>
                                </td>
                                <td>
                                    <span style="font-weight: 600; color: #0d6efd;"><?php echo $patient['total_appointments']; ?></span>
                                </td>
                                <td>
                                    <?php echo $patient['last_visit'] ? date('d M Y', strtotime($patient['last_visit'])) : 'Never'; ?>
                                </td>
                                <td>
                                    <?php if ($is_active): ?>
                                        <span class="badge badge-active">Active</span>
                                    <?php else: ?>
                                        <span class="badge badge-inactive">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="#" class="btn-action btn-view" title="View Details">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        <a href="<?php echo SITE_URL; ?>hospital/update_test.php?patient_id=<?php echo $patient['patient_id']; ?>" class="btn-action btn-test" title="Update Test">
                                            <i class="fas fa-flask"></i> Test
                                        </a>
                                        <a href="<?php echo SITE_URL; ?>hospital/update_vaccination.php?patient_id=<?php echo $patient['patient_id']; ?>" class="btn-action btn-vaccine" title="Update Vaccination">
                                            <i class="fas fa-syringe"></i> Vaccine
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-users"></i>
                    <h3>No Patients Found</h3>
                    <p>There are no patients who have visited your hospital yet.</p>
                    <?php if (!empty($search)): ?>
                        <a href="patients.php" class="empty-btn">
                            <i class="fas fa-times"></i> Clear Search
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Quick Actions -->
        <div class="table-card" style="margin-top: 20px;">
            <h3 style="margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-bolt" style="color: #0d6efd;"></i>
                Quick Actions
            </h3>
            <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                <a href="<?php echo SITE_URL; ?>hospital/update_test.php" class="btn-action btn-test" style="padding: 12px 25px;">
                    <i class="fas fa-flask"></i> Update Test Results
                </a>
                <a href="<?php echo SITE_URL; ?>hospital/update_vaccination.php" class="btn-action btn-vaccine" style="padding: 12px 25px;">
                    <i class="fas fa-syringe"></i> Update Vaccination
                </a>
                <a href="<?php echo SITE_URL; ?>hospital/appointments.php" class="btn-action btn-view" style="padding: 12px 25px;">
                    <i class="fas fa-calendar-alt"></i> View Appointments
                </a>
            </div>
        </div>
        
    </div>
</main>

<?php require_once '../includes/footer.php'; ?>