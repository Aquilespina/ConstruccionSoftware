<!-- Partial de scripts (se incluye desde la plantilla de recepción) -->
<script>
  // Script mínimo para cambiar contenido en el layout de recepción
  document.addEventListener('DOMContentLoaded', function () {
    const navItems = document.querySelectorAll('.sidebar .nav-item');
    const pageTitle = document.getElementById('pageTitle');

    navItems.forEach(btn => btn.addEventListener('click', function () {
      const target = btn.getAttribute('data-target');
      pageTitle.textContent = btn.querySelector('.nav-label').textContent;
      // Navegación simple: carga la vista mediante fetch a rutas web si necesitas
      // Por ahora dejamos comportamiento visual solamente
      navItems.forEach(n => n.classList.remove('active'));
      btn.classList.add('active');
    }));
  });
</script>
