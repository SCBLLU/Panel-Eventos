<?php
session_start();
if (!isset($_SESSION['nivel']) || $_SESSION['nivel'] != 'empleado') {
    header("Location: index.php");
    exit();
}

// Conexión a la base de datos
include './Database/Connection_sql/config.php';

class EmployeeDashboard
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    // Crear nuevo evento
    public function crearEvento($nombre, $fecha_inicio, $hora_inicio, $fecha_fin, $hora_fin, $lugar, $descripcion)
    {
        $estado = 'Pendiente'; // Estado predeterminado
        $sql = "INSERT INTO eventos (nombre, fecha_inicio, hora_inicio, fecha_fin, hora_fin, lugar, descripcion, estado) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        if ($stmt = $this->conn->prepare($sql)) {
            $stmt->bind_param('ssssssss', $nombre, $fecha_inicio, $hora_inicio, $fecha_fin, $hora_fin, $lugar, $descripcion, $estado);
            if ($stmt->execute()) {
                return true;
            } else {
                error_log('Error en ejecución: ' . $stmt->error);
                return false;
            }
        } else {
            error_log('Error en prepare: ' . $this->conn->error);
            return false;
        }
    }

    // Eliminar evento
    public function eliminarEvento($id)
    {
        $sql = "DELETE FROM eventos WHERE id = ?";
        if ($stmt = $this->conn->prepare($sql)) {
            $stmt->bind_param('i', $id);
            if ($stmt->execute()) {
                return true;
            } else {
                error_log('Error en ejecución: ' . $stmt->error);
                return false;
            }
        } else {
            error_log('Error en prepare: ' . $this->conn->error);
            return false;
        }
    }

    // Actualizar evento
    public function actualizarEvento($id, $nombre, $fecha_inicio, $hora_inicio, $fecha_fin, $hora_fin, $lugar, $descripcion, $estado)
    {
        $sql = "UPDATE eventos SET nombre = ?, fecha_inicio = ?, hora_inicio = ?, fecha_fin = ?, hora_fin = ?, lugar = ?, descripcion = ?, estado = ? WHERE id = ?";
        if ($stmt = $this->conn->prepare($sql)) {
            $stmt->bind_param('ssssssssi', $nombre, $fecha_inicio, $hora_inicio, $fecha_fin, $hora_fin, $lugar, $descripcion, $estado, $id);
            if ($stmt->execute()) {
                return true;
            } else {
                error_log('Error en ejecución: ' . $stmt->error);
                return false;
            }
        } else {
            error_log('Error en prepare: ' . $this->conn->error);
            return false;
        }
    }

    // Ver eventos
    public function verEventos()
    {
        $sql = "SELECT * FROM eventos";
        $result = $this->conn->query($sql);
        if ($result->num_rows > 0) {
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

$employeeDashboard = new EmployeeDashboard($conn);

// Manejo de solicitudes POST para crear, eliminar y actualizar eventos
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $response = array('success' => false, 'message' => 'Acción no reconocida.');

    switch ($_POST['action']) {
        case 'crear':
            $nombre = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_STRING);
            $fecha_inicio = filter_input(INPUT_POST, 'fecha_inicio', FILTER_SANITIZE_STRING);
            $hora_inicio = filter_input(INPUT_POST, 'hora_inicio', FILTER_SANITIZE_STRING);
            $fecha_fin = filter_input(INPUT_POST, 'fecha_fin', FILTER_SANITIZE_STRING);
            $hora_fin = filter_input(INPUT_POST, 'hora_fin', FILTER_SANITIZE_STRING);
            $lugar = filter_input(INPUT_POST, 'lugar', FILTER_SANITIZE_STRING);
            $descripcion = filter_input(INPUT_POST, 'descripcion', FILTER_SANITIZE_STRING);

            if ($employeeDashboard->crearEvento($nombre, $fecha_inicio, $hora_inicio, $fecha_fin, $hora_fin, $lugar, $descripcion)) {
                $response = array('success' => true, 'message' => 'Evento creado exitosamente.');
            } else {
                $response = array('success' => false, 'message' => 'Error al crear el evento.');
            }
            break;

        case 'eliminar':
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            if ($id && $employeeDashboard->eliminarEvento($id)) {
                $response = array('success' => true, 'message' => 'Evento eliminado exitosamente.');
            } else {
                $response = array('success' => false, 'message' => 'Error al eliminar el evento.');
            }
            break;

        case 'actualizar':
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            $nombre = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_STRING);
            $fecha_inicio = filter_input(INPUT_POST, 'fecha_inicio', FILTER_SANITIZE_STRING);
            $hora_inicio = filter_input(INPUT_POST, 'hora_inicio', FILTER_SANITIZE_STRING);
            $fecha_fin = filter_input(INPUT_POST, 'fecha_fin', FILTER_SANITIZE_STRING);
            $hora_fin = filter_input(INPUT_POST, 'hora_fin', FILTER_SANITIZE_STRING);
            $lugar = filter_input(INPUT_POST, 'lugar', FILTER_SANITIZE_STRING);
            $descripcion = filter_input(INPUT_POST, 'descripcion', FILTER_SANITIZE_STRING);
            $estado = filter_input(INPUT_POST, 'estado', FILTER_SANITIZE_STRING);

            if ($id && $employeeDashboard->actualizarEvento($id, $nombre, $fecha_inicio, $hora_inicio, $fecha_fin, $hora_fin, $lugar, $descripcion, $estado)) {
                $response = array('success' => true, 'message' => 'Evento actualizado exitosamente.');
            } else {
                $response = array('success' => false, 'message' => 'Error al actualizar el evento.');
            }
            break;
    }

    echo json_encode($response);
    exit();
}

// Obtener todos los eventos para el calendario
if (isset($_GET['action']) && $_GET['action'] == 'getEventos') {
    $eventos = $employeeDashboard->verEventos();
    echo json_encode(array_map(function ($evento) {
        return [
            'id' => $evento['id'],
            'title' => $evento['nombre'],
            'start' => $evento['fecha_inicio'] . 'T' . $evento['hora_inicio'],
            'end' => $evento['fecha_fin'] . 'T' . $evento['hora_fin'],
            'lugar' => $evento['lugar'],
            'descripcion' => $evento['descripcion'],
            'estado' => $evento['estado']
        ];
    }, $eventos));
    exit();
}

$conn->close();
?>




<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Panel de Empleado</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src='./Scripts/Calendar.js'></script>
    <script src="./Scripts/Events.js"></script>
</head>

<body>
    <div class="container mt-5">
        <a href="logout.php" class="btn btn-danger">Cerrar Sesión</a>

        <h1 class="mb-4">Panel de Empleado</h1>

        <!-- Alertas dinámicas -->
        <div id="alertContainer"></div>

        <!-- Botón para abrir el modal de crear evento -->
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#crearEventoModal">
            Crear Nuevo Evento
        </button>

        <!-- Modal para crear evento -->
        <div class="modal fade" id="crearEventoModal" tabindex="-1" aria-labelledby="crearEventoModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="crearEventoModalLabel">Crear Nuevo Evento</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="createEventForm">
                            <input type="hidden" name="action" value="crear">
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre</label>
                                <input type="text" id="nombre" name="nombre" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="fecha_inicio" class="form-label">Fecha de Inicio</label>
                                <input type="date" id="fecha_inicio" name="fecha_inicio" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="hora_inicio" class="form-label">Hora de Inicio</label>
                                <input type="time" id="hora_inicio" name="hora_inicio" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="fecha_fin" class="form-label">Fecha de Fin</label>
                                <input type="date" id="fecha_fin" name="fecha_fin" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="hora_fin" class="form-label">Hora de Fin</label>
                                <input type="time" id="hora_fin" name="hora_fin" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="lugar" class="form-label">Lugar</label>
                                <input type="text" id="lugar" name="lugar" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="descripcion" class="form-label">Descripción</label>
                                <textarea id="descripcion" name="descripcion" class="form-control" rows="3" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Crear Evento</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal para editar evento -->
        <div class="modal fade" id="eventoModal" tabindex="-1" aria-labelledby="eventoModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="eventoModalLabel">Detalles del Evento</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="updateEventForm">
                            <input type="hidden" id="eventoId">
                            <div class="mb-3">
                                <label for="editNombre" class="form-label">Nombre</label>
                                <input type="text" id="editNombre" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="editFechaInicio" class="form-label">Fecha de Inicio</label>
                                <input type="date" id="editFechaInicio" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="editHoraInicio" class="form-label">Hora de Inicio</label>
                                <input type="time" id="editHoraInicio" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="editFechaFin" class="form-label">Fecha de Fin</label>
                                <input type="date" id="editFechaFin" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="editHoraFin" class="form-label">Hora de Fin</label>
                                <input type="time" id="editHoraFin" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="editLugar" class="form-label">Lugar</label>
                                <input type="text" id="editLugar" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="editDescripcion" class="form-label">Descripción</label>
                                <textarea id="editDescripcion" class="form-control" rows="3"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="editEstado" class="form-label">Estado</label>
                                <select id="editEstado" class="form-select">
                                    <option value="Pendiente">Pendiente</option>
                                    <option value="Confirmado">Confirmado</option>
                                    <option value="En Progreso">En Progreso</option>
                                    <option value="Finalizado">Finalizado</option>
                                    <option value="Cancelado">Cancelado</option>
                                </select>
                            </div>
                            <button type="button" id="deleteEventButton" class="btn btn-danger">Eliminar Evento</button>
                            <button type="submit" class="btn btn-primary">Actualizar Evento</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>


        <!-- Mostrar eventos en el calendario -->
        <div id="calendar" class="mt-4"></div>


    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

</body>

</html>