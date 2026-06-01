/**
 * Utilidades compartidas para validación inline en formularios de recepción.
 */
const FormValidation = {
    mostrarErrorCampo(fieldId, mensaje) {
        const input = document.getElementById(fieldId);
        const errorEl = document.getElementById('error-' + fieldId);

        if (input) {
            input.classList.add('is-invalid');
        }

        if (errorEl) {
            errorEl.textContent = mensaje;
            errorEl.style.display = 'block';
        }
    },

    limpiarErrorCampo(fieldId) {
        const input = document.getElementById(fieldId);
        const errorEl = document.getElementById('error-' + fieldId);

        if (input) {
            input.classList.remove('is-invalid');
        }

        if (errorEl) {
            errorEl.textContent = '';
            errorEl.style.display = 'none';
        }
    },

    limpiarErrores(fieldIds) {
        fieldIds.forEach((fieldId) => this.limpiarErrorCampo(fieldId));
    },
};
