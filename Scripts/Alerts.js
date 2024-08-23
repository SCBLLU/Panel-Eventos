document.addEventListener('DOMContentLoaded', function () {
    const createEventForm = document.getElementById('createEventForm');
    const deleteEventForm = document.getElementById('deleteEventForm');
    const updateEventForm = document.getElementById('updateEventForm');

    if (createEventForm) {
        createEventForm.addEventListener('submit', function (e) {
            e.preventDefault();
            submitForm(this, 'crearEventoModal');
        });
    }

    if (deleteEventForm) {
        deleteEventForm.addEventListener('click', function (e) {
            e.preventDefault();
            // Aquí asumo que el formulario de eliminación tiene ID 'deleteEventForm'
            const form = document.getElementById('deleteEventForm');
            submitForm(form, 'eliminarEventoModal');
        });
    }

    if (updateEventForm) {
        updateEventForm.addEventListener('submit', function (e) {
            e.preventDefault();
            submitForm(this, 'actualizarEventoModal');
        });
    }

    function submitForm(form, modalId) {
        if (!(form instanceof HTMLFormElement)) {
            console.error('El argumento proporcionado no es un elemento de formulario válido.');
            return;
        }
        const formData = new FormData(form);
        fetch('./employee_dashboard.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                showAlert(data.success, data.message);
                if (data.success) {
                    form.reset();
                    const modal = bootstrap.Modal.getInstance(document.getElementById(modalId));
                    if (modal) {
                        modal.hide();
                    }
                }
            })
            .catch(error => {
                showAlert(false, 'Error de red al procesar el formulario.');
                console.error('Error al procesar el formulario:', error);
            });
    }

    function showAlert(success, message) {
        const alertContainer = document.getElementById('alertContainer');
        if (alertContainer) {
            alertContainer.innerHTML = `
                <div class="alert ${success ? 'alert-success' : 'alert-danger'} alert-dismissible fade show" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
        }
    }
});
