<?php
session_start();
require_once '../config.php';
require_once '../includes/functions.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

$hospital_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get hospital details
$query = "SELECT * FROM hospitals WHERE hospital_id = $hospital_id";
$result = mysqli_query($conn, $query);
$hospital = mysqli_fetch_assoc($result);

// Handle form submission
if ($_POST) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $status = $_POST['status'];
    
    $update = "UPDATE hospitals SET 
                name = '$name',
                email = '$email',
                phone = '$phone',
                address = '$address',
                city = '$city',
                status = '$status'
              WHERE hospital_id = $hospital_id";
    
    if (mysqli_query($conn, $update)) {
        $success = "Hospital updated successfully!";
        // Refresh data
        $result = mysqli_query($conn, $query);
        $hospital = mysqli_fetch_assoc($result);
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Hospital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Edit Hospital</h2>
        
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="mb-3">
                <label>Hospital Name</label>
                <input type="text" name="name" class="form-control" value="<?php echo $hospital['name']; ?>" required>
            </div>
            
            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control" value="<?php echo $hospital['email']; ?>" required>
            </div>
            
            <div class="mb-3">
                <label>Phone</label>
                <input type="text" name="phone" class="form-control" value="<?php echo $hospital['phone']; ?>" required>
            </div>
            
            <div class="mb-3">
                <label>Address</label>
                <textarea name="address" class="form-control" required><?php echo $hospital['address']; ?></textarea>
            </div>
            
            <div class="mb-3">
                <label>City</label>
                <input type="text" name="city" class="form-control" value="<?php echo $hospital['city']; ?>" required>
            </div>
            
            <div class="mb-3">
                <label>Status</label>
                <select name="status" class="form-control">
                    <option value="active" <?php echo ($hospital['status'] == 'active') ? 'selected' : ''; ?>>Active</option>
                    <option value="pending" <?php echo ($hospital['status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                    <option value="inactive" <?php echo ($hospital['status'] == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                </select>
            </div>
            
            <button type="submit" class="btn btn-primary">Update Hospital</button>
            <a href="manage_hospitals.php" class="btn btn-secondary">Back</a>
        </form>
    </div>
</body>
</html>