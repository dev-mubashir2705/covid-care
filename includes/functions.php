<?php
/**
 * COVID-CARE System Functions
 * All common functions used throughout the application
 */

// Prevent direct access
if (!defined('SITE_URL')) {
    exit('Direct access not permitted');
}

/**
 * ============================================
 * DATABASE FUNCTIONS
 * ============================================
 */

/**
 * Get single record from database
 */
function getRecord($conn, $table, $where, $value) {
    $value = mysqli_real_escape_string($conn, $value);
    $query = "SELECT * FROM $table WHERE $where = '$value' LIMIT 1";
    $result = mysqli_query($conn, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }
    return null;
}

/**
 * Get multiple records from database
 */
function getRecords($conn, $table, $where = '', $order = '', $limit = '') {
    $query = "SELECT * FROM $table";
    
    if ($where != '') {
        $query .= " WHERE $where";
    }
    if ($order != '') {
        $query .= " ORDER BY $order";
    }
    if ($limit != '') {
        $query .= " LIMIT $limit";
    }
    
    $result = mysqli_query($conn, $query);
    $data = [];
    
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
    }
    return $data;
}

/**
 * Count records in table - FIXED
 */
function countRecords($conn, $table, $where = '') {
    $query = "SELECT COUNT(*) as total FROM $table";
    
    if ($where != '') {
        $query .= " WHERE $where";
    }
    
    $result = mysqli_query($conn, $query);
    
    // ✅ FIXED: Proper check before accessing
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        return (int)$row['total'];
    }
    
    return 0;
}

/**
 * Insert record into database
 */
function insertRecord($conn, $table, $data) {
    $columns = implode(", ", array_keys($data));
    
    $values = array_map(function($value) use ($conn) {
        return "'" . mysqli_real_escape_string($conn, $value) . "'";
    }, $data);
    
    $valuesString = implode(", ", $values);
    
    $query = "INSERT INTO $table ($columns) VALUES ($valuesString)";
    
    if (mysqli_query($conn, $query)) {
        return mysqli_insert_id($conn);
    }
    return false;
}

/**
 * Update record in database
 */
function updateRecord($conn, $table, $data, $where, $value) {
    $sets = [];
    
    foreach ($data as $key => $val) {
        $sets[] = "$key = '" . mysqli_real_escape_string($conn, $val) . "'";
    }
    
    $setString = implode(", ", $sets);
    $value = mysqli_real_escape_string($conn, $value);
    
    $query = "UPDATE $table SET $setString WHERE $where = '$value'";
    return mysqli_query($conn, $query);
}

/**
 * Delete record from database
 */
function deleteRecord($conn, $table, $where, $value) {
    $value = mysqli_real_escape_string($conn, $value);
    $query = "DELETE FROM $table WHERE $where = '$value'";
    return mysqli_query($conn, $query);
}

/**
 * ============================================
 * USER AUTHENTICATION FUNCTIONS
 * ============================================
 */

/**
 * Check if user is logged in
 */
function isLoggedIn($type = null) {
    if ($type == 'patient') {
        return isset($_SESSION['patient_id']);
    } elseif ($type == 'hospital') {
        return isset($_SESSION['hospital_id']);
    } elseif ($type == 'admin') {
        return isset($_SESSION['admin_id']);
    } else {
        return isset($_SESSION['patient_id']) || isset($_SESSION['hospital_id']) || isset($_SESSION['admin_id']);
    }
}

/**
 * Get current user ID
 */
function getCurrentUserId() {
    if (isset($_SESSION['patient_id'])) {
        return $_SESSION['patient_id'];
    } elseif (isset($_SESSION['hospital_id'])) {
        return $_SESSION['hospital_id'];
    } elseif (isset($_SESSION['admin_id'])) {
        return $_SESSION['admin_id'];
    }
    return null;
}

/**
 * Get current user type
 */
function getCurrentUserType() {
    if (isset($_SESSION['patient_id'])) {
        return 'patient';
    } elseif (isset($_SESSION['hospital_id'])) {
        return 'hospital';
    } elseif (isset($_SESSION['admin_id'])) {
        return 'admin';
    }
    return null;
}

/**
 * Get current user name
 */
function getCurrentUserName() {
    if (isset($_SESSION['patient_name'])) {
        return $_SESSION['patient_name'];
    } elseif (isset($_SESSION['hospital_name'])) {
        return $_SESSION['hospital_name'];
    } elseif (isset($_SESSION['admin_name'])) {
        return $_SESSION['admin_name'];
    }
    return 'Guest';
}

/**
 * Require login - redirect if not logged in
 */
function requireLogin($type = null) {
    if (!isLoggedIn($type)) {
        if ($type == 'admin') {
            redirect(SITE_URL . 'admin/login.php');
        } elseif ($type == 'hospital') {
            redirect(SITE_URL . 'hospital/login.php');
        } else {
            redirect(SITE_URL . 'patient/login.php');
        }
    }
}

/**
 * ============================================
 * DATA FORMATTING FUNCTIONS
 * ============================================
 */

/**
 * Format date
 */
function formatDate($date, $format = 'd M, Y') {
    if ($date && $date != '0000-00-00' && $date != null) {
        return date($format, strtotime($date));
    }
    return 'N/A';
}

/**
 * Format time
 */
function formatTime($time, $format = 'h:i A') {
    if ($time && $time != '00:00:00' && $time != null) {
        return date($format, strtotime($time));
    }
    return 'N/A';
}

/**
 * Format datetime
 */
function formatDateTime($datetime, $format = 'd M, Y h:i A') {
    if ($datetime && $datetime != '0000-00-00 00:00:00' && $datetime != null) {
        return date($format, strtotime($datetime));
    }
    return 'N/A';
}

/**
 * Format phone number - Pakistan format
 */
function formatPhone($phone) {
    if (empty($phone)) {
        return 'N/A';
    }
    
    // Pakistan mobile: 03XXXXXXXXX
    if (strlen($phone) == 11 && substr($phone, 0, 1) == '0') {
        return substr($phone, 0, 4) . ' ' . substr($phone, 4, 3) . ' ' . substr($phone, 7, 4);
    }
    
    // Pakistan landline: 021XXXXXXX
    if (strlen($phone) == 10 && substr($phone, 0, 1) == '0') {
        return substr($phone, 0, 3) . ' ' . substr($phone, 3, 3) . ' ' . substr($phone, 6, 4);
    }
    
    return $phone;
}

/**
 * Truncate text
 */
function truncateText($text, $length = 100) {
    if (strlen($text) > $length) {
        return substr($text, 0, $length) . '...';
    }
    return $text;
}

/**
 * ============================================
 * STATUS & BADGE FUNCTIONS
 * ============================================
 */

/**
 * Get status badge HTML
 */
function getStatusBadge($status) {
    switch (strtolower($status)) {
        case 'pending':
            return '<span class="badge bg-warning text-dark">Pending</span>';
        case 'approved':
            return '<span class="badge bg-success">Approved</span>';
        case 'rejected':
            return '<span class="badge bg-danger">Rejected</span>';
        case 'completed':
            return '<span class="badge bg-info">Completed</span>';
        case 'cancelled':
            return '<span class="badge bg-secondary">Cancelled</span>';
        case 'available':
            return '<span class="badge bg-success">Available</span>';
        case 'unavailable':
            return '<span class="badge bg-danger">Unavailable</span>';
        case 'positive':
            return '<span class="badge bg-danger">Positive</span>';
        case 'negative':
            return '<span class="badge bg-success">Negative</span>';
        default:
            return '<span class="badge bg-secondary">' . ucfirst($status) . '</span>';
    }
}

/**
 * Get appointment type badge
 */
function getAppointmentTypeBadge($type) {
    if ($type == 'test') {
        return '<span class="badge bg-primary">Test</span>';
    } elseif ($type == 'vaccination') {
        return '<span class="badge bg-success">Vaccination</span>';
    }
    return '<span class="badge bg-secondary">' . ucfirst($type) . '</span>';
}

/**
 * ============================================
 * NAME/ID LOOKUP FUNCTIONS
 * ============================================
 */

/**
 * Get patient name by ID
 */
function getPatientName($conn, $patient_id) {
    $patient = getRecord($conn, 'patients', 'patient_id', $patient_id);
    return $patient ? $patient['name'] : 'Unknown Patient';
}

/**
 * Get hospital name by ID
 */
function getHospitalName($conn, $hospital_id) {
    $hospital = getRecord($conn, 'hospitals', 'hospital_id', $hospital_id);
    return $hospital ? $hospital['name'] : 'Unknown Hospital';
}

/**
 * Get vaccine name by ID
 */
function getVaccineName($conn, $vaccine_id) {
    $vaccine = getRecord($conn, 'vaccines', 'vaccine_id', $vaccine_id);
    return $vaccine ? $vaccine['name'] : 'Unknown Vaccine';
}

/**
 * Get admin name by ID
 */
function getAdminName($conn, $admin_id) {
    $admin = getRecord($conn, 'admin', 'admin_id', $admin_id);
    return $admin ? $admin['username'] : 'Unknown Admin';
}

/**
 * ============================================
 * DASHBOARD STATISTICS FUNCTIONS
 * ============================================
 */

/**
 * Get patient dashboard statistics
 */
function getPatientStats($conn, $patient_id) {
    $stats = [
        'total_appointments' => 0,
        'pending_appointments' => 0,
        'approved_appointments' => 0,
        'completed_appointments' => 0,
        'total_tests' => 0,
        'total_vaccinations' => 0
    ];
    
    $stats['total_appointments'] = countRecords($conn, 'appointments', "patient_id = '$patient_id'");
    $stats['pending_appointments'] = countRecords($conn, 'appointments', "patient_id = '$patient_id' AND status = 'pending'");
    $stats['approved_appointments'] = countRecords($conn, 'appointments', "patient_id = '$patient_id' AND status = 'approved'");
    $stats['completed_appointments'] = countRecords($conn, 'appointments', "patient_id = '$patient_id' AND status = 'completed'");
    $stats['total_tests'] = countRecords($conn, 'test_results', "patient_id = '$patient_id'");
    $stats['total_vaccinations'] = countRecords($conn, 'vaccination_records', "patient_id = '$patient_id'");
    
    return $stats;
}

/**
 * Get hospital dashboard statistics
 */
function getHospitalStats($conn, $hospital_id) {
    $today = date('Y-m-d');
    
    $stats = [
        'total_appointments' => 0,
        'pending_appointments' => 0,
        'approved_appointments' => 0,
        'completed_appointments' => 0,
        'today_appointments' => 0,
        'total_patients' => 0,
        'total_tests' => 0,
        'total_vaccinations' => 0
    ];
    
    $stats['total_appointments'] = countRecords($conn, 'appointments', "hospital_id = '$hospital_id'");
    $stats['pending_appointments'] = countRecords($conn, 'appointments', "hospital_id = '$hospital_id' AND status = 'pending'");
    $stats['approved_appointments'] = countRecords($conn, 'appointments', "hospital_id = '$hospital_id' AND status = 'approved'");
    $stats['completed_appointments'] = countRecords($conn, 'appointments', "hospital_id = '$hospital_id' AND status = 'completed'");
    $stats['today_appointments'] = countRecords($conn, 'appointments', "hospital_id = '$hospital_id' AND appointment_date = '$today'");
    
    // Get unique patients count
    $query = "SELECT COUNT(DISTINCT patient_id) as total FROM appointments WHERE hospital_id = '$hospital_id'";
    $result = mysqli_query($conn, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $stats['total_patients'] = (int)$row['total'];
    }
    
    $stats['total_tests'] = countRecords($conn, 'test_results', "hospital_id = '$hospital_id'");
    $stats['total_vaccinations'] = countRecords($conn, 'vaccination_records', "hospital_id = '$hospital_id'");
    
    return $stats;
}

/**
 * Get admin dashboard statistics
 */
function getAdminStats($conn) {
    $stats = [
        'total_patients' => 0,
        'total_hospitals' => 0,
        'pending_hospitals' => 0,
        'approved_hospitals' => 0,
        'total_appointments' => 0,
        'total_tests' => 0,
        'total_vaccinations' => 0,
        'total_vaccines' => 0
    ];
    
    $stats['total_patients'] = countRecords($conn, 'patients');
    $stats['total_hospitals'] = countRecords($conn, 'hospitals');
    $stats['pending_hospitals'] = countRecords($conn, 'hospitals', "status = 'pending'");
    $stats['approved_hospitals'] = countRecords($conn, 'hospitals', "status = 'approved'");
    $stats['total_appointments'] = countRecords($conn, 'appointments');
    $stats['total_tests'] = countRecords($conn, 'test_results');
    $stats['total_vaccinations'] = countRecords($conn, 'vaccination_records');
    $stats['total_vaccines'] = countRecords($conn, 'vaccines');
    
    return $stats;
}

/**
 * ============================================
 * APPOINTMENT FUNCTIONS
 * ============================================
 */

/**
 * Check if time slot is available
 */
function isTimeSlotAvailable($conn, $hospital_id, $date, $time) {
    $count = countRecords($conn, 'appointments', 
        "hospital_id = '$hospital_id' AND appointment_date = '$date' AND appointment_time = '$time' AND status != 'cancelled'"
    );
    return $count == 0;
}

/**
 * Get available time slots for a hospital on a specific date
 */
function getAvailableTimeSlots($conn, $hospital_id, $date) {
    $all_slots = ['09:00:00', '10:00:00', '11:00:00', '12:00:00', '14:00:00', '15:00:00', '16:00:00', '17:00:00'];
    $booked_slots = [];
    
    $query = "SELECT appointment_time FROM appointments 
              WHERE hospital_id = '$hospital_id' 
              AND appointment_date = '$date' 
              AND status != 'cancelled'";
    $result = mysqli_query($conn, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $booked_slots[] = $row['appointment_time'];
        }
    }
    
    return array_diff($all_slots, $booked_slots);
}

/**
 * Get patient appointments
 */
function getPatientAppointments($conn, $patient_id, $limit = null) {
    $query = "SELECT a.*, h.name as hospital_name, h.address, h.city, h.phone 
              FROM appointments a
              JOIN hospitals h ON a.hospital_id = h.hospital_id
              WHERE a.patient_id = '$patient_id'
              ORDER BY a.appointment_date DESC, a.appointment_time DESC";
    
    if ($limit) {
        $query .= " LIMIT $limit";
    }
    
    $result = mysqli_query($conn, $query);
    $appointments = [];
    
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $appointments[] = $row;
        }
    }
    return $appointments;
}

/**
 * Get hospital appointments
 */
function getHospitalAppointments($conn, $hospital_id, $status = null, $date = null, $limit = null) {
    $where = "hospital_id = '$hospital_id'";
    
    if ($status) {
        $where .= " AND status = '$status'";
    }
    
    if ($date) {
        $where .= " AND appointment_date = '$date'";
    }
    
    $query = "SELECT a.*, p.name as patient_name, p.phone, p.email 
              FROM appointments a
              JOIN patients p ON a.patient_id = p.patient_id
              WHERE $where
              ORDER BY a.appointment_date DESC, a.appointment_time DESC";
    
    if ($limit) {
        $query .= " LIMIT $limit";
    }
    
    $result = mysqli_query($conn, $query);
    $appointments = [];
    
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $appointments[] = $row;
        }
    }
    return $appointments;
}

/**
 * ============================================
 * TEST RESULTS FUNCTIONS
 * ============================================
 */

/**
 * Get patient test results
 */
function getPatientTestResults($conn, $patient_id) {
    $query = "SELECT t.*, h.name as hospital_name, a.appointment_date 
              FROM test_results t
              JOIN hospitals h ON t.hospital_id = h.hospital_id
              JOIN appointments a ON t.appointment_id = a.appointment_id
              WHERE t.patient_id = '$patient_id'
              ORDER BY t.test_date DESC";
    
    $result = mysqli_query($conn, $query);
    $results = [];
    
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $results[] = $row;
        }
    }
    return $results;
}

/**
 * ============================================
 * VACCINATION FUNCTIONS
 * ============================================
 */

/**
 * Get patient vaccination records
 */
function getPatientVaccinations($conn, $patient_id) {
    $query = "SELECT v.*, h.name as hospital_name, vac.name as vaccine_name, vac.manufacturer
              FROM vaccination_records v
              JOIN hospitals h ON v.hospital_id = h.hospital_id
              JOIN vaccines vac ON v.vaccine_id = vac.vaccine_id
              WHERE v.patient_id = '$patient_id'
              ORDER BY v.vaccination_date DESC";
    
    $result = mysqli_query($conn, $query);
    $records = [];
    
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $records[] = $row;
        }
    }
    return $records;
}

/**
 * Get available vaccines
 */
function getAvailableVaccines($conn) {
    return getRecords($conn, 'vaccines', "status = 'available'", 'name');
}

/**
 * ============================================
 * HOSPITAL FUNCTIONS
 * ============================================
 */

/**
 * Get approved hospitals
 */
function getApprovedHospitals($conn, $city = null, $limit = null) {
    $where = "status = 'approved'";
    if ($city) {
        $where .= " AND city LIKE '%$city%'";
    }
    $limitStr = $limit ? "0, $limit" : '';
    return getRecords($conn, 'hospitals', $where, 'name', $limitStr);
}

/**
 * Search hospitals by city or name
 */
function searchHospitals($conn, $keyword) {
    $keyword = mysqli_real_escape_string($conn, $keyword);
    $where = "status = 'approved' AND (name LIKE '%$keyword%' OR city LIKE '%$keyword%' OR address LIKE '%$keyword%')";
    return getRecords($conn, 'hospitals', $where, 'name');
}

/**
 * ============================================
 * REPORT FUNCTIONS
 * ============================================
 */

/**
 * Generate appointment report
 */
function getAppointmentReport($conn, $start_date, $end_date) {
    $query = "SELECT a.*, p.name as patient_name, h.name as hospital_name 
              FROM appointments a
              JOIN patients p ON a.patient_id = p.patient_id
              JOIN hospitals h ON a.hospital_id = h.hospital_id
              WHERE a.appointment_date BETWEEN '$start_date' AND '$end_date'
              ORDER BY a.appointment_date DESC";
    
    $result = mysqli_query($conn, $query);
    $data = [];
    
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
    }
    return $data;
}

/**
 * Generate test report
 */
function getTestReport($conn, $start_date, $end_date) {
    $query = "SELECT t.*, p.name as patient_name, h.name as hospital_name 
              FROM test_results t
              JOIN patients p ON t.patient_id = p.patient_id
              JOIN hospitals h ON t.hospital_id = h.hospital_id
              WHERE t.test_date BETWEEN '$start_date' AND '$end_date'
              ORDER BY t.test_date DESC";
    
    $result = mysqli_query($conn, $query);
    $data = [];
    
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
    }
    return $data;
}

/**
 * Generate vaccination report
 */
function getVaccinationReport($conn, $start_date, $end_date) {
    $query = "SELECT v.*, p.name as patient_name, h.name as hospital_name, vac.name as vaccine_name
              FROM vaccination_records v
              JOIN patients p ON v.patient_id = p.patient_id
              JOIN hospitals h ON v.hospital_id = h.hospital_id
              JOIN vaccines vac ON v.vaccine_id = vac.vaccine_id
              WHERE v.vaccination_date BETWEEN '$start_date' AND '$end_date'
              ORDER BY v.vaccination_date DESC";
    
    $result = mysqli_query($conn, $query);
    $data = [];
    
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
    }
    return $data;
}

/**
 * ============================================
 * ALERT & MESSAGE FUNCTIONS
 * ============================================
 */

/**
 * Show alert message
 */
function showAlert($message, $type = 'success') {
    $icons = [
        'success' => 'fa-check-circle',
        'danger' => 'fa-exclamation-circle',
        'warning' => 'fa-exclamation-triangle',
        'info' => 'fa-info-circle'
    ];
    $icon = isset($icons[$type]) ? $icons[$type] : 'fa-info-circle';
    
    return '<div class="alert alert-' . $type . ' alert-dismissible fade show" role="alert">
                <i class="fa ' . $icon . ' me-2"></i>
                ' . $message . '
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
}

/**
 * Set flash message (for redirects)
 */
function setFlashMessage($message, $type = 'success') {
    $_SESSION['flash_message'] = $message;
    $_SESSION['flash_type'] = $type;
}

/**
 * Show flash message (call this on the page after redirect)
 */
function showFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        $type = $_SESSION['flash_type'] ?? 'success';
        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_type']);
        return showAlert($message, $type);
    }
    return '';
}

/**
 * ============================================
 * VALIDATION FUNCTIONS
 * ============================================
 */

/**
 * Validate email
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Validate phone (Pakistan format)
 */
function validatePhone($phone) {
    // Pakistan mobile: 03XXXXXXXXX or +92XXXXXXXXXX
    return preg_match('/^(03[0-9]{9})$|^(\+92[0-9]{10})$/', $phone);
}

/**
 * Validate date
 */
function validateDate($date, $format = 'Y-m-d') {
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}

/**
 * ============================================
 * MISCELLANEOUS FUNCTIONS
 * ============================================
 */

/**
 * Generate random string
 */
function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $index = rand(0, strlen($characters) - 1);
        $randomString .= $characters[$index];
    }
    return $randomString;
}

/**
 * Get age from date of birth
 */
function getAge($dob) {
    if ($dob && $dob != '0000-00-00' && $dob != null) {
        $birthDate = new DateTime($dob);
        $today = new DateTime('today');
        $age = $birthDate->diff($today)->y;
        return $age;
    }
    return 0;
}

/**
 * Get greeting based on time
 */
function getGreeting() {
    $hour = date('H');
    if ($hour < 12) {
        return 'Good Morning';
    } elseif ($hour < 17) {
        return 'Good Afternoon';
    } elseif ($hour < 20) {
        return 'Good Evening';
    } else {
        return 'Good Night';
    }
}

/**
 * Debug function - print array prettily
 */
function debug($data) {
    echo '<pre>';
    print_r($data);
    echo '</pre>';
}

/**
 * Time elapsed string (e.g., "2 minutes ago")
 */
function time_elapsed_string($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );
    
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}

?>