<?php

// Definición de la clase EditarResidenteController
class EditarResidenteController {

    // ---------------------------------------------
    // Simulación de datos de residentes (estáticos)
    // En un caso real, esto se leería desde una base de datos
    // ---------------------------------------------
    private $residentes = [
        ['id' => 101, 'nombre' => 'Sofia Enciso', 'contacto' => '3022927343', 'casa' => 'C - 3'],
        ['id' => 102, 'nombre' => 'Paula Garcia', 'contacto' => '3016849918', 'casa' => 'A - 5'],
        ['id' => 103, 'nombre' => 'Nicolas Mora', 'contacto' => '3177650234', 'casa' => 'G - 4'],
    ];

    // ---------------------------------------------
    // Método index(): retorna todos los residentes
    // ---------------------------------------------
    public function index() {
        return $this->residentes;
    }

    // ---------------------------------------------
    // Método show($id): busca y retorna un residente específico por su ID
    // ---------------------------------------------
    public function show($id) {
        // Recorre la lista de residentes
        foreach ($this->residentes as $residente) {
            // Si encuentra coincidencia con el ID buscado, lo retorna
            if ($residente['id'] == $id) {
                return $residente;
            }
        }
        // Si no encuentra, retorna null
        return null;
    }

    // ---------------------------------------------
    // Método update($id, $nuevoData): actualiza los datos de un residente
    // ---------------------------------------------
    public function update($id, $nuevoData) {
        // Recorre la lista con referencia (&) para modificar el valor directamente
        foreach ($this->residentes as &$residente) {
            if ($residente['id'] == $id) {
                // Actualiza solo si se recibió un nuevo dato, de lo contrario deja el valor anterior
                $residente['nombre'] = $nuevoData['nombre'] ?? $residente['nombre'];
                $residente['contacto'] = $nuevoData['contacto'] ?? $residente['contacto'];
                $residente['casa'] = $nuevoData['casa'] ?? $residente['casa'];

                // Retorna los datos actualizados del residente
                return $residente;
            }
        }
        // Si no se encuentra el ID, retorna null
        return null;
    }

    // ---------------------------------------------
    // Método delete($id): elimina un residente según su ID
    // ---------------------------------------------
    public function delete($id) {
        // Recorre el array con índice para poder eliminarlo con unset()
        foreach ($this->residentes as $key => $residente) {
            if ($residente['id'] == $id) {
                unset($this->residentes[$key]); // Elimina el residente del array
                return true; // Retorna true si se eliminó exitosamente
            }
        }
        return false; // Retorna false si no se encontró el ID
    }
}
