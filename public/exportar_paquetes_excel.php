<?php
session_start(); // Inicia la sesión para acceder a variables de usuario

require_once __DIR__ . '/../src/Config/database.php'; // Incluye la configuración y clase de conexión a la base de datos

use App\Config\Database; // Importa la clase Database del namespace correspondiente

// Validar sesión y permisos (solo admin y super admin pueden exportar)
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], [1, 2])) {
    header('Location: login.php');
    exit;
}

// Obtiene los parámetros enviados por POST (fechas y tipos de paquetes a exportar)
$fecha_inicio = $_POST['fecha_inicio'] ?? '';
$fecha_fin = $_POST['fecha_fin'] ?? '';
$incluir_entregados = isset($_POST['incluir_entregados']) && $_POST['incluir_entregados'] === 'on';
$incluir_pendientes = isset($_POST['incluir_pendientes']) && $_POST['incluir_pendientes'] === 'on';

// Valida que las fechas hayan sido enviadas
if (empty($fecha_inicio) || empty($fecha_fin)) {
    die('Las fechas son requeridas');
}

// Valida que al menos un tipo de paquete haya sido seleccionado
if (!$incluir_entregados && !$incluir_pendientes) {
    die('Debe seleccionar al menos un tipo de paquete');
}

try {
    // Conexión a la base de datos usando la clase Database
    $db = new Database();
    $conn = $db->getConnection();

    // Consulta SQL para obtener los paquetes en el rango de fechas y con los estados seleccionados
    $sql = "SELECT
        p.id_paquete,
        p.descripcion,
        p.fech_hor_recep,
        p.fech_hor_entre,
        p.id_estado,
        CASE
            WHEN p.id_estado = 1 THEN 'Pendiente'
            WHEN p.id_estado = 2 THEN 'Entregado'
            ELSE 'Desconocido'
        END as estado_nombre,
        res.nombre as nombre_residente,
        res.apellido as apellido_residente,
        res.documento as documento_residente,
        vig.nombre as nombre_vigilante,
        vig.apellido as apellido_vigilante
    FROM paquetes p
    INNER JOIN usuarios res ON p.id_usuarios = res.documento
    INNER JOIN usuarios vig ON p.id_vigilante = vig.documento
    WHERE DATE(p.fech_hor_recep) BETWEEN :fecha_inicio AND :fecha_fin";

    // Construye la condición para filtrar por estados según los checkboxes seleccionados
    $estados = [];
    if ($incluir_entregados) $estados[] = 2; // Estado entregado
    if ($incluir_pendientes) $estados[] = 1; // Estado pendiente

    if (!empty($estados)) {
        $sql .= " AND p.id_estado IN (" . implode(",", $estados) . ")";
    }

    $sql .= " ORDER BY p.fech_hor_recep DESC";

    // Prepara y ejecuta la consulta
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':fecha_inicio', $fecha_inicio);
    $stmt->bindParam(':fecha_fin', $fecha_fin);
    $stmt->execute();
    $paquetes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Si no hay resultados, muestra un mensaje y termina la ejecución
    if (empty($paquetes)) {
        die('No hay paquetes registrados para el rango de fechas seleccionado');
    }

    // Configura las cabeceras para descargar el archivo como CSV
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="Reporte_Paquetes_' . date('Y-m-d') . '.csv"');

    $output = fopen('php://output', 'w');

    // Escribe el BOM para que Excel reconozca correctamente el UTF-8
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

    // Escribe la fila de encabezados en el archivo CSV
    fputcsv($output, [
        'ID',
        'Residente',
        'Documento Residente',
        'Vigilante',
        'Descripción',
        'Fecha Recepción',
        'Fecha Entrega',
        'Estado'
    ]);

    // Escribe los datos de cada paquete en el archivo CSV
    foreach ($paquetes as $paquete) {
        fputcsv($output, [
            $paquete['id_paquete'],
            $paquete['nombre_residente'] . ' ' . $paquete['apellido_residente'],
            $paquete['documento_residente'],
            $paquete['nombre_vigilante'] . ' ' . $paquete['apellido_vigilante'],
            $paquete['descripcion'],
            date('d/m/Y H:i', strtotime($paquete['fech_hor_recep'])),
            $paquete['fech_hor_entre'] ? date('d/m/Y H:i', strtotime($paquete['fech_hor_entre'])) : 'Pendiente',
            $paquete['estado_nombre']
        ]);
    }

    fclose($output); // Cierra el archivo de salida
    exit;

} catch (PDOException $e) {
    // Si ocurre un error de base de datos, muestra el mensaje de error
    die("Error en la base de datos: " . $e->getMessage());
}
?>