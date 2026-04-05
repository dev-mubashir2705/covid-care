<?php
session_start();
require_once '../config.php';
require_once '../includes/functions.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    setFlashMessage('Please login to access this page', 'danger');
    redirect(SITE_URL . 'admin/login.php');
}

$page_title = 'Reports - Admin Panel';
require_once '../includes/header.php';
require_once '../includes/navbar.php';

$report_type = isset($_GET['type']) ? $_GET['type'] : 'appointments';
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d', strtotime('-30 days'));
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

if ($report_type == 'appointments') {
    $data = getAppointmentReport($conn, $start_date, $end_date);
} elseif ($report_type == 'tests') {
    $data = getTestReport($conn, $start_date, $end_date);
} elseif ($report_type == 'vaccinations') {
    $data = getVaccinationReport($conn, $start_date, $end_date);
}
?>

<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Segoe UI', sans-serif; background: #f8f9fa; min-height: 100vh; display: flex; flex-direction: column; }
    .main-content { flex: 1; padding: 40px 0; }
    .container { max-width: 1200px; margin: 0 auto; padding: 0 20px; width: 100%; }
    
    .page-header { background: linear-gradient(135deg, #0d6efd, #0b5ed7); padding: 60px 0; margin-bottom: 40px; color: white; text-align: center; }
    .page-header h1 { font-size: 42px; font-weight: 700; margin-bottom: 10px; }
    
    .filter-card { background: white; border-radius: 15px; padding: 25px; margin-bottom: 30px; border: 1px solid #eee; }
    .filter-form { display: flex; gap: 15px; flex-wrap: wrap; align-items: flex-end; }
    .filter-group { flex: 1; min-width: 150px; }
    .filter-group label { display: block; margin-bottom: 5px; font-weight: 600; color: #333; }
    .filter-group input, .filter-group select { width: 100%; padding: 10px; border: 2px solid #e9ecef; border-radius: 8px; }
    .filter-btn { padding: 10px 25px; background: #0d6efd; color: white; border: none; border-radius: 8px; cursor: pointer; }
    
    .report-card { background: white; border-radius: 15px; padding: 25px; border: 1px solid #eee; }
    table { width: 100%; border-collapse: collapse; }
    th { text-align: left; padding: 12px; background: #f8f9fa; color: #333; font-weight: 600; border-bottom: 2px solid #dee2e6; }
    td { padding: 12px; color: #6c757d; border-bottom: 1px solid #eee; }
    
    .badge { display: inline-block; padding: 5px 12px; border-radius: 50px; font-size: 12px; font-weight: 600; }
    .badge-positive { background: #f8d7da; color: #842029; }
    .badge-negative { background: #d1e7dd; color: #0f5132; }
    
    .export-btn { margin-top: 20px; text-align: right; }
    .btn-export { padding: 10px 25px; background: #28a745; color: white; border: none; border-radius: 8px; cursor: pointer; }
</style>

<section class="page-header">
    <div class="container">
        <h1>Reports</h1>
        <p>View and export system reports</p>
    </div>
</section>

<main class="main-content">
    <div class="container">
        <!-- Filter Form -->
        <div class="filter-card">
            <form method="GET" class="filter-form">
                <div class="filter-group">
                    <label>Report Type</label>
                    <select name="type">
                        <option value="appointments" <?php echo $report_type == 'appointments' ? 'selected' : ''; ?>>Appointments</option>
                        <option value="tests" <?php echo $report_type == 'tests' ? 'selected' : ''; ?>>Test Results</option>
                        <option value="vaccinations" <?php echo $report_type == 'vaccinations' ? 'selected' : ''; ?>>Vaccinations</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label>Start Date</label>
                    <input type="date" name="start_date" value="<?php echo $start_date; ?>">
                </div>
                <div class="filter-group">
                    <label>End Date</label>
                    <input type="date" name="end_date" value="<?php echo $end_date; ?>">
                </div>
                <button type="submit" class="filter-btn">Generate Report</button>
            </form>
        </div>
        
        <!-- Report Data -->
        <div class="report-card">
            <h3 style="margin-bottom: 20px;">
                <?php echo ucfirst($report_type); ?> Report 
                (<?php echo date('d M Y', strtotime($start_date)); ?> - <?php echo date('d M Y', strtotime($end_date)); ?>)
            </h3>
            
            <?php if (!empty($data)): ?>
            <table>
                <thead>
                    <tr>
                        <?php if ($report_type == 'appointments'): ?>
                            <th>Date</th>
                            <th>Patient</th>
                            <th>Hospital</th>
                            <th>Type</th>
                            <th>Status</th>
                        <?php elseif ($report_type == 'tests'): ?>
                            <th>Date</th>
                            <th>Patient</th>
                            <th>Hospital</th>
                            <th>Result</th>
                        <?php elseif ($report_type == 'vaccinations'): ?>
                            <th>Date</th>
                            <th>Patient</th>
                            <th>Hospital</th>
                            <th>Vaccine</th>
                            <th>Dose</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data as $row): ?>
                    <tr>
                        <?php if ($report_type == 'appointments'): ?>
                            <td><?php echo date('d M Y', strtotime($row['appointment_date'])); ?></td>
                            <td><?php echo $row['patient_name']; ?></td>
                            <td><?php echo $row['hospital_name']; ?></td>
                            <td><?php echo ucfirst($row['appointment_type']); ?></td>
                            <td><span class="badge badge-<?php echo $row['status']; ?>"><?php echo ucfirst($row['status']); ?></span></td>
                        <?php elseif ($report_type == 'tests'): ?>
                            <td><?php echo date('d M Y', strtotime($row['test_date'])); ?></td>
                            <td><?php echo $row['patient_name']; ?></td>
                            <td><?php echo $row['hospital_name']; ?></td>
                            <td><span class="badge badge-<?php echo $row['result']; ?>"><?php echo ucfirst($row['result']); ?></span></td>
                        <?php elseif ($report_type == 'vaccinations'): ?>
                            <td><?php echo date('d M Y', strtotime($row['vaccination_date'])); ?></td>
                            <td><?php echo $row['patient_name']; ?></td>
                            <td><?php echo $row['hospital_name']; ?></td>
                            <td><?php echo $row['vaccine_name']; ?></td>
                            <td>Dose <?php echo $row['dose_number']; ?></td>
                        <?php endif; ?>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <div class="export-btn">
                <button class="btn-export" onclick="alert('Export feature coming soon!')">
                    <i class="fas fa-download"></i> Export to CSV
                </button>
            </div>
            <?php else: ?>
                <p class="text-center text-muted">No data found for selected period.</p>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php require_once '../includes/footer.php'; ?>