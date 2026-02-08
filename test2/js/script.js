/* ============================================
   CIAO EVENTI - CLEAN JavaScript File
   ============================================ */

// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function () {
    initMobileMenu();
    initSmoothScroll();
    initFormValidation();
    initScrollAnimations();
    initNavbarScroll();
    initPasswordToggle();
    initLikeSystem();  // NEW: Initialize like system
    initImagePreview();
    initCountdowns();
    initSearchFilter();
});

/* ============================================
   1. MOBILE MENU TOGGLE
   ============================================ */
function initMobileMenu() {
    const menuBtn = document.querySelector('.mobile-menu-btn');
    const navLinks = document.querySelector('.nav-links');
    const navItems = document.querySelectorAll('.nav-link');

    if (!menuBtn || !navLinks) return;

    menuBtn.addEventListener('click', function () {
        navLinks.classList.toggle('active');

        const icon = menuBtn.querySelector('i, svg');
        if (icon) {
            if (navLinks.classList.contains('active')) {
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-times');
            } else {
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        }

        const isExpanded = navLinks.classList.contains('active');
        menuBtn.setAttribute('aria-expanded', isExpanded);
    });

    navItems.forEach(function (item) {
        item.addEventListener('click', function () {
            navLinks.classList.remove('active');
            menuBtn.setAttribute('aria-expanded', 'false');
        });
    });

    document.addEventListener('click', function (e) {
        if (!navLinks.contains(e.target) && !menuBtn.contains(e.target)) {
            navLinks.classList.remove('active');
            menuBtn.setAttribute('aria-expanded', 'false');
        }
    });
}

/* ============================================
   2. SMOOTH SCROLL
   ============================================ */
function initSmoothScroll() {
    const anchorLinks = document.querySelectorAll('a[href^="#"]');

    anchorLinks.forEach(function (link) {
        link.addEventListener('click', function (e) {
            const href = this.getAttribute('href');

            if (href === '#' || href === '') return;

            const target = document.querySelector(href);

            if (target) {
                e.preventDefault();

                const navbarHeight = document.querySelector('.navbar')?.offsetHeight || 80;
                const targetPosition = target.getBoundingClientRect().top + window.pageYOffset - navbarHeight;

                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });
            }
        });
    });
}

/* ============================================
   3. LIKE SYSTEM - CLEAN VERSION
   ============================================ */
function initLikeSystem() {
    // Add click handlers to all like buttons
    document.addEventListener('click', function (e) {
        // Find the clicked like button
        let likeBtn = e.target.closest('.card-like');
        if (!likeBtn && e.target.classList.contains('fa-heart')) {
            likeBtn = e.target.closest('button') || e.target.parentElement;
        }

        if (likeBtn && likeBtn.hasAttribute('data-event-id')) {
            e.preventDefault();
            e.stopPropagation();

            handleLikeButton(likeBtn);
        }
    });

    // Restore liked state from localStorage
    restoreLikedState();
}

function handleLikeButton(likeBtn) {
    const eventId = likeBtn.getAttribute('data-event-id');
    const isCurrentlyLiked = likeBtn.classList.contains('liked');
    const newState = !isCurrentlyLiked;

    // Update visual state
    likeBtn.classList.toggle('liked');

    // Update like count in the card
    updateLikeCount(likeBtn, newState);

    // Animation
    likeBtn.style.transform = 'scale(1.2)';
    setTimeout(() => {
        likeBtn.style.transform = 'scale(1)';
    }, 300);

    // Send to backend
    sendLikeToServer(eventId, newState);

    // Save to localStorage
    saveLikeToLocalStorage(eventId, newState);
}

function updateLikeCount(likeBtn, newState) {
    const card = likeBtn.closest('.card');
    if (!card) return;

    // Find the like count element
    let countElement = card.querySelector('.like-count');

    // If not found by class, try other selectors
    if (!countElement) {
        countElement = card.querySelector('.text-gold.font-semibold');
    }

    if (!countElement) {
        // Try to find any number before "likes"
        const allElements = card.querySelectorAll('*');
        allElements.forEach(el => {
            const text = el.textContent.trim();
            if (/^\d+$/.test(text)) {
                const nextSibling = el.nextElementSibling;
                if (nextSibling && nextSibling.textContent.includes('like')) {
                    countElement = el;
                }
            }
        });
    }

    if (countElement) {
        let currentCount = parseInt(countElement.textContent.replace(/,/g, '')) || 0;
        let newCount = newState ? currentCount + 1 : currentCount - 1;
        if (newCount < 0) newCount = 0;

        countElement.textContent = newCount.toLocaleString();

        // Update "likes" text if it exists
        const likesText = countElement.nextElementSibling;
        if (likesText && likesText.textContent.includes('like')) {
            likesText.textContent = newCount === 1 ? ' like' : ' likes';
        }
    }
}

function sendLikeToServer(eventId, isLiked) {
    fetch('api/like.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            event_id: eventId,
            action: isLiked ? 'like' : 'unlike'
        })
    })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                console.error('Like error:', data.error);
            }
        })
        .catch(error => {
            console.log('Like saved locally');
        });
}

function saveLikeToLocalStorage(eventId, isLiked) {
    const storageKey = 'ciao_liked_events';
    let likedEvents = JSON.parse(localStorage.getItem(storageKey) || '[]');

    if (isLiked) {
        if (!likedEvents.includes(eventId)) {
            likedEvents.push(eventId);
        }
    } else {
        const index = likedEvents.indexOf(eventId);
        if (index > -1) {
            likedEvents.splice(index, 1);
        }
    }

    localStorage.setItem(storageKey, JSON.stringify(likedEvents));
}

function restoreLikedState() {
    const storageKey = 'ciao_liked_events';
    const likedEvents = JSON.parse(localStorage.getItem(storageKey) || '[]');

    likedEvents.forEach(eventId => {
        const likeBtn = document.querySelector(`[data-event-id="${eventId}"]`);
        if (likeBtn) {
            likeBtn.classList.add('liked');
        }
    });
}

// Make function available globally for onclick handlers
window.handleLike = function (eventId) {
    const likeBtn = document.querySelector(`[data-event-id="${eventId}"]`);
    if (likeBtn) {
        handleLikeButton(likeBtn);
    }
    return false;
};

/* ============================================
   4. FORM VALIDATION
   ============================================ */
function initFormValidation() {
    // Validation Rules - SINGLE DECLARATION
    const validationRules = {
        email: {
            pattern: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
            message: 'Please enter a valid email address'
        },
        password: {
            pattern: /^.{6,}$/,
            message: 'Password must be at least 6 characters'
        },
        phone: {
            pattern: /^[\d\s\-\+\(\)]{10,}$/,
            message: 'Please enter a valid phone number'
        }
    };

    // Login Form
    const loginForm = document.querySelector('#login-form, .login-form');
    if (loginForm) {
        loginForm.addEventListener('submit', function (e) {
            if (!validateForm(this)) {
                e.preventDefault();
            }
        });
    }

    // Register Form
    const registerForm = document.querySelector('#register-form, .register-form');
    if (registerForm) {
        registerForm.addEventListener('submit', function (e) {
            if (!validateForm(this)) {
                e.preventDefault();
            }
        });
    }

    // Real-time validation
    const inputs = document.querySelectorAll('.form-input[required]');
    inputs.forEach(function (input) {
        input.addEventListener('blur', function () {
            validateField(this);
        });
    });
}

function validateField(field) {
    const value = field.value.trim();

    // Clear previous error
    clearFieldError(field);

    // Required check
    if (field.hasAttribute('required') && !value) {
        showFieldError(field, 'This field is required');
        return false;
    }

    field.classList.add('valid');
    return true;
}

function validateForm(form) {
    let isValid = true;
    const requiredFields = form.querySelectorAll('[required]');

    requiredFields.forEach(field => {
        if (!validateField(field)) {
            isValid = false;
        }
    });

    return isValid;
}

function showFieldError(field, message) {
    field.classList.add('error');

    const existingError = field.parentNode.querySelector('.form-error');
    if (existingError) existingError.remove();

    const errorEl = document.createElement('span');
    errorEl.className = 'form-error';
    errorEl.textContent = message;
    field.parentNode.appendChild(errorEl);
}

function clearFieldError(field) {
    field.classList.remove('error');

    const existingError = field.parentNode.querySelector('.form-error');
    if (existingError) existingError.remove();
}

/* ============================================
   5. SCROLL ANIMATIONS
   ============================================ */
function initScrollAnimations() {
    const animatedElements = document.querySelectorAll('.animate-on-scroll, .card');

    if (!animatedElements.length) return;

    const observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting) {
                const el = entry.target;
                el.classList.add('animated', 'animate-fade-up');
                observer.unobserve(el);
            }
        });
    }, {
        root: null,
        rootMargin: '0px 0px -50px 0px',
        threshold: 0.1
    });

    animatedElements.forEach(function (el) {
        el.classList.add('will-animate');
        observer.observe(el);
    });
}

/* ============================================
   6. NAVBAR SCROLL EFFECT
   ============================================ */
function initNavbarScroll() {
    const navbar = document.querySelector('.navbar');
    if (!navbar) return;

    window.addEventListener('scroll', function () {
        if (window.pageYOffset > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });
}

/* ============================================
   7. PASSWORD TOGGLE
   ============================================ */
function initPasswordToggle() {
    const passwordFields = document.querySelectorAll('input[type="password"]');

    passwordFields.forEach(function (field) {
        const wrapper = document.createElement('div');
        wrapper.className = 'password-wrapper';
        wrapper.style.cssText = 'position: relative; width: 100%;';

        field.parentNode.insertBefore(wrapper, field);
        wrapper.appendChild(field);

        const toggleBtn = document.createElement('button');
        toggleBtn.type = 'button';
        toggleBtn.className = 'password-toggle';
        toggleBtn.innerHTML = '👁️';
        toggleBtn.style.cssText = `
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: transparent;
            border: none;
            cursor: pointer;
            font-size: 16px;
            opacity: 0.6;
            transition: opacity 0.2s;
        `;

        toggleBtn.addEventListener('click', function () {
            const type = field.getAttribute('type');
            field.setAttribute('type', type === 'password' ? 'text' : 'password');
            this.innerHTML = type === 'password' ? '🙈' : '👁️';
        });

        wrapper.appendChild(toggleBtn);
    });
}

/* ============================================
   8. IMAGE PREVIEW
   ============================================ */
function initImagePreview() {
    const fileInputs = document.querySelectorAll('input[type="file"][accept*="image"]');

    fileInputs.forEach(function (input) {
        input.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (!file) return;

            if (!file.type.startsWith('image/')) {
                alert('Please select an image file');
                input.value = '';
                return;
            }

            const maxSize = 5 * 1024 * 1024;
            if (file.size > maxSize) {
                alert('Image must be less than 5MB');
                input.value = '';
                return;
            }

            const previewId = input.dataset.preview;
            const previewEl = document.getElementById(previewId);

            if (previewEl) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    previewEl.innerHTML = `<img src="${e.target.result}" alt="Preview" style="max-width: 100%; border-radius: 8px;">`;
                };
                reader.readAsDataURL(file);
            }
        });
    });
}

/* ============================================
   9. COUNTDOWN TIMERS
   ============================================ */
function initCountdowns() {
    const countdowns = document.querySelectorAll('[data-countdown]');

    countdowns.forEach(function (el) {
        const targetDate = new Date(el.dataset.countdown).getTime();

        const timer = setInterval(function () {
            const now = new Date().getTime();
            const distance = targetDate - now;

            if (distance < 0) {
                clearInterval(timer);
                el.innerHTML = '<span style="color: #ff2d7a;">Event Started!</span>';
                return;
            }

            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            el.innerHTML = `
                <div style="display: inline-block; padding: 8px; margin: 4px; background: rgba(255,255,255,0.05); border-radius: 8px; min-width: 60px;">
                    <div style="font-size: 24px; font-weight: bold; color: #ff2d7a;">${days}</div>
                    <div style="font-size: 12px; color: rgba(255,255,255,0.5);">Days</div>
                </div>
                <div style="display: inline-block; padding: 8px; margin: 4px; background: rgba(255,255,255,0.05); border-radius: 8px; min-width: 60px;">
                    <div style="font-size: 24px; font-weight: bold; color: #ff2d7a;">${hours}</div>
                    <div style="font-size: 12px; color: rgba(255,255,255,0.5);">Hours</div>
                </div>
                <div style="display: inline-block; padding: 8px; margin: 4px; background: rgba(255,255,255,0.05); border-radius: 8px; min-width: 60px;">
                    <div style="font-size: 24px; font-weight: bold; color: #ff2d7a;">${minutes}</div>
                    <div style="font-size: 12px; color: rgba(255,255,255,0.5);">Mins</div>
                </div>
                <div style="display: inline-block; padding: 8px; margin: 4px; background: rgba(255,255,255,0.05); border-radius: 8px; min-width: 60px;">
                    <div style="font-size: 24px; font-weight: bold; color: #ff2d7a;">${seconds}</div>
                    <div style="font-size: 12px; color: rgba(255,255,255,0.5);">Secs</div>
                </div>
            `;
        }, 1000);
    });
}

/* ============================================
   10. SEARCH FILTER
   ============================================ */
function initSearchFilter() {
    const searchInput = document.querySelector('#search-events');
    const categoryFilter = document.querySelector('#category-filter');

    if (!searchInput && !categoryFilter) return;

    function filterEvents() {
        const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
        const category = categoryFilter ? categoryFilter.value : '';

        document.querySelectorAll('.card').forEach(function (card) {
            const title = card.querySelector('.card-title')?.textContent.toLowerCase() || '';
            const description = card.querySelector('.card-description')?.textContent.toLowerCase() || '';
            const cardCategory = card.dataset.category || '';

            const matchesSearch = title.includes(searchTerm) || description.includes(searchTerm);
            const matchesCategory = !category || category === 'all' || cardCategory.includes(category);

            if (matchesSearch && matchesCategory) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
    }

    if (searchInput) {
        searchInput.addEventListener('input', filterEvents);
    }

    if (categoryFilter) {
        categoryFilter.addEventListener('change', filterEvents);
    }
}


/* ============================================
   14. EVENT EDIT/DELETE CONFIRMATION
   ============================================ */
function initEventActions() {
    // Delete confirmation
    document.querySelectorAll('a[href*="delete_event"]').forEach(link => {
        link.addEventListener('click', function (e) {
            if (!confirm('Are you sure you want to delete this event? This action cannot be undone.')) {
                e.preventDefault();
            }
        });
    });

    // Edit form validation
    const editForm = document.querySelector('#edit-event-form');
    if (editForm) {
        editForm.addEventListener('submit', function (e) {
            const title = this.querySelector('#title').value.trim();
            const date = this.querySelector('#event_date').value;

            if (!title) {
                e.preventDefault();
                alert('Event title is required');
                return false;
            }

            if (date && new Date(date) < new Date()) {
                e.preventDefault();
                alert('Event date must be in the future');
                return false;
            }

            return true;
        });
    }
}

// Add to DOMContentLoaded
document.addEventListener('DOMContentLoaded', function () {
    initEventActions();
});


/* ============================================
   END OF SCRIPT
   ============================================ */