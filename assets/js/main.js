/**
 * Global Connect - Main Public JavaScript
 */

document.addEventListener('DOMContentLoaded', () => {
  // Mobile Hamburger Menu Toggle
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
      if (link && window.innerWidth <= 768) {
        link.addEventListener('click', (e) => {
          if (window.innerWidth <= 768) {
            e.preventDefault();
            item.classList.toggle('dropdown-open');
          }
        });
      }
    });
  }

  // Auto-dismiss alert boxes after 5 seconds
  const alerts = document.querySelectorAll('.alert');
  alerts.forEach(alert => {
    setTimeout(() => {
      alert.style.opacity = '0';
      alert.style.transition = 'opacity 0.5s ease';
      setTimeout(() => alert.remove(), 500);
    }, 5000);
  });
});
