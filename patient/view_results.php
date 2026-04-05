<?php
require_once '../config.php';
require_once '../includes/functions.php';

// Check if patient is logged in
if (!isset($_SESSION['patient_id'])) {
    setFlashMessage('Please login to view test results', 'danger');
    redirect(SITE_URL . 'patient/login.php');
}

$page_title = 'Test Results - COVID Care System';
require_once '../includes/header.php';
require_once '../includes/navbar.php';

$patient_id = $_SESSION['patient_id'];

// Get test results
$test_results = getPatientTestResults($conn, $patient_id);
?>

<style>
    /* ===== TEST RESULTS STYLES ===== */
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
    
    .results-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 25px;
        margin-bottom: 50px;
    }
    
    .result-card {
        background: white;
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        border: 1px solid #eee;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .result-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(13,110,253,0.15);
        border-color: #0d6efd;
    }
    
    .result-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 1px solid #eee;
    }
    
    .result-date {
        font-size: 16px;
        font-weight: 600;
        color: #0d6efd;
    }
    
    .result-date i {
        margin-right: 5px;
    }
    
    .result-badge {
        padding: 5px 15px;
        border-radius: 50px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
    }
    
    .result-positive {
        background: #f8d7da;
        color: #842029;
    }
    
    .result-negative {
        background: #d1e7dd;
        color: #0f5132;
    }
    
    .result-hospital {
        font-size: 18px;
        font-weight: 700;
        color: #333;
        margin-bottom: 15px;
    }
    
    .result-hospital i {
        color: #0d6efd;
        margin-right: 8px;
    }
    
    .result-details {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 15px;
        margin-top: 15px;
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
        font-size: 14px;
    }
    
    .detail-value {
        font-weight: 600;
        color: #333;
        font-size: 14px;
    }
    
    .result-footer {
        margin-top: 20px;
        padding-top: 15px;
        border-top: 1px solid #eee;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }
    
    .btn-download {
        padding: 8px 15px;
        background: #0d6efd;
        color: white;
        text-decoration: none;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        transition: all 0.3s ease;
    }
    
    .btn-download:hover {
        background: #0b5ed7;
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
        .results-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <h1>Test Results</h1>
        <p>View your COVID-19 test results</p>
    </div>
</section>

<!-- Results Section -->
<section class="container">
    <?php if (!empty($test_results)): ?>
    <div class="results-grid">
        <?php foreach ($test_results as $test): ?>
        <div class="result-card">
            <div class="result-header">
                <span class="result-date">
                    <i class="fas fa-calendar"></i>
                    <?php echo formatDate($test['test_date']); ?>
                </span>
                <span class="result-badge <?php echo $test['result'] == 'positive' ? 'result-positive' : 'result-negative'; ?>">
                    <?php echo strtoupper($test['result']); ?>
                </span>
            </div>
            
            <div class="result-hospital">
                <i class="fas fa-hospital"></i>
                <?php echo $test['hospital_name']; ?>
            </div>
            
            <div class="result-details">
                <div class="detail-row">
                    <span class="detail-label">Test Type</span>
                    <span class="detail-value">COVID-19 RT-PCR</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Test Date</span>
                    <span class="detail-value"><?php echo formatDate($test['test_date']); ?></span>
                </div>
                <?php if (!empty($test['notes'])): ?>
                <div class="detail-row">
                    <span class="detail-label">Notes</span>
                    <span class="detail-value"><?php echo $test['notes']; ?></span>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="result-footer">
                <a href="download_report.php?test_id=<?php echo $test['result']; ?>" class="btn btn-primary">
    <i class="fas fa-download"></i> Download PDF
</a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    
    <?php else: ?>
    <div class="empty-state">
        <i class="fas fa-flask"></i>
        <h3>No Test Results Found</h3>
        <p>You haven't taken any COVID-19 tests yet.</p>
        <a href="<?php echo SITE_URL; ?>patient/book_appointment.php?type=test" class="btn btn-primary">
            <i class="fas fa-calendar-plus me-2"></i>Book a Test
        </a>
    </div>
    <?php endif; ?>
</section>

<?php
require_once '../includes/footer.php';
?>