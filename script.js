// DOM Elements
const themeToggle = document.getElementById('theme-toggle');
const searchInput = document.getElementById('search-input');
const searchBtn = document.getElementById('search-btn');
const reviewGrid = document.querySelector('.review-grid');
const loadMoreBtn = document.getElementById('load-more-btn');
const priceFilter = document.getElementById('price-filter');
const ratingFilter = document.getElementById('rating-filter');
const newsletterForm = document.getElementById('newsletter-form');

// Dark Mode Toggle - Subtle dan Professional
function initThemeToggle() {
    const savedTheme = localStorage.getItem('theme') || 'light';
    document.body.setAttribute('data-theme', savedTheme);
    updateThemeIcon(savedTheme);

    themeToggle.addEventListener('click', () => {
        const currentTheme = document.body.getAttribute('data-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        
        document.body.setAttribute('data-theme', newTheme);
        localStorage.setItem('theme', newTheme);
        updateThemeIcon(newTheme);
    });
}

function updateThemeIcon(theme) {
    const icon = themeToggle.querySelector('i');
    if (theme === 'dark') {
        icon.className = 'fas fa-sun';
        themeToggle.setAttribute('aria-label', 'Switch to light mode');
    } else {
        icon.className = 'fas fa-moon';
        themeToggle.setAttribute('aria-label', 'Switch to dark mode');
    }
}

// Search Functionality - Clean dan Responsive
function initSearch() {
    const searchHandler = () => {
        const searchTerm = searchInput.value.toLowerCase().trim();
        const allCards = document.querySelectorAll('.review-grid .card, .brand-grid .card');
        
        allCards.forEach(card => {
            const title = card.querySelector('h3').textContent.toLowerCase();
            const description = card.querySelector('p').textContent.toLowerCase();
            
            if (searchTerm === '' || title.includes(searchTerm) || description.includes(searchTerm)) {
                card.style.display = 'block';
                card.style.opacity = '1';
            } else {
                card.style.display = 'none';
                card.style.opacity = '0';
            }
        });
    };

    searchBtn.addEventListener('click', searchHandler);
    searchInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            searchHandler();
        }
    });

    // Debounced search untuk performa
    let searchTimeout;
    searchInput.addEventListener('input', () => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(searchHandler, 300);
    });
}

// Filter System - Simple dan Efektif
function initFilters() {
    priceFilter.addEventListener('change', applyFilters);
    ratingFilter.addEventListener('change', applyFilters);
}

function applyFilters() {
    const priceRange = priceFilter.value;
    const minRating = ratingFilter.value;
    const laptopCards = document.querySelectorAll('.review-grid .card');
    
    laptopCards.forEach(card => {
        const priceText = card.querySelector('.price')?.textContent;
        const ratingText = card.querySelector('p strong')?.textContent;
        
        let showCard = true;
        
        // Price filter
        if (priceRange !== 'all' && priceText) {
            const price = parseFloat(priceText.replace(/[^0-9]/g, ''));
            const [min, max] = priceRange.split('-').map(Number);
            
            if (max) {
                showCard = price >= min && price <= max;
            } else {
                showCard = price >= min;
            }
        }
        
        // Rating filter
        if (minRating !== 'all' && ratingText && showCard) {
            const rating = parseFloat(ratingText.split(' ')[1]);
            showCard = rating >= parseFloat(minRating);
        }
        
        card.style.display = showCard ? 'block' : 'none';
        card.style.opacity = showCard ? '1' : '0';
    });
}

// Load More dengan Mock Data Real - Laptop yang benar-benar ada
let currentPage = 1;
const laptopsPerPage = 3;

const mockLaptops = [
    // AXIOO - Real Models
    {
        name: "Axioo MyBook 14H",
        rating: 4.0,
        description: "Laptop produktivitas dengan layar 14 inci Full HD dan performa handal untuk pekerjaan sehari-hari.",
        price: "Rp 7.499.000",
        image: "IMG/axioo-mybook14h.jpg",
        date: "2024-02-15"
    },
    {
        name: "Axioo Slimbook 13",
        rating: 3.8,
        description: "Ultrabook tipis dan ringan dengan desain premium untuk mobilitas maksimal profesional muda.",
        price: "Rp 8.299.000",
        image: "IMG/axioo-slimbook13.jpg",
        date: "2024-01-20"
    },
    {
        name: "Axioo Neon RNE",
        rating: 3.6,
        description: "Laptop entry-level dengan performa stabil untuk kebutuhan office dan multimedia basic.",
        price: "Rp 4.299.000",
        image: "IMG/axioo-neon-rne.jpg",
        date: "2024-03-10"
    },
    {
        name: "Axioo Pongo 730",
        rating: 4.1,
        description: "Gaming laptop terbaru dengan cooling yang lebih baik dan performa gaming yang mengesankan.",
        price: "Rp 13.999.000",
        image: "IMG/axioo-pongo730.jpg",
        date: "2024-04-05"
    },
    // ADVAN - Real Models
    {
        name: "Advan Soulmate 13",
        rating: 3.7,
        description: "Laptop compact 13 inci dengan desain elegan untuk lifestyle dan productivity ringan.",
        price: "Rp 5.499.000",
        image: "IMG/advan-soulmate13.jpg",
        date: "2024-02-25"
    },
    {
        name: "Advan 360 Stylus",
        rating: 3.9,
        description: "Laptop 2-in-1 dengan stylus support untuk creative work dan note-taking digital.",
        price: "Rp 6.999.000",
        image: "IMG/advan-360stylus.jpg",
        date: "2024-03-15"
    },
    {
        name: "Advan WorkNote",
        rating: 3.5,
        description: "Business laptop dengan port lengkap dan durability tinggi untuk penggunaan kantor intensif.",
        price: "Rp 4.799.000",
        image: "IMG/advan-worknote.jpg",
        date: "2024-01-30"
    },
    {
        name: "Advan CloudBook",
        rating: 3.4,
        description: "Laptop ultra-budget untuk cloud computing dan web browsing dengan efisiensi maksimal.",
        price: "Rp 2.999.000",
        image: "IMG/advan-cloudbook.jpg",
        date: "2024-03-20"
    },
    {
        name: "Advan X7 Pro",
        rating: 3.8,
        description: "Laptop multimedia dengan audio enhanced dan display berkualitas untuk entertainment.",
        price: "Rp 5.999.000",
        image: "IMG/advan-x7pro.jpg",
        date: "2024-04-10"
    }
];

function initLoadMore() {
    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', loadMoreLaptops);
    }
}

function loadMoreLaptops() {
    const startIndex = currentPage * laptopsPerPage;
    const endIndex = startIndex + laptopsPerPage;
    const laptopsToLoad = mockLaptops.slice(startIndex, endIndex);
    
    if (laptopsToLoad.length === 0) {
        loadMoreBtn.textContent = 'Semua laptop telah dimuat';
        loadMoreBtn.disabled = true;
        loadMoreBtn.style.opacity = '0.6';
        return;
    }
    
    // Simple loading state
    loadMoreBtn.textContent = 'Memuat...';
    loadMoreBtn.disabled = true;
    
    // Simulate API delay
    setTimeout(() => {
        laptopsToLoad.forEach((laptop, index) => {
            const laptopCard = createLaptopCard(laptop);
            reviewGrid.appendChild(laptopCard);
            
            // Subtle fade in animation
            setTimeout(() => {
                laptopCard.style.opacity = '1';
            }, index * 100);
        });
        
        currentPage++;
        loadMoreBtn.textContent = 'Muat Lebih Banyak';
        loadMoreBtn.disabled = false;
        
        if (currentPage * laptopsPerPage >= mockLaptops.length) {
            loadMoreBtn.textContent = 'Semua laptop telah dimuat';
            loadMoreBtn.disabled = true;
            loadMoreBtn.style.opacity = '0.6';
        }
    }, 800);
}

function createLaptopCard(laptop) {
    const article = document.createElement('article');
    article.className = 'card';
    article.style.opacity = '0';
    article.style.transition = 'opacity 0.5s ease';
    article.innerHTML = `
        <img src="${laptop.image}" alt="${laptop.name}" width="200" height="200" onerror="this.src='IMG/placeholder.jpg'">
        <h3>${laptop.name}</h3>
        <p><strong>Rating: ${laptop.rating} dari 5</strong></p>
        <p>${laptop.description}</p>
        <p class="price">${laptop.price}</p>
        <time datetime="${laptop.date}">${formatDate(laptop.date)}</time>
        <br><br>
        <a href="#" class="btn btn-secondary">Baca Review</a>
    `;
    return article;
}

function formatDate(dateString) {
    const date = new Date(dateString);
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return date.toLocaleDateString('id-ID', options);
}

// Newsletter - Clean Form Handling
function initNewsletter() {
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', handleNewsletterSubmit);
    }
}

function handleNewsletterSubmit(e) {
    e.preventDefault();
    
    const emailInput = newsletterForm.querySelector('input[type="email"]');
    const submitBtn = newsletterForm.querySelector('button');
    const email = emailInput.value;
    
    if (!isValidEmail(email)) {
        showNotification('Email tidak valid!', 'error');
        return;
    }
    
    submitBtn.textContent = 'Mendaftar...';
    submitBtn.disabled = true;
    
    setTimeout(() => {
        showNotification('Terima kasih! Anda telah berlangganan newsletter.', 'success');
        emailInput.value = '';
        submitBtn.textContent = 'Berlangganan';
        submitBtn.disabled = false;
    }, 1200);
}

function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

// Subtle Notification System
function showNotification(message, type = 'info') {
    const existingNotification = document.querySelector('.notification');
    if (existingNotification) {
        existingNotification.remove();
    }
    
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <span>${message}</span>
        <button class="notification-close">&times;</button>
    `;
    
    document.body.appendChild(notification);
    
    // Smooth slide in
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
        notification.style.opacity = '1';
    }, 100);
    
    setTimeout(() => {
        hideNotification(notification);
    }, 4000);
    
    notification.querySelector('.notification-close').addEventListener('click', () => {
        hideNotification(notification);
    });
}

function hideNotification(notification) {
    notification.style.transform = 'translateX(100%)';
    notification.style.opacity = '0';
    setTimeout(() => {
        notification.remove();
    }, 300);
}

// Smooth Navigation
function initSmoothScrolling() {
    const navLinks = document.querySelectorAll('nav a[href^="#"]');
    
    navLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            
            const targetId = link.getAttribute('href');
            const targetElement = document.querySelector(targetId);
            
            if (targetElement) {
                const headerOffset = 100;
                const elementPosition = targetElement.offsetTop;
                const offsetPosition = elementPosition - headerOffset;
                
                window.scrollTo({
                    top: offsetPosition,
                    behavior: 'smooth'
                });
                
                // Update active nav
                navLinks.forEach(navLink => navLink.classList.remove('active'));
                link.classList.add('active');
            }
        });
    });
}

// Minimal Scroll to Top
function initScrollToTop() {
    const scrollTopBtn = document.getElementById('scroll-top');
    
    window.addEventListener('scroll', () => {
        if (window.pageYOffset > 400) {
            scrollTopBtn.style.opacity = '1';
            scrollTopBtn.style.pointerEvents = 'all';
        } else {
            scrollTopBtn.style.opacity = '0';
            scrollTopBtn.style.pointerEvents = 'none';
        }
    });
    
    scrollTopBtn.addEventListener('click', () => {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
}

// Subtle Card Effects - No Over-Animation
function initCardEffects() {
    const cards = document.querySelectorAll('.card');
    
    cards.forEach(card => {
        card.addEventListener('mouseenter', () => {
            card.style.transform = 'translateY(-4px)';
        });
        
        card.addEventListener('mouseleave', () => {
            card.style.transform = 'translateY(0)';
        });
    });
}

// Progressive Image Loading
function initLazyLoading() {
    const images = document.querySelectorAll('img[loading="lazy"]');
    
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.style.opacity = '1';
                    imageObserver.unobserve(img);
                }
            });
        });
        
        images.forEach(img => {
            img.style.opacity = '0';
            img.style.transition = 'opacity 0.3s ease';
            imageObserver.observe(img);
        });
    }
}

// Subtle Scroll Animations
function initScrollAnimations() {
    const animateElements = document.querySelectorAll('.animate-on-scroll');
    
    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -30px 0px'
        });
        
        animateElements.forEach(el => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(20px)';
            el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            observer.observe(el);
        });
    }
}

// Minimal Reading Progress
function initReadingProgress() {
    const progressBar = document.getElementById('reading-progress');
    
    window.addEventListener('scroll', () => {
        const winScroll = document.body.scrollTop || document.documentElement.scrollTop;
        const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
        const scrolled = (winScroll / height) * 100;
        
        progressBar.style.width = Math.min(scrolled, 100) + '%';
    });
}

// Initialize - Clean dan Organized
document.addEventListener('DOMContentLoaded', () => {
    initThemeToggle();
    initSearch();
    initFilters();
    initLoadMore();
    initNewsletter();
    initSmoothScrolling();
    initScrollToTop();
    initCardEffects();
    initLazyLoading();
    initScrollAnimations();
    initReadingProgress();
    
    // Simple page load animation
    document.body.style.opacity = '0';
    setTimeout(() => {
        document.body.style.transition = 'opacity 0.5s ease';
        document.body.style.opacity = '1';
    }, 100);
    
    console.log('✅ Website Review Laptop Lokal loaded successfully');
});