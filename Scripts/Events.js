document.addEventListener('DOMContentLoaded', function () {
    var calendarEl = document.getElementById('calendar');

    var calendar = new FullCalendar.Calendar(calendarEl, {
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
        },
        initialDate: new Date(),
        navLinks: true,
        businessHours: true,
        editable: true,
        selectable: true,
        events: function (fetchInfo, successCallback, failureCallback) {
            fetch('./employee_dashboard.php?action=getEventos')
                .then(response => response.json())
                .then(data => successCallback(data))
                .catch(error => {
                    showAlert(false, 'Error al cargar eventos.');
                    failureCallback(error);
                });
        },
        eventDrop: function (info) {
            updateEvent(info.event);
        },
        eventClick: function (info) {
            showEventModal(info.event);
        }
    });

    calendar.render();

    function updateEvent(event) {
        // Verifica los datos antes de enviarlos
        console.log({
            id: event.id,
            nombre: event.title,
            fecha_inicio: event.startStr.split('T')[0],
            hora_inicio: event.startStr.split('T')[1].split('Z')[0],
            fecha_fin: event.endStr.split('T')[0],
            hora_fin: event.endStr.split('T')[1].split('Z')[0],
            lugar: event.extendedProps.lugar,
            descripcion: event.extendedProps.descripcion,
            estado: event.extendedProps.estado
        });

        fetch('./employee_dashboard.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams({
                action: 'actualizar',
                id: event.id,
                nombre: event.title,
                fecha_inicio: event.startStr.split('T')[0],
                hora_inicio: event.startStr.split('T')[1].split('Z')[0],
                fecha_fin: event.endStr.split('T')[0],
                hora_fin: event.endStr.split('T')[1].split('Z')[0],
                lugar: event.extendedProps.lugar,
                descripcion: event.extendedProps.descripcion,
                estado: event.extendedProps.estado
            })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert(true, 'Evento actualizado correctamente.');
                } else {
                    showAlert(false, 'Error al actualizar el evento: ' + data.message);
                }
            })
            .catch(error => {
                showAlert(false, 'Error al actualizar el evento.');
                console.error('Error al actualizar el evento:', error);
            });
    }

    function showEventModal(event) {
        var modal = new bootstrap.Modal(document.getElementById('eventoModal'));
        document.getElementById('eventoId').value = event.id;
        document.getElementById('editNombre').value = event.title;

        let startDate = new Date(event.startStr);
        let endDate = new Date(event.endStr);

        document.getElementById('editFechaInicio').value = startDate.toISOString().split('T')[0];
        document.getElementById('editHoraInicio').value = formatTime(startDate);
        document.getElementById('editFechaFin').value = endDate.toISOString().split('T')[0];
        document.getElementById('editHoraFin').value = formatTime(endDate);

        document.getElementById('editLugar').value = event.extendedProps.lugar;
        document.getElementById('editDescripcion').value = event.extendedProps.descripcion;
        document.getElementById('editEstado').value = event.extendedProps.estado;

        modal.show();
    }

    document.getElementById('deleteEventButton').addEventListener('click', function () {
        var eventId = document.getElementById('eventoId').value;
        fetch('./employee_dashboard.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams({
                action: 'eliminar',
                id: eventId
            })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    calendar.getEventById(eventId).remove();
                    var modal = bootstrap.Modal.getInstance(document.getElementById('eventoModal'));
                    modal.hide();
                    showAlert(true, 'Evento eliminado correctamente.');
                } else {
                    showAlert(false, 'Error al eliminar el evento.');
                }
            })
            .catch(error => {
                showAlert(false, 'Error de red al eliminar el evento.');
                console.error('Error al eliminar el evento:', error);
            });
    });

    document.getElementById('createEventForm').addEventListener('submit', function (event) {
        event.preventDefault();
        var formData = new FormData(this);
        formData.append('action', 'crear');

        fetch('./employee_dashboard.php', {
            method: 'POST',
            body: new URLSearchParams(formData)
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert(true, 'Evento creado exitosamente.');
                    calendar.refetchEvents();
                    var modal = bootstrap.Modal.getInstance(document.getElementById('crearEventoModal'));
                    modal.hide();
                    this.reset();
                } else {
                    showAlert(false, 'Error al crear el evento.');
                }
            })
            .catch(error => {
                showAlert(false, 'Error de red al crear el evento.');
                console.error('Error al crear el evento:', error);
            });
    });

    function formatDate(dateString) {
        return new Date(dateString).toISOString().split('T')[0];
    }

    function formatTime(date) {
        let d = new Date(date);
        return d.toTimeString().split(' ')[0].split(':').slice(0, 2).join(':');
    }

    function showAlert(success, message, duration = 10000) {
        const alertContainer = document.getElementById('alertContainer');
        if (alertContainer) {
            alertContainer.innerHTML = `
                <div class="alert ${success ? 'alert-success' : 'alert-danger'} alert-dismissible fade show" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;

            // Eliminar la alerta después de la duración especificada
            setTimeout(() => {
                alertContainer.innerHTML = '';
            }, duration);
        }
    }
});
