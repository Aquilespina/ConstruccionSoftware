// Gestión de Usuarios - JavaScript específico
class UserManager {
    constructor() {
        this.init();
    }

    init() {
        console.log('UserManager inicializado'); // Para debug
        this.loadUsers();
        this.setupEventListeners();
    }

    setupEventListeners() {
        // Manejar envío del formulario
        const userForm = document.getElementById('userForm');
        if (userForm) {
            userForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.addUser();
            });
        }
    }

    // Cargar usuarios
    async loadUsers() {
        console.log('Cargando usuarios...'); // Para debug
        
        try {
            const response = await fetch('/usuarios', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            console.log('Respuesta del servidor:', response); // Para debug

            if (!response.ok) {
                throw new Error(`Error HTTP: ${response.status}`);
            }

            const data = await response.json();
            console.log('Datos recibidos:', data); // Para debug
            
            this.renderUsers(data);
        } catch (error) {
            console.error('Error al cargar usuarios:', error);
            this.showMessage('Error al cargar los usuarios: ' + error.message, 'error');
            this.renderUsers([]); // Mostrar tabla vacía en caso de error
        }
    }

    // Renderizar usuarios en la tabla
    renderUsers(users) {
        const tbody = document.getElementById('usersTableBody');
        
        if (!users || users.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" style="text-align: center;">No hay usuarios registrados</td></tr>';
            return;
        }

        tbody.innerHTML = users.map(user => {
            // Usar las propiedades de la tabla 'usuario'
            const userName = user.nombre_usuario || 'Sin nombre';
            const userEmail = user.correo_electronico;
            const userRole = user.tipo_permiso || 'Sin rol';
            const userStatus = user.estado; // Ya es string gracias al accessor
            // Usar la fecha correcta
            const userDate = user.fecha_creacion || user.created_at;

            return `
                <tr>
                    <td>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <div class="user-avatar" style="width: 32px; height: 32px; font-size: 12px;">
                                ${this.getInitials(userName)}
                            </div>
                            ${userName}
                        </div>
                    </td>
                    <td>${userEmail}</td>
                    <td>${this.getRoleText(userRole)}</td>
                    <td>${this.formatDate(userDate)}</td>
                    <td>
                        <span class="status-badge ${userStatus === 'activo' ? 'status-active' : 'status-inactive'}">
                            ${userStatus}
                        </span>
                    </td>
                    <td>
                        <button class="btn-outline" style="padding: 4px 8px; font-size: 12px;" 
                                onclick="userManager.editUser(${user.id_usuario})">Editar</button>
                        <button class="btn-secondary" style="padding: 4px 8px; font-size: 12px;" 
                                onclick="userManager.deleteUser(${user.id_usuario})">Eliminar</button>
                    </td>
                </tr>
            `;
        }).join('');
    }

    // Agregar usuario
    async addUser() {
        const form = document.getElementById('userForm');
        const formData = new FormData(form);

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                }
            });

            const data = await response.json();

            if (response.ok) {
                this.showMessage(data.message, 'success');
                form.reset();
                this.loadUsers(); // Recargar la tabla
                this.hideUserForm();
            } else {
                let errorMessage = 'Errores en el formulario: ';
                if (data.errors) {
                    for (const field in data.errors) {
                        errorMessage += data.errors[field].join(', ') + ' ';
                    }
                } else {
                    errorMessage = data.message || 'Error al agregar el usuario';
                }
                this.showMessage(errorMessage, 'error');
            }
        } catch (error) {
            console.error('Error al agregar usuario:', error);
            this.showMessage('Error al agregar el usuario', 'error');
        }
    }

    // Mostrar mensajes
    showMessage(message, type) {
        const container = document.getElementById('messageContainer');
        const alertClass = type === 'success' ? 'alert-success' : 'alert-error';
        
        container.innerHTML = `<div class="alert ${alertClass}">${message}</div>`;
        
        // Ocultar mensaje después de 5 segundos
        setTimeout(() => {
            container.innerHTML = '';
        }, 5000);
    }

    // Funciones auxiliares
    getInitials(name) {
        if (!name) return '??';
        return name.split(' ')
            .map(part => part.charAt(0))
            .join('')
            .toUpperCase()
            .substring(0, 2);
    }

    formatDate(dateString) {
        if (!dateString) return 'N/A';
        
        try {
            const options = { day: 'numeric', month: 'short', year: 'numeric' };
            const date = new Date(dateString);
            return date.toLocaleDateString('es-ES', options);
        } catch (error) {
            return 'Fecha inválida';
        }
    }

    getRoleText(role) {
        const roles = {
            'administrador': 'Administrador',
            'medico': 'Médico',
            'recepcionista': 'Recepcionista'
        };
        return roles[role] || role;
    }

    // Funciones de UI
    showUserForm() {
        document.getElementById('userFormContainer').style.display = 'block';
    }

    hideUserForm() {
        document.getElementById('userFormContainer').style.display = 'none';
        document.getElementById('messageContainer').innerHTML = '';
    }

    // Placeholder para editar y eliminar
    editUser(id) {
        alert(`Editar usuario con ID: ${id}`);
        // Implementar lógica de edición
    }

    async deleteUser(id) {
        if (confirm('¿Estás seguro de que deseas eliminar este usuario?')) {
            try {
                // Implementar lógica de eliminación
                const response = await fetch(`/usuarios/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (response.ok) {
                    this.showMessage('Usuario eliminado correctamente', 'success');
                    this.loadUsers();
                } else {
                    this.showMessage('Error al eliminar el usuario', 'error');
                }
            } catch (error) {
                console.error('Error al eliminar usuario:', error);
                this.showMessage('Error al eliminar el usuario', 'error');
            }
        }
    }
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM cargado, inicializando UserManager...'); // Para debug
    window.userManager = new UserManager();
});

// Funciones globales para acceso desde HTML
function showUserForm() {
    if (window.userManager) {
        window.userManager.showUserForm();
    }
}

function hideUserForm() {
    if (window.userManager) {
        window.userManager.hideUserForm();
    }
}