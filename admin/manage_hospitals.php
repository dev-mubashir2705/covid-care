<?php
session_start();
require_once '../config.php';
require_once '../includes/functions.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Handle delete request
if (isset($_GET['delete'])) {
    $hospital_id = (int)$_GET['delete'];
    
    // Pehle check karo ke is hospital ke appointments hain ya nahi
    $check_query = "SELECT COUNT(*) as total FROM appointments WHERE hospital_id = $hospital_id";
    $check_result = mysqli_query($conn, $check_query);
    $check_row = mysqli_fetch_assoc($check_result);
    
    if ($check_row['total'] > 0) {
        // Pehle appointments delete karo (ya phir hospital delete mat karo)
        $delete_appointments = "DELETE FROM appointments WHERE hospital_id = $hospital_id";
        mysqli_query($conn, $delete_appointments);
    }
    
    // Ab hospital delete karo
    $delete_query = "DELETE FROM hospitals WHERE hospital_id = $hospital_id";
    
    if (mysqli_query($conn, $delete_query)) {
        $success = "Hospital deleted successfully!";
    } else {
        $error = "Error deleting hospital: " . mysqli_error($conn);
    }
}

// Get all hospitals
$query = "SELECT * FROM hospitals ORDER BY name ASC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Hospitals</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Manage Hospitals</h2>
        
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>City</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($hospital = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?php echo $hospital['hospital_id']; ?></td>
                    <td><?php echo htmlspecialchars($hospital['name']); ?></td>
                    <td><?php echo htmlspecialchars($hospital['email']); ?></td>
                    <td><?php echo htmlspecialchars($hospital['phone']); ?></td>
                    <td><?php echo htmlspecialchars($hospital['city']); ?></td>
                    <td>
                        <span class="badge bg-<?php echo ($hospital['status'] == 'active') ? 'success' : 'warning'; ?>">
                            <?php echo ucfirst($hospital['status']); ?>
                        </span>
                    </td>
                    <td>
                        <a href="edit_hospital.php?id=<?php echo $hospital['hospital_id']; ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="?delete=<?php echo $hospital['hospital_id']; ?>" 
                           class="btn btn-danger btn-sm" 
                           onclick="return confirm('Are you sure you want to delete this hospital? All related appointments will also be deleted.')">
                            <i class="fas fa-trash"></i> Delete
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>