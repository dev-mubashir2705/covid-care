<?php
require_once 'config.php';
require_once 'includes/functions.php';

$page_title = 'About Us - COVID Care System';
require_once 'includes/header.php';
require_once 'includes/navbar.php';

// Database se statistics fetch karna
$total_hospitals = countRecords($conn, 'hospitals', "status = 'approved'");
$total_patients = countRecords($conn, 'patients');
$total_vaccines = countRecords($conn, 'vaccines', "status = 'available'");
$total_appointments = countRecords($conn, 'appointments');

// Static Doctors Data with Social Links
$doctors = [
    [
        'name' => 'Dr. Ahmed Khan',
        'specialty' => 'Cardiology Specialist',
        'qualification' => 'MBBS, FCPS',
        'experience' => '15+ years',
        'image' => 'team-1.jpg',
        'facebook' => 'https://facebook.com/dr.ahmedkhan',
        'twitter' => 'https://twitter.com/dr.ahmedkhan',
        'linkedin' => 'https://linkedin.com/in/dr.ahmedkhan',
        'whatsapp' => 'https://wa.me/923001234567'
    ],
    [
        'name' => 'Dr. Fatima Ali',
        'specialty' => 'Pulmonology Specialist',
        'qualification' => 'MBBS, MD',
        'experience' => '12+ years',
        'image' => 'team-2.jpg',
        'facebook' => 'https://facebook.com/dr.fatimaali',
        'twitter' => 'https://twitter.com/dr.fatimaali',
        'linkedin' => 'https://linkedin.com/in/dr.fatimaali',
        'whatsapp' => 'https://wa.me/923001234568'
    ],
    [
        'name' => 'Dr. Usman Malik',
        'specialty' => 'Vaccination Expert',
        'qualification' => 'MBBS, MPH',
        'experience' => '10+ years',
        'image' => 'team-3.jpg',
        'facebook' => 'https://facebook.com/dr.usmanmalik',
        'twitter' => 'https://twitter.com/dr.usmanmalik',
        'linkedin' => 'https://linkedin.com/in/dr.usmanmalik',
        'whatsapp' => 'https://wa.me/923001234569'
    ]
];
?>

<style>
    /* ========== COMPLETE ABOUT PAGE STYLES ========== */
    
    /* ===== HERO SECTION ===== */
    .about-hero {
        position: relative;
        background: linear-gradient(135deg, #0d6efd 0%, #0099ff 50%, #0b5ed7 100%);
        padding: 120px 0;
        margin-bottom: 80px;
        text-align: center;
        color: white;
        overflow: hidden;
    }
    
    .about-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-image: url('<?php echo SITE_URL; ?>assets/img/pattern.png');
        background-repeat: repeat;
        background-size: 200px;
        opacity: 0.1;
        animation: patternMove 30s linear infinite;
        z-index: 1;
    }
    
    @keyframes patternMove {
        0% { background-position: 0 0; }
        100% { background-position: 200px 200px; }
    }
    
    .hero-shape {
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.1);
        z-index: 2;
    }
    
    .shape-1 {
        width: 300px;
        height: 300px;
        top: -100px;
        right: -50px;
        animation: float 15s ease-in-out infinite;
    }
    
    .shape-2 {
        width: 200px;
        height: 200px;
        bottom: -50px;
        left: -30px;
        animation: float 18s ease-in-out infinite reverse;
    }
    
    .shape-3 {
        width: 150px;
        height: 150px;
        top: 30%;
        left: 10%;
        animation: float 12s ease-in-out infinite;
    }
    
    @keyframes float {
        0%, 100% { transform: translate(0, 0) scale(1); }
        50% { transform: translate(-30px, 30px) scale(1.1); }
    }
    
    .hero-content {
        position: relative;
        z-index: 10;
        max-width: 800px;
        margin: 0 auto;
    }
    
    .hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(10px);
        padding: 10px 30px;
        border-radius: 50px;
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 25px;
        border: 1px solid rgba(255, 255, 255, 0.3);
        animation: fadeInDown 1s ease;
    }
    
    .hero-badge i {
        color: #ffc107;
        font-size: 16px;
    }
    
    .hero-content h1 {
        font-size: 52px;
        font-weight: 800;
        margin-bottom: 20px;
        text-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
        animation: fadeInUp 1s ease 0.2s both;
    }
    
    .hero-content h1 span {
        color: #ffc107;
        position: relative;
        display: inline-block;
    }
    
    .hero-content p {
        font-size: 18px;
        margin-bottom: 30px;
        opacity: 0.95;
        line-height: 1.8;
        animation: fadeInUp 1s ease 0.4s both;
    }
    
    .hero-stats-mini {
        display: flex;
        justify-content: center;
        gap: 40px;
        animation: fadeInUp 1s ease 0.6s both;
    }
    
    .hero-stat-item {
        text-align: center;
    }
    
    .hero-stat-number {
        font-size: 28px;
        font-weight: 700;
        margin-bottom: 5px;
    }
    
    .hero-stat-label {
        font-size: 13px;
        opacity: 0.8;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(40px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @keyframes fadeInDown {
        from {
            opacity: 0;
            transform: translateY(-40px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    /* ===== ABOUT SECTION ===== */
    .about-section {
        padding: 0 0 80px 0;
    }
    
    .about-image-wrapper {
        position: relative;
        padding: 20px;
    }
    
    .about-image {
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        position: relative;
        z-index: 2;
    }
    
    .about-image img {
        width: 100%;
        height: auto;
        transition: transform 0.5s ease;
    }
    
    .about-image:hover img {
        transform: scale(1.05);
    }
    
    .about-image::before {
        content: '';
        position: absolute;
        top: 15px;
        left: 15px;
        right: 15px;
        bottom: 15px;
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-radius: 10px;
        z-index: 3;
        pointer-events: none;
    }
    
    .about-badge {
        position: absolute;
        bottom: 30px;
        right: 0;
        background: linear-gradient(135deg, #0d6efd, #0b5ed7);
        color: white;
        padding: 15px 25px;
        border-radius: 50px 0 0 50px;
        box-shadow: 0 10px 25px rgba(13,110,253,0.3);
        z-index: 10;
    }
    
    .about-badge h3 {
        font-size: 24px;
        font-weight: 700;
        margin: 0;
        line-height: 1;
    }
    
    .about-badge p {
        font-size: 12px;
        margin: 5px 0 0;
        opacity: 0.9;
    }
    
    .about-content {
        padding-left: 40px;
    }
    
    .section-tag {
        display: inline-block;
        background: #e7f1ff;
        color: #0d6efd;
        padding: 5px 20px;
        border-radius: 50px;
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 15px;
    }
    
    .about-content h2 {
        font-size: 38px;
        font-weight: 700;
        color: #333;
        margin-bottom: 20px;
        line-height: 1.3;
    }
    
    .about-content h2 span {
        color: #0d6efd;
        border-bottom: 3px solid #0d6efd;
        padding-bottom: 5px;
    }
    
    .about-content p {
        color: #6c757d;
        line-height: 1.8;
        margin-bottom: 20px;
        font-size: 16px;
    }
    
    /* ===== STATS SECTION ===== */
    .stats-section {
        padding: 60px 0;
        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    }
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 25px;
    }
    
    .stat-card {
        background: white;
        padding: 35px 20px;
        border-radius: 15px;
        text-align: center;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        border: 1px solid #eee;
    }
    
    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 5px;
        background: linear-gradient(90deg, #0d6efd, #0b5ed7);
        transform: scaleX(0);
        transition: transform 0.3s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(13,110,253,0.15);
    }
    
    .stat-card:hover::before {
        transform: scaleX(1);
    }
    
    .stat-icon {
        width: 70px;
        height: 70px;
        background: #e7f1ff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        color: #0d6efd;
        font-size: 30px;
        transition: all 0.3s ease;
    }
    
    .stat-card:hover .stat-icon {
        background: #0d6efd;
        color: white;
        transform: rotate(360deg);
    }
    
    .stat-number {
        font-size: 42px;
        font-weight: 800;
        color: #0d6efd;
        margin-bottom: 5px;
        line-height: 1;
    }
    
    .stat-label {
        color: #6c757d;
        font-size: 15px;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    
    /* ===== FEATURES SECTION ===== */
    .features-section {
        padding: 80px 0;
    }
    
    .section-header {
        text-align: center;
        margin-bottom: 60px;
    }
    
    .section-header .subtitle {
        display: inline-block;
        background: #e7f1ff;
        color: #0d6efd;
        padding: 5px 20px;
        border-radius: 50px;
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 15px;
    }
    
    .section-header h2 {
        font-size: 38px;
        font-weight: 700;
        color: #333;
        margin-bottom: 15px;
    }
    
    .section-header h2 span {
        color: #0d6efd;
    }
    
    .section-header p {
        color: #6c757d;
        font-size: 18px;
        max-width: 700px;
        margin: 0 auto;
    }
    
    .features-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 30px;
    }
    
    .feature-card {
        background: white;
        padding: 40px 25px;
        border-radius: 15px;
        text-align: center;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
        border: 1px solid #eee;
        position: relative;
        overflow: hidden;
    }
    
    .feature-card::before {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 5px;
        background: linear-gradient(90deg, #0d6efd, #0b5ed7);
        transform: scaleX(0);
        transition: transform 0.3s ease;
    }
    
    .feature-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(13,110,253,0.15);
    }
    
    .feature-card:hover::before {
        transform: scaleX(1);
    }
    
    .feature-icon {
        width: 80px;
        height: 80px;
        background: #e7f1ff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 25px;
        color: #0d6efd;
        font-size: 35px;
        transition: all 0.3s ease;
    }
    
    .feature-card:hover .feature-icon {
        background: #0d6efd;
        color: white;
        transform: rotateY(360deg);
    }
    
    .feature-card h4 {
        font-size: 22px;
        font-weight: 600;
        color: #333;
        margin-bottom: 15px;
    }
    
    .feature-card p {
        color: #6c757d;
        line-height: 1.7;
        margin: 0;
        font-size: 15px;
    }
    
    /* ===== DOCTORS SECTION WITH SOCIAL ICONS ===== */
    .doctors-section {
        padding: 80px 0;
        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    }
    
    .doctors-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 30px;
    }
    
    .doctor-card {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }
    
    .doctor-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(13,110,253,0.2);
    }
    
    .doctor-image {
        position: relative;
        width: 100%;
        height: 300px;
        overflow: hidden;
    }
    
    .doctor-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }
    
    .doctor-card:hover .doctor-image img {
        transform: scale(1.1);
    }
    
    .doctor-overlay {
        position: absolute;
        bottom: -50px;
        left: 0;
        right: 0;
        background: linear-gradient(to top, rgba(13,110,253,0.9), transparent);
        padding: 20px;
        text-align: center;
        transition: all 0.3s ease;
        opacity: 0;
    }
    
    .doctor-card:hover .doctor-overlay {
        bottom: 0;
        opacity: 1;
    }
    
    .doctor-overlay p {
        color: white;
        margin: 5px 0;
        font-size: 13px;
    }
    
    .doctor-overlay i {
        margin-right: 5px;
    }
    
    .doctor-info {
        padding: 25px;
        text-align: center;
    }
    
    .doctor-info h4 {
        font-size: 22px;
        font-weight: 700;
        color: #333;
        margin-bottom: 5px;
    }
    
    .doctor-info .specialty {
        color: #0d6efd;
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 15px;
    }
    
    /* Social Icons */
    .doctor-social {
        display: flex;
        justify-content: center;
        gap: 12px;
        margin-top: 15px;
        padding-top: 15px;
        border-top: 1px solid #eee;
    }
    
    .social-icon {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 16px;
        transition: all 0.3s ease;
        text-decoration: none;
    }
    
    .social-icon.facebook {
        background: #1877f2;
    }
    
    .social-icon.twitter {
        background: #1da1f2;
    }
    
    .social-icon.linkedin {
        background: #0077b5;
    }
    
    .social-icon.whatsapp {
        background: #25D366;
    }
    
    .social-icon:hover {
        transform: translateY(-5px) scale(1.1);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }
    
    .social-icon.facebook:hover {
        background: #0d6efd;
    }
    
    .social-icon.twitter:hover {
        background: #0d6efd;
    }
    
    .social-icon.linkedin:hover {
        background: #0d6efd;
    }
    
    .social-icon.whatsapp:hover {
        background: #0d6efd;
    }
    
    /* ===== CTA SECTION ===== */
    .cta-section {
        background: linear-gradient(135deg, #0d6efd, #0b5ed7);
        padding: 80px 0;
        color: white;
        text-align: center;
        position: relative;
        overflow: hidden;
    }
    
    .cta-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: repeating-linear-gradient(45deg, transparent, transparent 30px, rgba(255,255,255,0.05) 30px, rgba(255,255,255,0.05) 60px);
        animation: ctaMove 20s linear infinite;
    }
    
    @keyframes ctaMove {
        0% { background-position: 0 0; }
        100% { background-position: 100px 100px; }
    }
    
    .cta-section .container {
        position: relative;
        z-index: 10;
    }
    
    .cta-section h2 {
        font-size: 42px;
        font-weight: 800;
        margin-bottom: 20px;
        text-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }
    
    .cta-section p {
        font-size: 18px;
        margin-bottom: 30px;
        opacity: 0.95;
    }
    
    .cta-buttons {
        display: flex;
        justify-content: center;
        gap: 20px;
    }
    
    .cta-btn {
        display: inline-block;
        padding: 15px 40px;
        border-radius: 50px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
        font-size: 16px;
    }
    
    .cta-btn-primary {
        background: white;
        color: #0d6efd;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    }
    
    .cta-btn-primary:hover {
        background: #f8f9fa;
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.3);
    }
    
    .cta-btn-outline {
        background: transparent;
        color: white;
        border: 2px solid white;
    }
    
    .cta-btn-outline:hover {
        background: white;
        color: #0d6efd;
        transform: translateY(-5px);
    }
    
    /* ===== RESPONSIVE ===== */
    @media (max-width: 992px) {
        .about-content {
            padding-left: 0;
            margin-top: 30px;
        }
        
        .stats-grid,
        .features-grid,
        .doctors-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        
        .hero-content h1 {
            font-size: 42px;
        }
    }
    
    @media (max-width: 768px) {
        .hero-content h1 {
            font-size: 32px;
        }
        
        .about-content h2 {
            font-size: 30px;
        }
        
        .stats-grid,
        .features-grid,
        .doctors-grid {
            grid-template-columns: 1fr;
        }
        
        .hero-stats-mini {
            flex-direction: column;
            gap: 20px;
        }
        
        .cta-buttons {
            flex-direction: column;
        }
        
        .cta-section h2 {
            font-size: 32px;
        }
    }
</style>

<!-- HERO SECTION -->
<section class="about-hero">
    <div class="hero-shape shape-1"></div>
    <div class="hero-shape shape-2"></div>
    <div class="hero-shape shape-3"></div>
    
    <div class="container">
        <div class="hero-content">
            <div class="hero-badge">
                <i class="fa fa-shield-virus"></i>
                <span>Trusted Since 2020</span>
            </div>
            <h1>About <span><?php echo SITE_NAME; ?></span></h1>
            <p>Your trusted partner in COVID-19 testing and vaccination across Pakistan. We connect patients with registered hospitals for seamless healthcare services.</p>
            
            <div class="hero-stats-mini">
                <div class="hero-stat-item">
                    <div class="hero-stat-number"><?php echo $total_hospitals; ?>+</div>
                    <div class="hero-stat-label">Hospitals</div>
                </div>
                <div class="hero-stat-item">
                    <div class="hero-stat-number"><?php echo $total_patients; ?>+</div>
                    <div class="hero-stat-label">Patients</div>
                </div>
                <div class="hero-stat-item">
                    <div class="hero-stat-number"><?php echo $total_appointments; ?>+</div>
                    <div class="hero-stat-label">Appointments</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ABOUT SECTION -->
<section class="about-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="about-image-wrapper">
                    <div class="about-image">
                        <img src="<?php echo SITE_URL; ?>assets/img/about.png" alt="About Us" class="img-fluid">
                    </div>
                    <div class="about-badge">
                        <h3><?php echo date('Y') - 2020; ?>+</h3>
                        <p>Years of Service</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="about-content">
                    <span class="section-tag">Who We Are</span>
                    <h2>Leading <span>Healthcare</span> Platform in Pakistan</h2>
                    <p><?php echo SITE_NAME; ?> is a web application for online test and vaccination booking system for patients. The Covid-19 web app connects the people with the hospital and the administration to come together and fight the pandemic.</p>
                    <p>With this web app, you can track vaccination appointments, history and COVID solution guidelines for symptoms. Our platform facilitates online appointments for vaccination and testing with registered hospitals across Karachi.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- STATS SECTION -->
<section class="stats-section">
    <div class="container">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon"><i class="fa fa-hospital"></i></div>
                <div class="stat-number"><?php echo $total_hospitals; ?></div>
                <div class="stat-label">Hospitals</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fa fa-users"></i></div>
                <div class="stat-number"><?php echo $total_patients; ?></div>
                <div class="stat-label">Patients</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fa fa-syringe"></i></div>
                <div class="stat-number"><?php echo $total_vaccines; ?></div>
                <div class="stat-label">Vaccines</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fa fa-calendar-check"></i></div>
                <div class="stat-number"><?php echo $total_appointments; ?></div>
                <div class="stat-label">Appointments</div>
            </div>
        </div>
    </div>
</section>

<!-- FEATURES SECTION -->
<section class="features-section">
    <div class="container">
        <div class="section-header">
            <span class="subtitle">Why Choose Us</span>
            <h2>Key <span>Features</span></h2>
            <p>We provide the best healthcare services with modern technology</p>
        </div>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon"><i class="fa fa-calendar-plus"></i></div>
                <h4>Easy Booking</h4>
                <p>Book your appointments online in just a few clicks. Choose your preferred hospital and time slot.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fa fa-flask"></i></div>
                <h4>Fast Testing</h4>
                <p>Get your COVID-19 test results quickly. Hospitals update results instantly on our platform.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fa fa-shield-alt"></i></div>
                <h4>Safe Vaccination</h4>
                <p>Certified vaccines from trusted sources. All partner hospitals follow strict safety protocols.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fa fa-file-medical"></i></div>
                <h4>Digital Records</h4>
                <p>Access your test results and vaccination history anytime, anywhere from your profile.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fa fa-headset"></i></div>
                <h4>24/7 Support</h4>
                <p>Our support team is always available to assist you with any queries or issues.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fa fa-shield-virus"></i></div>
                <h4>Safe & Secure</h4>
                <p>Your data is safe with us. We follow strict privacy and security protocols.</p>
            </div>
        </div>
    </div>
</section>

<!-- DOCTORS SECTION WITH SOCIAL ICONS -->
<section class="doctors-section">
    <div class="container">
        <div class="section-header">
            <span class="subtitle">Our Doctors</span>
            <h2>Medical <span>Experts</span></h2>
            <p>Meet our team of experienced healthcare professionals</p>
        </div>
        <div class="doctors-grid">
            <?php foreach ($doctors as $doctor): ?>
            <div class="doctor-card">
                <div class="doctor-image">
                    <img src="<?php echo SITE_URL; ?>assets/img/<?php echo $doctor['image']; ?>" alt="<?php echo $doctor['name']; ?>">
                    <div class="doctor-overlay">
                        <p><i class="fa fa-graduation-cap"></i> <?php echo $doctor['qualification']; ?></p>
                        <p><i class="fa fa-clock"></i> <?php echo $doctor['experience']; ?></p>
                    </div>
                </div>
                <div class="doctor-info">
                    <h4><?php echo $doctor['name']; ?></h4>
                    <p class="specialty"><?php echo $doctor['specialty']; ?></p>
                    
                    <div class="doctor-social">
                        <a href="<?php echo $doctor['facebook']; ?>" class="social-icon facebook" title="Facebook" target="_blank">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="<?php echo $doctor['twitter']; ?>" class="social-icon twitter" title="Twitter" target="_blank">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="<?php echo $doctor['linkedin']; ?>" class="social-icon linkedin" title="LinkedIn" target="_blank">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <a href="<?php echo $doctor['whatsapp']; ?>" class="social-icon whatsapp" title="WhatsApp" target="_blank">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CTA SECTION -->
<section class="cta-section">
    <div class="container">
        <h2>Ready to Get Started?</h2>
        <p>Join thousands of satisfied patients who trust <?php echo SITE_NAME; ?> for their healthcare needs.</p>
        <div class="cta-buttons">
            <a href="<?php echo SITE_URL; ?>patient/register.php" class="cta-btn cta-btn-primary">
                <i class="fa fa-user-plus me-2"></i>Register as Patient
            </a>
            <a href="<?php echo SITE_URL; ?>patient/book_appointment.php" class="cta-btn cta-btn-outline">
                <i class="fa fa-calendar-check me-2"></i>Book Appointment
            </a>
        </div>
    </div>
</section>

<?php
require_once 'includes/footer.php';
?>