
    // JavaScript para la funcionalidad del panel
    document.addEventListener('DOMContentLoaded', function() {
      const sidebar = document.getElementById('sidebar');
      const toggleSidebar = document.getElementById('toggleSidebar');
      const overlay = document.getElementById('overlay');
      const navItems = document.querySelectorAll('.nav-item');
      const modules = document.querySelectorAll('.module');
      const pageTitle = document.getElementById('pageTitle');
      
      // Toggle sidebar en móviles
      toggleSidebar.addEventListener('click', function() {
        sidebar.classList.toggle('active');
        overlay.classList.toggle('active');
      });
      
      // Cerrar sidebar al hacer clic en el overlay
      overlay.addEventListener('click', function() {
        sidebar.classList.remove('active');
        overlay.classList.remove('active');
      });
      
      // Cambiar módulos al hacer clic en los items del sidebar
      navItems.forEach(item => {
        item.addEventListener('click', function() {
          const target = this.getAttribute('data-target');
          
          // Remover clase active de todos los items y módulos
          navItems.forEach(nav => nav.classList.remove('active'));
          modules.forEach(mod => mod.classList.remove('active'));
          
          // Agregar clase active al item y módulo seleccionado
          this.classList.add('active');
          document.getElementById(`mod-${target}`).classList.add('active');
          
          // Actualizar título de la página
          const label = this.querySelector('.nav-label').textContent;
          pageTitle.textContent = label;
          
          // Cerrar sidebar en móviles después de seleccionar
          if (window.innerWidth <= 1024) {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
          }
        });
      });
      
      // Manejo de tabs
      const tabButtons = document.querySelectorAll('.tab-button');
      tabButtons.forEach(button => {
        button.addEventListener('click', function() {
          const parent = this.closest('.tabs');
          parent.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
          this.classList.add('active');
        });
      });
    });