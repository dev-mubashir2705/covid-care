<?php
require_once '../config.php';
require_once '../includes/functions.php';

// Check if patient is logged in
if (!isset($_SESSION['patient_id'])) {
    setFlashMessage('Please login to view vaccination history', 'danger');
    redirect(SITE_URL . 'patient/login.php');
}

$page_title = 'Vaccination History - COVID Care System';
require_once '../includes/header.php';
require_once '../includes/navbar.php';

$patient_id = $_SESSION['patient_id'];

// Get vaccination records
$vaccinations = getPatientVaccinations($conn, $patient_id);
?>

<style>
    /* ===== VACCINATION HISTORY STYLES ===== */
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
    
    .timeline {
        position: relative;
        max-width: 800px;
        margin: 0 auto 50px;
    }
    
    .timeline::before {
        content: '';
        position: absolute;
        left: 50%;
        transform: translateX(-50%);
        width: 4px;
        height: 100%;
        background: linear-gradient(180deg, #0d6efd, #0b5ed7);
        border-radius: 2px;
    }
    
    .timeline-item {
        position: relative;
        margin-bottom: 50px;
        width: 100%;
    }
    
    .timeline-item:nth-child(odd) {
        padding-right: 50%;
    }
    
    .timeline-item:nth-child(even) {
        padding-left: 50%;
    }
    
    .timeline-content {
        background: white;
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        border: 1px solid #eee;
        position: relative;
        transition: all 0.3s ease;
    }
    
    .timeline-content:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(13,110,253,0.15);
        border-color: #0d6efd;
    }
    
    .timeline-dot {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 20px;
        height: 20px;
        background: #0d6efd;
        border: 4px solid white;
        border-radius: 50%;
        box-shadow: 0 0 0 3px rgba(13,110,253,0.2);
        z-index: 2;
    }
    
    .timeline-item:nth-child(odd) .timeline-dot {
        right: -10px;
    }
    
    .timeline-item:nth-child(even) .timeline-dot {
        left: -10px;
    }
    
    .vaccine-badge {
        display: inline-block;
        padding: 5px 15px;
        background: #0d6efd;
        color: white;
        border-radius: 50px;
        font-size: 12px;
        font-weight: 600;
        margin-bottom: 15px;
    }
    
    .vaccine-name {
        font-size: 22px;
        font-weight: 700;
        color: #333;
        margin-bottom: 10px;
    }
    
    .vaccine-manufacturer {
        color: #6c757d;
        font-size: 14px;
        margin-bottom: 15px;
        padding-bottom: 15px;
        border-bottom: 1px solid #eee;
    }
    
    .vaccine-details {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
    }
    
    .detail-box {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 12px;
        text-align: center;
    }
    
    .detail-label {
        font-size: 12px;
        color: #6c757d;
        margin-bottom: 5px;
    }
    
    .detail-value {
        font-size: 16px;
        font-weight: 700;
        color: #0d6efd;
    }
    
    .hospital-info {
        margin-top: 15px;
        padding-top: 15px;
        border-top: 1px solid #eee;
        display: flex;
        align-items: center;
        gap: 10px;
        color: #6c757d;
        font-size: 14px;
    }
    
    .hospital-info i {
        color: #0d6efd;
    }
    
    .grid-view {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 25px;
        margin-bottom: 50px;
    }
    
    .vaccine-card {
        background: white;
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        border: 1px solid #eee;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .vaccine-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(13,110,253,0.15);
        border-color: #0d6efd;
    }
    
    .vaccine-card .dose-number {
        position: absolute;
        top: 10px;
        right: 10px;
        background: #0d6efd;
        color: white;
        padding: 5px 12px;
        border-radius: 50px;
        font-size: 12px;
        font-weight: 600;
    }
    
    .vaccine-card .vaccine-icon {
        width: 60px;
        height: 60px;
        background: #e7f1ff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 15px;
        color: #0d6efd;
        font-size: 30px;
    }
    
    .view-toggle {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-bottom: 30px;
    }
    
    .toggle-btn {
        padding: 8px 15px;
        background: white;
        border: 1px solid #eee;
        border-radius: 8px;
        color: #6c757d;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .toggle-btn.active {
        background: #0d6efd;
        color: white;
        border-color: #0d6efd;
    }
    
    .toggle-btn i {
        margin-right: 5px;
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
    
    @media (max-width: 768px) {
        .timeline::before {
            left: 30px;
        }
        
        .timeline-item:nth-child(odd),
        .timeline-item:nth-child(even) {
            padding: 0 0 0 80px;
        }
        
        .timeline-dot {
            left: 20px !important;
            right: auto !important;
        }
    }
</style>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <h1>Vaccination History</h1>
        <p>Track your COVID-19 vaccination records</p>
    </div>
</section>

<!-- Vaccination History Section -->
<section class="container">
    <?php if (!empty($vaccinations)): ?>
    
    <div class="view-toggle">
        <button class="toggle-btn active" onclick="showTimeline()" id="timelineBtn">
            <i class="fas fa-stream"></i> Timeline
        </button>
        <button class="toggle-btn" onclick="showGrid()" id="gridBtn">
            <i class="fas fa-th"></i> Grid
        </button>
    </div>
    
    <!-- Timeline View -->
    <div id="timelineView">
        <div class="timeline">
            <?php 
            $dose_count = 1;
            foreach ($vaccinations as $vac): 
            ?>
            <div class="timeline-item">
                <div class="timeline-dot"></div>
                <div class="timeline-content">
                    <span class="vaccine-badge">Dose <?php echo $dose_count++; ?></span>
                    <h3 class="vaccine-name"><?php echo $vac['vaccine_name']; ?></h3>
                    <div class="vaccine-manufacturer"><?php echo $vac['manufacturer']; ?></div>
                    
                    <div class="vaccine-details">
                        <div class="detail-box">
                            <div class="detail-label">Vaccination Date</div>
                            <div class="detail-value"><?php echo formatDate($vac['vaccination_date']); ?></div>
                        </div>
                        <?php if ($vac['next_due_date']): ?>
                        <div class="detail-box">
                            <div class="detail-label">Next Dose Due</div>
                            <div class="detail-value"><?php echo formatDate($vac['next_due_date']); ?></div>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="hospital-info">
                        <i class="fas fa-hospital"></i>
                        <?php echo $vac['hospital_name']; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <!-- Grid View (Hidden by default) -->
    <div id="gridView" style="display: none;">
        <div class="grid-view">
            <?php foreach ($vaccinations as $vac): ?>
            <div class="vaccine-card">
                <span class="dose-number">Dose <?php echo $vac['dose_number']; ?></span>
                <div class="vaccine-icon">
                    <i class="fas fa-syringe"></i>
                </div>
                <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 5px;"><?php echo $vac['vaccine_name']; ?></h3>
                <p style="color: #6c757d; font-size: 13px; margin-bottom: 15px;"><?php echo $vac['manufacturer']; ?></p>
                
                <div style="margin-bottom: 10px;">
                    <span style="color: #6c757d; font-size: 12px;">Vaccination Date</span><br>
                    <span style="font-weight: 600; color: #0d6efd;"><?php echo formatDate($vac['vaccination_date']); ?></span>
                </div>
                
                <?php if ($vac['next_due_date']): ?>
                <div style="margin-bottom: 10px;">
                    <span style="color: #6c757d; font-size: 12px;">Next Dose Due</span><br>
                    <span style="font-weight: 600; color: #0d6efd;"><?php echo formatDate($vac['next_due_date']); ?></span>
                </div>
                <?php endif; ?>
                
                <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #eee; color: #6c757d; font-size: 13px;">
                    <i class="fas fa-hospital me-1" style="color: #0d6efd;"></i>
                    <?php echo $vac['hospital_name']; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <?php else: ?>
    <div class="empty-state">
        <i class="fas fa-syringe"></i>
        <h3>No Vaccination Records Found</h3>
        <p>You haven't received any COVID-19 vaccinations yet.</p>
        <a href="<?php echo SITE_URL; ?>patient/book_appointment.php?type=vaccination" class="btn btn-primary">
            <i class="fas fa-calendar-plus me-2"></i>Book Vaccination
        </a>
    </div>
    <?php endif; ?>
</section>

<script>
    function showTimeline() {
        document.getElementById('timelineView').style.display = 'block';
        document.getElementById('gridView').style.display = 'none';
        document.getElementById('timelineBtn').classList.add('active');
        document.getElementById('gridBtn').classList.remove('active');
    }
    
    function showGrid() {
        document.getElementById('timelineView').style.display = 'none';
        document.getElementById('gridView').style.display = 'block';
        document.getElementById('timelineBtn').classList.remove('active');
        document.getElementById('gridBtn').classList.add('active');
    }
</script>

<?php
require_once '../includes/footer.php';
?>