<?php
session_start(); // Inicia la sesión para acceder a variables de usuario

require_once __DIR__ . '/../src/Config/database.php'; // Incluye la configuración y clase de conexión a la base de datos

use App\Config\Database; // Importa la clase Database del namespace correspondiente

// Validar sesión y permisos (solo admin y super admin pueden exportar)
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], [1, 2])) {
    header('Location: login.php');
    exit;
}

// Validar fechas recibidas por POST
$fecha_inicio = $_POST['fecha_inicio'] ?? '';
$fecha_fin = $_POST['fecha_fin'] ?? '';

// Si alguna fecha no fue enviada, termina el script con un mensaje
if (empty($fecha_inicio) || empty($fecha_fin)) {
    die('Las fechas son requeridas');
}

// Si la fecha final es menor que la inicial, termina el script con un mensaje
if ($fecha_fin < $fecha_inicio) {
    die('El rango de fechas no es válido');
}

try {
    // Conexión a la base de datos usando la clase Database
    $db = new Database();
    $conn = $db->getConnection();

    // Consulta SQL: obtiene visitas entre las fechas dadas, con datos de visitante, residente, casa, motivo y estado
    $sql = "SELECT
        v.fecha_ingreso,
        CONCAT(v.nombre, ' ', v.apellido) as visitante_nombre,
        CONCAT(u.nombre, ' ', u.apellido) as residente_nombre,
        u.direccion_casa,
        mv.motivo_visita as motivo_visita,
        v.hora_ingreso,
        v.id_estado
    FROM visitas v
    INNER JOIN usuarios u ON v.id_usuarios = u.documento
    INNER JOIN motivo_visita mv ON v.id_mot_visi = mv.id_mot_visi
    WHERE DATE(v.fecha_ingreso) BETWEEN :fecha_inicio AND :fecha_fin
    ORDER BY v.fecha_ingreso DESC";

    // Prepara y ejecuta la consulta con los parámetros de fecha
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':fecha_inicio', $fecha_inicio);
    $stmt->bindParam(':fecha_fin', $fecha_fin);
    $stmt->execute();
    $visitas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Si no hay resultados, termina el script con un mensaje
    if (empty($visitas)) {
        die('No hay visitas registradas en el rango de fechas seleccionado');
    }

    // Configura las cabeceras para descargar el archivo como CSV
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename=\"Reporte_Visitas_' . date('Y-m-d') . '.csv\"');

    $output = fopen('php://output', 'w');

    // Escribe el BOM para que Excel reconozca correctamente el UTF-8
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

    // Escribe la fila de encabezados en el archivo CSV
    fputcsv($output, [
        'Fecha',
        'Visitante',
        'Residente',
        'Casa',
        'Motivo',
        'Hora',
        'Estado'
    ]);

    // Escribe los datos de cada visita en el archivo CSV
    foreach ($visitas as $visita) {
        // Traduce el estado numérico a texto legible
        $estado = 'Desconocido';
        if ($visita['id_estado'] == 1) {
            $estado = 'Pendiente';
        } elseif ($visita['id_estado'] == 2) {
            $estado = 'Aprobada';
        }

        fputcsv($output, [
            date("d/m/Y", strtotime($visita['fecha_ingreso'])),
            $visita['visitante_nombre'],
            $visita['residente_nombre'],
            $visita['direccion_casa'],
            $visita['motivo_visita'],
            date('g:i a', strtotime($visita['hora_ingreso'])),
            $estado
        ]);
    }

    fclose($output); // Cierra el archivo de salida
    exit;

} catch (Exception $e) {
    // Si ocurre un error, muestra el mensaje de error
    die("Error en la base de datos: " . $e->getMessage());
}
?>
