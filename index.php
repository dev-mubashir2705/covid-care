<?php
require_once 'config.php';
require_once 'includes/functions.php';

$page_title = 'Home - COVID Care System';
require_once 'includes/header.php';
require_once 'includes/navbar.php';

// Database se statistics fetch karna
$total_hospitals = countRecords($conn, 'hospitals', "status = 'approved'");
$total_patients = countRecords($conn, 'patients');
$total_vaccines = countRecords($conn, 'vaccines', "status = 'available'");
$total_appointments = countRecords($conn, 'appointments');

// Approved hospitals fetch karna (sirf 1)
$hospitals = getApprovedHospitals($conn, null, 1);

// Available vaccines fetch karna
$vaccines = getAvailableVaccines($conn);

// Testimonials ke liye random patients
$testimonials_query = "SELECT p.name, p.city, p.patient_id 
                      FROM patients p 
                      WHERE EXISTS (
                          SELECT 1 FROM appointments a 
                          WHERE a.patient_id = p.patient_id 
                          AND a.status = 'completed'
                      )
                      ORDER BY RAND() LIMIT 3";
$testimonials_result = mysqli_query($conn, $testimonials_query);
$testimonials = [];
if ($testimonials_result) {
    while ($row = mysqli_fetch_assoc($testimonials_result)) {
        $testimonials[] = $row;
    }
}
?>

<style>
    /* ========== GLOBAL STYLES ========== */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        overflow-x: hidden;
    }
    
    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }
    
    /* ========== HERO SECTION ========== */
    .hero-section {
        position: relative;
        min-height: 650px;
        display: flex;
        align-items: center;
        background: linear-gradient(135deg, #0d6efd 0%, #0099ff 60%, #0b5ed7 100%);
        overflow: hidden;
        padding: 80px 0;
    }
    
    /* Pattern Image Background */
    .hero-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-image: url('<?php echo SITE_URL; ?>assets/img/pattern.png');
        background-repeat: repeat;
        background-size: 250px;
        opacity: 0.12;
        animation: patternMove 40s linear infinite;
        z-index: 1;
    }
    
    @keyframes patternMove {
        0% { background-position: 0 0; }
        100% { background-position: 250px 250px; }
    }
    
    /* Animated Circles */
    .hero-circle {
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.1);
        z-index: 2;
    }
    
    .circle-1 {
        width: 400px;
        height: 400px;
        top: -150px;
        right: -100px;
        animation: floatCircle 15s ease-in-out infinite;
    }
    
    .circle-2 {
        width: 300px;
        height: 300px;
        bottom: -100px;
        left: -50px;
        animation: floatCircle 18s ease-in-out infinite reverse;
    }
    
    .circle-3 {
        width: 200px;
        height: 200px;
        top: 50%;
        right: 15%;
        animation: floatCircle 12s ease-in-out infinite;
    }
    
    @keyframes floatCircle {
        0%, 100% { transform: translate(0, 0) scale(1); }
        50% { transform: translate(-30px, 30px) scale(1.1); }
    }
    
    /* Floating Particles */
    .hero-particle {
        position: absolute;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        pointer-events: none;
        z-index: 2;
    }
    
    .particle-1 {
        width: 15px;
        height: 15px;
        top: 20%;
        left: 10%;
        animation: particleMove 8s ease-in-out infinite;
    }
    
    .particle-2 {
        width: 25px;
        height: 25px;
        top: 60%;
        right: 15%;
        animation: particleMove 10s ease-in-out infinite reverse;
    }
    
    .particle-3 {
        width: 20px;
        height: 20px;
        bottom: 25%;
        left: 20%;
        animation: particleMove 9s ease-in-out infinite;
    }
    
    .particle-4 {
        width: 30px;
        height: 30px;
        top: 30%;
        right: 25%;
        animation: particleMove 12s ease-in-out infinite;
    }
    
    @keyframes particleMove {
        0%, 100% { transform: translate(0, 0); }
        25% { transform: translate(20px, -20px); }
        50% { transform: translate(40px, 0); }
        75% { transform: translate(20px, 20px); }
    }
    
    /* Hero Content */
    .hero-content {
        position: relative;
        z-index: 10;
        color: white;
        text-align: center;
        max-width: 900px;
        margin: 0 auto;
        padding: 40px 20px;
    }
    
    .hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(10px);
        padding: 12px 30px;
        border-radius: 60px;
        font-weight: 600;
        font-size: 16px;
        margin-bottom: 30px;
        border: 1px solid rgba(255, 255, 255, 0.3);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        animation: fadeInDown 0.8s ease;
    }
    
    .hero-badge i {
        font-size: 20px;
        color: #ffc107;
    }
    
    .hero-title {
        font-size: 64px;
        font-weight: 800;
        margin-bottom: 25px;
        line-height: 1.2;
        text-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
        animation: fadeInUp 0.8s ease 0.2s both;
    }
    
    .hero-title span {
        display: inline-block;
        background: linear-gradient(45deg, #fff, #ffc107);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        position: relative;
        padding: 0 10px;
    }
    
    .hero-title span::before {
        content: '';
        position: absolute;
        bottom: 10px;
        left: 0;
        width: 100%;
        height: 10px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 10px;
        z-index: -1;
    }
    
    .hero-subtitle {
        font-size: 20px;
        margin-bottom: 40px;
        opacity: 0.95;
        line-height: 1.8;
        max-width: 700px;
        margin-left: auto;
        margin-right: auto;
        animation: fadeInUp 0.8s ease 0.4s both;
    }
    
    .hero-buttons {
        display: flex;
        justify-content: center;
        gap: 20px;
        flex-wrap: wrap;
        animation: fadeInUp 0.8s ease 0.6s both;
    }
    
    .hero-btn {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 16px 40px;
        border-radius: 60px;
        font-weight: 600;
        font-size: 16px;
        text-decoration: none;
        transition: all 0.3s ease;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }
    
    .hero-btn i {
        font-size: 18px;
        transition: transform 0.3s ease;
    }
    
    .hero-btn-primary {
        background: white;
        color: #0d6efd;
    }
    
    .hero-btn-primary:hover {
        background: #f8f9fa;
        transform: translateY(-5px);
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
    }
    
    .hero-btn-primary:hover i {
        transform: translateX(5px);
    }
    
    .hero-btn-outline {
        background: transparent;
        color: white;
        border: 2px solid white;
    }
    
    .hero-btn-outline:hover {
        background: white;
        color: #0d6efd;
        transform: translateY(-5px);
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
    }
    
    .hero-btn-outline:hover i {
        transform: translateX(5px);
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
    
    /* ========== STATS SECTION ========== */
    .stats-section {
        padding: 80px 0;
        background: #f8f9fa;
    }
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 25px;
    }
    
    .stat-card {
        background: white;
        border-radius: 20px;
        padding: 35px 25px;
        text-align: center;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        border-bottom: 5px solid #0d6efd;
    }
    
    .stat-card::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: linear-gradient(45deg, transparent, rgba(13, 110, 253, 0.03), transparent);
        transform: rotate(45deg);
        transition: all 0.6s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(13, 110, 253, 0.15);
    }
    
    .stat-card:hover::before {
        transform: rotate(45deg) translate(50%, 50%);
    }
    
    .stat-icon {
        width: 90px;
        height: 90px;
        background: linear-gradient(135deg, #0d6efd, #0b5ed7);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 25px;
        color: white;
        font-size: 40px;
        box-shadow: 0 10px 20px rgba(13, 110, 253, 0.3);
        transition: all 0.3s ease;
    }
    
    .stat-card:hover .stat-icon {
        transform: rotate(360deg);
        background: linear-gradient(135deg, #0b5ed7, #0d6efd);
    }
    
    .stat-number {
        font-size: 48px;
        font-weight: 800;
        color: #0d6efd;
        margin-bottom: 10px;
        line-height: 1;
    }
    
    .stat-label {
        color: #6c757d;
        font-size: 16px;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    
    /* ========== ABOUT SECTION ========== */
    .about-section {
        padding: 80px 0;
    }
    
    .about-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 50px;
        align-items: center;
    }
    
    .about-image {
        position: relative;
        border-radius: 30px;
        overflow: hidden;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    }
    
    .about-image::before {
        content: '';
        position: absolute;
        top: 20px;
        left: 20px;
        right: 20px;
        bottom: 20px;
        border: 3px solid rgba(255, 255, 255, 0.3);
        border-radius: 20px;
        z-index: 2;
        pointer-events: none;
    }
    
    .about-image img {
        width: 100%;
        height: auto;
        display: block;
        transition: transform 0.6s ease;
    }
    
    .about-image:hover img {
        transform: scale(1.1);
    }
    
    .about-content {
        padding-right: 30px;
    }
    
    .section-subtitle {
        display: inline-block;
        background: linear-gradient(135deg, #0d6efd, #0b5ed7);
        color: white;
        padding: 8px 25px;
        border-radius: 50px;
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 20px;
        box-shadow: 0 5px 15px rgba(13, 110, 253, 0.3);
    }
    
    .section-title {
        font-size: 42px;
        font-weight: 700;
        color: #333;
        margin-bottom: 20px;
        line-height: 1.2;
    }
    
    .section-text {
        font-size: 16px;
        color: #6c757d;
        line-height: 1.8;
        margin-bottom: 20px;
    }
    
    .features-list {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
        margin-top: 30px;
    }
    
    .feature-item {
        display: flex;
        align-items: center;
        gap: 15px;
    }
    
    .feature-item i {
        font-size: 28px;
        color: #0d6efd;
        background: rgba(13, 110, 253, 0.1);
        padding: 12px;
        border-radius: 12px;
    }
    
    .feature-item h6 {
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 5px;
    }
    
    .feature-item small {
        color: #6c757d;
        font-size: 13px;
    }
    
    /* ========== FEATURES SECTION ========== */
    .features-section {
        padding: 80px 0;
        background: #f8f9fa;
    }
    
    .features-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 25px;
        margin-top: 50px;
    }
    
    .feature-box {
        text-align: center;
        padding: 40px 25px;
        background: white;
        border-radius: 20px;
        transition: all 0.3s ease;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        border-bottom: 4px solid transparent;
    }
    
    .feature-box:hover {
        background: #0d6efd;
        color: white;
        transform: translateY(-10px);
        border-bottom-color: #ffc107;
        box-shadow: 0 20px 40px rgba(13, 110, 253, 0.2);
    }
    
    .feature-box:hover i {
        color: white;
        transform: scale(1.2);
    }
    
    .feature-box i {
        font-size: 50px;
        color: #0d6efd;
        margin-bottom: 25px;
        transition: all 0.3s ease;
    }
    
    .feature-box h4 {
        font-size: 22px;
        font-weight: 600;
        margin-bottom: 15px;
    }
    
    .feature-box p {
        font-size: 15px;
        margin: 0;
        opacity: 0.9;
        line-height: 1.6;
    }
    
       /* ========== HOSPITAL SECTION WITH IMAGE ========== */
    .hospital-section {
        padding: 80px 0;
        background: #f8f9fa;
    }
    
    .hospital-wrapper {
        display: flex;
        justify-content: center;
        margin-top: 40px;
    }
    
    .hospital-card {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        max-width: 500px;
        width: 100%;
        transition: all 0.3s ease;
        border: 1px solid #eee;
    }
    
    .hospital-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 25px 50px rgba(13, 110, 253, 0.2);
    }
    
    /* Hospital Image */
    .hospital-image {
        width: 100%;
        height: 220px;
        overflow: hidden;
    }
    
    .hospital-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }
    
    .hospital-card:hover .hospital-image img {
        transform: scale(1.1);
    }
    
    /* Hospital Header */
    .hospital-header {
        background: linear-gradient(135deg, #0d6efd, #0b5ed7);
        color: white;
        padding: 20px;
        text-align: center;
    }
    
    .hospital-header h3 {
        font-size: 24px;
        font-weight: 700;
        margin: 0;
    }
    
    /* Hospital Body */
    .hospital-body {
        padding: 25px;
    }
    
    .hospital-info-item {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 1px dashed #eee;
    }
    
    .hospital-info-item:last-child {
        border-bottom: none;
    }
    
    .hospital-info-item i {
        width: 35px;
        height: 35px;
        background: rgba(13, 110, 253, 0.1);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #0d6efd;
        font-size: 16px;
    }
    
    .hospital-info-item span {
        color: #555;
        font-size: 15px;
        flex: 1;
    }
    
    /* Hospital Footer */
    .hospital-footer {
        padding: 20px 25px 25px;
        text-align: center;
    }
    
    .hospital-btn {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: #0d6efd;
        color: white;
        padding: 12px 30px;
        border-radius: 50px;
        text-decoration: none;
        font-size: 16px;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 5px 15px rgba(13, 110, 253, 0.3);
    }
    
    .hospital-btn:hover {
        background: #0b5ed7;
        transform: scale(1.05);
    }
    
    /* ========== VACCINES SECTION ========== */
    .vaccines-section {
        padding: 80px 0;
        background: #f8f9fa;
    }
    
    .vaccines-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 25px;
        margin-top: 40px;
    }
    
    .vaccine-card {
        background: white;
        border-radius: 20px;
        padding: 30px 20px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
        text-align: center;
        border: 1px solid #eee;
        position: relative;
        overflow: hidden;
    }
    
    .vaccine-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 5px;
        background: linear-gradient(90deg, #0d6efd, #0b5ed7);
    }
    
    .vaccine-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(13, 110, 253, 0.15);
    }
    
    .vaccine-icon {
        width: 90px;
        height: 90px;
        background: rgba(13, 110, 253, 0.1);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        color: #0d6efd;
        font-size: 40px;
        transition: all 0.3s ease;
    }
    
    .vaccine-card:hover .vaccine-icon {
        background: #0d6efd;
        color: white;
        transform: rotate(360deg);
    }
    
    .vaccine-name {
        font-size: 22px;
        font-weight: 700;
        color: #333;
        margin-bottom: 5px;
    }
    
    .vaccine-manufacturer {
        color: #6c757d;
        font-size: 14px;
        margin-bottom: 20px;
    }
    
    .vaccine-details {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 15px;
        margin-top: 15px;
    }
    
    .vaccine-detail {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        font-size: 14px;
    }
    
    .vaccine-detail:not(:last-child) {
        border-bottom: 1px dashed #dee2e6;
    }
    
    .vaccine-detail .label {
        color: #6c757d;
    }
    
    .vaccine-detail .value {
        font-weight: 700;
        color: #0d6efd;
    }
    
    .vaccine-status {
        display: inline-block;
        margin-top: 15px;
        padding: 5px 15px;
        background: #28a745;
        color: white;
        border-radius: 50px;
        font-size: 12px;
        font-weight: 600;
    }
    
    /* ========== APPOINTMENT SECTION ========== */
    .appointment-section {
        padding: 80px 0;
        background: linear-gradient(135deg, #0d6efd, #0b5ed7);
    }
    
    .appointment-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 50px;
        align-items: center;
    }
    
    .appointment-content {
        color: white;
    }
    
    .appointment-content .section-subtitle {
        background: rgba(255, 255, 255, 0.2);
        color: white;
        box-shadow: none;
    }
    
    .appointment-content h2 {
        font-size: 42px;
        font-weight: 700;
        margin: 20px 0;
        line-height: 1.2;
    }
    
    .appointment-content p {
        font-size: 18px;
        margin-bottom: 30px;
        opacity: 0.9;
        line-height: 1.6;
    }
    
    .appointment-buttons {
        display: flex;
        gap: 20px;
        flex-wrap: wrap;
    }
    
    .appointment-btn {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 15px 35px;
        border-radius: 60px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
        font-size: 16px;
    }
    
    .appointment-btn-light {
        background: white;
        color: #0d6efd;
    }
    
    .appointment-btn-light:hover {
        background: #f8f9fa;
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }
    
    .appointment-btn-outline {
        background: transparent;
        color: white;
        border: 2px solid white;
    }
    
    .appointment-btn-outline:hover {
        background: white;
        color: #0d6efd;
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }
    
    .appointment-form {
        background: white;
        border-radius: 30px;
        padding: 40px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
    }
    
    .appointment-form h3 {
        text-align: center;
        font-size: 28px;
        font-weight: 700;
        color: #333;
        margin-bottom: 30px;
    }
    
    .form-group {
        margin-bottom: 20px;
    }
    
    .form-control {
        width: 100%;
        padding: 15px 20px;
        border: 2px solid #e9ecef;
        border-radius: 12px;
        font-size: 15px;
        transition: all 0.3s ease;
    }
    
    .form-control:focus {
        outline: none;
        border-color: #0d6efd;
        box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.1);
    }
    
    .form-submit {
        width: 100%;
        padding: 15px;
        background: #0d6efd;
        color: white;
        border: none;
        border-radius: 12px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .form-submit:hover {
        background: #0b5ed7;
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(13, 110, 253, 0.3);
    }
    
    /* ========== TESTIMONIALS SECTION ========== */
    .testimonials-section {
        padding: 80px 0;
    }
    
    .testimonials-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 30px;
        margin-top: 40px;
    }
    
    .testimonial-card {
        background: white;
        border-radius: 20px;
        padding: 35px 25px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        text-align: center;
        position: relative;
        transition: all 0.3s ease;
        border: 1px solid #eee;
    }
    
    .testimonial-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(13, 110, 253, 0.15);
    }
    
    .testimonial-card::before {
        content: '\f10d';
        font-family: 'Font Awesome 5 Free';
        font-weight: 900;
        position: absolute;
        top: 20px;
        left: 25px;
        font-size: 40px;
        color: #0d6efd;
        opacity: 0.2;
    }
    
    .testimonial-img {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        margin: 0 auto 20px;
        border: 4px solid #0d6efd;
        object-fit: cover;
        box-shadow: 0 10px 20px rgba(13, 110, 253, 0.2);
    }
    
    .testimonial-name {
        font-size: 20px;
        font-weight: 600;
        color: #333;
        margin-bottom: 5px;
    }
    
    .testimonial-city {
        color: #6c757d;
        font-size: 14px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
    }
    
    .testimonial-city i {
        color: #0d6efd;
        font-size: 12px;
    }
    
    .testimonial-text {
        font-size: 15px;
        line-height: 1.8;
        color: #555;
        font-style: italic;
        position: relative;
    }
    
    /* ========== CTA SECTION ========== */
    .cta-section {
        padding: 80px 0;
        background: linear-gradient(135deg, #0d6efd, #0b5ed7);
        color: white;
        text-align: center;
    }
    
    .cta-content {
        max-width: 700px;
        margin: 0 auto;
    }
    
    .cta-title {
        font-size: 48px;
        font-weight: 800;
        margin-bottom: 20px;
    }
    
    .cta-text {
        font-size: 20px;
        margin-bottom: 40px;
        opacity: 0.9;
    }
    
    .cta-buttons {
        display: flex;
        justify-content: center;
        gap: 20px;
        flex-wrap: wrap;
    }
    
    .cta-btn {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 16px 40px;
        border-radius: 60px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
        font-size: 16px;
    }
    
    .cta-btn-light {
        background: white;
        color: #0d6efd;
    }
    
    .cta-btn-light:hover {
        background: #f8f9fa;
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
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
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }
    
    /* ========== RESPONSIVE ========== */
    @media (max-width: 992px) {
        .hero-title {
            font-size: 48px;
        }
        
        .stats-grid,
        .features-grid,
        .vaccines-grid,
        .testimonials-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        
        .about-grid,
        .appointment-grid {
            grid-template-columns: 1fr;
            gap: 40px;
        }
        
        .about-content {
            padding-right: 0;
        }
        
        .section-title {
            font-size: 36px;
        }
    }
    
    @media (max-width: 768px) {
        .hero-title {
            font-size: 36px;
        }
        
        .stats-grid,
        .features-grid,
        .vaccines-grid,
        .testimonials-grid {
            grid-template-columns: 1fr;
        }
        
        .features-list {
            grid-template-columns: 1fr;
        }
        
        .hero-buttons,
        .appointment-buttons,
        .cta-buttons {
            flex-direction: column;
            align-items: stretch;
        }
        
        .hero-btn,
        .appointment-btn,
        .cta-btn {
            text-align: center;
            justify-content: center;
        }
        
        .section-title {
            font-size: 30px;
        }
        
        .cta-title {
            font-size: 36px;
        }
    }
</style>

<!-- HERO SECTION -->
<section class="hero-section">
    <!-- Animated Circles -->
    <div class="hero-circle circle-1"></div>
    <div class="hero-circle circle-2"></div>
    <div class="hero-circle circle-3"></div>
    
    <!-- Floating Particles -->
    <div class="hero-particle particle-1"></div>
    <div class="hero-particle particle-2"></div>
    <div class="hero-particle particle-3"></div>
    <div class="hero-particle particle-4"></div>
    
    <div class="container">
        <div class="hero-content">
            <div class="hero-badge">
                <i class="fa fa-shield-virus"></i>
                <span>Trusted Healthcare Platform in Pakistan</span>
            </div>
            <h1 class="hero-title">
                Welcome to <span><?php echo SITE_NAME; ?></span>
            </h1>
            <p class="hero-subtitle">
                Pakistan's most trusted platform for COVID-19 testing and vaccination. 
                Book your appointment online with registered hospitals and get your results digitally.
            </p>
            <div class="hero-buttons">
                <a href="<?php echo SITE_URL; ?>patient/book_appointment.php" class="hero-btn hero-btn-primary">
                    <i class="fa fa-calendar-check"></i>
                    Book Appointment
                </a>
                <a href="<?php echo SITE_URL; ?>patient/register.php" class="hero-btn hero-btn-outline">
                    <i class="fa fa-user-plus"></i>
                    Register Now
                </a>
            </div>
        </div>
    </div>
</section>

<!-- STATS SECTION -->
<section class="stats-section">
    <div class="container">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fa fa-hospital"></i>
                </div>
                <div class="stat-number"><?php echo $total_hospitals; ?></div>
                <div class="stat-label">Registered Hospitals</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fa fa-users"></i>
                </div>
                <div class="stat-number"><?php echo $total_patients; ?></div>
                <div class="stat-label">Active Patients</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fa fa-syringe"></i>
                </div>
                <div class="stat-number"><?php echo $total_vaccines; ?></div>
                <div class="stat-label">Available Vaccines</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fa fa-calendar-check"></i>
                </div>
                <div class="stat-number"><?php echo $total_appointments; ?></div>
                <div class="stat-label">Total Appointments</div>
            </div>
        </div>
    </div>
</section>

<!-- ABOUT SECTION -->
<section class="about-section">
    <div class="container">
        <div class="about-grid">
            <div class="about-image">
                <img src="<?php echo SITE_URL; ?>assets/img/about.png" alt="About Us">
            </div>
            <div class="about-content">
                <span class="section-subtitle">About Us</span>
                <h2 class="section-title">Your Trusted Healthcare Partner</h2>
                <p class="section-text">
                    Across the world, people are facing unforeseen challenges due to Coronavirus pandemic. <?php echo SITE_NAME; ?> is a web application for online test and vaccination booking system for patients. The Covid-19 web app connects the people with the hospital and the administration to come together and fight the pandemic.
                </p>
                <p class="section-text">
                    With this web app, you can track vaccination appointments, history and COVID solution guidelines for symptoms. Our platform facilitates online appointments for vaccination and testing with registered hospitals across Karachi.
                </p>
                <div class="features-list">
                    <div class="feature-item">
                        <i class="fa fa-check-circle"></i>
                        <div>
                            <h6>Qualified Hospitals</h6>
                            <small>Verified & Registered</small>
                        </div>
                    </div>
                    <div class="feature-item">
                        <i class="fa fa-check-circle"></i>
                        <div>
                            <h6>Safe Vaccination</h6>
                            <small>Certified Vaccines</small>
                        </div>
                    </div>
                    <div class="feature-item">
                        <i class="fa fa-check-circle"></i>
                        <div>
                            <h6>Accurate Testing</h6>
                            <small>99.9% Accuracy</small>
                        </div>
                    </div>
                    <div class="feature-item">
                        <i class="fa fa-check-circle"></i>
                        <div>
                            <h6>24/7 Support</h6>
                            <small>Always Available</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FEATURES SECTION -->
<section class="features-section">
    <div class="container">
        <div style="text-align: center; margin-bottom: 40px;">
            <span class="section-subtitle">Why Choose Us</span>
            <h2 class="section-title">Key Features</h2>
            <p class="section-text" style="max-width: 600px; margin: 0 auto;">We provide the best healthcare services with modern technology</p>
        </div>
        <div class="features-grid">
            <div class="feature-box">
                <i class="fa fa-calendar-plus"></i>
                <h4>Easy Booking</h4>
                <p>Book appointments online in just few clicks</p>
            </div>
            <div class="feature-box">
                <i class="fa fa-flask"></i>
                <h4>Fast Testing</h4>
                <p>Quick COVID test results from labs</p>
            </div>
            <div class="feature-box">
                <i class="fa fa-shield-alt"></i>
                <h4>Safe Vaccination</h4>
                <p>Certified vaccines from trusted sources</p>
            </div>
            <div class="feature-box">
                <i class="fa fa-file-medical"></i>
                <h4>Digital Records</h4>
                <p>Access your health records anytime</p>
            </div>
        </div>
    </div>
</section>

<!-- HOSPITAL SECTION -->
<?php if (!empty($hospitals)): ?>
<section class="hospital-section">
    <div class="container">
        <div style="text-align: center; margin-bottom: 30px;">
            <span style="display: inline-block; background: #0d6efd; color: white; padding: 8px 25px; border-radius: 50px; font-size: 14px; font-weight: 600; margin-bottom: 15px;">Our Partner Hospital</span>
            <h2 style="font-size: 36px; font-weight: 700; color: #333; margin-bottom: 10px;">Ziauddin Hospital</h2>
            <p style="font-size: 16px; color: #6c757d;">Leading healthcare provider in Karachi</p>
        </div>
        <div class="hospital-wrapper">
            <?php foreach ($hospitals as $hospital): ?>
            <div class="hospital-card">
                <!-- Hospital Image -->
                <div class="hospital-image">
                    <img src="<?php echo SITE_URL; ?>assets/img/hospital-ziauddin.jpg" 
                         alt="<?php echo $hospital['name']; ?>"
                         onerror="this.src='<?php echo SITE_URL; ?>assets/img/about.png'">
                </div>
                <div class="hospital-header">
                    <h3><?php echo $hospital['name']; ?></h3>
                </div>
                <div class="hospital-body">
                    <div class="hospital-info-item">
                        <i class="fa fa-map-marker-alt"></i>
                        <span><?php echo $hospital['address']; ?>, <?php echo $hospital['city']; ?></span>
                    </div>
                    <div class="hospital-info-item">
                        <i class="fa fa-phone"></i>
                        <span><?php echo formatPhone($hospital['phone']); ?></span>
                    </div>
                    <div class="hospital-info-item">
                        <i class="fa fa-envelope"></i>
                        <span><?php echo $hospital['email']; ?></span>
                    </div>
                </div>
                <div class="hospital-footer">
                    <a href="<?php echo SITE_URL; ?>patient/book_appointment.php?hospital=<?php echo $hospital['hospital_id']; ?>" class="hospital-btn">
                        <i class="fa fa-calendar-check"></i> Book Appointment
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>
<!-- VACCINES SECTION -->
<section class="vaccines-section">
    <div class="container">
        <div style="text-align: center; margin-bottom: 40px;">
            <span class="section-subtitle">Available Vaccines</span>
            <h2 class="section-title">COVID-19 Vaccines</h2>
            <p class="section-text">We offer multiple vaccine options for your safety</p>
        </div>
        <div class="vaccines-grid">
            <?php if (!empty($vaccines)): ?>
                <?php foreach ($vaccines as $vaccine): ?>
                <div class="vaccine-card">
                    <div class="vaccine-icon">
                        <i class="fa fa-syringe"></i>
                    </div>
                    <h4 class="vaccine-name"><?php echo $vaccine['name']; ?></h4>
                    <div class="vaccine-manufacturer"><?php echo $vaccine['manufacturer']; ?></div>
                    <div class="vaccine-details">
                        <div class="vaccine-detail">
                            <span class="label">Doses Required:</span>
                            <span class="value"><?php echo $vaccine['doses_required']; ?></span>
                        </div>
                        <?php if ($vaccine['gap_between_doses']): ?>
                        <div class="vaccine-detail">
                            <span class="label">Gap Between Doses:</span>
                            <span class="value"><?php echo $vaccine['gap_between_doses']; ?> days</span>
                        </div>
                        <?php endif; ?>
                    </div>
                    <span class="vaccine-status">Available Now</span>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="grid-column: 1/-1; text-align: center; padding: 50px;">
                    <p class="section-text">No vaccines available at the moment.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- APPOINTMENT SECTION -->
<section class="appointment-section">
    <div class="container">
        <div class="appointment-grid">
            <div class="appointment-content">
                <span class="section-subtitle">Book Appointment</span>
                <h2>Ready to Get Tested or Vaccinated?</h2>
                <p>Book your appointment online with our registered hospitals. Choose your preferred hospital, date and time for COVID-19 test or vaccination. Get your results online and track your vaccination history.</p>
                <div class="appointment-buttons">
                    <a href="<?php echo SITE_URL; ?>patient/book_appointment.php" class="appointment-btn appointment-btn-light">
                        <i class="fa fa-calendar-check"></i>
                        Book Now
                    </a>
                    <a href="<?php echo SITE_URL; ?>patient/register.php" class="appointment-btn appointment-btn-outline">
                        <i class="fa fa-user-plus"></i>
                        Register First
                    </a>
                </div>
            </div>
            <div class="appointment-form">
                <h3>Quick Appointment</h3>
                <form action="<?php echo SITE_URL; ?>patient/book_appointment.php" method="GET">
                    <div class="form-group">
                        <select class="form-control" name="type" required>
                            <option value="">Select Appointment Type</option>
                            <option value="test">COVID-19 Test</option>
                            <option value="vaccination">Vaccination</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <select class="form-control" name="hospital" required>
                            <option value="">Select Hospital</option>
                            <?php 
                            $all_hospitals = getApprovedHospitals($conn);
                            foreach ($all_hospitals as $h):
                            ?>
                            <option value="<?php echo $h['hospital_id']; ?>"><?php echo $h['name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="form-submit">
                        <i class="fa fa-search me-2"></i>
                        Check Availability
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- TESTIMONIALS SECTION -->
<section class="testimonials-section">
    <div class="container">
        <div style="text-align: center; margin-bottom: 40px;">
            <span class="section-subtitle">Testimonials</span>
            <h2 class="section-title">What Our Happy Patients Say</h2>
            <p class="section-text">Read what our patients have to say about our services</p>
        </div>
        <div class="testimonials-grid">
            <?php if (!empty($testimonials)): ?>
                <?php foreach ($testimonials as $index => $testimonial): ?>
                <div class="testimonial-card">
                    <img src="<?php echo SITE_URL; ?>assets/img/testimonial-<?php echo ($index % 3) + 1; ?>.jpg" alt="<?php echo $testimonial['name']; ?>" class="testimonial-img">
                    <h4 class="testimonial-name"><?php echo $testimonial['name']; ?></h4>
                    <div class="testimonial-city">
                        <i class="fa fa-map-marker-alt"></i>
                        <?php echo $testimonial['city']; ?>
                    </div>
                    <p class="testimonial-text">"Great experience with <?php echo SITE_NAME; ?>. Easy booking, professional staff, and quick results. Highly recommended!"</p>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <?php for ($i = 1; $i <= 3; $i++): ?>
                <div class="testimonial-card">
                    <img src="<?php echo SITE_URL; ?>assets/img/testimonial-<?php echo $i; ?>.jpg" alt="Patient" class="testimonial-img">
                    <h4 class="testimonial-name">Happy Patient</h4>
                    <div class="testimonial-city">
                        <i class="fa fa-map-marker-alt"></i>
                        Karachi
                    </div>
                    <p class="testimonial-text">"Great experience with <?php echo SITE_NAME; ?>. Easy booking, professional staff, and quick results. Highly recommended!"</p>
                </div>
                <?php endfor; ?>
            <?php endif; ?>
        </div>
    </div>
</section>



<?php
require_once 'includes/footer.php';
?>