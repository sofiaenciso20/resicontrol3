<?php

// Se importa la clase Database del namespace App\Config
use App\Config\Database;

// Declaración de la clase controladora encargada de la autenticación
class AuthController {

    // Método público para iniciar sesión. Recibe el correo y la contraseña del usuario.
    public function login($email, $password) {

        // --------------------------------------------
        // Establecer conexión a la base de datos
        // --------------------------------------------

        // Se crea una nueva instancia de la clase Database y se obtiene la conexión PDO
        $db = (new Database())->getConnection();

        // --------------------------------------------
        // Consulta del usuario por su correo
        // --------------------------------------------

        // Se prepara una consulta SQL segura para obtener un usuario por su correo
        $stmt = $db->prepare("SELECT * FROM usuarios WHERE correo = ? LIMIT 1");

        // Se ejecuta la consulta pasando el correo como parámetro para evitar inyecciones SQL
        $stmt->execute([$email]);

        // Se obtiene el primer (y único) usuario que coincide con ese correo
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // --------------------------------------------
        // Verificación del usuario y contraseña
        // --------------------------------------------

        // Se verifica que el usuario exista y que la contraseña ingresada coincida
        // con la contraseña almacenada (comparación con hash)
        if ($user && password_verify($password, $user['contrasena'])) {
            // Si es correcto, se devuelve un arreglo con los datos necesarios del usuario
            return [
                'documento' => $user['documento'],
                'nombre' => $user['nombre'] . ' ' . $user['apellido'],
                'correo' => $user['correo'],
                'rol' => $user['id_rol'] // El rol servirá para controlar permisos
            ];
        }

        // Si no coincide o no se encontró el usuario, retorna null (inicio de sesión fallido)
        return null;
    }
}
