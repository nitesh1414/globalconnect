/**
 * Global Connect - Main Interactive JavaScript
 * Animations, Scroll Reveals, Live Counters, Hero Slider & Navigation
 */

document.addEventListener('DOMContentLoaded', () => {

  // 1. Sticky Header Glassmorphic Effect
  const siteHeader = document.getElementById('siteHeader');
  function updateHeaderScroll() {
    if (siteHeader) {
      if (window.scrollY > 30) {
        siteHeader.classList.add('scrolled');
      } else {
        siteHeader.classList.remove('scrolled');
      }
    }
  }
  window.addEventListener('scroll', updateHeaderScroll, { passive: true });
  updateHeaderScroll();

  // 2. Mobile Hamburger Menu Toggle
  const mobileToggle = document.getElementById('mobileToggle');
  const navMenu = document.getElementById('navMenu');

  if (mobileToggle && navMenu) {
    mobileToggle.addEventListener('click', () => {
      navMenu.classList.toggle('active');
      const isExpanded = navMenu.classList.contains('active');
      mobileToggle.setAttribute('aria-expanded', isExpanded);
    });

    // Close menu when clicking outside
    document.addEventListener('click', (e) => {
      if (!navMenu.contains(e.target) && !mobileToggle.contains(e.target) && navMenu.classList.contains('active')) {
        navMenu.classList.remove('active');
      }
    });

    // Mobile dropdown toggle on click
    const dropdownItems = navMenu.querySelectorAll('.nav-item.has-dropdown');
    dropdownItems.forEach((item) => {
      const link = item.querySelector('.nav-link');
      if (link) {
        link.addEventListener('click', (e) => {
          if (window.innerWidth <= 768) {
            e.preventDefault();
            item.classList.toggle('dropdown-open');
          }
        });
      }
    });
  }

  // 3. Scroll Reveal Animation Engine (IntersectionObserver)
  const revealElements = document.querySelectorAll('.reveal-up, .reveal-left, .reveal-right, .reveal-scale');
  if ('IntersectionObserver' in window && revealElements.length > 0) {
    const revealObserver = new IntersectionObserver((entries, observer) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-revealed');
          observer.unobserve(entry.target);
        }
      });
    }, {
      root: null,
      threshold: 0.12,
      rootMargin: '0px 0px -40px 0px'
    });

    revealElements.forEach(el => revealObserver.observe(el));
  } else {
    // Fallback for older browsers
    revealElements.forEach(el => el.classList.add('is-revealed'));
  }

  // 4. Smooth Number Counter Animation
  const countUpElements = document.querySelectorAll('.count-up');
  if (countUpElements.length > 0) {
    function animateCountUp(el) {
      const rawVal = el.getAttribute('data-value') || el.innerText;
      const cleanVal = rawVal.replace(/[^0-9]/g, '');
      const targetNum = parseInt(cleanVal, 10);
      if (isNaN(targetNum)) return;

      const prefix = rawVal.match(/^[^\d]*/) ? rawVal.match(/^[^\d]*/)[0] : '';
      const suffix = rawVal.match(/[^\d]*$/) ? rawVal.match(/[^\d]*$/)[0] : '';
      const duration = 1800; // ms
      const startTime = performance.now();

      function updateCounter(currentTime) {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);
        // Easing out cubic
        const easeProgress = 1 - Math.pow(1 - progress, 3);
        const currentVal = Math.floor(easeProgress * targetNum);

        // Format with commas if target >= 1000
        const formatted = (targetNum >= 1000) ? currentVal.toLocaleString() : currentVal.toString();
        el.innerText = prefix + formatted + suffix;

        if (progress < 1) {
          requestAnimationFrame(updateCounter);
        } else {
          el.innerText = rawVal;
        }
      }

      requestAnimationFrame(updateCounter);
    }

    if ('IntersectionObserver' in window) {
      const counterObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            animateCountUp(entry.target);
            observer.unobserve(entry.target);
          }
        });
      }, { threshold: 0.2 });

      countUpElements.forEach(el => counterObserver.observe(el));
    } else {
      countUpElements.forEach(el => animateCountUp(el));
    }
  }

  // 5. Hero Slider Carousel
  const heroSlider = document.getElementById('heroSlider');
  if (heroSlider) {
    const slides = heroSlider.querySelectorAll('.hero-slide-item');
    const prevBtn = heroSlider.querySelector('.hero-nav-prev');
    const nextBtn = heroSlider.querySelector('.hero-nav-next');
    const dots = heroSlider.querySelectorAll('.hero-dot');
    let currentIndex = 0;
    let autoPlayTimer = null;

    function showSlide(index) {
      if (slides.length === 0) return;
      if (index < 0) index = slides.length - 1;
      if (index >= slides.length) index = 0;

      slides.forEach((slide, i) => {
        slide.classList.toggle('active', i === index);
      });

      dots.forEach((dot, i) => {
        dot.classList.toggle('active', i === index);
      });

      currentIndex = index;
    }

    function nextSlide() {
      showSlide(currentIndex + 1);
    }

    function prevSlide() {
      showSlide(currentIndex - 1);
    }

    function startAutoPlay() {
      if (slides.length > 1) {
        stopAutoPlay();
        autoPlayTimer = setInterval(nextSlide, 6500);
      }
    }

    function stopAutoPlay() {
      if (autoPlayTimer) {
        clearInterval(autoPlayTimer);
        autoPlayTimer = null;
      }
    }

    if (prevBtn) {
      prevBtn.addEventListener('click', () => {
        prevSlide();
        startAutoPlay();
      });
    }

    if (nextBtn) {
      nextBtn.addEventListener('click', () => {
        nextSlide();
        startAutoPlay();
      });
    }

    dots.forEach((dot) => {
      dot.addEventListener('click', () => {
        const slideIndex = parseInt(dot.getAttribute('data-slide-to'), 10);
        if (!isNaN(slideIndex)) {
          showSlide(slideIndex);
          startAutoPlay();
        }
      });
    });

    // Touch swipe support for mobile
    let touchStartX = 0;
    let touchEndX = 0;

    heroSlider.addEventListener('touchstart', (e) => {
      touchStartX = e.changedTouches[0].screenX;
      stopAutoPlay();
    }, { passive: true });

    heroSlider.addEventListener('touchend', (e) => {
      touchEndX = e.changedTouches[0].screenX;
      const diff = touchStartX - touchEndX;
      if (Math.abs(diff) > 40) {
        if (diff > 0) {
          nextSlide();
        } else {
          prevSlide();
        }
      }
      startAutoPlay();
    }, { passive: true });

    heroSlider.addEventListener('mouseenter', stopAutoPlay);
    heroSlider.addEventListener('mouseleave', startAutoPlay);

    startAutoPlay();
  }

  // 6. Floating Back to Top Button with Scroll Progress
  const backToTopBtn = document.getElementById('backToTop');
  const progressCircle = document.getElementById('progressCircle');

  if (backToTopBtn) {
    const totalCircumference = 2 * Math.PI * 21; // ~131.95

    function updateBackToTop() {
      const scrollTop = window.scrollY || document.documentElement.scrollTop;
      const docHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;
      const scrollPercent = docHeight > 0 ? (scrollTop / docHeight) : 0;

      if (scrollTop > 280) {
        backToTopBtn.classList.add('show');
      } else {
        backToTopBtn.classList.remove('show');
      }

      if (progressCircle) {
        const offset = totalCircumference - (scrollPercent * totalCircumference);
        progressCircle.style.strokeDashoffset = Math.max(0, offset);
      }
    }

    window.addEventListener('scroll', updateBackToTop, { passive: true });
    updateBackToTop();

    backToTopBtn.addEventListener('click', () => {
      window.scrollTo({
        top: 0,
        behavior: 'smooth'
      });
    });
  }

  // 7. Auto-dismiss alert boxes after 5 seconds
  const alerts = document.querySelectorAll('.alert');
  alerts.forEach(alert => {
    setTimeout(() => {
      alert.style.opacity = '0';
      alert.style.transition = 'opacity 0.5s ease';
      setTimeout(() => alert.remove(), 500);
    }, 5000);
  });
});
