<?php

namespace App\Controllers;

use App\Models\Participant;
use DateTime;
use Exception;

class UploadController {
    private $participantModel;
    private $uploadsPath;
    
    public function __construct() {
        $this->participantModel = new Participant();
        $this->uploadsPath = __DIR__ . '/../../public/uploads/';
        
        // Asegurar que el directorio de uploads existe
        if (!is_dir($this->uploadsPath)) {
            mkdir($this->uploadsPath, 0755, true);
        }
    }
    
    public function uploadCsv() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            return;
        }
        
        if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['success' => false, 'message' => 'Error al subir el archivo']);
            return;
        }
        
        $file = $_FILES['csv_file'];
        
        // Validar extensión
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if ($extension !== 'csv') {
            echo json_encode(['success' => false, 'message' => 'Solo se permiten archivos CSV']);
            return;
        }
        
        // Validar tamaño (máximo 5MB)
        if ($file['size'] > 5 * 1024 * 1024) {
            echo json_encode(['success' => false, 'message' => 'El archivo es muy grande. Máximo 5MB.']);
            return;
        }
        
        try {
            // Generar nombre único para el archivo
            $timestamp = date('Y-m-d_H-i-s');
            $originalName = pathinfo($file['name'], PATHINFO_FILENAME);
            $filename = "participantes_{$originalName}_{$timestamp}.csv";
            $uploadPath = $this->uploadsPath . $filename;
            
            // Mover archivo a la carpeta de uploads
            if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
                throw new \Exception('Error al guardar el archivo CSV');
            }
            
            // Leer y procesar CSV desde su nueva ubicación
            $participants = $this->processCsv($uploadPath);
            
            if (empty($participants)) {
                unlink($uploadPath); // Eliminar archivo si está vacío
                echo json_encode(['success' => false, 'message' => 'El archivo CSV está vacío o tiene formato incorrecto']);
                return;
            }
            
            $result = $this->participantModel->bulkInsert($participants);
            
            $response = [
                'success' => true,
                'message' => "Se insertaron {$result['inserted']} registros correctamente",
                'inserted' => $result['inserted'],
                'errors' => $result['errors'],
                'file_saved' => $filename
            ];
            
            if (!empty($result['errors'])) {
                $response['message'] .= '. Algunos registros tuvieron errores.';
            }
            
            echo json_encode($response);
            
        } catch (Exception $e) {
            // Si hay error y el archivo existe, intentar eliminarlo
            if (isset($uploadPath) && file_exists($uploadPath)) {
                unlink($uploadPath);
            }
            echo json_encode(['success' => false, 'message' => 'Error al procesar los datos: ' . $e->getMessage()]);
        }
    }
    
    private function processCsv($filePath) {
        $participants = [];
        
        if (($handle = fopen($filePath, "r")) !== false) {
            $headers = fgetcsv($handle); // Primera fila como headers
            
            if (!$headers) {
                return $participants;
            }
            
            // Mapear headers esperados (flexible)
            $headerMap = $this->mapHeaders($headers);
            
            while (($data = fgetcsv($handle)) !== false) {
                if (count($data) >= 4) { // Mínimo 4 columnas esperadas
                    $participant = [
                        'name' => isset($data[$headerMap['name']]) ? trim($data[$headerMap['name']]) : '',
                        'email' => isset($data[$headerMap['email']]) ? trim($data[$headerMap['email']]) : '',
                        'course' => isset($data[$headerMap['course']]) ? trim($data[$headerMap['course']]) : '',
                        'date_completed' => isset($data[$headerMap['date']]) ? $this->formatDate($data[$headerMap['date']]) : ''
                    ];
                    
                    // Solo agregar si tiene datos básicos
                    if (!empty($participant['name']) && !empty($participant['email'])) {
                        $participants[] = $participant;
                    }
                }
            }
            fclose($handle);
        }
        
        return $participants;
    }
    
    private function mapHeaders($headers) {
        $map = [];
        
        foreach ($headers as $index => $header) {
            $header = strtolower(trim($header));
            
            // Mapear variaciones comunes de nombres de columnas
            if (in_array($header, ['nombre', 'name', 'participante'])) {
                $map['name'] = $index;
            } elseif (in_array($header, ['email', 'correo', 'e-mail', 'mail'])) {
                $map['email'] = $index;
            } elseif (in_array($header, ['curso', 'course', 'programa'])) {
                $map['course'] = $index;
            } elseif (in_array($header, ['fecha', 'date', 'fecha_completado', 'date_completed'])) {
                $map['date'] = $index;
            }
        }
        
        // Valores por defecto si no se encuentran
        $map['name'] = $map['name'] ?? 0;
        $map['email'] = $map['email'] ?? 1;
        $map['course'] = $map['course'] ?? 2;
        $map['date'] = $map['date'] ?? 3;
        
        return $map;
    }
    
    private function formatDate($dateString) {
        $dateString = trim($dateString);
        
        if (empty($dateString)) {
            return date('Y-m-d'); // Fecha actual como fallback
        }
        
        // Intentar diferentes formatos de fecha
        $formats = ['Y-m-d', 'd/m/Y', 'm/d/Y', 'd-m-Y', 'Y/m/d'];
        
        foreach ($formats as $format) {
            $date = DateTime::createFromFormat($format, $dateString);
            if ($date !== false) {
                return $date->format('Y-m-d');
            }
        }
        
        // Si nada funciona, usar fecha actual
        return date('Y-m-d');
    }
}