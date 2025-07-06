<?php
// Se define la clase que maneja el registro de reservas
class RegistroReservaController {

    // Método principal para crear una nueva reserva
    public function crear() {

        // Verifica que la solicitud sea de tipo POST (formulario enviado)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Captura los datos del formulario y elimina espacios al inicio y fin
            $zona = trim($_POST['zona']);         // Zona reservada (ej: Salón comunal)
            $fecha = trim($_POST['fecha']);       // Fecha de la reserva
            $horario = trim($_POST['horario']);   // Horario elegido por el residente
            $residente = trim($_POST['residente']); // Nombre o documento del residente

            // Validación básica: si alguno de los campos está vacío, se muestra un mensaje de error
            if (empty($zona) || empty($fecha) || empty($horario) || empty($residente)) {
                return "
                    <h4 style='color: red;'>Todos los campos son obligatorios.</h4>
                    <a href='javascript:history.back()'>Volver</a>
                ";
            }

            // Si todos los datos están presentes, se simula que la reserva fue registrada con éxito
            // (aquí normalmente se guardaría en una base de datos)
            return "
                <h3>✅ Reserva registrada con éxito</h3>
                <ul>
                  <li><strong>Zona:</strong> $zona</li>
                  <li><strong>Fecha:</strong> $fecha</li>
                  <li><strong>Horario:</strong> $horario</li>
                  <li><strong>Residente:</strong> $residente</li>
                </ul>
                <a href='../index.php'>Volver al inicio</a>
            ";
        } else {
            // Si el método de solicitud no es POST, se muestra un mensaje de acceso denegado
            return "<h4 style='color: red;'>Acceso no permitido</h4>";
        }
    }

    // Método adicional (opcional): puede usarse para listar reservas si se conecta a una base de datos
    public function index() {
        // En este ejemplo, solo devuelve un array vacío
        return [];
    }
}
