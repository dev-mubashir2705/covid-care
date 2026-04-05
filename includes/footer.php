<?php
$current_year = date('Y');
?>

<style>
    /* SUPER SIMPLE FOOTER - NO ISSUES */
    .simple-footer {
        background: #1a2b3c;
        color: white;
        width: 100%;
        font-family: Arial, sans-serif;
        margin-top: 50px;
    }
    
    .footer-top {
        padding: 50px 0 30px;
    }
    
    .footer-widget {
        margin-bottom: 30px;
    }
    
    .footer-widget h3 {
        color: white;
        font-size: 22px;
        margin-bottom: 20px;
    }
    
    .footer-widget h3 i {
        color: #0d6efd;
        margin-right: 10px;
    }
    
    .footer-widget h4 {
        color: white;
        font-size: 18px;
        margin-bottom: 20px;
        border-bottom: 2px solid #0d6efd;
        padding-bottom: 10px;
        display: inline-block;
    }
    
    .footer-widget p {
        color: #b0c4de;
        line-height: 1.6;
        font-size: 14px;
        margin-bottom: 20px;
    }
    
    .footer-links {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .footer-links li {
        margin-bottom: 10px;
    }
    
    .footer-links a {
        color: #b0c4de;
        text-decoration: none;
        font-size: 14px;
        display: inline-block;
    }
    
    .footer-links a i {
        margin-right: 8px;
        color: #0d6efd;
    }
    
    .footer-links a:hover {
        color: white;
        padding-left: 5px;
    }
    
    .contact-info {
        margin-bottom: 15px;
    }
    
    .contact-info div {
        display: flex;
        align-items: center;
        margin-bottom: 12px;
        color: #b0c4de;
        font-size: 14px;
    }
    
    .contact-info i {
        width: 25px;
        color: #0d6efd;
    }
    
    .contact-info a {
        color: #b0c4de;
        text-decoration: none;
    }
    
    .contact-info a:hover {
        color: #0d6efd;
    }
    
    .social-icons {
        display: flex;
        gap: 8px;
        margin-top: 15px;
    }
    
    .social-icons a {
        width: 35px;
        height: 35px;
        background: rgba(255,255,255,0.1);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        text-decoration: none;
    }
    
    .social-icons a:hover {
        background: #0d6efd;
    }
    
    .hours-box {
        background: rgba(255,255,255,0.05);
        padding: 15px;
        border-radius: 5px;
        margin-top: 15px;
    }
    
    .hours-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
        font-size: 13px;
        color: #b0c4de;
    }
    
    .hours-row span:last-child {
        color: #0d6efd;
        font-weight: bold;
    }
    
    .newsletter-box {
        background: rgba(255,255,255,0.05);
        padding: 20px;
        border-radius: 5px;
        margin-top: 20px;
    }
    
    .newsletter-box h5 {
        color: white;
        margin-bottom: 15px;
    }
    
    .newsletter-box h5 i {
        color: #0d6efd;
        margin-right: 8px;
    }
    
    .newsletter-form {
        display: flex;
        gap: 10px;
    }
    
    .newsletter-form input {
        flex: 1;
        padding: 10px;
        border: none;
        border-radius: 5px;
        background: rgba(255,255,255,0.1);
        color: white;
    }
    
    .newsletter-form button {
        padding: 10px 20px;
        background: #0d6efd;
        border: none;
        border-radius: 5px;
        color: white;
        cursor: pointer;
    }
    
    /* SIMPLE BOTTOM BAR - NO LINE, NO EXTRA SPACE */
    .bottom-bar {
        background: #152635;
        padding: 15px 0;
        width: 100%;
    }
    
    .bottom-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
    }
    
    .copyright-text {
        color: #b0c4de;
        font-size: 13px;
        margin: 0;
    }
    
    .copyright-text a {
        color: #0d6efd;
        text-decoration: none;
    }
    
    .bottom-links {
        display: flex;
        gap: 15px;
    }
    
    .bottom-links a {
        color: #b0c4de;
        text-decoration: none;
        font-size: 13px;
    }
    
    .bottom-links a:hover {
        color: #0d6efd;
    }
    
    .back-top {
        position: fixed;
        bottom: 20px;
        right: 20px;
        width: 40px;
        height: 40px;
        background: #0d6efd;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        opacity: 0;
        visibility: hidden;
        transition: 0.3s;
        z-index: 999;
    }
    
    .back-top.show {
        opacity: 1;
        visibility: visible;
    }
    
    @media (max-width: 768px) {
        .bottom-content {
            flex-direction: column;
            text-align: center;
            gap: 10px;
        }
    }
</style>

<!-- FOOTER - SIMPLE & CLEAN -->
<footer class="simple-footer">
    <!-- Main Footer -->
    <div class="footer-top">
        <div class="container">
            <div class="row">
                <!-- About Column -->
                <div class="col-lg-4 col-md-6">
                    <div class="footer-widget">
                        <h3><i class="fa fa-shield-virus"></i> <?php echo SITE_NAME; ?></h3>
                        <p>Your trusted partner in COVID-19 testing and vaccination in Karachi, Pakistan. We connect patients with registered hospitals for seamless healthcare services.</p>
                        
                        <!-- Social Icons -->
                        <div class="social-icons">
                            <a href="https://facebook.com" target="_blank"><i class="fab fa-facebook-f"></i></a>
                            <a href="https://twitter.com" target="_blank"><i class="fab fa-twitter"></i></a>
                            <a href="https://linkedin.com" target="_blank"><i class="fab fa-linkedin-in"></i></a>
                            <a href="https://instagram.com" target="_blank"><i class="fab fa-instagram"></i></a>
                            <a href="https://wa.me/923001234567" target="_blank"><i class="fab fa-whatsapp"></i></a>
                            <a href="https://youtube.com" target="_blank"><i class="fab fa-youtube"></i></a>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Links -->
                <div class="col-lg-2 col-md-6">
                    <div class="footer-widget">
                        <h4>Quick Links</h4>
                        <ul class="footer-links">
                            <li><a href="<?php echo SITE_URL; ?>"><i class="fa fa-angle-right"></i> Home</a></li>
                            <li><a href="<?php echo SITE_URL; ?>about.php"><i class="fa fa-angle-right"></i> About</a></li>
                            <li><a href="<?php echo SITE_URL; ?>contact.php"><i class="fa fa-angle-right"></i> Contact</a></li>
                            <li><a href="<?php echo SITE_URL; ?>patient/book_appointment.php"><i class="fa fa-angle-right"></i> Book Appointment</a></li>
                            <li><a href="<?php echo SITE_URL; ?>patient/view_vaccines.php"><i class="fa fa-angle-right"></i> Vaccines</a></li>
                        </ul>
                    </div>
                </div>
                
                <!-- Patients -->
                <div class="col-lg-2 col-md-6">
                    <div class="footer-widget">
                        <h4>Patients</h4>
                        <ul class="footer-links">
                            <li><a href="<?php echo SITE_URL; ?>patient/login.php"><i class="fa fa-angle-right"></i> Login</a></li>
                            <li><a href="<?php echo SITE_URL; ?>patient/register.php"><i class="fa fa-angle-right"></i> Register</a></li>
                            <li><a href="<?php echo SITE_URL; ?>patient/my_appointments.php"><i class="fa fa-angle-right"></i> Appointments</a></li>
                            <li><a href="<?php echo SITE_URL; ?>patient/view_results.php"><i class="fa fa-angle-right"></i> Test Results</a></li>
                            <li><a href="<?php echo SITE_URL; ?>patient/profile.php"><i class="fa fa-angle-right"></i> Profile</a></li>
                        </ul>
                    </div>
                </div>
                
                <!-- Hospitals -->
                <div class="col-lg-2 col-md-6">
                    <div class="footer-widget">
                        <h4>Hospitals</h4>
                        <ul class="footer-links">
                            <li><a href="<?php echo SITE_URL; ?>hospital/login.php"><i class="fa fa-angle-right"></i> Login</a></li>
                            <li><a href="<?php echo SITE_URL; ?>hospital/register.php"><i class="fa fa-angle-right"></i> Register</a></li>
                            <li><a href="<?php echo SITE_URL; ?>hospital/appointments.php"><i class="fa fa-angle-right"></i> Appointments</a></li>
                            <li><a href="<?php echo SITE_URL; ?>hospital/patients.php"><i class="fa fa-angle-right"></i> Patients</a></li>
                        </ul>
                    </div>
                </div>
                
                <!-- Contact -->
                <div class="col-lg-2 col-md-6">
                    <div class="footer-widget">
                        <h4>Contact</h4>
                        <div class="contact-info">
                            <div><i class="fa fa-map-marker-alt"></i> <a href="https://maps.google.com/?q=Shahrah-e-Faisal,Karachi,Pakistan" target="_blank">Shahrah-e-Faisal, Karachi</a></div>
                            <div><i class="fa fa-phone-alt"></i> <a href="tel:+922134567890">+92 21 3456 7890</a></div>
                            <div><i class="fa fa-mobile-alt"></i> <a href="tel:+923001234567">+92 300 1234567</a></div>
                            <div><i class="fa fa-envelope"></i> <a href="mailto:info@covid-care.pk">info@covid-care.pk</a></div>
                        </div>
                        
                        <!-- Hours -->
                        <div class="hours-box">
                            <div class="hours-row"><span>Mon-Fri:</span> <span>9AM-9PM</span></div>
                            <div class="hours-row"><span>Sat-Sun:</span> <span>10AM-6PM</span></div>
                            <div class="hours-row"><span>Emergency:</span> <span>24/7</span></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Newsletter -->
            <div class="row">
                <div class="col-12">
                    <div class="newsletter-box">
                        <div class="row align-items-center">
                            <div class="col-md-7">
                                <h5><i class="fa fa-envelope-open-text"></i> Subscribe for Updates</h5>
                                <p style="color:#b0c4de; margin:0;">Get latest updates about vaccines and health guidelines.</p>
                            </div>
                            <div class="col-md-5">
                                <div class="newsletter-form">
                                    <input type="email" placeholder="Your email">
                                    <button>Subscribe</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- BOTTOM BAR - ABSOLUTELY NO LINE, NO EXTRA SPACE -->
    <div class="bottom-bar">
        <div class="container">
            <div class="bottom-content">
                <div class="copyright-text">
                    &copy; <?php echo $current_year; ?> <a href="<?php echo SITE_URL; ?>"><?php echo SITE_NAME; ?></a>. All Rights Reserved.
                </div>
                <div class="bottom-links">
                    <a href="<?php echo SITE_URL; ?>privacy.php">Privacy</a>
                    <a href="<?php echo SITE_URL; ?>terms.php">Terms</a>
                    <a href="<?php echo SITE_URL; ?>faqs.php">FAQs</a>
                    <a href="<?php echo SITE_URL; ?>support.php">Support</a>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- Back to Top -->
<a href="#" class="back-top" id="backTop">
    <i class="fa fa-arrow-up"></i>
</a>

<script>
    // Simple back to top
    const backTop = document.getElementById('backTop');
    window.onscroll = function() {
        if (document.body.scrollTop > 300 || document.documentElement.scrollTop > 300) {
            backTop.classList.add('show');
        } else {
            backTop.classList.remove('show');
        }
    };
    
    backTop.onclick = function(e) {
        e.preventDefault();
        window.scrollTo({top: 0, behavior: 'smooth'});
    };
</script>