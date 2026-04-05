<?php
require_once 'config.php';
require_once 'includes/functions.php';

$page_title = 'Hospitals - COVID Care System';
require_once 'includes/header.php';
require_once 'includes/navbar.php';

// Get search parameters
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$city = isset($_GET['city']) ? sanitize($_GET['city']) : '';

// Build query conditions
$conditions = ["status = 'approved'"];
if (!empty($search)) {
    $conditions[] = "(name LIKE '%$search%' OR address LIKE '%$search%' OR city LIKE '%$search%')";
}
if (!empty($city)) {
    $conditions[] = "city = '$city'";
}
$where = implode(' AND ', $conditions);

// Get all approved hospitals
$hospitals = getRecords($conn, 'hospitals', $where, 'name');

// Get unique cities for filter
$cities_query = "SELECT DISTINCT city FROM hospitals WHERE status = 'approved' ORDER BY city";
$cities_result = mysqli_query($conn, $cities_query);
$cities = [];
if ($cities_result) {
    while ($row = mysqli_fetch_assoc($cities_result)) {
        $cities[] = $row['city'];
    }
}

// Get statistics
$total_hospitals = count($hospitals);
$total_cities = count($cities);
$total_vaccines = countRecords($conn, 'vaccines', "status = 'available'");
?>

<style>
    /* ===== HOSPITALS PAGE STYLES ===== */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: #f8f9fa;
    }
    
    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }
    
    /* Page Header */
    .page-header {
        background: linear-gradient(135deg, #0d6efd, #0b5ed7);
        padding: 80px 0;
        margin-bottom: 50px;
        text-align: center;
        color: white;
    }
    
    .page-header h1 {
        font-size: 48px;
        font-weight: 700;
        margin-bottom: 15px;
    }
    
    .page-header p {
        font-size: 18px;
        max-width: 700px;
        margin: 0 auto;
        opacity: 0.95;
    }
    
    /* Stats Bar */
    .stats-wrapper {
        margin-bottom: 40px;
    }
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 25px;
    }
    
    .stat-card {
        background: white;
        border-radius: 15px;
        padding: 30px 20px;
        text-align: center;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
        border: 1px solid #eee;
        border-bottom: 4px solid #0d6efd;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(13,110,253,0.15);
    }
    
    .stat-icon {
        width: 60px;
        height: 60px;
        background: #e7f1ff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        color: #0d6efd;
        font-size: 28px;
        transition: all 0.3s ease;
    }
    
    .stat-card:hover .stat-icon {
        background: #0d6efd;
        color: white;
        transform: rotate(360deg);
    }
    
    .stat-value {
        font-size: 36px;
        font-weight: 800;
        color: #0d6efd;
        margin-bottom: 5px;
        line-height: 1.2;
    }
    
    .stat-label {
        color: #6c757d;
        font-size: 14px;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    /* Search Section */
    .search-wrapper {
        margin-bottom: 40px;
    }
    
    .search-card {
        background: white;
        border-radius: 15px;
        padding: 30px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        border: 1px solid #eee;
    }
    
    .search-form {
        display: flex;
        gap: 15px;
        align-items: center;
        flex-wrap: wrap;
    }
    
    .search-group {
        flex: 2;
        min-width: 280px;
        position: relative;
    }
    
    .search-group i {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #0d6efd;
        font-size: 16px;
    }
    
    .search-group input {
        width: 100%;
        padding: 15px 15px 15px 45px;
        border: 2px solid #e9ecef;
        border-radius: 12px;
        font-size: 15px;
        transition: all 0.3s ease;
    }
    
    .search-group input:focus {
        outline: none;
        border-color: #0d6efd;
        box-shadow: 0 0 0 4px rgba(13,110,253,0.1);
    }
    
    .filter-group {
        flex: 1;
        min-width: 180px;
        position: relative;
    }
    
    .filter-group i {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #0d6efd;
        font-size: 16px;
        z-index: 1;
    }
    
    .filter-group select {
        width: 100%;
        padding: 15px 15px 15px 45px;
        border: 2px solid #e9ecef;
        border-radius: 12px;
        font-size: 15px;
        background: white;
        cursor: pointer;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%236c757d' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 15px center;
    }
    
    .filter-group select:focus {
        outline: none;
        border-color: #0d6efd;
    }
    
    .btn-group {
        flex: 0.5;
        min-width: 120px;
    }
    
    .btn-search {
        width: 100%;
        padding: 15px;
        background: #0d6efd;
        color: white;
        border: none;
        border-radius: 12px;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }
    
    .btn-search:hover {
        background: #0b5ed7;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(13,110,253,0.3);
    }
    
    .reset-group {
        flex: 0.3;
        min-width: 80px;
    }
    
    .btn-reset {
        width: 100%;
        padding: 15px;
        background: #f8f9fa;
        color: #6c757d;
        border: 2px solid #e9ecef;
        border-radius: 12px;
        font-size: 15px;
        font-weight: 500;
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        transition: all 0.3s ease;
    }
    
    .btn-reset:hover {
        background: #e9ecef;
        color: #333;
    }
    
    /* Results Header */
    .results-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        flex-wrap: wrap;
        gap: 15px;
    }
    
    .results-count {
        background: white;
        padding: 12px 25px;
        border-radius: 50px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        border: 1px solid #eee;
    }
    
    .results-count i {
        color: #0d6efd;
        margin-right: 8px;
    }
    
    .results-count strong {
        color: #0d6efd;
        font-size: 18px;
        margin: 0 3px;
    }
    
    .results-sort {
        background: white;
        padding: 8px 15px;
        border-radius: 50px;
        border: 1px solid #eee;
    }
    
    .results-sort select {
        border: none;
        background: transparent;
        padding: 5px 20px 5px 10px;
        font-size: 14px;
        color: #333;
        cursor: pointer;
        outline: none;
    }
    
    /* Hospitals Grid */
    .hospitals-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 30px;
        margin-bottom: 60px;
    }
    
    .hospital-card {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
        border: 1px solid #eee;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    
    .hospital-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 30px rgba(13,110,253,0.15);
        border-color: #0d6efd;
    }
    
    .hospital-image {
        height: 150px;
        background: linear-gradient(135deg, #0d6efd, #0b5ed7);
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .hospital-icon {
        width: 70px;
        height: 70px;
        background: rgba(255,255,255,0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 35px;
        backdrop-filter: blur(5px);
        border: 2px solid rgba(255,255,255,0.3);
    }
    
    .hospital-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        background: rgba(255,255,255,0.2);
        color: white;
        padding: 5px 12px;
        border-radius: 50px;
        font-size: 12px;
        font-weight: 600;
        backdrop-filter: blur(5px);
        border: 1px solid rgba(255,255,255,0.3);
    }
    
    .hospital-badge i {
        margin-right: 5px;
        font-size: 10px;
    }
    
    .hospital-content {
        padding: 25px;
        flex: 1;
    }
    
    .hospital-title {
        font-size: 20px;
        font-weight: 700;
        color: #333;
        margin-bottom: 15px;
        line-height: 1.3;
    }
    
    .hospital-detail {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        margin-bottom: 12px;
        color: #6c757d;
        font-size: 14px;
        line-height: 1.5;
    }
    
    .hospital-detail i {
        width: 18px;
        color: #0d6efd;
        font-size: 15px;
        margin-top: 2px;
    }
    
    .hospital-detail a {
        color: #6c757d;
        text-decoration: none;
        transition: color 0.3s ease;
    }
    
    .hospital-detail a:hover {
        color: #0d6efd;
    }
    
    .hospital-footer {
        padding: 20px 25px;
        border-top: 1px solid #eee;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #f8f9fa;
    }
    
    .verified-badge {
        display: flex;
        align-items: center;
        gap: 5px;
        color: #28a745;
        font-size: 13px;
        font-weight: 500;
    }
    
    .verified-badge i {
        font-size: 14px;
    }
    
    .book-btn {
        background: #0d6efd;
        color: white;
        padding: 8px 20px;
        border-radius: 50px;
        text-decoration: none;
        font-size: 13px;
        font-weight: 600;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    
    .book-btn:hover {
        background: #0b5ed7;
        transform: translateX(5px);
    }
    
    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 80px 20px;
        background: white;
        border-radius: 20px;
        border: 1px solid #eee;
        margin-bottom: 60px;
    }
    
    .empty-state i {
        font-size: 80px;
        color: #dee2e6;
        margin-bottom: 25px;
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
        font-size: 16px;
    }
    
    .empty-btn {
        display: inline-block;
        padding: 12px 35px;
        background: #0d6efd;
        color: white;
        text-decoration: none;
        border-radius: 50px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .empty-btn:hover {
        background: #0b5ed7;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(13,110,253,0.3);
    }
    
    /* CTA Section */
    .cta-section {
        background: linear-gradient(135deg, #0d6efd, #0b5ed7);
        padding: 60px 0;
        color: white;
        margin-top: 40px;
    }
    
    .cta-content {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 30px;
    }
    
    .cta-text h3 {
        font-size: 32px;
        font-weight: 700;
        margin-bottom: 10px;
    }
    
    .cta-text p {
        font-size: 18px;
        opacity: 0.95;
        margin: 0;
    }
    
    .cta-btn {
        background: white;
        color: #0d6efd;
        padding: 15px 40px;
        border-radius: 50px;
        text-decoration: none;
        font-weight: 600;
        font-size: 16px;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        white-space: nowrap;
    }
    
    .cta-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    }
    
    /* Responsive */
    @media (max-width: 992px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        
        .hospitals-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        
        .page-header h1 {
            font-size: 36px;
        }
    }
    
    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }
        
        .hospitals-grid {
            grid-template-columns: 1fr;
        }
        
        .search-form {
            flex-direction: column;
        }
        
        .search-group,
        .filter-group,
        .btn-group,
        .reset-group {
            width: 100%;
        }
        
        .results-header {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .cta-content {
            flex-direction: column;
            text-align: center;
        }
        
        .cta-btn {
            width: 100%;
            justify-content: center;
        }
        
        .page-header h1 {
            font-size: 30px;
        }
    }
</style>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <h1>Partner Hospitals</h1>
        <p>Find registered hospitals for COVID-19 testing and vaccination across Pakistan</p>
    </div>
</section>

<!-- Statistics Section -->
<section class="container stats-wrapper">
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fa fa-hospital"></i>
            </div>
            <div class="stat-value"><?php echo $total_hospitals; ?></div>
            <div class="stat-label">Total Hospitals</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fa fa-map-marker-alt"></i>
            </div>
            <div class="stat-value"><?php echo $total_cities; ?></div>
            <div class="stat-label">Cities Covered</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fa fa-syringe"></i>
            </div>
            <div class="stat-value"><?php echo $total_vaccines; ?></div>
            <div class="stat-label">Vaccine Types</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fa fa-clock"></i>
            </div>
            <div class="stat-value">24/7</div>
            <div class="stat-label">Emergency</div>
        </div>
    </div>
</section>

<!-- Search Section -->
<section class="container search-wrapper">
    <div class="search-card">
        <form method="GET" action="" class="search-form">
            <div class="search-group">
                <i class="fa fa-search"></i>
                <input type="text" name="search" placeholder="Search by hospital name, address or city..." value="<?php echo htmlspecialchars($search); ?>">
            </div>
            
            <div class="filter-group">
                <i class="fa fa-map-marker-alt"></i>
                <select name="city">
                    <option value="">All Cities</option>
                    <?php foreach ($cities as $city_name): ?>
                    <option value="<?php echo $city_name; ?>" <?php echo ($city == $city_name) ? 'selected' : ''; ?>>
                        <?php echo $city_name; ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="btn-group">
                <button type="submit" class="btn-search">
                    <i class="fa fa-search"></i> Search
                </button>
            </div>
            
            <?php if (!empty($search) || !empty($city)): ?>
            <div class="reset-group">
                <a href="<?php echo SITE_URL; ?>hospitals.php" class="btn-reset">
                    <i class="fa fa-times"></i> Reset
                </a>
            </div>
            <?php endif; ?>
        </form>
    </div>
</section>

<!-- Results Section -->
<section class="container">
    <?php if (!empty($hospitals)): ?>
    
    <div class="results-header">
        <div class="results-count">
            <i class="fa fa-hospital"></i>
            <strong><?php echo count($hospitals); ?></strong> hospitals found
            <?php if (!empty($search)): ?>
                matching "<strong><?php echo htmlspecialchars($search); ?></strong>"
            <?php endif; ?>
            <?php if (!empty($city)): ?>
                in <strong><?php echo htmlspecialchars($city); ?></strong>
            <?php endif; ?>
        </div>
        
        <div class="results-sort">
            <select onchange="window.location.href=this.value">
                <option value="?sort=name<?php echo !empty($search) ? '&search='.$search : ''; ?><?php echo !empty($city) ? '&city='.$city : ''; ?>">Sort by Name</option>
                <option value="?sort=city<?php echo !empty($search) ? '&search='.$search : ''; ?><?php echo !empty($city) ? '&city='.$city : ''; ?>">Sort by City</option>
                <option value="?sort=newest<?php echo !empty($search) ? '&search='.$search : ''; ?><?php echo !empty($city) ? '&city='.$city : ''; ?>">Newest First</option>
            </select>
        </div>
    </div>
    
    <div class="hospitals-grid">
        <?php foreach ($hospitals as $hospital): ?>
        <div class="hospital-card">
            <div class="hospital-image">
                <div class="hospital-icon">
                    <i class="fa fa-hospital"></i>
                </div>
                <span class="hospital-badge">
                    <i class="fa fa-check-circle"></i> Verified
                </span>
            </div>
            
            <div class="hospital-content">
                <h3 class="hospital-title"><?php echo $hospital['name']; ?></h3>
                
                <div class="hospital-detail">
                    <i class="fa fa-map-marker-alt"></i>
                    <span><?php echo $hospital['address']; ?>, <?php echo $hospital['city']; ?></span>
                </div>
                
                <div class="hospital-detail">
                    <i class="fa fa-phone"></i>
                    <a href="tel:<?php echo $hospital['phone']; ?>"><?php echo formatPhone($hospital['phone']); ?></a>
                </div>
                
                <div class="hospital-detail">
                    <i class="fa fa-envelope"></i>
                    <a href="mailto:<?php echo $hospital['email']; ?>"><?php echo $hospital['email']; ?></a>
                </div>
                
                <div class="hospital-detail">
                    <i class="fa fa-id-card"></i>
                    <span>Reg No: <?php echo $hospital['registration_no']; ?></span>
                </div>
            </div>
            
            <div class="hospital-footer">
                <span class="verified-badge">
                    <i class="fa fa-check-circle"></i> Verified
                </span>
                <a href="<?php echo SITE_URL; ?>patient/book_appointment.php?hospital=<?php echo $hospital['hospital_id']; ?>" class="book-btn">
                    Book Now <i class="fa fa-arrow-right"></i>
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    
    <?php else: ?>
    
    <div class="empty-state">
        <i class="fa fa-hospital"></i>
        <h3>No Hospitals Found</h3>
        <p>We couldn't find any hospitals matching your search criteria.</p>
        <a href="<?php echo SITE_URL; ?>hospitals.php" class="empty-btn">
            <i class="fa fa-refresh me-2"></i>View All Hospitals
        </a>
    </div>
    
    <?php endif; ?>
</section>

<!-- CTA Section -->
<section class="cta-section">
    <div class="container">
        <div class="cta-content">
            <div class="cta-text">
                <h3>Want to list your hospital?</h3>
                <p>Register your hospital with us and reach thousands of patients across Pakistan.</p>
            </div>
            <a href="<?php echo SITE_URL; ?>hospital/register.php" class="cta-btn">
                <i class="fa fa-plus-circle"></i> Register Hospital
            </a>
        </div>
    </div>
</section>

<?php
require_once 'includes/footer.php';
?>