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
      
      // Navigation between modules (client-side: prevent full page navigation only for SPA targets)
      navItems.forEach(item => {
        item.addEventListener('click', function(event) {
          const target = this.getAttribute('data-target');

          // If there's no data-target, allow normal anchor navigation (do not preventDefault)
          if (!target) return; // nothing to do

          // Prevent default only when we handle SPA-style switching
          event.preventDefault();

          // Update active nav item
          navItems.forEach(nav => nav.classList.remove('active'));
          this.classList.add('active');

          // Show target module
          modules.forEach(module => module.classList.remove('active'));
          const targetEl = document.getElementById(`mod-${target}`);
          if (targetEl) targetEl.classList.add('active');

          // Update page title
          pageTitle.textContent = moduleTitles[target] || pageTitle.textContent;

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

      // Funcionalidades específicas para módulo de médicos
function initModuloMedicos() {
    const btnNuevoMedico = document.getElementById('btn-nuevo-medico');
    if (btnNuevoMedico) {
        btnNuevoMedico.addEventListener('click', abrirModalNuevoMedico);
    }

    // Filtros de búsqueda
    const searchInput = document.getElementById('search-medicos');
    const filterEspecialidad = document.getElementById('filter-especialidad');
    const filterEstado = document.getElementById('filter-estado');

    if (searchInput) {
        searchInput.addEventListener('input', filtrarMedicos);
    }
    if (filterEspecialidad) {
        filterEspecialidad.addEventListener('change', filtrarMedicos);
    }
    if (filterEstado) {
        filterEstado.addEventListener('change', filtrarMedicos);
    }
}

});

function abrirModalNuevoMedico() {
    const modal = document.getElementById('modal-medico');
    const titulo = document.getElementById('modal-medico-titulo');
    const form = document.getElementById('form-medico');
    
    titulo.textContent = 'Nuevo Médico';
    form.reset();
    modal.style.display = 'flex';
}

function cerrarModalMedico() {
    const modal = document.getElementById('modal-medico');
    modal.style.display = 'none';
}

function guardarMedico() {
    const form = document.getElementById('form-medico');
    const formData = new FormData(form);
    
    // Aquí iría la lógica para guardar el médico
    console.log('Guardando médico:', Object.fromEntries(formData));
    
    // Simulación de guardado exitoso
    alert('Médico guardado correctamente');
    cerrarModalMedico();
    // Recargar la tabla de médicos
}

function editarMedico(id) {
    const modal = document.getElementById('modal-medico');
    const titulo = document.getElementById('modal-medico-titulo');
    const form = document.getElementById('form-medico');
    
    titulo.textContent = 'Editar Médico';
    
    // Aquí cargaríamos los datos del médico según el ID
    // Por ahora simulamos datos
    document.getElementById('medico-nombre').value = 'Dra. Laura Méndez';
    document.getElementById('medico-especialidad').value = 'cirugia';
    document.getElementById('medico-email').value = 'laura.mendez@vetclinic.com';
    document.getElementById('medico-telefono').value = '+1 234 567 890';
    document.getElementById('medico-cedula').value = 'CP123456';
    document.getElementById('medico-estado').value = 'activo';
    
    modal.style.display = 'flex';
}

function gestionarHorarios(id) {
    alert(`Gestionar horarios del médico ID: ${id}`);
    // Aquí se abriría otro modal o se redirigiría a la gestión de horarios
}

function filtrarMedicos() {
    const searchTerm = document.getElementById('search-medicos').value.toLowerCase();
    const especialidad = document.getElementById('filter-especialidad').value;
    const estado = document.getElementById('filter-estado').value;
    
    const filas = document.querySelectorAll('#tabla-medicos tr');
    
    filas.forEach(fila => {
        const textoFila = fila.textContent.toLowerCase();
        const especialidadFila = fila.querySelector('.specialty-badge')?.textContent.toLowerCase() || '';
        const estadoFila = fila.querySelector('.status-badge')?.textContent.toLowerCase() || '';
        
        const coincideBusqueda = textoFila.includes(searchTerm);
        const coincideEspecialidad = !especialidad || especialidadFila.includes(especialidad);
        const coincideEstado = !estado || estadoFila.includes(estado);
        
        if (coincideBusqueda && coincideEspecialidad && coincideEstado) {
            fila.style.display = '';
        } else {
            fila.style.display = 'none';
        }
    });
}

// Inicializar módulo de médicos cuando se muestre
// Inicializar módulos específicos cuando se muestren
const observer = new MutationObserver(function(mutations) {
  mutations.forEach(function(mutation) {
    if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
      if (mutation.target.id === 'mod-medicos' && mutation.target.classList.contains('active')) {
        initModuloMedicos();
      }
    }
  });
});

const modMedicosEl = document.getElementById('mod-medicos');
if (modMedicosEl) {
  observer.observe(modMedicosEl, {
    attributes: true,
    attributeFilter: ['class']
  });
}