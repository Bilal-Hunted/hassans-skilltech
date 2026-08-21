// ============================================
// DOM READY
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    
    // ============================================
    // NAVIGATION TOGGLE (Mobile)
    // ============================================
    const hamburger = document.getElementById('hamburger');
    const navMenu = document.getElementById('navMenu');
    
    if (hamburger && navMenu) {
        hamburger.addEventListener('click', function() {
            this.classList.toggle('active');
            navMenu.classList.toggle('active');
            document.body.style.overflow = navMenu.classList.contains('active') ? 'hidden' : '';
        });
        
        // Close menu on link click (mobile)
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', function() {
                hamburger.classList.remove('active');
                navMenu.classList.remove('active');
                document.body.style.overflow = '';
            });
        });
    }
    
    // ============================================
    // DROPDOWN TOGGLE (Mobile)
    // ============================================
    document.querySelectorAll('.dropdown > a').forEach(function(dropdownLink) {
        dropdownLink.addEventListener('click', function(e) {
            if (window.innerWidth <= 768) {
                e.preventDefault();
                const parent = this.parentElement;
                parent.classList.toggle('open');
            }
        });
    });
    
    // ============================================
    // ACTIVE NAV LINK ON SCROLL
    // ============================================
    const sections = document.querySelectorAll('section[id]');
    const navLinks = document.querySelectorAll('.nav-link');
    
    window.addEventListener('scroll', function() {
        let current = '';
        sections.forEach(section => {
            const sectionTop = section.offsetTop - 120;
            if (window.scrollY >= sectionTop) {
                current = section.getAttribute('id');
            }
        });
        
        navLinks.forEach(link => {
            link.classList.remove('active');
            if (link.getAttribute('href') === '#' + current) {
                link.classList.add('active');
            }
        });
    });
    
    // ============================================
    // COUNTER ANIMATION (Stats)
    // ============================================
    const counters = document.querySelectorAll('.counter');
    let countersAnimated = false;
    
    function animateCounters() {
        if (countersAnimated) return;
        
        counters.forEach(counter => {
            const target = parseInt(counter.getAttribute('data-target'));
            const duration = 2000;
            const increment = target / (duration / 16);
            let current = 0;
            
            const timer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    counter.textContent = target;
                    clearInterval(timer);
                } else {
                    counter.textContent = Math.floor(current);
                }
            }, 16);
        });
        
        countersAnimated = true;
    }
    
    // Trigger counters when stats section is visible
    const statsSection = document.querySelector('.stats');
    if (statsSection) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    animateCounters();
                }
            });
        }, { threshold: 0.3 });
        observer.observe(statsSection);
    }
    
    // ============================================
    // ACADEMY TABS
    // ============================================
    const tabBtns = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            // Remove active class from all buttons and contents
            tabBtns.forEach(b => b.classList.remove('active'));
            tabContents.forEach(c => c.classList.remove('active'));
            
            // Add active class to clicked button
            this.classList.add('active');
            
            // Show corresponding content
            const tabId = this.getAttribute('data-tab');
            const targetContent = document.getElementById('tab-' + tabId);
            if (targetContent) {
                targetContent.classList.add('active');
            }
        });
    });
    
    // ============================================
    // CONDITIONAL FORM FIELDS
    // ============================================
    const purposeSelect = document.getElementById('purpose');
    const agencySection = document.getElementById('agencySection');
    const academySection = document.getElementById('academySection');
    
    if (purposeSelect) {
        purposeSelect.addEventListener('change', function() {
            const value = this.value;
            
            // Hide both sections first
            if (agencySection) agencySection.style.display = 'none';
            if (academySection) academySection.style.display = 'none';
            
            // Show relevant section
            if (value === 'agency' || value === 'both') {
                if (agencySection) agencySection.style.display = 'block';
                // Make required fields required
                document.getElementById('projectDetails').required = true;
            } else {
                document.getElementById('projectDetails').required = false;
            }
            
            if (value === 'academy' || value === 'both') {
                if (academySection) academySection.style.display = 'block';
                document.getElementById('courseName').required = true;
                document.getElementById('motivation').required = true;
            } else {
                document.getElementById('courseName').required = false;
                document.getElementById('motivation').required = false;
            }
        });
    }
    
    // ============================================
    // FORM SUBMISSION (AJAX)
    // ============================================
    // ============================================
// FORM SUBMISSION - SIMPLIFIED & WORKING
// ============================================
const form = document.getElementById('enquiryForm');
const formMessage = document.getElementById('formMessage');

if (form) {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Show loading
        const submitBtn = this.querySelector('.btn-submit');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
        submitBtn.disabled = true;
        
        // Clear message
        formMessage.className = 'form-message';
        formMessage.style.display = 'none';
        
        // Create FormData (without file)
        const formData = new FormData(this);
        formData.delete('document'); // Remove file field if exists
        
        // Send to Formspree
        fetch('https://formspree.io/f/xjybljpr', {
            method: 'POST',
            body: formData,
            headers: {
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (response.ok) {
                return response.json();
            } else {
                return response.json().then(data => {
                    throw new Error(data.error || 'Form submission failed');
                });
            }
        })
        .then(data => {
            formMessage.style.display = 'block';
            formMessage.className = 'form-message success';
            formMessage.innerHTML = '<strong>✅ Thank You!</strong> Your enquiry has been submitted successfully! Our team will contact you within 24 hours.';
            form.reset();
            
            // Hide conditional sections
            const agencySection = document.getElementById('agencySection');
            const academySection = document.getElementById('academySection');
            if (agencySection) agencySection.style.display = 'none';
            if (academySection) academySection.style.display = 'none';
        })
        .catch(error => {
            formMessage.style.display = 'block';
            formMessage.className = 'form-message error';
            formMessage.innerHTML = '<strong>❌ Submission Failed!</strong> ' + error.message + ' Please try calling us at +92 301 9005410.';
        })
        .finally(() => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    });
}
    
    // ============================================
    // SCROLL TO TOP BUTTON
    // ============================================
    const scrollBtn = document.getElementById('scrollTop');
    
    window.addEventListener('scroll', function() {
        if (window.scrollY > 400) {
            scrollBtn.classList.add('visible');
        } else {
            scrollBtn.classList.remove('visible');
        }
    });
    
    if (scrollBtn) {
        scrollBtn.addEventListener('click', function() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }
    
    // ============================================
    // SMOOTH SCROLL FOR ALL ANCHOR LINKS
    // ============================================
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
            
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                e.preventDefault();
                const navbarHeight = document.querySelector('.navbar').offsetHeight;
                const targetPosition = targetElement.offsetTop - navbarHeight - 10;
                window.scrollTo({ top: targetPosition, behavior: 'smooth' });
            }
        });
    });
    
});