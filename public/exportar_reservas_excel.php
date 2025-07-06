exportar_reservas_excel
 
<?php
session_start(); // Inicia la sesión para acceder a variables de usuario

require_once __DIR__ . '/../src/Config/database.php'; // Incluye la configuración y clase de conexión a la base de datos

use App\Config\Database; // Importa la clase Database del namespace correspondiente

// Validar sesión y permisos (solo admin y super admin pueden exportar)
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], [1, 2])) {
    header('Location: login.php');
    exit;
}

// Obtiene los parámetros enviados por POST (mes, año y si incluir rechazadas)
$mes = $_POST['mes'] ?? date('n'); // Si no se envía, usa el mes actual
$anio = $_POST['anio'] ?? date('Y'); // Si no se envía, usa el año actual
$incluir_rechazadas = isset($_POST['incluir_rechazadas']) && $_POST['incluir_rechazadas'] === 'on';

try {
    // Conexión a la base de datos usando la clase Database
    $db = new Database();
    $conn = $db->getConnection();

    // Consulta SQL para obtener reservas del mes y año seleccionados
    $sql = "SELECT
        r.id_reservas,
        r.fecha,
        r.fecha_apro,
        r.observaciones,
        zc.nombre_zona as nombre_zona,
        h.horario,
        u.nombre as nombre_residente,
        u.apellido as apellido_residente,
        u.documento as documento_residente,
        mz.motivo_zonas as motivo_zonas,
        CASE r.id_estado
            WHEN 1 THEN 'Pendiente'
            WHEN 2 THEN 'Aprobada'
            WHEN 3 THEN 'Rechazada'
            ELSE 'Desconocido'
        END as estado,
        admin.nombre as nombre_administrador,
        admin.apellido as apellido_administrador
    FROM reservas r
    INNER JOIN zonas_comunes zc ON r.id_zonas_comu = zc.id_zonas_comu
    INNER JOIN usuarios u ON r.id_usuarios = u.documento
    INNER JOIN horario h ON r.id_horario = h.id_horario
    LEFT JOIN motivo_zonas mz ON r.id_mot_zonas = mz.id_mot_zonas
    LEFT JOIN usuarios admin ON r.id_administrador = admin.documento
    WHERE MONTH(r.fecha) = :mes
    AND YEAR(r.fecha) = :anio";

    // Si no se deben incluir rechazadas, agrega condición para excluirlas
    if (!$incluir_rechazadas) {
        $sql .= " AND r.id_estado != 3";
    }

    $sql .= " ORDER BY r.fecha, h.horario";

    // Prepara y ejecuta la consulta
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':mes', $mes, PDO::PARAM_INT);
    $stmt->bindParam(':anio', $anio, PDO::PARAM_INT);
    $stmt->execute();
    $reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Si no hay resultados, muestra un mensaje y termina la ejecución
    if (empty($reservas)) {
        die('No hay reservas registradas para el mes seleccionado');
    }

    // Configura las cabeceras para descargar el archivo como CSV
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="Reporte_Reservas_' . $mes . '_' . $anio . '.csv"');

    $output = fopen('php://output', 'w');

    // Escribe el BOM para que Excel reconozca correctamente el UTF-8
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

    // Escribe la fila de encabezados en el archivo CSV
    fputcsv($output, [
        'Fecha',
        'Zona',
        'Horario',
        'Residente',
        'Documento',
        'Motivo',
        'Estado',
        'Fecha Aprobación',
        'Administrador',
        'Observaciones'
    ]);

    // Escribe los datos de cada reserva en el archivo CSV
    foreach ($reservas as $reserva) {
        fputcsv($output, [
            date('d/m/Y', strtotime($reserva['fecha'])),
            $reserva['nombre_zona'],
            $reserva['horario'],
            $reserva['nombre_residente'] . ' ' . $reserva['apellido_residente'],
            $reserva['documento_residente'],
            $reserva['motivo_zonas'],
            $reserva['estado'],
            $reserva['fecha_apro'] ? date('d/m/Y', strtotime($reserva['fecha_apro'])) : 'N/A',
            $reserva['nombre_administrador'] ? $reserva['nombre_administrador'] . ' ' . $reserva['apellido_administrador'] : 'N/A',
            $reserva['observaciones'] ?: 'N/A'
        ]);
    }

    fclose($output); // Cierra el archivo de salida
    exit;

} catch (PDOException $e) {
    // Si ocurre un error de base de datos, muestra el mensaje de error
    die("Error en la base de datos: " . $e->getMessage());
}
?>