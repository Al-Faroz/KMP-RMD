const operatorMenu = document.getElementById('menu-operator');
const mobileMenuButton = document.getElementById('buka-menu-operator');
const desktopMenuButton = document.getElementById('toggle-menu-desktop');

operatorMenu.addEventListener('show.bs.offcanvas', () => {
  mobileMenuButton.setAttribute('aria-expanded', 'true');
});

operatorMenu.addEventListener('hidden.bs.offcanvas', () => {
  mobileMenuButton.setAttribute('aria-expanded', 'false');
});

desktopMenuButton.addEventListener('click', () => {
  const collapsed = document.documentElement.classList.toggle('rmd-sidebar-collapsed');
  desktopMenuButton.setAttribute('aria-expanded', String(!collapsed));
  desktopMenuButton.setAttribute('aria-label', collapsed ? 'Buka menu' : 'Tutup menu');
});
