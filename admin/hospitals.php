<?php
session_start();
require_once '../config.php';
require_once '../includes/functions.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    setFlashMessage('Please login to access this page', 'danger');
    redirect(SITE_URL . 'admin/login.php');
}

$page_title = 'Manage Hospitals - Admin Panel';
require_once '../includes/header.php';
require_once '../includes/navbar.php';

// Handle status update
if (isset($_GET['approve'])) {
    $id = (int)$_GET['approve'];
    $query = "UPDATE hospitals SET status = 'approved' WHERE hospital_id = $id";
    if (mysqli_query($conn, $query)) {
        setFlashMessage('Hospital approved successfully!', 'success');
    } else {
        setFlashMessage('Error approving hospital!', 'danger');
    }
    redirect('hospitals.php');
}

if (isset($_GET['reject'])) {
    $id = (int)$_GET['reject'];
    $query = "UPDATE hospitals SET status = 'rejected' WHERE hospital_id = $id";
    if (mysqli_query($conn, $query)) {
        setFlashMessage('Hospital rejected!', 'warning');
    } else {
        setFlashMessage('Error rejecting hospital!', 'danger');
    }
    redirect('hospitals.php');
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $query = "DELETE FROM hospitals WHERE hospital_id = $id";
    if (mysqli_query($conn, $query)) {
        setFlashMessage('Hospital deleted successfully!', 'success');
    } else {
        setFlashMessage('Error deleting hospital!', 'danger');
    }
    redirect('hospitals.php');
}

// Get filter
$status = isset($_GET['status']) ? $_GET['status'] : '';

// ✅ FIXED: Direct SQL query instead of getRecords() function
if (!empty($status)) {
    $query = "SELECT * FROM hospitals WHERE status = '$status' ORDER BY created_at DESC";
} else {
    $query = "SELECT * FROM hospitals ORDER BY created_at DESC";
}

$result = mysqli_query($conn, $query);
$hospitals = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $hospitals[] = $row;
    }
}

// Get counts for stats
$total_query = "SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
    SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved,
    SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected
    FROM hospitals";
$counts_result = mysqli_query($conn, $total_query);
$counts = mysqli_fetch_assoc($counts_result);
?>

<style>
    /* ===== HOSPITALS PAGE STYLES ===== */
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
    
    /* Filter Bar */
    .filter-bar {
        background: white;
        border-radius: 15px;
        padding: 20px;
        margin-bottom: 30px;
        border: 1px solid #eee;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
    }
    
    .filter-tabs {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }
    
    .filter-tab {
        padding: 8px 20px;
        background: #f8f9fa;
        border: 1px solid #eee;
        border-radius: 30px;
        color: #6c757d;
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 5px;
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
    
    .filter-tab i {
        font-size: 12px;
    }
    
    .filter-tab .count {
        background: rgba(0,0,0,0.1);
        padding: 2px 8px;
        border-radius: 20px;
        font-size: 11px;
        margin-left: 5px;
    }
    
    .filter-tab.active .count {
        background: rgba(255,255,255,0.2);
    }
    
    .search-box {
        display: flex;
        gap: 10px;
    }
    
    .search-box input {
        padding: 10px 15px;
        border: 2px solid #e9ecef;
        border-radius: 30px;
        width: 250px;
        font-size: 14px;
    }
    
    .search-box input:focus {
        outline: none;
        border-color: #0d6efd;
    }
    
    .search-box button {
        background: #0d6efd;
        color: white;
        border: none;
        border-radius: 30px;
        padding: 10px 20px;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .search-box button:hover {
        background: #0b5ed7;
    }
    
    /* Table Card */
    .table-card {
        background: white;
        border-radius: 20px;
        padding: 25px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        border: 1px solid #eee;
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
    
    .hospital-name {
        font-weight: 700;
        color: #333;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .hospital-name i {
        color: #0d6efd;
    }
    
    .badge {
        display: inline-block;
        padding: 5px 12px;
        border-radius: 50px;
        font-size: 12px;
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
        background: #ffc107;
        color: #333;
    }
    
    .btn-reject:hover {
        background: #e0a800;
        transform: translateY(-2px);
    }
    
    .btn-view {
        background: #0d6efd;
        color: white;
    }
    
    .btn-view:hover {
        background: #0b5ed7;
        transform: translateY(-2px);
    }
    
    .btn-delete {
        background: #dc3545;
        color: white;
    }
    
    .btn-delete:hover {
        background: #c82333;
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
        font-size: 20px;
        font-weight: 600;
        color: #333;
        margin-bottom: 10px;
    }
    
    .empty-state p {
        color: #6c757d;
    }
    
    /* Alert Messages */
    .alert {
        padding: 15px 20px;
        border-radius: 10px;
        margin-bottom: 25px;
        display: flex;
        align-items: center;
        gap: 12px;
        animation: slideIn 0.3s ease;
    }
    
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .alert-success {
        background: #d1e7dd;
        color: #0f5132;
        border: 1px solid #badbcc;
    }
    
    .alert-danger {
        background: #f8d7da;
        color: #842029;
        border: 1px solid #f5c2c7;
    }
    
    .alert i {
        font-size: 20px;
    }
    
    /* Responsive */
    @media (max-width: 992px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        
        .filter-bar {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .search-box {
            width: 100%;
        }
        
        .search-box input {
            width: 100%;
        }
    }
    
    @media (max-width: 768px) {
        .page-header .container {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .page-header h1 {
            font-size: 32px;
        }
        
        .stats-grid {
            grid-template-columns: 1fr;
        }
        
        .filter-tabs {
            width: 100%;
        }
        
        .filter-tab {
            flex: 1;
            text-align: center;
        }
        
        .action-buttons {
            flex-direction: column;
        }
        
        .btn-action {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <div>
            <h1>Manage Hospitals</h1>
            <p>View and manage all registered hospitals</p>
        </div>
        <div class="header-badge">
            <i class="fas fa-hospital"></i>
            <?php echo $counts['total']; ?> Hospitals Total
        </div>
    </div>
</section>

<!-- Main Content -->
<main class="main-content">
    <div class="container">
        
        <!-- Flash Messages -->
        <?php echo showFlashMessage(); ?>
        
        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-hospital"></i>
                </div>
                <div class="stat-value"><?php echo $counts['total']; ?></div>
                <div class="stat-label">Total Hospitals</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-value"><?php echo $counts['pending']; ?></div>
                <div class="stat-label">Pending</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-value"><?php echo $counts['approved']; ?></div>
                <div class="stat-label">Approved</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="stat-value"><?php echo $counts['rejected']; ?></div>
                <div class="stat-label">Rejected</div>
            </div>
        </div>
        
        <!-- Filter Bar -->
        <div class="filter-bar">
            <div class="filter-tabs">
                <a href="?status=" class="filter-tab <?php echo empty($status) ? 'active' : ''; ?>">
                    <i class="fas fa-list"></i> All
                    <span class="count"><?php echo $counts['total']; ?></span>
                </a>
                <a href="?status=pending" class="filter-tab <?php echo $status == 'pending' ? 'active' : ''; ?>">
                    <i class="fas fa-clock"></i> Pending
                    <span class="count"><?php echo $counts['pending']; ?></span>
                </a>
                <a href="?status=approved" class="filter-tab <?php echo $status == 'approved' ? 'active' : ''; ?>">
                    <i class="fas fa-check-circle"></i> Approved
                    <span class="count"><?php echo $counts['approved']; ?></span>
                </a>
                <a href="?status=rejected" class="filter-tab <?php echo $status == 'rejected' ? 'active' : ''; ?>">
                    <i class="fas fa-times-circle"></i> Rejected
                    <span class="count"><?php echo $counts['rejected']; ?></span>
                </a>
            </div>
            
            <form method="GET" class="search-box">
                <input type="text" name="search" placeholder="Search hospitals..." 
                       value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                <button type="submit"><i class="fas fa-search"></i></button>
            </form>
        </div>
        
        <!-- Hospitals Table -->
        <div class="table-card">
            <?php if (!empty($hospitals)): ?>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Hospital Name</th>
                                <th>Contact</th>
                                <th>Location</th>
                                <th>Registration No.</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($hospitals as $h): ?>
                            <tr>
                                <td>#<?php echo $h['hospital_id']; ?></td>
                                <td>
                                    <div class="hospital-name">
                                        <i class="fas fa-hospital"></i>
                                        <?php echo htmlspecialchars($h['name']); ?>
                                    </div>
                                </td>
                                <td>
                                    <div><i class="fas fa-phone"></i> <?php echo htmlspecialchars($h['phone']); ?></div>
                                    <div><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($h['email']); ?></div>
                                </td>
                                <td>
                                    <div><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($h['address']); ?></div>
                                    <div><i class="fas fa-city"></i> <?php echo htmlspecialchars($h['city']); ?></div>
                                </td>
                                <td><?php echo htmlspecialchars($h['registration_no']); ?></td>
                                <td>
                                    <?php
                                    $badge_class = '';
                                    if ($h['status'] == 'pending') $badge_class = 'badge-pending';
                                    elseif ($h['status'] == 'approved') $badge_class = 'badge-approved';
                                    elseif ($h['status'] == 'rejected') $badge_class = 'badge-rejected';
                                    ?>
                                    <span class="badge <?php echo $badge_class; ?>">
                                        <?php echo ucfirst($h['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <?php if ($h['status'] == 'pending'): ?>
                                            <a href="?approve=<?php echo $h['hospital_id']; ?>" 
                                               class="btn-action btn-approve" 
                                               onclick="return confirm('Approve this hospital?')">
                                                <i class="fas fa-check"></i> Approve
                                            </a>
                                            <a href="?reject=<?php echo $h['hospital_id']; ?>" 
                                               class="btn-action btn-reject" 
                                               onclick="return confirm('Reject this hospital?')">
                                                <i class="fas fa-times"></i> Reject
                                            </a>
                                        <?php endif; ?>
                                        
                                        <a href="#" class="btn-action btn-view">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        
                                        <a href="?delete=<?php echo $h['hospital_id']; ?>" 
                                           class="btn-action btn-delete" 
                                           onclick="return confirm('Are you sure you want to delete this hospital? This action cannot be undone.')">
                                            <i class="fas fa-trash"></i> Delete
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
                    <i class="fas fa-hospital"></i>
                    <h3>No Hospitals Found</h3>
                    <p>There are no hospitals matching your criteria.</p>
                </div>
            <?php endif; ?>
        </div>
        
    </div>
</main>

<?php require_once '../includes/footer.php'; ?>