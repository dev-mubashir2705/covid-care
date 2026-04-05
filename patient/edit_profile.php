<?php
require_once '../config.php';
require_once '../includes/functions.php';

// Check if patient is logged in
if (!isset($_SESSION['patient_id'])) {
    setFlashMessage('Please login to edit profile', 'danger');
    redirect(SITE_URL . 'patient/login.php');
}

$page_title = 'Edit Profile - COVID Care System';
require_once '../includes/header.php';
require_once '../includes/navbar.php';

$patient_id = $_SESSION['patient_id'];

// Get current patient data
$patient = getRecord($conn, 'patients', 'patient_id', $patient_id);

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitize($_POST['name']);
    $phone = sanitize($_POST['phone']);
    $address = sanitize($_POST['address']);
    $city = sanitize($_POST['city']);
    $dob = sanitize($_POST['dob']);
    $gender = sanitize($_POST['gender']);
    
    // Update query
    $query = "UPDATE patients SET 
              name = '$name',
              phone = '$phone',
              address = '$address',
              city = '$city',
              dob = '$dob',
              gender = '$gender'
              WHERE patient_id = $patient_id";
    
    if (mysqli_query($conn, $query)) {
        $_SESSION['patient_name'] = $name;
        setFlashMessage('Profile updated successfully!', 'success');
        redirect(SITE_URL . 'patient/profile.php');
    } else {
        $error = "Failed to update profile. Please try again.";
    }
}
?>

<style>
    /* ===== EDIT PROFILE STYLES ===== */
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
    
    .edit-container {
        max-width: 700px;
        margin: 0 auto 60px;
    }
    
    .edit-card {
        background: white;
        border-radius: 20px;
        padding: 40px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        border: 1px solid #eee;
    }
    
    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 20px;
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
    
    .btn-group {
        display: flex;
        gap: 15px;
        margin-top: 30px;
    }
    
    .btn-save {
        flex: 2;
        padding: 15px;
        background: linear-gradient(135deg, #0d6efd, #0b5ed7);
        color: white;
        border: none;
        border-radius: 12px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }
    
    .btn-save:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 30px rgba(13,110,253,0.3);
    }
    
    .btn-cancel {
        flex: 1;
        padding: 15px;
        background: #6c757d;
        color: white;
        border: none;
        border-radius: 12px;
        font-size: 16px;
        font-weight: 600;
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        transition: all 0.3s ease;
    }
    
    .btn-cancel:hover {
        background: #5a6268;
        transform: translateY(-2px);
    }
    
    .alert {
        padding: 16px 20px;
        border-radius: 12px;
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
    
    @media (max-width: 768px) {
        .edit-card {
            padding: 25px;
        }
        
        .form-row {
            grid-template-columns: 1fr;
            gap: 0;
        }
        
        .btn-group {
            flex-direction: column;
        }
    }
</style>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <h1>Edit Profile</h1>
        <p>Update your personal information</p>
    </div>
</section>

<!-- Edit Form -->
<section class="container">
    <div class="edit-container">
        <?php if ($error): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <div class="edit-card">
            <form method="POST" action="">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-user"></i> Full Name
                        </label>
                        <input type="text" class="form-control" name="name" value="<?php echo $patient['name']; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-phone"></i> Phone Number
                        </label>
                        <input type="tel" class="form-control" name="phone" value="<?php echo $patient['phone']; ?>" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-city"></i> City
                        </label>
                        <input type="text" class="form-control" name="city" value="<?php echo $patient['city']; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-calendar"></i> Date of Birth
                        </label>
                        <input type="date" class="form-control" name="dob" value="<?php echo $patient['dob']; ?>" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-venus-mars"></i> Gender
                    </label>
                    <select class="form-control" name="gender" required>
                        <option value="">Select Gender</option>
                        <option value="Male" <?php echo ($patient['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                        <option value="Female" <?php echo ($patient['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
                        <option value="Other" <?php echo ($patient['gender'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-map-marker-alt"></i> Address
                    </label>
                    <textarea class="form-control" name="address" rows="3" required><?php echo $patient['address']; ?></textarea>
                </div>
                
                <div class="btn-group">
                    <button type="submit" class="btn-save">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                    <a href="<?php echo SITE_URL; ?>patient/profile.php" class="btn-cancel">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</section>

<?php
require_once '../includes/footer.php';
?>