<?php
// Inicia una sesión para poder utilizar variables de sesión como mensajes
session_start();

// Incluye la clase que maneja la conexión a la base de datos
require_once __DIR__ . '/../config/Database.php';

// Usa el espacio de nombres correcto para acceder a la clase Database
use App\Config\Database;

// Se define la clase del controlador llamada TerrenoController
class TerrenoController {
    
    // Método público para registrar un terreno (bloque o manzana)
    public function registrar() {

        // Verifica que el formulario haya sido enviado por el método POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            // Obtiene el tipo de terreno enviado desde el formulario (bloque o manzana)
            $tipo_terreno = $_POST['tipo_terreno'];

            // Crea una instancia de la base de datos y obtiene la conexión
            $db = new Database();
            $conn = $db->getConnection();

            // Si el tipo de terreno es "bloque", procede a registrar un bloque
            if ($tipo_terreno === 'bloque') {

                // Obtiene el número de apartamentos desde el formulario
                $cantidad_apartamentos = $_POST['apartamentos'];

                // Consulta SQL para insertar un nuevo bloque
                $sql = "INSERT INTO bloque (cantidad_apartamentos) VALUES (:cantidad_apartamentos)";

                // Prepara la sentencia
                $stmt = $conn->prepare($sql);

                // Enlaza el parámetro :cantidad_apartamentos con el valor recibido
                $stmt->bindParam(':cantidad_apartamentos', $cantidad_apartamentos);

                // Ejecuta la consulta e informa mediante mensaje si fue exitoso o no
                if ($stmt->execute()) {
                    $_SESSION['mensaje'] = "✅ ¡Bloque registrado exitosamente!";
                } else {
                    $_SESSION['mensaje'] = "❌ Error al registrar el bloque.";
                }

            } 
            // Si el tipo es "manzana", procede a registrar una manzana
            elseif ($tipo_terreno === 'manzana') {

                // Obtiene el número de casas desde el formulario
                $cantidad_casas = $_POST['casas'];

                // Consulta SQL para insertar una nueva manzana
                $sql = "INSERT INTO manzana (cantidad_casas) VALUES (:cantidad_casas)";

                // Prepara la consulta
                $stmt = $conn->prepare($sql);

                // Enlaza el parámetro con el valor recibido
                $stmt->bindParam(':cantidad_casas', $cantidad_casas);

                // Ejecuta e informa si tuvo éxito
                if ($stmt->execute()) {
                    $_SESSION['mensaje'] = "✅ ¡Manzana registrada exitosamente!";
                } else {
                    $_SESSION['mensaje'] = "❌ Error al registrar la manzana.";
                }

            } 
            // Si el tipo de terreno no es válido
            else {
                $_SESSION['mensaje'] = "⚠️ Tipo de terreno no válido.";
            }

            // Redirige al formulario de registro de terreno, mostrando el mensaje de sesión
            header("Location: /registro_terreno.php");
            exit();
        }
    }
}

// Crea una instancia del controlador y ejecuta el método registrar (si es un POST)
$controller = new TerrenoController();
$controller->registrar();
