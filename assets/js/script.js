// ========================================
// COVID-19 VACCINATION SYSTEM
// PROFESSIONAL ANIMATIONS
// ========================================

// Wait for DOM to load
document.addEventListener('DOMContentLoaded', function() {
    
    // ========== INITIALIZE ANIMATIONS ==========
    initAOS();
    initHoverEffects();
    initCounters();
    initParticles();
    initTypingEffect();
    initParallax();
    initProgressBars();
    initTooltips();
    initSmoothScroll();
    
});

// ========== ANIMATION ON SCROLL ==========
function initAOS() {
    const elements = document.querySelectorAll('.card, .stat-card, .quick-action-card, .fade-in');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if(entry.isIntersecting) {
                entry.target.classList.add('animated', 'fadeInUp');
                observer.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    });
    
    elements.forEach(el => observer.observe(el));
}

// ========== ADVANCED HOVER EFFECTS ==========
function initHoverEffects() {
    // Magnetic effect on buttons
    document.querySelectorAll('.btn').forEach(btn => {
        btn.addEventListener('mousemove', function(e) {
            const rect = this.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            
            const centerX = rect.width / 2;
            const centerY = rect.height / 2;
            
            const deltaX = (x - centerX) / 10;
            const deltaY = (y - centerY) / 10;
            
            this.style.transform = `translate(${deltaX}px, ${deltaY}px) scale(1.05)`;
        });
        
        btn.addEventListener('mouseleave', function() {
            this.style.transform = 'translate(0, 0) scale(1)';
        });
    });
    
    // Tilt effect on cards
    document.querySelectorAll('.card').forEach(card => {
        card.addEventListener('mousemove', function(e) {
            const rect = this.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            
            const centerX = rect.width / 2;
            const centerY = rect.height / 2;
            
            const rotateX = (y - centerY) / 20;
            const rotateY = (centerX - x) / 20;
            
            this.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale(1.02)`;
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'perspective(1000px) rotateX(0) rotateY(0) scale(1)';
        });
    });
    
    // Ripple effect
    document.querySelectorAll('.btn, .nav-link').forEach(el => {
        el.addEventListener('click', function(e) {
            const ripple = document.createElement('span');
            ripple.classList.add('ripple');
            
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            
            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = e.clientX - rect.left - size/2 + 'px';
            ripple.style.top = e.clientY - rect.top - size/2 + 'px';
            
            this.appendChild(ripple);
            
            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });
}

// ========== ANIMATED COUNTERS ==========
function initCounters() {
    const counters = document.querySelectorAll('.stat-card h3, .counter');
    
    counters.forEach(counter => {
        const updateCount = () => {
            const target = parseInt(counter.innerText);
            const current = parseInt(counter.innerText) || 0;
            const increment = target / 50;
            
            if(current < target) {
                counter.innerText = Math.ceil(current + increment);
                setTimeout(updateCount, 20);
            } else {
                counter.innerText = target;
            }
        };
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if(entry.isIntersecting) {
                    updateCount();
                    observer.unobserve(entry.target);
                }
            });
        });
        
        observer.observe(counter);
    });
}

// ========== PARTICLES BACKGROUND ==========
function initParticles() {
    const particlesContainer = document.createElement('div');
    particlesContainer.className = 'particles';
    particlesContainer.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        z-index: -1;
    `;
    
    document.body.appendChild(particlesContainer);
    
    for(let i = 0; i < 50; i++) {
        createParticle(particlesContainer);
    }
}

function createParticle(container) {
    const particle = document.createElement('div');
    particle.style.cssText = `
        position: absolute;
        width: ${Math.random() * 5 + 2}px;
        height: ${Math.random() * 5 + 2}px;
        background: linear-gradient(135deg, #667eea, #764ba2);
        border-radius: 50%;
        left: ${Math.random() * 100}%;
        top: ${Math.random() * 100}%;
        opacity: ${Math.random() * 0.3};
        animation: floatParticle ${Math.random() * 10 + 10}s infinite linear;
    `;
    
    container.appendChild(particle);
}

// Add particle animation to stylesheet
const style = document.createElement('style');
style.textContent = `
    @keyframes floatParticle {
        0% {
            transform: translateY(0) translateX(0);
        }
        25% {
            transform: translateY(-20px) translateX(10px);
        }
        50% {
            transform: translateY(0) translateX(20px);
        }
        75% {
            transform: translateY(20px) translateX(10px);
        }
        100% {
            transform: translateY(0) translateX(0);
        }
    }
    
    @keyframes ripple {
        0% {
            transform: scale(0);
            opacity: 1;
        }
        100% {
            transform: scale(4);
            opacity: 0;
        }
    }
    
    .ripple {
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.5);
        animation: ripple 0.6s ease-out;
        pointer-events: none;
    }
    
    .btn, .nav-link {
        position: relative;
        overflow: hidden;
    }
`;

document.head.appendChild(style);

// ========== TYPING EFFECT ==========
function initTypingEffect() {
    const elements = document.querySelectorAll('.typing-effect');
    
    elements.forEach(el => {
        const text = el.innerText;
        el.innerText = '';
        
        let i = 0;
        const type = () => {
            if(i < text.length) {
                el.innerText += text.charAt(i);
                i++;
                setTimeout(type, 100);
            }
        };
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if(entry.isIntersecting) {
                    type();
                    observer.unobserve(entry.target);
                }
            });
        });
        
        observer.observe(el);
    });
}

// ========== PARALLAX EFFECT ==========
function initParallax() {
    window.addEventListener('scroll', () => {
        const scrolled = window.pageYOffset;
        
        document.querySelectorAll('.parallax').forEach(el => {
            const speed = el.dataset.speed || 0.5;
            el.style.transform = `translateY(${scrolled * speed}px)`;
        });
    });
}

// ========== PROGRESS BARS ==========
function initProgressBars() {
    const progressBars = document.querySelectorAll('.progress-bar');
    
    progressBars.forEach(bar => {
        const target = bar.dataset.target || 0;
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if(entry.isIntersecting) {
                    bar.style.width = target + '%';
                }
            });
        });
        
        observer.observe(bar);
    });
}

// ========== BOOTSTRAP TOOLTIPS ==========
function initTooltips() {
    const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    if(tooltips.length > 0 && typeof bootstrap !== 'undefined') {
        tooltips.forEach(tooltip => new bootstrap.Tooltip(tooltip));
    }
}

// ========== SMOOTH SCROLL ==========
function initSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            
            const target = document.querySelector(this.getAttribute('href'));
            if(target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
}

// ========== LOADING SCREEN ==========
window.addEventListener('load', function() {
    // Hide loading screen if exists
    const loader = document.querySelector('.loader');
    if(loader) {
        loader.classList.add('hidden');
        setTimeout(() => {
            loader.style.display = 'none';
        }, 500);
    }
    
    // Confetti effect for success messages
    const successAlerts = document.querySelectorAll('.alert-success');
    successAlerts.forEach(alert => {
        if(alert.innerText.includes('success') || alert.innerText.includes('Success')) {
            confetti({
                particleCount: 100,
                spread: 70,
                origin: { y: 0.6 }
            });
        }
    });
});

// ========== CONFETTI FUNCTION ==========
function confetti(options) {
    // Simple confetti implementation
    const canvas = document.createElement('canvas');
    canvas.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        z-index: 9999;
    `;
    document.body.appendChild(canvas);
    
    const ctx = canvas.getContext('2d');
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;
    
    const particles = [];
    const colors = ['#4361ee', '#f72585', '#4cc9f0', '#f8961e', '#43e97b'];
    
    for(let i = 0; i < options.particleCount; i++) {
        particles.push({
            x: Math.random() * canvas.width,
            y: Math.random() * canvas.height - canvas.height,
            size: Math.random() * 5 + 2,
            color: colors[Math.floor(Math.random() * colors.length)],
            speed: Math.random() * 3 + 2,
            angle: (Math.random() * 60 - 30) * Math.PI / 180
        });
    }
    
    function animate() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        
        particles.forEach(p => {
            ctx.fillStyle = p.color;
            ctx.fillRect(p.x, p.y, p.size, p.size);
            
            p.x += Math.sin(p.angle) * p.speed;
            p.y += Math.cos(p.angle) * p.speed + 0.5;
            
            if(p.y > canvas.height) {
                p.y = -p.size;
                p.x = Math.random() * canvas.width;
            }
        });
        
        requestAnimationFrame(animate);
    }
    
    animate();
    
    setTimeout(() => {
        canvas.remove();
    }, 5000);
}

// ========== LIVE CLOCK ==========
function updateClock() {
    const clock = document.querySelector('.live-clock');
    if(clock) {
        const now = new Date();
        clock.innerText = now.toLocaleTimeString();
    }
}

setInterval(updateClock, 1000);

// ========== FORM VALIDATION WITH ANIMATIONS ==========
document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', function(e) {
        const requiredFields = this.querySelectorAll('[required]');
        let valid = true;
        
        requiredFields.forEach(field => {
            if(!field.value.trim()) {
                valid = false;
                field.classList.add('error');
                
                // Shake animation
                field.style.animation = 'shake 0.5s ease';
                setTimeout(() => {
                    field.style.animation = '';
                }, 500);
            } else {
                field.classList.remove('error');
            }
        });
        
        if(!valid) {
            e.preventDefault();
            
            // Show error message
            const errorMsg = document.createElement('div');
            errorMsg.className = 'alert alert-danger';
            errorMsg.innerText = 'Please fill all required fields!';
            errorMsg.style.animation = 'slideInRight 0.5s ease';
            
            form.prepend(errorMsg);
            
            setTimeout(() => {
                errorMsg.remove();
            }, 3000);
        } else {
            // Show loading state
            const submitBtn = form.querySelector('[type="submit"]');
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Processing...';
            submitBtn.disabled = true;
        }
    });
});