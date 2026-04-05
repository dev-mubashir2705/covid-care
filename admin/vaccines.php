<?php
session_start();
require_once '../config.php';
require_once '../includes/functions.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$page_title = 'Manage Vaccines - Admin Panel';
require_once '../includes/header.php';
require_once '../includes/navbar.php';

// Handle add vaccine
if (isset($_POST['add'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $manufacturer = mysqli_real_escape_string($conn, $_POST['manufacturer']);
    $doses = (int)$_POST['doses'];
    $gap = !empty($_POST['gap']) ? (int)$_POST['gap'] : 'NULL';
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    
    $query = "INSERT INTO vaccines (name, manufacturer, doses_required, gap_between_doses, description, status) 
              VALUES ('$name', '$manufacturer', $doses, $gap, '$desc', 'available')";
    
    if (mysqli_query($conn, $query)) {
        $_SESSION['success'] = "Vaccine added successfully!";
    } else {
        $_SESSION['error'] = "Error adding vaccine: " . mysqli_error($conn);
    }
    header("Location: vaccines.php");
    exit();
}

// Handle delete vaccine
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $query = "DELETE FROM vaccines WHERE vaccine_id = $id";
    
    if (mysqli_query($conn, $query)) {
        $_SESSION['success'] = "Vaccine deleted successfully!";
    } else {
        $_SESSION['error'] = "Error deleting vaccine!";
    }
    header("Location: vaccines.php");
    exit();
}

// Handle toggle status
if (isset($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    
    // Get current status
    $result = mysqli_query($conn, "SELECT status FROM vaccines WHERE vaccine_id = $id");
    $row = mysqli_fetch_assoc($result);
    $new_status = ($row['status'] == 'available') ? 'unavailable' : 'available';
    
    $query = "UPDATE vaccines SET status = '$new_status' WHERE vaccine_id = $id";
    
    if (mysqli_query($conn, $query)) {
        $_SESSION['success'] = "Vaccine status updated!";
    } else {
        $_SESSION['error'] = "Error updating status!";
    }
    header("Location: vaccines.php");
    exit();
}

// Handle edit vaccine
if (isset($_POST['edit'])) {
    $id = (int)$_POST['id'];
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $manufacturer = mysqli_real_escape_string($conn, $_POST['manufacturer']);
    $doses = (int)$_POST['doses'];
    $gap = !empty($_POST['gap']) ? (int)$_POST['gap'] : 'NULL';
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    
    $query = "UPDATE vaccines SET 
              name = '$name',
              manufacturer = '$manufacturer',
              doses_required = $doses,
              gap_between_doses = $gap,
              description = '$desc'
              WHERE vaccine_id = $id";
    
    if (mysqli_query($conn, $query)) {
        $_SESSION['success'] = "Vaccine updated successfully!";
    } else {
        $_SESSION['error'] = "Error updating vaccine!";
    }
    header("Location: vaccines.php");
    exit();
}

// Get all vaccines
$query = "SELECT * FROM vaccines ORDER BY name ASC";
$result = mysqli_query($conn, $query);
$vaccines = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $vaccines[] = $row;
    }
}

// Get vaccine for editing
$edit_vaccine = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $result = mysqli_query($conn, "SELECT * FROM vaccines WHERE vaccine_id = $edit_id");
    $edit_vaccine = mysqli_fetch_assoc($result);
}

// Count statistics
$total_vaccines = count($vaccines);
$available_vaccines = 0;
$unavailable_vaccines = 0;

foreach ($vaccines as $v) {
    if ($v['status'] == 'available') {
        $available_vaccines++;
    } else {
        $unavailable_vaccines++;
    }
}

// Get flash messages
$success = isset($_SESSION['success']) ? $_SESSION['success'] : '';
$error = isset($_SESSION['error']) ? $_SESSION['error'] : '';
unset($_SESSION['success']);
unset($_SESSION['error']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Vaccines - Admin Panel</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: #f8f9fa;
        }
        
        .container-fluid {
            padding: 20px;
        }
        
        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            color: white;
        }
        
        .page-header h1 {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            border: 1px solid #eee;
        }
        
        .stat-icon {
            width: 50px;
            height: 50px;
            background: #f0e7ff;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
            color: #667eea;
            font-size: 24px;
        }
        
        .stat-value {
            font-size: 28px;
            font-weight: 700;
            color: #333;
            margin-bottom: 5px;
        }
        
        .stat-label {
            color: #6c757d;
            font-size: 14px;
        }
        
        .form-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            border: 1px solid #eee;
        }
        
        .form-card h3 {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }
        
        .form-control {
            width: 100%;
            padding: 10px 15px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102,126,234,0.1);
        }
        
        textarea.form-control {
            min-height: 100px;
            resize: vertical;
        }
        
        .btn {
            padding: 10px 25px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102,126,234,0.3);
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
        }
        
        .btn-sm {
            padding: 5px 12px;
            font-size: 12px;
            margin: 0 2px;
        }
        
        .btn-warning {
            background: #ffc107;
            color: #333;
        }
        
        .btn-info {
            background: #0dcaf0;
            color: white;
        }
        
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        
        .table-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            border: 1px solid #eee;
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
            padding: 12px;
            background: #f8f9fa;
            color: #333;
            font-weight: 600;
            border-bottom: 2px solid #dee2e6;
        }
        
        td {
            padding: 12px;
            color: #6c757d;
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
        
        .badge-success {
            background: #d1e7dd;
            color: #0f5132;
        }
        
        .badge-danger {
            background: #f8d7da;
            color: #842029;
        }
        
        .alert {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        
        .alert-success {
            background: #d1e7dd;
            color: #0f5132;
            border: 1px solid #badbcc;
        }
        
        .alert-danger {
            background: #f8d7da;
            color: #842029;
            border: 1px solid #f5c2c7;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .action-buttons {
            display: flex;
            gap: 5px;
        }
        
        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .btn-sm {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <?php require_once '../includes/navbar.php'; ?>
    
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1><i class="fas fa-syringe me-3"></i>Manage Vaccines</h1>
            <p class="mb-0">Add, edit and manage COVID-19 vaccines</p>
        </div>
        
        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-syringe"></i>
                </div>
                <div class="stat-value"><?php echo $total_vaccines; ?></div>
                <div class="stat-label">Total Vaccines</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-value"><?php echo $available_vaccines; ?></div>
                <div class="stat-label">Available</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="stat-value"><?php echo $unavailable_vaccines; ?></div>
                <div class="stat-label">Unavailable</div>
            </div>
        </div>
        
        <!-- Alert Messages -->
        <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle me-2"></i>
                <?php echo $success; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle me-2"></i>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <!-- Add/Edit Form -->
        <div class="form-card">
            <h3>
                <i class="fas <?php echo $edit_vaccine ? 'fa-edit' : 'fa-plus-circle'; ?> me-2"></i>
                <?php echo $edit_vaccine ? 'Edit Vaccine' : 'Add New Vaccine'; ?>
            </h3>
            
            <form method="POST" action="">
                <?php if ($edit_vaccine): ?>
                    <input type="hidden" name="id" value="<?php echo $edit_vaccine['vaccine_id']; ?>">
                <?php endif; ?>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Vaccine Name</label>
                        <input type="text" class="form-control" name="name" 
                               value="<?php echo $edit_vaccine ? htmlspecialchars($edit_vaccine['name']) : ''; ?>" 
                               placeholder="e.g. Covishield" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Manufacturer</label>
                        <input type="text" class="form-control" name="manufacturer" 
                               value="<?php echo $edit_vaccine ? htmlspecialchars($edit_vaccine['manufacturer']) : ''; ?>" 
                               placeholder="e.g. Serum Institute" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Doses Required</label>
                        <input type="number" class="form-control" name="doses" 
                               value="<?php echo $edit_vaccine ? $edit_vaccine['doses_required'] : '2'; ?>" 
                               min="1" max="5" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Gap Between Doses (days)</label>
                        <input type="number" class="form-control" name="gap" 
                               value="<?php echo $edit_vaccine ? $edit_vaccine['gap_between_doses'] : ''; ?>" 
                               min="0" max="365" placeholder="Optional">
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Description</label>
                    <textarea class="form-control" name="description" 
                         placeholder="Enter vaccine description..."><?php echo htmlspecialchars($vaccine['description'] ?? ''); ?></textarea>
                </div> 
                
                <div>
                    <?php if ($edit_vaccine): ?>
                        <button type="submit" name="edit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update Vaccine
                        </button>
                        <a href="vaccines.php" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                    <?php else: ?>
                        <button type="submit" name="add" class="btn btn-primary">
                            <i class="fas fa-plus-circle me-2"></i>Add Vaccine
                        </button>
                    <?php endif; ?>
                </div>
            </form>
        </div>
        
        <!-- Vaccine List -->
        <div class="table-card">
            <h3><i class="fas fa-list me-2"></i>Vaccine List</h3>
            
            <?php if (!empty($vaccines)): ?>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Vaccine Name</th>
                                <th>Manufacturer</th>
                                <th>Doses</th>
                                <th>Gap (Days)</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($vaccines as $v): ?>
                            <tr>
                                <td>#<?php echo $v['vaccine_id']; ?></td>
                                <td><strong><?php echo htmlspecialchars($v['name']); ?></strong></td>
                                <td><?php echo htmlspecialchars($v['manufacturer']); ?></td>
                                <td><?php echo $v['doses_required']; ?></td>
                                <td><?php echo $v['gap_between_doses'] ?: '-'; ?></td>
                                <td>
                                    <span class="badge <?php echo $v['status'] == 'available' ? 'badge-success' : 'badge-danger'; ?>">
                                        <?php echo ucfirst($v['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="?edit=<?php echo $v['vaccine_id']; ?>" class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="?toggle=<?php echo $v['vaccine_id']; ?>" class="btn btn-sm btn-info" title="Toggle Status">
                                            <i class="fas fa-sync-alt"></i>
                                        </a>
                                        <a href="?delete=<?php echo $v['vaccine_id']; ?>" class="btn btn-sm btn-danger" 
                                           onclick="return confirm('Are you sure you want to delete this vaccine?')" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-syringe fa-3x text-muted mb-3"></i>
                    <h4>No Vaccines Found</h4>
                    <p class="text-muted">Add your first vaccine using the form above.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php require_once '../includes/footer.php'; ?>