document.addEventListener('DOMContentLoaded', function () {
  const toggles = document.querySelectorAll('.submenu-toggle');

  toggles.forEach(toggle => {
    toggle.addEventListener('click', function (e) {
      e.preventDefault();
      e.stopPropagation(); // <- clave: evita que el click llegue al document y cierre inmediatamente

      const parent = this.parentElement;

      // Cerrar otros submenús abiertos
      document.querySelectorAll('.submenu.active').forEach(s => {
        if (s !== parent) s.classList.remove('active');
      });

      // Alternar el submenú actual
      parent.classList.toggle('active');
    });
  });

  // Cerrar submenús al hacer clic fuera del menú
  document.addEventListener('click', function (e) {
    if (!e.target.closest('.menu-horizontal')) {
      document.querySelectorAll('.submenu.active').forEach(s => s.classList.remove('active'));
    }
  });

  // Cerrar con ESC
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
      document.querySelectorAll('.submenu.active').forEach(s => s.classList.remove('active'));
    }
  });
});