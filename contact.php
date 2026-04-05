<?php
require_once 'config.php';
require_once 'includes/functions.php';

$page_title = 'Contact Us - COVID Care System';
require_once 'includes/header.php';
require_once 'includes/navbar.php';

// Form submission handling
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $subject = sanitize($_POST['subject']);
    $message = sanitize($_POST['message']);
    
    // Here you can add code to send email or save to database
    if (!empty($name) && !empty($email) && !empty($message)) {
        // For now, just show success message
        $success = "Thank you for contacting us, $name! We'll get back to you soon.";
    } else {
        $error = "Please fill in all required fields.";
    }
}
?>

<style>
    /* ===== CONTACT PAGE STYLES - CLEAN & STYLISH ===== */
    
    /* Section Titles */
    .section-header {
        text-align: center;
        margin-bottom: 60px;
    }
    
    .section-header h5 {
        color: #0d6efd;
        font-size: 18px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 15px;
        display: inline-block;
        border-bottom: 3px solid #0d6efd;
        padding-bottom: 5px;
    }
    
    .section-header h1 {
        font-size: 42px;
        font-weight: 700;
        color: #333;
        margin-bottom: 15px;
    }
    
    .section-header p {
        color: #6c757d;
        font-size: 18px;
        max-width: 700px;
        margin: 0 auto;
    }
    
    /* Contact Info Cards */
    .contact-section {
        padding: 80px 0;
    }
    
    .contact-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 30px;
        margin-top: 50px;
    }
    
    .contact-card {
        background: white;
        border-radius: 20px;
        padding: 40px 30px;
        text-align: center;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
        border: 1px solid #eee;
        position: relative;
        overflow: hidden;
    }
    
    .contact-card::before {
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
    
    .contact-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(13,110,253,0.15);
    }
    
    .contact-card:hover::before {
        transform: scaleX(1);
    }
    
    .contact-icon {
        width: 90px;
        height: 90px;
        background: linear-gradient(135deg, #0d6efd, #0b5ed7);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 25px;
        color: white;
        font-size: 35px;
        box-shadow: 0 10px 20px rgba(13,110,253,0.3);
        transition: all 0.3s ease;
    }
    
    .contact-card:hover .contact-icon {
        transform: rotateY(180deg);
    }
    
    .contact-card h3 {
        font-size: 22px;
        font-weight: 700;
        color: #333;
        margin-bottom: 15px;
    }
    
    .contact-card p {
        color: #6c757d;
        line-height: 1.8;
        margin-bottom: 8px;
        font-size: 15px;
    }
    
    .contact-card a {
        color: #6c757d;
        text-decoration: none;
        transition: all 0.3s ease;
    }
    
    .contact-card a:hover {
        color: #0d6efd;
    }
    
    /* Map Section */
    .map-section {
        padding: 40px 0 80px;
        background: #f8f9fa;
    }
    
    .map-container {
        height: 450px;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        border: 5px solid white;
    }
    
    .map-container iframe {
        width: 100%;
        height: 100%;
        border: none;
    }
    
    /* Contact Form Section */
    .form-section {
        padding: 80px 0;
    }
    
    .form-wrapper {
        max-width: 800px;
        margin: 0 auto;
        background: white;
        border-radius: 20px;
        padding: 50px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        border: 1px solid #eee;
    }
    
    .form-title {
        text-align: center;
        margin-bottom: 40px;
    }
    
    .form-title h2 {
        font-size: 32px;
        font-weight: 700;
        color: #333;
        margin-bottom: 10px;
    }
    
    .form-title p {
        color: #6c757d;
        font-size: 16px;
    }
    
    .form-group {
        margin-bottom: 25px;
    }
    
    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }
    
    .form-control {
        width: 100%;
        padding: 15px 20px;
        border: 2px solid #e9ecef;
        border-radius: 12px;
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
    
    textarea.form-control {
        resize: vertical;
        min-height: 150px;
    }
    
    .btn-submit {
        background: linear-gradient(135deg, #0d6efd, #0b5ed7);
        color: white;
        border: none;
        padding: 16px 30px;
        font-size: 16px;
        font-weight: 600;
        border-radius: 12px;
        width: 100%;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 10px 20px rgba(13,110,253,0.2);
    }
    
    .btn-submit:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 30px rgba(13,110,253,0.3);
    }
    
    .btn-submit i {
        margin-right: 8px;
    }
    
    /* Alert Messages */
    .alert {
        padding: 16px 20px;
        border-radius: 12px;
        margin-bottom: 30px;
        display: flex;
        align-items: center;
        gap: 10px;
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
    
    .alert i {
        font-size: 20px;
    }
    
    /* Working Hours Section */
    .hours-section {
        padding: 60px 0;
        background: linear-gradient(135deg, #0d6efd, #0b5ed7);
    }
    
    .hours-wrapper {
        max-width: 700px;
        margin: 0 auto;
        background: white;
        border-radius: 20px;
        padding: 40px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.2);
    }
    
    .hours-wrapper h2 {
        text-align: center;
        font-size: 32px;
        font-weight: 700;
        color: #333;
        margin-bottom: 30px;
    }
    
    .hours-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 15px 30px;
    }
    
    .hours-item {
        display: flex;
        justify-content: space-between;
        padding: 12px 0;
        border-bottom: 1px dashed #dee2e6;
    }
    
    .hours-day {
        font-weight: 600;
        color: #0d6efd;
    }
    
    .hours-time {
        color: #333;
        font-weight: 500;
    }
    
    .hours-note {
        margin-top: 30px;
        padding: 15px;
        background: #e7f1ff;
        border-radius: 12px;
        color: #0d6efd;
        font-weight: 500;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }
    
    .hours-note i {
        font-size: 20px;
    }
    
    /* Responsive */
    @media (max-width: 992px) {
        .contact-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        
        .section-header h1 {
            font-size: 36px;
        }
    }
    
    @media (max-width: 768px) {
        .contact-grid {
            grid-template-columns: 1fr;
        }
        
        .form-row {
            grid-template-columns: 1fr;
            gap: 15px;
        }
        
        .form-wrapper {
            padding: 30px 20px;
        }
        
        .hours-grid {
            grid-template-columns: 1fr;
        }
        
        .section-header h1 {
            font-size: 30px;
        }
        
        .hours-wrapper {
            padding: 30px 20px;
        }
        
        .hours-wrapper h2 {
            font-size: 26px;
        }
    }
</style>

<!-- Contact Info Section -->
<section class="contact-section">
    <div class="container">
        <div class="section-header">
            <h5 class="d-inline-block text-primary text-uppercase border-bottom border-5">Get In Touch</h5>
            <h1>Contact Information</h1>
            <p>We're here to help you with any questions or concerns</p>
        </div>
        
        <div class="contact-grid">
            <!-- Address Card -->
            <div class="contact-card">
                <div class="contact-icon">
                    <i class="fa fa-map-marker-alt"></i>
                </div>
                <h3>Our Location</h3>
                <p>
                    <a href="https://maps.google.com/?q=Shahrah-e-Faisal,Karachi,Pakistan" target="_blank">
                        Shahrah-e-Faisal,<br>Karachi, Pakistan
                    </a>
                </p>
            </div>
            
            <!-- Phone Card -->
            <div class="contact-card">
                <div class="contact-icon">
                    <i class="fa fa-phone-alt"></i>
                </div>
                <h3>Phone Numbers</h3>
                <p>
                    <a href="tel:+922134567890">+92 21 3456 7890</a><br>
                    <a href="tel:+923001234567">+92 300 1234567</a>
                </p>
                <p style="color: #0d6efd; font-size: 13px; margin-top: 5px;">24/7 Emergency Support</p>
            </div>
            
            <!-- Email Card -->
            <div class="contact-card">
                <div class="contact-icon">
                    <i class="fa fa-envelope"></i>
                </div>
                <h3>Email Address</h3>
                <p>
                    <a href="mailto:info@covid-care.pk">info@covid-care.pk</a><br>
                    <a href="mailto:support@covid-care.pk">support@covid-care.pk</a>
                </p>
                <p style="color: #0d6efd; font-size: 13px; margin-top: 5px;">24/7 Email Support</p>
            </div>
        </div>
    </div>
</section>

<!-- Map Section -->
<section class="map-section">
    <div class="container">
        <div class="map-container">
            <iframe 
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d115819.87988950023!2d66.97683145!3d24.8614622!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3eb33f0f5e5c3c8f%3A0x8d1b9f8f8f8f8f8f!2sShahrah-e-Faisal%2C%20Karachi%2C%20Pakistan!5e0!3m2!1sen!2s!4v1620000000000!5m2!1sen!2s" 
                allowfullscreen="" 
                loading="lazy">
            </iframe>
        </div>
    </div>
</section>

<!-- Contact Form Section -->
<section class="form-section">
    <div class="container">
        <div class="form-wrapper">
            <div class="form-title">
                <h2>Send Us a Message</h2>
                <p>We'll get back to you as soon as possible</p>
            </div>
            
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fa fa-check-circle"></i>
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i class="fa fa-exclamation-circle"></i>
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-row">
                    <div class="form-group">
                        <input type="text" class="form-control" name="name" placeholder="Your Full Name" required>
                    </div>
                    <div class="form-group">
                        <input type="email" class="form-control" name="email" placeholder="Your Email Address" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <input type="text" class="form-control" name="subject" placeholder="Subject (Optional)">
                </div>
                
                <div class="form-group">
                    <textarea class="form-control" name="message" placeholder="Your Message" required></textarea>
                </div>
                
                <button type="submit" class="btn-submit">
                    <i class="fa fa-paper-plane"></i>
                    Send Message
                </button>
            </form>
        </div>
    </div>
</section>

<!-- Working Hours Section -->
<section class="hours-section">
    <div class="container">
        <div class="hours-wrapper">
            <h2>Working Hours</h2>
            <div class="hours-grid">
                <div class="hours-item">
                    <span class="hours-day">Monday - Friday</span>
                    <span class="hours-time">9:00 AM - 9:00 PM</span>
                </div>
                <div class="hours-item">
                    <span class="hours-day">Saturday</span>
                    <span class="hours-time">10:00 AM - 6:00 PM</span>
                </div>
                <div class="hours-item">
                    <span class="hours-day">Sunday</span>
                    <span class="hours-time">11:00 AM - 4:00 PM</span>
                </div>
                <div class="hours-item">
                    <span class="hours-day">Emergency</span>
                    <span class="hours-time">24/7 Available</span>
                </div>
                <div class="hours-item">
                    <span class="hours-day">Jumuah Break</span>
                    <span class="hours-time">1:00 PM - 2:30 PM</span>
                </div>
            </div>
            <div class="hours-note">
                <i class="fa fa-clock"></i>
                We're available 24/7 for emergency support
            </div>
        </div>
    </div>
</section>

<?php
require_once 'includes/footer.php';
?>