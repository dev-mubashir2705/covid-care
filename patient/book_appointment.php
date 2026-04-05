<?php
require_once '../config.php';
require_once '../includes/functions.php';

// Check if patient is logged in
if (!isset($_SESSION['patient_id'])) {
    setFlashMessage('Please login to book appointment', 'danger');
    redirect(SITE_URL . 'patient/login.php');
}

$page_title = 'Book Appointment - COVID Care System';
require_once '../includes/header.php';
require_once '../includes/navbar.php';

$patient_id = $_SESSION['patient_id'];
$error = '';
$success = '';

// Get all approved hospitals
$hospitals = getApprovedHospitals($conn);

// Get available vaccines
$vaccines = getAvailableVaccines($conn);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $hospital_id = sanitize($_POST['hospital_id']);
    $appointment_type = sanitize($_POST['appointment_type']);
    $appointment_date = sanitize($_POST['appointment_date']);
    $appointment_time = sanitize($_POST['appointment_time']);
    $vaccine_id = isset($_POST['vaccine_id']) ? sanitize($_POST['vaccine_id']) : null;
    
    // Check if slot is available
    if (!isTimeSlotAvailable($conn, $hospital_id, $appointment_date, $appointment_time)) {
        $error = "This time slot is already booked. Please choose another time.";
    } else {
        $query = "INSERT INTO appointments (patient_id, hospital_id, appointment_type, appointment_date, appointment_time, status) 
                  VALUES ('$patient_id', '$hospital_id', '$appointment_type', '$appointment_date', '$appointment_time', 'pending')";
        
        if (mysqli_query($conn, $query)) {
            setFlashMessage('Appointment booked successfully! Waiting for approval.', 'success');
            redirect(SITE_URL . 'patient/my_appointments.php');
        } else {
            $error = "Failed to book appointment. Please try again.";
        }
    }
}

// Get available time slots for selected date (if any)
$available_slots = [];
if (isset($_GET['date']) && isset($_GET['hospital'])) {
    $selected_date = sanitize($_GET['date']);
    $selected_hospital = sanitize($_GET['hospital']);
    $available_slots = getAvailableTimeSlots($conn, $selected_hospital, $selected_date);
}

// Get today's date for min attribute
$today = date('Y-m-d');
?>

<style>
    /* ===== BOOK APPOINTMENT STYLES ===== */
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
    
    .booking-container {
        max-width: 800px;
        margin: 0 auto 60px;
    }
    
    .booking-card {
        background: white;
        border-radius: 20px;
        padding: 40px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        border: 1px solid #eee;
    }
    
    .form-section {
        margin-bottom: 30px;
        padding-bottom: 30px;
        border-bottom: 1px solid #eee;
    }
    
    .form-section:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }
    
    .section-title {
        font-size: 20px;
        font-weight: 700;
        color: #333;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .section-title i {
        color: #0d6efd;
        font-size: 24px;
    }
    
    .form-group {
        margin-bottom: 20px;
    }
    
    .form-label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #333;
        font-size: 14px;
    }
    
    .form-label i {
        color: #0d6efd;
        margin-right: 5px;
    }
    
    .form-control {
        width: 100%;
        padding: 12px 15px;
        border: 2px solid #e9ecef;
        border-radius: 10px;
        font-size: 15px;
        transition: all 0.3s ease;
        background: #f8f9fa;
    }
    
    .form-control:focus {
        outline: none;
        border-color: #0d6efd;
        background: white;
        box-shadow: 0 0 0 4px rgba(13,110,253,0.1);
    }
    
    select.form-control {
        cursor: pointer;
    }
    
    .time-slots {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 10px;
        margin-top: 15px;
    }
    
    .time-slot {
        padding: 12px;
        text-align: center;
        border: 2px solid #e9ecef;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 14px;
        font-weight: 500;
    }
    
    .time-slot:hover {
        border-color: #0d6efd;
        background: #f0f7ff;
    }
    
    .time-slot.selected {
        background: #0d6efd;
        color: white;
        border-color: #0d6efd;
    }
    
    .time-slot.disabled {
        background: #f8f9fa;
        color: #adb5bd;
        border-color: #e9ecef;
        cursor: not-allowed;
        opacity: 0.6;
    }
    
    .btn-submit {
        width: 100%;
        padding: 15px;
        background: linear-gradient(135deg, #0d6efd, #0b5ed7);
        color: white;
        border: none;
        border-radius: 12px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 10px 20px rgba(13,110,253,0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        margin-top: 30px;
    }
    
    .btn-submit:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 30px rgba(13,110,253,0.3);
    }
    
    .info-box {
        background: #e7f1ff;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 30px;
        display: flex;
        align-items: flex-start;
        gap: 15px;
    }
    
    .info-box i {
        font-size: 24px;
        color: #0d6efd;
    }
    
    .info-box p {
        margin: 0;
        color: #333;
        font-size: 14px;
        line-height: 1.6;
    }
    
    .info-box strong {
        color: #0d6efd;
    }
    
    @media (max-width: 768px) {
        .page-header h1 {
            font-size: 32px;
        }
        
        .booking-card {
            padding: 25px;
        }
        
        .time-slots {
            grid-template-columns: repeat(2, 1fr);
        }
    }
</style>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <h1>Book Appointment</h1>
        <p>Schedule your COVID-19 test or vaccination appointment</p>
    </div>
</section>

<!-- Booking Form -->
<section class="container">
    <div class="booking-container">
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="booking-card">
            <div class="info-box">
                <i class="fas fa-info-circle"></i>
                <p>
                    <strong>Important:</strong> Please select your preferred hospital, date and time. 
                    Your appointment will be pending until approved by the hospital. 
                    You'll receive confirmation once approved.
                </p>
            </div>
            
            <form method="POST" action="" id="bookingForm">
                <!-- Hospital Selection -->
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-hospital"></i>
                        Select Hospital
                    </h3>
                    <div class="form-group">
                        <select class="form-control" name="hospital_id" id="hospital" required onchange="updateDates()">
                            <option value="">Choose a hospital</option>
                            <?php foreach ($hospitals as $hospital): ?>
                            <option value="<?php echo $hospital['hospital_id']; ?>">
                                <?php echo $hospital['name']; ?> - <?php echo $hospital['city']; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <!-- Appointment Type -->
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-syringe"></i>
                        Appointment Type
                    </h3>
                    <div class="form-group">
                        <select class="form-control" name="appointment_type" id="appointment_type" required onchange="toggleVaccineField()">
                            <option value="">Select type</option>
                            <option value="test">COVID-19 Test</option>
                            <option value="vaccination">Vaccination</option>
                        </select>
                    </div>
                    
                    <!-- Vaccine Selection (hidden by default) -->
                    <div class="form-group" id="vaccine_group" style="display: none;">
                        <label class="form-label">
                            <i class="fas fa-syringe"></i> Select Vaccine
                        </label>
                        <select class="form-control" name="vaccine_id" id="vaccine">
                            <option value="">Choose vaccine</option>
                            <?php foreach ($vaccines as $vaccine): ?>
                            <option value="<?php echo $vaccine['vaccine_id']; ?>">
                                <?php echo $vaccine['name']; ?> (<?php echo $vaccine['manufacturer']; ?>)
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <!-- Date Selection -->
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-calendar"></i>
                        Select Date
                    </h3>
                    <div class="form-group">
                        <input type="date" class="form-control" name="appointment_date" id="appointment_date" 
                               min="<?php echo $today; ?>" required onchange="checkAvailability()">
                    </div>
                </div>
                
                <!-- Time Slots -->
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-clock"></i>
                        Select Time
                    </h3>
                    <div id="time_slots_container">
                        <p class="text-muted">Please select hospital and date first</p>
                    </div>
                    <input type="hidden" name="appointment_time" id="selected_time">
                </div>
                
                <button type="submit" class="btn-submit" id="submitBtn" disabled>
                    <i class="fas fa-calendar-check"></i>
                    Book Appointment
                </button>
            </form>
        </div>
    </div>
</section>

<script>
    let availableSlots = [];
    
    function toggleVaccineField() {
        const type = document.getElementById('appointment_type').value;
        const vaccineGroup = document.getElementById('vaccine_group');
        
        if (type === 'vaccination') {
            vaccineGroup.style.display = 'block';
        } else {
            vaccineGroup.style.display = 'none';
        }
    }
    
    function updateDates() {
        const hospital = document.getElementById('hospital').value;
        const dateInput = document.getElementById('appointment_date');
        
        if (hospital) {
            dateInput.disabled = false;
        } else {
            dateInput.disabled = true;
        }
    }
    
    function checkAvailability() {
        const hospital = document.getElementById('hospital').value;
        const date = document.getElementById('appointment_date').value;
        const container = document.getElementById('time_slots_container');
        const submitBtn = document.getElementById('submitBtn');
        
        if (hospital && date) {
            // In a real application, you'd make an AJAX call here
            // For now, we'll show sample time slots
            const slots = [
                '09:00:00', '10:00:00', '11:00:00', '12:00:00',
                '14:00:00', '15:00:00', '16:00:00', '17:00:00'
            ];
            
            let html = '<div class="time-slots">';
            slots.forEach(slot => {
                const time = slot.substring(0, 5);
                html += `<div class="time-slot" onclick="selectTime('${slot}')">${time}</div>`;
            });
            html += '</div>';
            
            container.innerHTML = html;
            submitBtn.disabled = true;
        } else {
            container.innerHTML = '<p class="text-muted">Please select hospital and date first</p>';
            submitBtn.disabled = true;
        }
    }
    
    function selectTime(time) {
        // Remove selected class from all slots
        document.querySelectorAll('.time-slot').forEach(slot => {
            slot.classList.remove('selected');
        });
        
        // Add selected class to clicked slot
        event.target.classList.add('selected');
        
        // Set hidden input value
        document.getElementById('selected_time').value = time;
        
        // Enable submit button
        document.getElementById('submitBtn').disabled = false;
    }
</script>

<?php
require_once '../includes/footer.php';
?>