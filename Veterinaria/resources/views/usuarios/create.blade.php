<!-- Formulario para agregar usuario -->
<form id="userForm" method="POST" action="{{ route('usuarios.store') }}" class="user-form">
    @csrf
    <div class="form-row">
        <div class="form-group">
            <label for="nombre_usuario">Nombre de Usuario *</label>
            <input type="text" id="nombre_usuario" name="nombre_usuario" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="correo_electronico">Correo Electrónico *</label>
            <input type="email" id="correo_electronico" name="correo_electronico" class="form-control" required>
        </div>
    </div>
    
    <div class="form-row">
        <div class="form-group">
            <label for="password">Contraseña *</label>
            <input type="password" id="password" name="password" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="password_confirmation">Confirmar Contraseña *</label>
            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required>
        </div>
    </div>
    
    <div class="form-row">
        <div class="form-group">
            <label for="tipo_permiso">Rol *</label>
            <select id="tipo_permiso" name="tipo_permiso" class="form-control" required>
                <option value="">Seleccionar Rol</option>
                <option value="administrador">Administrador</option>
                <option value="medico">Médico</option>
                <option value="recepcionista">Recepcionista</option>
            </select>
        </div>
        <div class="form-group">
            <label for="estado">Estado *</label>
            <select id="estado" name="estado" class="form-control" required>
                <option value="activo">Activo</option>
                <option value="inactivo">Inactivo</option>
            </select>
        </div>
    </div>
    
    <div class="form-actions">
        <button type="submit" class="btn-primary">
            <i class="fas fa-save"></i> Guardar Usuario
        </button>
        <button type="button" class="btn-secondary" onclick="hideUserForm()">
            <i class="fas fa-times"></i> Cancelar
        </button>
    </div>
</form>