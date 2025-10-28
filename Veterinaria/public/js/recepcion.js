 document.addEventListener('DOMContentLoaded', function() {
      // Elements
      const sidebar = document.getElementById('sidebar');
      const toggleSidebar = document.getElementById('toggleSidebar');
      const overlay = document.getElementById('overlay');
      const navItems = document.querySelectorAll('.nav-item');
      const modules = document.querySelectorAll('.module');
      const pageTitle = document.getElementById('pageTitle');
      
      // Module titles mapping
      const moduleTitles = {
        'home': 'Inicio',
        'usuarios': 'Usuarios',
        'propietarios': 'Propietarios',
        'mascotas': 'Mascotas',
        'profesionales': 'Profesionales',
        'citas': 'Citas',
        'expedientes': 'Expedientes',
        'recetas': 'Recetas',
        'honorarios': 'Honorarios',
        'hospitalizaciones': 'Hospitalizaciones',
        'servicios': 'Catálogo de Servicios'
      };
      
      // Toggle sidebar
      toggleSidebar.addEventListener('click', function() {
        sidebar.classList.toggle('collapsed');
        
        // On mobile, show/hide overlay when sidebar is open
        if (window.innerWidth <= 1024) {
          if (!sidebar.classList.contains('collapsed')) {
            sidebar.classList.add('open');
            overlay.classList.add('active');
          } else {
            sidebar.classList.remove('open');
            overlay.classList.remove('active');
          }
        }
      });
      
      // Close sidebar when clicking on overlay (mobile)
      overlay.addEventListener('click', function() {
        sidebar.classList.remove('open');
        sidebar.classList.add('collapsed');
        overlay.classList.remove('active');
      });
      
      // Navigation between modules
      navItems.forEach(item => {
        item.addEventListener('click', function() {
          const target = this.getAttribute('data-target');
          
          // Update active nav item
          navItems.forEach(nav => nav.classList.remove('active'));
          this.classList.add('active');
          
          // Show target module
          modules.forEach(module => module.classList.remove('active'));
          document.getElementById(`mod-${target}`).classList.add('active');
          
          // Update page title
          pageTitle.textContent = moduleTitles[target];
          
          // On mobile, close sidebar after selection
          if (window.innerWidth <= 1024) {
            sidebar.classList.remove('open');
            sidebar.classList.add('collapsed');
            overlay.classList.remove('active');
          }
        });
      });
      
      // Handle window resize
      window.addEventListener('resize', function() {
        if (window.innerWidth > 1024) {
          sidebar.classList.remove('open');
          overlay.classList.remove('active');
        }
      });
      
      // Simulate loading data (for demo purposes)
      setTimeout(() => {
        // In a real application, you would fetch this data from your server
        const stats = {
          propietarios: 24,
          mascotas: 37,
          citasHoy: 5,
          hospitalizaciones: 2
        };
        
        document.querySelectorAll('.stat-value')[0].textContent = stats.propietarios;
        document.querySelectorAll('.stat-value')[1].textContent = stats.mascotas;
        document.querySelectorAll('.stat-value')[2].textContent = stats.citasHoy;
        document.querySelectorAll('.stat-value')[3].textContent = stats.hospitalizaciones;
      }, 1000);
    });