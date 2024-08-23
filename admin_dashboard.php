<?php
session_start();
if (!isset($_SESSION['nivel']) || $_SESSION['nivel'] != 'administrador') {
    header("Location: login.php");
    exit();
}

// Conexión a la base de datos
include './Database/Connection_sql/config.php';

class AdminDashboard
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    // Ver todos los eventos
    public function verEventos()
    {
        $sql = "SELECT * FROM eventos";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Ver todos los empleados
    public function verEmpleados()
    {
        $sql = "SELECT * FROM empleados";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Agregar empleado
    public function agregarEmpleado($nombre, $correo, $rol, $contraseña)
    {
        $hashPassword = $contraseña;
        $sql = "INSERT INTO empleados (nombre, correo, rol, contraseña) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('ssss', $nombre, $correo, $rol, $hashPassword);
        return $stmt->execute();
    }

    // Modificar empleado
    public function modificarEmpleado($id, $nombre, $correo, $rol)
    {
        $sql = "UPDATE empleados SET nombre = ?, correo = ?, rol = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('sssi', $nombre, $correo, $rol, $id);
        return $stmt->execute();
    }

    // Restablecer contraseña de empleado
    public function restablecerContraseña($id, $nuevaContraseña)
    {
        $hashPassword = password_hash($nuevaContraseña, PASSWORD_DEFAULT);
        $sql = "UPDATE empleados SET contraseña = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('si', $hashPassword, $id);
        return $stmt->execute();
    }

    // Eliminar empleado
    public function eliminarEmpleado($id)
    {
        $sql = "DELETE FROM empleados WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }

    // Eliminar evento
    public function eliminarEvento($id)
    {
        $sql = "DELETE FROM eventos WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }

    // Modificar evento
    public function modificarEvento($id, $nombre, $fecha, $hora, $lugar, $descripcion)
    {
        $sql = "UPDATE eventos SET nombre = ?, fecha = ?, hora = ?, lugar = ?, descripcion = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('sssssi', $nombre, $fecha, $hora, $lugar, $descripcion, $id);
        return $stmt->execute();
    }
}

$adminDashboard = new AdminDashboard($conn);

// Manejo de solicitudes POST para agregar, modificar o eliminar empleados y eventos
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'agregar_empleado':
                $nombre = $_POST['nombre'];
                $correo = $_POST['correo'];
                $rol = $_POST['rol'];
                $contraseña = $_POST['contraseña'];
                if ($adminDashboard->agregarEmpleado($nombre, $correo, $rol, $contraseña)) {
                    $mensaje = "Empleado agregado exitosamente.";
                } else {
                    $mensaje = "Error al agregar el empleado.";
                }
                break;
            case 'modificar_empleado':
                $id = $_POST['id'];
                $nombre = $_POST['nombre'];
                $correo = $_POST['correo'];
                $rol = $_POST['rol'];
                if ($adminDashboard->modificarEmpleado($id, $nombre, $correo, $rol)) {
                    $mensaje = "Empleado modificado exitosamente.";
                } else {
                    $mensaje = "Error al modificar el empleado.";
                }
                break;
            case 'restablecer_contraseña':
                $id = $_POST['id'];
                $nuevaContraseña = $_POST['nuevaContraseña'];
                if ($adminDashboard->restablecerContraseña($id, $nuevaContraseña)) {
                    $mensaje = "Contraseña restablecida exitosamente.";
                } else {
                    $mensaje = "Error al restablecer la contraseña.";
                }
                break;
            case 'eliminar_empleado':
                $id = $_POST['id'];
                if ($adminDashboard->eliminarEmpleado($id)) {
                    $mensaje = "Empleado eliminado exitosamente.";
                } else {
                    $mensaje = "Error al eliminar el empleado.";
                }
                break;
            case 'eliminar_evento':
                $id = $_POST['id'];
                if ($adminDashboard->eliminarEvento($id)) {
                    $mensaje = "Evento eliminado exitosamente.";
                } else {
                    $mensaje = "Error al eliminar el evento.";
                }
                break;
            case 'modificar_evento':
                $id = $_POST['id'];
                $nombre = $_POST['nombre'];
                $fecha = $_POST['fecha'];
                $hora = $_POST['hora'];
                $lugar = $_POST['lugar'];
                $descripcion = $_POST['descripcion'];
                if ($adminDashboard->modificarEvento($id, $nombre, $fecha, $hora, $lugar, $descripcion)) {
                    $mensaje = "Evento modificado exitosamente.";
                } else {
                    $mensaje = "Error al modificar el evento.";
                }
                break;
        }
    }
}

// Ejemplo de visualización de datos:
$empleados = $adminDashboard->verEmpleados();
$eventos = $adminDashboard->verEventos();

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Panel de Administrador</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
    <div class="container mt-5">
        <a href="logout.php" class="btn btn-danger">Cerrar Sesión</a>
        <h1 class="mb-4">Panel de Administrador</h1>

        <!-- Mensaje de éxito o error -->
        <?php if (isset($mensaje)): ?>
            <div class="alert alert-info" role="alert">
                <?php echo htmlspecialchars($mensaje); ?>
            </div>
        <?php endif; ?>

        <!-- Sección para ver empleados -->
        <h2>Empleados</h2>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Rol</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($empleados as $empleado): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($empleado['id']); ?></td>
                            <td><?php echo htmlspecialchars($empleado['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($empleado['correo']); ?></td>
                            <td><?php echo htmlspecialchars($empleado['rol']); ?></td>
                            <td>
                                <!-- Botones de acciones -->
                                <a href="modificar_empleado.php?id=<?php echo $empleado['id']; ?>" class="btn btn-warning btn-sm">Modificar</a>
                                <a href="restablecer_contraseña.php?id=<?php echo $empleado['id']; ?>" class="btn btn-info btn-sm">Restablecer Contraseña</a>
                                <form method="post" action="" style="display:inline;">
                                    <input type="hidden" name="action" value="eliminar_empleado">
                                    <input type="hidden" name="id" value="<?php echo $empleado['id']; ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Sección para ver eventos -->
        <h2>Eventos</h2>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Lugar</th>
                        <th>Descripción</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($eventos as $evento): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($evento['id']); ?></td>
                            <td><?php echo htmlspecialchars($evento['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($evento['fecha']); ?></td>
                            <td><?php echo htmlspecialchars($evento['hora']); ?></td>
                            <td><?php echo htmlspecialchars($evento['lugar']); ?></td>
                            <td><?php echo htmlspecialchars($evento['descripcion']); ?></td>
                            <td>
                                <!-- Botones de acciones -->
                                <a href="modificar_evento.php?id=<?php echo $evento['id']; ?>" class="btn btn-warning btn-sm">Modificar</a>
                                <form method="post" action="" style="display:inline;">
                                    <input type="hidden" name="action" value="eliminar_evento">
                                    <input type="hidden" name="id" value="<?php echo $evento['id']; ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Sección para agregar empleados -->
        <div class="mt-4">
            <h3>Agregar Empleado</h3>
            <form method="post" action="">
                <input type="hidden" name="action" value="agregar_empleado">
                <div class="form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" id="nombre" name="nombre" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="correo">Correo:</label>
                    <input type="email" id="correo" name="correo" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="rol">Rol:</label>
                    <select id="rol" name="rol" class="form-control" required>
                        <option value="administrador">Administrador</option>
                        <option value="empleado">Empleado</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="contraseña">Contraseña:</label>
                    <input type="password" id="contraseña" name="contraseña" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Agregar Empleado</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

</body>

</html>