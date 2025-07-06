<?php
// Declaramos que este archivo trabaja dentro del espacio de nombres App\Config
namespace App\Config;

// Importamos las clases PDO y PDOException desde PHP para manejar conexión y errores
use PDO;
use PDOException;

// Definimos una clase llamada Database
class Database {
    // Propiedad privada que almacena el nombre del host (servidor de base de datos)
    private $host = 'localhost';

    // Propiedad privada que indica el nombre de la base de datos a usar
    private $db_name = 'resicontrol2';

    // Propiedad privada con el nombre de usuario de la base de datos
    private $username = 'root'; // Cambiar si tu usuario no es "root"

    // Propiedad privada con la contraseña del usuario de la base de datos
    private $password = ''; // Cambiar si tienes una contraseña configurada

    // Propiedad pública donde se guardará la conexión una vez se establezca
    public $conn;

    // Método público que se encarga de crear y devolver la conexión a la base de datos
    public function getConnection() {
        // Inicializa la variable de conexión en null
        $this->conn = null;

        // Intentamos establecer la conexión
        try {
            // Creamos una nueva instancia de PDO con los datos de conexión
            $this->conn = new PDO(
                "mysql:host={$this->host};dbname={$this->db_name};charset=utf8mb4", // Cadena de conexión
                $this->username, // Usuario de la base de datos
                $this->password  // Contraseña del usuario
            );

            // Configuramos PDO para que lance excepciones cuando ocurra un error
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        } catch(PDOException $exception) {
            // Si ocurre un error, detenemos el script y mostramos el mensaje de error
            die("Error de conexión: " . $exception->getMessage());
        }

        // Retornamos el objeto de conexión PDO para usarlo en otros archivos
        return $this->conn;
    }
}
