<?php
session_start();
require_once '../config.php';
require_once '../includes/functions.php';

// Check if hospital is logged in
if (!isset($_SESSION['hospital_id'])) {
    setFlashMessage('Please login to update vaccination', 'danger');
    redirect(SITE_URL . 'hospital/login.php');
}

$page_title = 'Update Vaccination - Hospital Panel';
require_once '../includes/header.php';
require_once '../includes/navbar.php';

$hospital_id = $_SESSION['hospital_id'];
$error = '';
$success = '';

// Get appointment ID from URL
$appointment_id = isset($_GET['appointment_id']) ? (int)$_GET['appointment_id'] : 0;

// Get all vaccines
$vaccines = getAvailableVaccines($conn);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $appointment_id = (int)$_POST['appointment_id'];
    $patient_id = (int)$_POST['patient_id'];
    $vaccine_id = (int)$_POST['vaccine_id'];
    $dose_number = (int)$_POST['dose_number'];
    $next_due_date = !empty($_POST['next_due_date']) ? $_POST['next_due_date'] : 'NULL';
    $vaccination_date = date('Y-m-d');
    
    $query = "INSERT INTO vaccination_records (patient_id, hospital_id, vaccine_id, dose_number, vaccination_date, next_due_date, status) 
              VALUES ($patient_id, $hospital_id, $vaccine_id, $dose_number, '$vaccination_date', " . ($next_due_date != 'NULL' ? "'$next_due_date'" : "NULL") . ", 'completed')";
    
    if (mysqli_query($conn, $query)) {
        mysqli_query($conn, "UPDATE appointments SET status = 'completed' WHERE appointment_id = $appointment_id");
        $success = "Vaccination record updated successfully!";
    } else {
        $error = "Failed to update vaccination record.";
    }
}

// Get pending vaccination appointments
$query = "SELECT a.*, p.name as patient_name, p.phone 
          FROM appointments a
          JOIN patients p ON a.patient_id = p.patient_id
          WHERE a.hospital_id = $hospital_id 
          AND a.appointment_type = 'vaccination' 
          AND a.status = 'approved'
          ORDER BY a.appointment_date DESC";
$result = mysqli_query($conn, $query);
$appointments = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $appointments[] = $row;
    }
}

// Get selected appointment
$selected_appointment = null;
if ($appointment_id > 0) {
    foreach ($appointments as $apt) {
        if ($apt['appointment_id'] == $appointment_id) {
            $selected_appointment = $apt;
            break;
        }
    }
}
?>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <h1>Update Vaccination</h1>
        <p>Record COVID-19 vaccination for patients</p>
    </div>
</section>

<!-- Main Content -->
<main class="main-content">
    <div class="container">
        
        <!-- Statistics -->
        <div class="stats-row">
            <div class="stat-box">
                <div class="stat-icon">
                    <i class="fas fa-hourglass-half"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo count($appointments); ?></h3>
                    <p>Pending Vaccinations</p>
                </div>
            </div>
            
            <div class="stat-box">
                <div class="stat-icon">
                    <i class="fas fa-syringe"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo count($vaccines); ?></h3>
                    <p>Available Vaccines</p>
                </div>
            </div>
            
            <div class="stat-box">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-info">
                    <h3>0</h3>
                    <p>Completed Today</p>
                </div>
            </div>
        </div>
        
        <!-- Alert Messages -->
        <?php if ($error): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?php echo $success; ?>
            </div>
        <?php endif; ?>
        
        <!-- Main Row -->
        <div class="content-row">
            <!-- Left Column - Pending Appointments -->
            <div class="content-card">
                <div class="card-title">
                    <i class="fas fa-clock"></i>
                    <h3>Pending Vaccination Appointments</h3>
                </div>
                
                <?php if (!empty($appointments)): ?>
                    <div class="appointment-list">
                        <?php foreach ($appointments as $apt): ?>
                        <a href="?appointment_id=<?php echo $apt['appointment_id']; ?>" 
                           class="appointment-item <?php echo ($apt['appointment_id'] == $appointment_id) ? 'selected' : ''; ?>">
                            <div class="item-patient">
                                <i class="fas fa-user-circle"></i>
                                <span class="patient-name"><?php echo htmlspecialchars($apt['patient_name']); ?></span>
                            </div>
                            
                            <div class="item-datetime">
                                <span><i class="fas fa-calendar"></i> <?php echo date('d M Y', strtotime($apt['appointment_date'])); ?></span>
                                <span><i class="fas fa-clock"></i> <?php echo date('h:i A', strtotime($apt['appointment_time'])); ?></span>
                            </div>
                            
                            <div class="item-phone">
                                <i class="fas fa-phone"></i> <?php echo formatPhone($apt['phone']); ?>
                            </div>
                            
                            <?php if ($apt['appointment_id'] == $appointment_id): ?>
                                <div class="selected-tag">
                                    <i class="fas fa-check"></i> Selected
                                </div>
                            <?php endif; ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-box">
                        <i class="fas fa-calendar-times"></i>
                        <h4>No Pending Vaccinations</h4>
                        <p>There are no approved vaccination appointments waiting.</p>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Right Column - Update Form -->
            <div class="content-card">
                <div class="card-title">
                    <i class="fas <?php echo $selected_appointment ? 'fa-syringe' : 'fa-hand-pointer'; ?>"></i>
                    <h3><?php echo $selected_appointment ? 'Record Vaccination' : 'Select Appointment'; ?></h3>
                </div>
                
                <?php if ($selected_appointment): ?>
                    <!-- Patient Summary -->
                    <div class="patient-box">
                        <div class="patient-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="patient-details">
                            <h4><?php echo htmlspecialchars($selected_appointment['patient_name']); ?></h4>
                            <p><i class="fas fa-phone"></i> <?php echo formatPhone($selected_appointment['phone']); ?></p>
                            <div class="appt-time">
                                <span><i class="fas fa-calendar"></i> <?php echo date('d M Y', strtotime($selected_appointment['appointment_date'])); ?></span>
                                <span><i class="fas fa-clock"></i> <?php echo date('h:i A', strtotime($selected_appointment['appointment_time'])); ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Form -->
                    <form method="POST">
                        <input type="hidden" name="appointment_id" value="<?php echo $selected_appointment['appointment_id']; ?>">
                        <input type="hidden" name="patient_id" value="<?php echo $selected_appointment['patient_id']; ?>">
                        
                        <div class="field-group">
                            <label><i class="fas fa-syringe"></i> Vaccine</label>
                            <select name="vaccine_id" required>
                                <option value="">Select Vaccine</option>
                                <?php foreach ($vaccines as $v): ?>
                                <option value="<?php echo $v['vaccine_id']; ?>">
                                    <?php echo htmlspecialchars($v['name']); ?> (<?php echo htmlspecialchars($v['manufacturer']); ?>)
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="field-group">
                            <label><i class="fas fa-syringe"></i> Dose Number</label>
                            <select name="dose_number" required>
                                <option value="">Select Dose</option>
                                <option value="1">Dose 1</option>
                                <option value="2">Dose 2</option>
                                <option value="3">Booster Dose</option>
                            </select>
                        </div>
                        
                        <div class="field-group">
                            <label><i class="fas fa-calendar"></i> Next Dose Due Date (Optional)</label>
                            <input type="date" name="next_due_date">
                        </div>
                        
                        <div class="button-row">
                            <button type="submit" class="btn-save">
                                <i class="fas fa-save"></i> Record Vaccination
                            </button>
                            <a href="update_vaccination.php" class="btn-cancel">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                    
                <?php else: ?>
                    <div class="empty-box">
                        <i class="fas fa-hand-pointer"></i>
                        <h4>No Appointment Selected</h4>
                        <p>Please select an appointment from the list.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
    </div>
</main>

<style>
    /* ===== SIMPLE CLEAN STYLES ===== */
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
    }
    
    /* Page Header */
    .page-header {
        background: linear-gradient(135deg, #0d6efd, #0b5ed7);
        padding: 60px 0;
        margin-bottom: 40px;
        color: white;
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
    
    /* Statistics */
    .stats-row {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 25px;
        margin-bottom: 40px;
    }
    
    .stat-box {
        background: white;
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        border: 1px solid #eee;
        display: flex;
        align-items: center;
        gap: 20px;
    }
    
    .stat-icon {
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
    
    .stat-info h3 {
        font-size: 32px;
        font-weight: 700;
        color: #333;
        margin-bottom: 5px;
    }
    
    .stat-info p {
        color: #6c757d;
        font-size: 14px;
    }
    
    /* Content Row */
    .content-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 25px;
        margin-bottom: 40px;
    }
    
    .content-card {
        background: white;
        border-radius: 20px;
        padding: 25px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        border: 1px solid #eee;
    }
    
    .card-title {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 25px;
        padding-bottom: 15px;
        border-bottom: 2px solid #f0f0f0;
    }
    
    .card-title i {
        color: #0d6efd;
        font-size: 22px;
    }
    
    .card-title h3 {
        font-size: 20px;
        font-weight: 600;
        color: #333;
        margin: 0;
    }
    
    /* Appointment List */
    .appointment-list {
        display: flex;
        flex-direction: column;
        gap: 15px;
        max-height: 500px;
        overflow-y: auto;
    }
    
    .appointment-item {
        background: #f8f9fa;
        border: 1px solid #eee;
        border-radius: 12px;
        padding: 20px;
        text-decoration: none;
        display: block;
        transition: all 0.3s ease;
    }
    
    .appointment-item:hover {
        background: #e7f1ff;
        border-color: #0d6efd;
    }
    
    .appointment-item.selected {
        background: #e7f1ff;
        border-color: #0d6efd;
        border-left: 5px solid #0d6efd;
    }
    
    .item-patient {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 12px;
    }
    
    .item-patient i {
        color: #0d6efd;
        font-size: 20px;
    }
    
    .patient-name {
        font-weight: 600;
        color: #333;
        font-size: 16px;
    }
    
    .item-datetime {
        display: flex;
        gap: 20px;
        margin-bottom: 10px;
        color: #6c757d;
        font-size: 14px;
    }
    
    .item-datetime span {
        display: flex;
        align-items: center;
        gap: 5px;
    }
    
    .item-datetime i {
        color: #0d6efd;
    }
    
    .item-phone {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #6c757d;
        font-size: 14px;
        padding-top: 10px;
        border-top: 1px dashed #dee2e6;
    }
    
    .item-phone i {
        color: #0d6efd;
    }
    
    .selected-tag {
        display: inline-block;
        margin-top: 12px;
        background: #0d6efd;
        color: white;
        padding: 4px 15px;
        border-radius: 50px;
        font-size: 12px;
    }
    
    /* Patient Box */
    .patient-box {
        background: #f8f9fa;
        border-radius: 15px;
        padding: 25px;
        margin-bottom: 30px;
        display: flex;
        align-items: center;
        gap: 20px;
    }
    
    .patient-avatar {
        width: 70px;
        height: 70px;
        background: linear-gradient(135deg, #0d6efd, #0b5ed7);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 30px;
    }
    
    .patient-details h4 {
        font-size: 20px;
        font-weight: 600;
        color: #333;
        margin-bottom: 8px;
    }
    
    .patient-details p {
        color: #6c757d;
        margin: 5px 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .appt-time {
        display: flex;
        gap: 20px;
        margin-top: 10px;
        padding-top: 10px;
        border-top: 1px solid #dee2e6;
    }
    
    .appt-time span {
        display: flex;
        align-items: center;
        gap: 5px;
        color: #6c757d;
        font-size: 14px;
    }
    
    .appt-time i {
        color: #0d6efd;
    }
    
    /* Form */
    .field-group {
        margin-bottom: 25px;
    }
    
    .field-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #333;
    }
    
    .field-group label i {
        color: #0d6efd;
        margin-right: 5px;
    }
    
    .field-group select,
    .field-group input {
        width: 100%;
        padding: 12px 15px;
        border: 2px solid #e9ecef;
        border-radius: 10px;
        font-size: 15px;
    }
    
    .field-group select:focus,
    .field-group input:focus {
        outline: none;
        border-color: #0d6efd;
    }
    
    .button-row {
        display: flex;
        gap: 15px;
        margin-top: 30px;
    }
    
    .btn-save {
        flex: 2;
        padding: 14px;
        background: #0d6efd;
        color: white;
        border: none;
        border-radius: 10px;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }
    
    .btn-save:hover {
        background: #0b5ed7;
    }
    
    .btn-cancel {
        flex: 1;
        padding: 14px;
        background: #6c757d;
        color: white;
        border: none;
        border-radius: 10px;
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }
    
    .btn-cancel:hover {
        background: #5a6268;
    }
    
    /* Alert */
    .alert {
        padding: 15px 20px;
        border-radius: 10px;
        margin-bottom: 25px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .alert-danger {
        background: #f8d7da;
        color: #842029;
        border: 1px solid #f5c2c7;
    }
    
    .alert-success {
        background: #d1e7dd;
        color: #0f5132;
        border: 1px solid #badbcc;
    }
    
    /* Empty Box */
    .empty-box {
        text-align: center;
        padding: 50px 20px;
    }
    
    .empty-box i {
        font-size: 50px;
        color: #dee2e6;
        margin-bottom: 15px;
    }
    
    .empty-box h4 {
        font-size: 18px;
        font-weight: 600;
        color: #333;
        margin-bottom: 5px;
    }
    
    .empty-box p {
        color: #6c757d;
    }
    
    /* Responsive */
    @media (max-width: 992px) {
        .stats-row {
            grid-template-columns: repeat(2, 1fr);
        }
        .content-row {
            grid-template-columns: 1fr;
        }
    }
    
    @media (max-width: 768px) {
        .stats-row {
            grid-template-columns: 1fr;
        }
        .patient-box {
            flex-direction: column;
            text-align: center;
        }
        .appt-time {
            flex-direction: column;
            gap: 10px;
        }
        .button-row {
            flex-direction: column;
        }
    }
</style>

<?php require_once '../includes/footer.php'; ?>