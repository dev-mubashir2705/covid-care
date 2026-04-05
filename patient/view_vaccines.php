<?php
require_once '../config.php';
require_once '../includes/functions.php';

// Check if patient is logged in
if (!isset($_SESSION['patient_id'])) {
    setFlashMessage('Please login to view vaccines', 'danger');
    redirect(SITE_URL . 'patient/login.php');
}

$page_title = 'Vaccine Information - COVID Care System';
require_once '../includes/header.php';
require_once '../includes/navbar.php';

$patient_id = $_SESSION['patient_id'];

// Get all available vaccines
$vaccines = getAvailableVaccines($conn);

// Get patient's vaccination history
$vaccinations = getPatientVaccinations($conn, $patient_id);
?>

<style>
    /* ===== VACCINE PAGE STYLES ===== */
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
    
    /* Section Title */
    .section-title {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 25px;
    }
    
    .section-title i {
        font-size: 28px;
        color: #0d6efd;
        background: #e7f1ff;
        padding: 12px;
        border-radius: 12px;
    }
    
    .section-title h2 {
        font-size: 28px;
        font-weight: 700;
        color: #333;
        margin: 0;
    }
    
    .section-title p {
        color: #6c757d;
        margin: 5px 0 0 0;
        font-size: 16px;
    }
    
    /* Vaccines Grid */
    .vaccines-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 25px;
        margin-bottom: 50px;
    }
    
    .vaccine-card {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        border: 1px solid #eee;
        transition: all 0.3s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    
    .vaccine-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 30px rgba(13,110,253,0.15);
        border-color: #0d6efd;
    }
    
    .vaccine-header {
        background: linear-gradient(135deg, #0d6efd, #0b5ed7);
        color: white;
        padding: 25px 20px;
        position: relative;
    }
    
    .vaccine-header h3 {
        font-size: 22px;
        font-weight: 700;
        margin: 0;
    }
    
    .vaccine-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        background: rgba(255,255,255,0.2);
        color: white;
        padding: 5px 12px;
        border-radius: 50px;
        font-size: 12px;
        font-weight: 600;
        backdrop-filter: blur(5px);
    }
    
    .vaccine-body {
        padding: 25px;
        flex: 1;
    }
    
    .vaccine-manufacturer {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #6c757d;
        font-size: 14px;
        margin-bottom: 15px;
        padding-bottom: 15px;
        border-bottom: 1px solid #eee;
    }
    
    .vaccine-manufacturer i {
        color: #0d6efd;
    }
    
    .vaccine-description {
        color: #6c757d;
        font-size: 14px;
        line-height: 1.6;
        margin-bottom: 20px;
    }
    
    .vaccine-details {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 15px;
        margin-bottom: 20px;
    }
    
    .detail-row {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        border-bottom: 1px dashed #dee2e6;
    }
    
    .detail-row:last-child {
        border-bottom: none;
    }
    
    .detail-label {
        color: #6c757d;
        font-size: 13px;
    }
    
    .detail-value {
        font-weight: 700;
        color: #0d6efd;
        font-size: 14px;
    }
    
    .vaccine-footer {
        padding: 20px 25px;
        border-top: 1px solid #eee;
        background: #f8f9fa;
    }
    
    .btn-book {
        display: inline-block;
        width: 100%;
        padding: 12px;
        background: #0d6efd;
        color: white;
        text-decoration: none;
        border-radius: 10px;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.3s ease;
        text-align: center;
    }
    
    .btn-book:hover {
        background: #0b5ed7;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(13,110,253,0.3);
    }
    
    /* History Table */
    .history-card {
        background: white;
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        border: 1px solid #eee;
        margin-bottom: 40px;
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
    
    .badge {
        display: inline-block;
        padding: 5px 12px;
        border-radius: 50px;
        font-size: 12px;
        font-weight: 600;
    }
    
    .badge-info {
        background: #e7f1ff;
        color: #0d6efd;
    }
    
    .badge-success {
        background: #d1e7dd;
        color: #0f5132;
    }
    
    .badge-warning {
        background: #fff3cd;
        color: #856404;
    }
    
    .badge-danger {
        background: #f8d7da;
        color: #842029;
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
        margin-bottom: 25px;
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
        box-shadow: 0 5px 15px rgba(13,110,253,0.3);
    }
    
    /* Info Cards */
    .info-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 25px;
        margin-top: 30px;
    }
    
    .info-card {
        background: white;
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        border: 1px solid #eee;
        display: flex;
        align-items: center;
        gap: 15px;
        transition: all 0.3s ease;
    }
    
    .info-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(13,110,253,0.1);
        border-color: #0d6efd;
    }
    
    .info-icon {
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
    
    .info-content h4 {
        font-size: 16px;
        font-weight: 600;
        color: #333;
        margin-bottom: 5px;
    }
    
    .info-content p {
        color: #6c757d;
        font-size: 13px;
        margin: 0;
    }
    
    /* Responsive */
    @media (max-width: 992px) {
        .vaccines-grid,
        .info-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    
    @media (max-width: 768px) {
        .vaccines-grid,
        .info-grid {
            grid-template-columns: 1fr;
        }
        
        .page-header h1 {
            font-size: 32px;
        }
        
        .section-title h2 {
            font-size: 24px;
        }
        
        .table-responsive {
            font-size: 13px;
        }
        
        td, th {
            padding: 10px;
        }
    }
</style>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <h1>Vaccine Information</h1>
        <p>Learn about available COVID-19 vaccines and track your vaccination history</p>
    </div>
</section>

<!-- Main Content -->
<main class="main-content">
    <div class="container">
        <!-- Available Vaccines Section -->
        <div class="section-title">
            <i class="fas fa-syringe"></i>
            <div>
                <h2>Available Vaccines</h2>
                <p>Currently available COVID-19 vaccines for vaccination</p>
            </div>
        </div>
        
        <?php if (!empty($vaccines)): ?>
            <div class="vaccines-grid">
                <?php foreach ($vaccines as $vaccine): ?>
                <div class="vaccine-card">
                    <div class="vaccine-header">
                        <h3><?php echo htmlspecialchars($vaccine['name']); ?></h3>
                        <span class="vaccine-badge">
                            <i class="fas fa-check-circle"></i> Available
                        </span>
                    </div>
                    <div class="vaccine-body">
                        <div class="vaccine-manufacturer">
                            <i class="fas fa-flask"></i>
                            <?php echo htmlspecialchars($vaccine['manufacturer']); ?>
                        </div>
                        
                        <div class="vaccine-description">
                            <?php echo $vaccine['description'] ?: 'No description available for this vaccine.'; ?>
                        </div>
                        
                        <div class="vaccine-details">
                            <div class="detail-row">
                                <span class="detail-label">Doses Required</span>
                                <span class="detail-value"><?php echo $vaccine['doses_required']; ?></span>
                            </div>
                            <?php if ($vaccine['gap_between_doses']): ?>
                            <div class="detail-row">
                                <span class="detail-label">Gap Between Doses</span>
                                <span class="detail-value"><?php echo $vaccine['gap_between_doses']; ?> days</span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="vaccine-footer">
                        <a href="<?php echo SITE_URL; ?>patient/book_appointment.php?type=vaccination" class="btn-book">
                            <i class="fas fa-calendar-plus me-2"></i>Book Vaccination
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state" style="background: white; border-radius: 20px; padding: 60px; margin-bottom: 40px;">
                <i class="fas fa-syringe"></i>
                <h3>No Vaccines Available</h3>
                <p>There are no vaccines available at the moment. Please check back later.</p>
            </div>
        <?php endif; ?>

        <!-- My Vaccination History -->
        <div class="history-card">
            <div class="section-title" style="margin-bottom: 25px;">
                <i class="fas fa-history" style="background: #d1e7dd; color: #0f5132;"></i>
                <div>
                    <h2>My Vaccination History</h2>
                    <p>Track your COVID-19 vaccination records</p>
                </div>
            </div>
            
            <?php if (!empty($vaccinations)): ?>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Vaccine</th>
                                <th>Hospital</th>
                                <th>Dose</th>
                                <th>Next Dose</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($vaccinations as $row): ?>
                            <tr>
                                <td><?php echo formatDate($row['vaccination_date']); ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($row['vaccine_name']); ?></strong>
                                </td>
                                <td><?php echo htmlspecialchars($row['hospital_name']); ?></td>
                                <td>
                                    <span class="badge badge-info">Dose <?php echo $row['dose_number']; ?></span>
                                </td>
                                <td>
                                    <?php if($row['next_due_date']): ?>
                                        <?php echo formatDate($row['next_due_date']); ?>
                                        <?php if(strtotime($row['next_due_date']) < time()): ?>
                                            <span class="badge badge-warning ms-2">Overdue</span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="badge badge-success">Completed</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge <?php echo $row['status'] == 'completed' ? 'badge-success' : 'badge-warning'; ?>">
                                        <?php echo ucfirst($row['status']); ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-syringe" style="font-size: 50px;"></i>
                    <p class="text-muted">You haven't taken any vaccination yet.</p>
                    <a href="<?php echo SITE_URL; ?>patient/book_appointment.php?type=vaccination" class="empty-btn">
                        <i class="fas fa-calendar-plus me-2"></i>Book Vaccination
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Important Information -->
        <div class="info-grid">
            <div class="info-card">
                <div class="info-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="info-content">
                    <h4>Two Doses Required</h4>
                    <p>For most vaccines, two doses are needed for full protection</p>
                </div>
            </div>
            
            <div class="info-card">
                <div class="info-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="info-content">
                    <h4>Gap Between Doses</h4>
                    <p>28-84 days depending on vaccine type</p>
                </div>
            </div>
            
            <div class="info-card">
                <div class="info-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <div class="info-content">
                    <h4>Booster Dose</h4>
                    <p>Available after 6 months of completion</p>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
require_once '../includes/footer.php';
?>