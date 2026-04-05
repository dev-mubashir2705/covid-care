<?php
// Determine active page
$current_page = basename($_SERVER['PHP_SELF']);
$current_folder = basename(dirname($_SERVER['PHP_SELF']));

// Get user info if logged in
$logged_in = false;
$user_name = '';
$user_type = '';
$user_id = '';

if(isset($_SESSION['patient_id'])) {
    $logged_in = true;
    $user_name = $_SESSION['patient_name'] ?? 'Patient';
    $user_type = 'patient';
    $user_id = $_SESSION['patient_id'];
} elseif(isset($_SESSION['hospital_id'])) {
    $logged_in = true;
    $user_name = $_SESSION['hospital_name'] ?? 'Hospital';
    $user_type = 'hospital';
    $user_id = $_SESSION['hospital_id'];
} elseif(isset($_SESSION['admin_id'])) {
    $logged_in = true;
    $user_name = $_SESSION['admin_name'] ?? 'Admin';
    $user_type = 'admin';
    $user_id = $_SESSION['admin_id'];
}
?>

<style>
    /* Simple Navbar */
    .simple-navbar {
        background: white;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        padding: 0;
        width: 100%;
        position: sticky;
        top: 0;
        z-index: 1000;
        font-family: Arial, sans-serif;
    }
    
    .nav-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 15px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        height: 65px;
    }
    
    /* Logo - YOUR EXISTING LOGO STYLES - UNCHANGED */
    .navbar-brand {
        display: flex;
        align-items: center;
        text-decoration: none;
        font-size: 22px;
        font-weight: 700;
    }
    
    .navbar-brand i {
        font-size: 22px;
        color: #0d6efd;
        background: #e7f1ff;
        padding: 6px;
        border-radius: 50%;
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 8px;
        animation: blink 2s infinite;
    }
    
    @keyframes blink {
        0% {
            background: #e7f1ff;
            color: #0d6efd;
            transform: scale(1);
        }
        50% {
            background: #0d6efd;
            color: white;
            transform: scale(1.1);
            box-shadow: 0 0 15px #0d6efd;
        }
        100% {
            background: #e7f1ff;
            color: #0d6efd;
            transform: scale(1);
        }
    }
    
    .navbar-brand span {
        color: #0d6efd;
        font-weight: 700;
    }
    
    .navbar-brand .brand-text {
        color: #333;
        font-weight: 400;
        margin-left: 2px;
    }
    
    /* Menu Items */
    .nav-menu {
        display: flex;
        align-items: center;
        list-style: none;
        margin: 0;
        padding: 0;
    }
    
    .nav-item {
        position: relative;
        margin: 0 3px;
    }
    
    .nav-item a {
        display: flex;
        align-items: center;
        padding: 8px 12px;
        color: #333;
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
        border-radius: 4px;
    }
    
    .nav-item a i {
        margin-right: 5px;
        color: #0d6efd;
        font-size: 13px;
    }
    
    .nav-item a:hover {
        background: #f0f7ff;
        color: #0d6efd;
    }
    
    .nav-item a.active {
        background: #e7f1ff;
        color: #0d6efd;
        font-weight: 600;
    }
    
    /* Dropdown */
    .dropdown-menu {
        position: absolute;
        top: 40px;
        left: 0;
        background: white;
        border-radius: 5px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.15);
        min-width: 180px;
        padding: 5px 0;
        display: none;
        z-index: 1001;
        border: 1px solid #eee;
    }
    
    .dropdown-menu.show {
        display: block;
    }
    
    .dropdown-menu a {
        padding: 8px 15px;
        color: #333;
        text-decoration: none;
        display: flex;
        align-items: center;
        font-size: 13px;
    }
    
    .dropdown-menu a i {
        width: 16px;
        margin-right: 8px;
        color: #0d6efd;
    }
    
    .dropdown-menu a:hover {
        background: #0d6efd;
        color: white;
    }
    
    .dropdown-menu a:hover i {
        color: white;
    }
    
    .dropdown-divider {
        height: 1px;
        background: #eee;
        margin: 5px 0;
    }
    
    .dropdown-toggle i.fa-chevron-down {
        font-size: 10px;
        margin-left: 5px;
        color: #999;
    }
    
    /* User Menu */
    .user-menu {
        display: flex;
        align-items: center;
        padding: 2px 8px 2px 2px;
        background: #f8f9fa;
        border-radius: 30px;
        border: 1px solid #e9ecef;
    }
    
    .user-avatar {
        width: 28px;
        height: 28px;
        background: #0d6efd;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 12px;
        font-weight: 600;
        margin-right: 5px;
    }
    
    .user-name {
        font-size: 12px;
        font-weight: 500;
        color: #333;
        max-width: 80px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    
    /* Buttons */
    .btn-book {
        background: #0d6efd;
        color: white !important;
        padding: 6px 16px !important;
        border-radius: 30px !important;
        margin-left: 5px;
    }
    
    .btn-book:hover {
        background: #0b5ed7;
        color: white !important;
    }
    
    .btn-book i {
        color: white !important;
    }
    
    /* Mobile Toggler */
    .mobile-toggler {
        display: none;
        font-size: 20px;
        color: #0d6efd;
        cursor: pointer;
        border: 2px solid #0d6efd;
        padding: 5px 10px;
        border-radius: 5px;
    }
    
    /* Mobile Responsive */
    @media (max-width: 991px) {
        .mobile-toggler {
            display: block;
        }
        
        .nav-menu {
            display: none;
            position: absolute;
            top: 65px;
            left: 0;
            right: 0;
            background: white;
            flex-direction: column;
            padding: 15px;
            box-shadow: 0 5px 10px rgba(0,0,0,0.1);
            border-top: 1px solid #eee;
        }
        
        .nav-menu.show {
            display: flex;
        }
        
        .nav-item {
            width: 100%;
            margin: 2px 0;
        }
        
        .nav-item a {
            width: 100%;
        }
        
        .dropdown-menu {
            position: static;
            box-shadow: none;
            padding-left: 20px;
            border-left: 2px solid #0d6efd;
            background: #f8f9fa;
            margin-top: 5px;
            width: 100%;
        }
        
        .user-menu {
            width: 100%;
            justify-content: center;
        }
        
        .btn-book {
            width: 100%;
            text-align: center;
            margin-left: 0;
        }
    }
</style>

<!-- Simple Navbar - WITHOUT TOPBAR -->
<div class="simple-navbar">
    <div class="nav-container">
       
        <!-- Brand with BLINKING EFFECT - EXACTLY YOUR LOGO -->
        <a class="navbar-brand" href="<?php echo SITE_URL; ?>">
            <i class="fa fa-shield-virus"></i>
            COVID<span class="brand-text">-CARE</span>
        </a>
        
        <!-- Mobile Toggler -->
        <div class="mobile-toggler" onclick="toggleMobileMenu()">
            <i class="fa fa-bars"></i>
        </div>
        
        <!-- Menu Items -->
        <ul class="nav-menu" id="navMenu">
            <!-- Home -->
            <li class="nav-item">
                <a href="<?php echo SITE_URL; ?>" class="<?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">
                    <i class="fa fa-home"></i> Home
                </a>
            </li>
            
            <!-- About -->
            <li class="nav-item">
                <a href="<?php echo SITE_URL; ?>about.php" class="<?php echo ($current_page == 'about.php') ? 'active' : ''; ?>">
                    <i class="fa fa-info-circle"></i> About
                </a>
            </li>
            
            <!-- Contact -->
            <li class="nav-item">
                <a href="<?php echo SITE_URL; ?>contact.php" class="<?php echo ($current_page == 'contact.php') ? 'active' : ''; ?>">
                    <i class="fa fa-envelope"></i> Contact
                </a>
            </li>
            
            <!-- Vaccines -->
            <li class="nav-item">
                <a href="<?php echo SITE_URL; ?>patient/view_vaccines.php" class="<?php echo ($current_page == 'view_vaccines.php') ? 'active' : ''; ?>">
                    <i class="fa fa-syringe"></i> Vaccines
                </a>
            </li>
            
            <!-- Hospitals -->
            <li class="nav-item">
                <a href="<?php echo SITE_URL; ?>hospitals.php" class="<?php echo ($current_page == 'hospitals.php') ? 'active' : ''; ?>">
                    <i class="fa fa-hospital"></i> Hospitals
                </a>
            </li>
            
            <?php if($logged_in): ?>
                
                <!-- Patient Dropdown -->
                <?php if($user_type == 'patient'): ?>
                <li class="nav-item dropdown-item">
                    <a href="javascript:void(0)" onclick="toggleDropdown('patientDropdown')" class="dropdown-toggle">
                        <i class="fa fa-user-md"></i> Patient <i class="fa fa-chevron-down"></i>
                    </a>
                    <div class="dropdown-menu" id="patientDropdown">
                        <a href="<?php echo SITE_URL; ?>patient/dashboard.php"><i class="fa fa-tachometer-alt"></i> Dashboard</a>
                        <a href="<?php echo SITE_URL; ?>patient/book_appointment.php"><i class="fa fa-calendar-plus"></i> Book Appointment</a>
                        <a href="<?php echo SITE_URL; ?>patient/my_appointments.php"><i class="fa fa-list"></i> My Appointments</a>
                        <a href="<?php echo SITE_URL; ?>patient/view_results.php"><i class="fa fa-flask"></i> Test Results</a>
                        <div class="dropdown-divider"></div>
                        <a href="<?php echo SITE_URL; ?>patient/profile.php"><i class="fa fa-user"></i> Profile</a>
                        <a href="<?php echo SITE_URL; ?>patient/logout.php"><i class="fa fa-sign-out-alt"></i> Logout</a>
                    </div>
                </li>
                
                <!-- Hospital Dropdown -->
                <?php elseif($user_type == 'hospital'): ?>
                <li class="nav-item dropdown-item">
                    <a href="javascript:void(0)" onclick="toggleDropdown('hospitalDropdown')" class="dropdown-toggle">
                        <i class="fa fa-hospital"></i> Hospital <i class="fa fa-chevron-down"></i>
                    </a>
                    <div class="dropdown-menu" id="hospitalDropdown">
                        <a href="<?php echo SITE_URL; ?>hospital/dashboard.php"><i class="fa fa-tachometer-alt"></i> Dashboard</a>
                        <a href="<?php echo SITE_URL; ?>hospital/appointments.php"><i class="fa fa-calendar-check"></i> Appointments</a>
                        <a href="<?php echo SITE_URL; ?>hospital/patients.php"><i class="fa fa-users"></i> Patients</a>
                        <a href="<?php echo SITE_URL; ?>hospital/update_test.php"><i class="fa fa-flask"></i> Update Test</a>
                        <a href="<?php echo SITE_URL; ?>hospital/update_vaccination.php"><i class="fa fa-syringe"></i> Update Vaccination</a>
                        <div class="dropdown-divider"></div>
                        <a href="<?php echo SITE_URL; ?>hospital/logout.php"><i class="fa fa-sign-out-alt"></i> Logout</a>
                    </div>
                </li>
                
                <!-- Admin Dropdown -->
                <?php elseif($user_type == 'admin'): ?>
                <li class="nav-item dropdown-item">
                    <a href="javascript:void(0)" onclick="toggleDropdown('adminDropdown')" class="dropdown-toggle">
                        <i class="fa fa-user-shield"></i> Admin <i class="fa fa-chevron-down"></i>
                    </a>
                    <div class="dropdown-menu" id="adminDropdown">
                        <a href="<?php echo SITE_URL; ?>admin/dashboard.php"><i class="fa fa-tachometer-alt"></i> Dashboard</a>
                        <a href="<?php echo SITE_URL; ?>admin/hospitals.php"><i class="fa fa-hospital"></i> Hospitals</a>
                        <a href="<?php echo SITE_URL; ?>admin/approve_hospital.php"><i class="fa fa-check-circle"></i> Approve Hospitals</a>
                        <a href="<?php echo SITE_URL; ?>admin/patients.php"><i class="fa fa-users"></i> Patients</a>
                        <a href="<?php echo SITE_URL; ?>admin/vaccines.php"><i class="fa fa-syringe"></i> Vaccines</a>
                        <a href="<?php echo SITE_URL; ?>admin/reports.php"><i class="fa fa-chart-bar"></i> Reports</a>
                        <div class="dropdown-divider"></div>
                        <a href="<?php echo SITE_URL; ?>admin/logout.php"><i class="fa fa-sign-out-alt"></i> Logout</a>
                    </div>
                </li>
                <?php endif; ?>
                
                <!-- User Info -->
                <li class="nav-item">
                    <a href="javascript:void(0)" onclick="toggleDropdown('userDropdown')" class="dropdown-toggle">
                        <div class="user-menu">
                            <span class="user-avatar"><?php echo strtoupper(substr($user_name, 0, 1)); ?></span>
                            <span class="user-name"><?php echo $user_name; ?></span>
                            <i class="fa fa-chevron-down" style="margin-left: 5px;"></i>
                        </div>
                    </a>
                    <div class="dropdown-menu" id="userDropdown" style="right: 0; left: auto;">
                        <a href="<?php echo SITE_URL . $user_type; ?>/profile.php"><i class="fa fa-user-circle"></i> Profile</a>
                        <a href="<?php echo SITE_URL . $user_type; ?>/change-password.php"><i class="fa fa-key"></i> Change Password</a>
                        <div class="dropdown-divider"></div>
                        <a href="<?php echo SITE_URL . $user_type; ?>/logout.php"><i class="fa fa-sign-out-alt"></i> Logout</a>
                    </div>
                </li>
                
            <?php else: ?>
                
                <!-- Login Dropdown -->
                <li class="nav-item dropdown-item">
                    <a href="javascript:void(0)" onclick="toggleDropdown('loginDropdown')" class="dropdown-toggle">
                        <i class="fa fa-sign-in-alt"></i> Login <i class="fa fa-chevron-down"></i>
                    </a>
                    <div class="dropdown-menu" id="loginDropdown">
                        <a href="<?php echo SITE_URL; ?>patient/login.php"><i class="fa fa-user"></i> Patient</a>
                        <a href="<?php echo SITE_URL; ?>hospital/login.php"><i class="fa fa-hospital"></i> Hospital</a>
                        <div class="dropdown-divider"></div>
                        <a href="<?php echo SITE_URL; ?>admin/login.php"><i class="fa fa-user-shield"></i> Admin</a>
                    </div>
                </li>
                
                <!-- Register Dropdown -->
                <li class="nav-item dropdown-item">
                    <a href="javascript:void(0)" onclick="toggleDropdown('registerDropdown')" class="dropdown-toggle">
                        <i class="fa fa-user-plus"></i> Register <i class="fa fa-chevron-down"></i>
                    </a>
                    <div class="dropdown-menu" id="registerDropdown">
                        <a href="<?php echo SITE_URL; ?>patient/register.php"><i class="fa fa-user"></i> Patient</a>
                        <a href="<?php echo SITE_URL; ?>hospital/register.php"><i class="fa fa-hospital"></i> Hospital</a>
                    </div>
                </li>
                
                <!-- Book Now -->
                <li class="nav-item">
                    <a href="<?php echo SITE_URL; ?>patient/book_appointment.php" class="btn-book">
                        <i class="fa fa-calendar-check"></i> Book Now
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</div>

<script>
    // Toggle mobile menu
    function toggleMobileMenu() {
        document.getElementById('navMenu').classList.toggle('show');
    }
    
    // Toggle dropdown
    function toggleDropdown(id) {
        // Close all dropdowns
        document.querySelectorAll('.dropdown-menu').forEach(function(menu) {
            if (menu.id !== id) {
                menu.classList.remove('show');
            }
        });
        
        // Toggle current
        var menu = document.getElementById(id);
        if (menu) {
            menu.classList.toggle('show');
        }
    }
    
    // Close when clicking outside
    document.addEventListener('click', function(event) {
        if (!event.target.closest('.dropdown-item') && !event.target.closest('.dropdown-toggle')) {
            document.querySelectorAll('.dropdown-menu').forEach(function(menu) {
                menu.classList.remove('show');
            });
        }
        
        // Close mobile menu on outside click
        if (!event.target.closest('.nav-menu') && !event.target.closest('.mobile-toggler')) {
            document.getElementById('navMenu').classList.remove('show');
        }
    });
    
    // Close on window resize
    window.addEventListener('resize', function() {
        if (window.innerWidth > 991) {
            document.getElementById('navMenu').classList.remove('show');
        }
    });
</script>