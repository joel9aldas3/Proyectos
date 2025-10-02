<?php

namespace App\Controllers;

use App\Models\Participant;
use App\Models\Certificate;

class CertificateController {
    private $participantModel;
    private $certificateModel;
    
    public function __construct() {
        $this->participantModel = new Participant();
        $this->certificateModel = new Certificate();
    }
    
    public function generate() {
        header('Content-Type: application/json');
        
        try {
            $participantId = $_POST['participant_id'] ?? null;
            
            if (!$participantId) {
                throw new \Exception('ID de participante requerido');
            }
            
            $participant = $this->participantModel->getById($participantId);
            
            if (!$participant) {
                throw new \Exception('Participante no encontrado');
            }
            
            error_log("Generando certificado para participante ID: " . $participantId);
            $result = $this->certificateModel->generate($participant);
            
            if (!$result || !isset($result['success']) || !$result['success']) {
                throw new \Exception('Error al generar el certificado: ' . ($result['error'] ?? 'Error desconocido'));
            }
            
            // Actualizar estado en la base de datos
            if (!$this->participantModel->updateCertificateStatus($participantId)) {
                throw new \Exception('Error al actualizar el estado del certificado');
            }
            
            error_log("Certificado generado exitosamente: " . ($result['filename'] ?? 'sin nombre'));
            
            echo json_encode([
                'success' => true,
                'message' => 'Certificado generado correctamente',
                'filename' => $result['filename'],
                'download_url' => 'index.php?action=download&file=' . $result['filename']
            ]);
            
        } catch (\Exception $e) {
            error_log("Error en generación de certificado: " . $e->getMessage());
            echo json_encode([
                'success' => false, 
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }
    
    public function download() {
        $filename = $_GET['file'] ?? null;
        
        if (!$filename) {
            header("HTTP/1.0 404 Not Found");
            echo "Archivo no especificado";
            return;
        }
        
        // Validar nombre de archivo por seguridad
        if (!preg_match('/^certificado_.*\.pdf$/', $filename)) {
            header("HTTP/1.0 400 Bad Request");
            echo "Nombre de archivo inválido";
            return;
        }
        
        $result = $this->certificateModel->download($filename);
        
        if (!$result) {
            header("HTTP/1.0 404 Not Found");
            echo "Archivo no encontrado";
        }
    }
    
    public function generateBatch() {
        error_reporting(E_ALL);
        ini_set('display_errors', 0);
        
        header('Content-Type: application/json');
        
        try {
            ob_start();
            error_log("=== INICIO generateBatch ===");
            
            $participantIds = [];
            
            if (isset($_POST['participant_ids']) && is_array($_POST['participant_ids'])) {
                $participantIds = array_values($_POST['participant_ids']);
            }
            
            $participantIds = array_filter(array_map('trim', $participantIds));
            
            if (empty($participantIds)) {
                throw new \Exception('No se seleccionaron participantes');
            }
            
            error_log("IDs de participantes a procesar: " . implode(', ', $participantIds));
            
            $generated = 0;
            $errors = [];
            $files = [];
            
            foreach ($participantIds as $id) {
                error_log("Procesando participante ID: " . $id);
                $participant = $this->participantModel->getById($id);
                
                if ($participant) {
                    try {
                        $result = $this->certificateModel->generate($participant);
                        error_log("Resultado generación para ID {$id}: " . print_r($result, true));
                        
                        if ($result['success']) {
                            $this->participantModel->updateCertificateStatus($id);
                            $generated++;
                            $files[] = [
                                'name' => $participant['name'],
                                'filename' => $result['filename'],
                                'download_url' => 'index.php?action=download&file=' . $result['filename']
                            ];
                            error_log("Certificado generado exitosamente para ID {$id}");
                        } else {
                            $error = "Error generando certificado para {$participant['name']}: " . ($result['error'] ?? 'Error desconocido');
                            $errors[] = $error;
                            error_log($error);
                        }
                        
                    } catch (\Exception $e) {
                        $error = "Error con {$participant['name']}: " . $e->getMessage();
                        $errors[] = $error;
                        error_log($error);
                    }
                } else {
                    $error = "Participante ID {$id} no encontrado";
                    $errors[] = $error;
                    error_log($error);
                }
            }
            
            $response = [
                'success' => true,
                'message' => "Se generaron {$generated} de " . count($participantIds) . " certificados" . 
                           (count($errors) > 0 ? " con " . count($errors) . " errores" : ""),
                'generated' => $generated,
                'total' => count($participantIds),
                'files' => $files,
                'errors' => $errors
            ];
            
            error_log("Respuesta final: " . print_r($response, true));
            
            if (ob_get_level() > 0) {
                ob_end_clean();
            }
            
            echo json_encode($response);
            error_log("=== FIN generateBatch ===");
            
        } catch (\Exception $e) {
            error_log("Error en generateBatch: " . $e->getMessage());
            
            if (ob_get_level() > 0) {
                ob_end_clean();
            }
            
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error al generar certificados: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Obtener lista de certificados generados
     */
    public function getGeneratedCertificates() {
        header('Content-Type: application/json');
        
        try {
            $generatedPath = __DIR__ . '/../../generated/';
            
            if (!is_dir($generatedPath)) {
                echo json_encode(['success' => false, 'message' => 'Directorio no encontrado']);
                return;
            }
            
            $files = glob($generatedPath . 'certificado_*.pdf');
            
            if (empty($files)) {
                echo json_encode(['success' => true, 'files' => []]);
                return;
            }
            
            // Ordenar por fecha de modificación (más recientes primero)
            usort($files, function($a, $b) {
                return filemtime($b) - filemtime($a);
            });
            
            // Extraer solo los nombres de archivo
            $filenames = array_map('basename', $files);
            
            echo json_encode([
                'success' => true,
                'files' => $filenames,
                'count' => count($filenames)
            ]);
            
        } catch (\Exception $e) {
            error_log("Error en getGeneratedCertificates: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    /**
     * Contar certificados generados
     */
    public function countGeneratedCertificates() {
        header('Content-Type: application/json');
        
        try {
            $generatedPath = __DIR__ . '/../../generated/';
            
            if (!is_dir($generatedPath)) {
                echo json_encode(['count' => 0]);
                return;
            }
            
            $files = glob($generatedPath . 'certificado_*.pdf');
            
            echo json_encode(['count' => count($files)]);
            
        } catch (\Exception $e) {
            error_log("Error en countGeneratedCertificates: " . $e->getMessage());
            echo json_encode(['count' => 0]);
        }
    }
    
    /**
     * Descargar todos los certificados en un archivo ZIP
     */
    public function downloadAllZip() {
        try {
            $generatedPath = __DIR__ . '/../../generated/';
            
            if (!is_dir($generatedPath)) {
                header("HTTP/1.0 404 Not Found");
                echo "No hay certificados generados";
                return;
            }
            
            // Obtener todos los PDFs
            $files = glob($generatedPath . 'certificado_*.pdf');
            
            if (empty($files)) {
                header("HTTP/1.0 404 Not Found");
                echo "No hay certificados para descargar";
                return;
            }
            
            // Nombre del archivo ZIP
            $zipFilename = 'certificados_' . date('Y-m-d_His') . '.zip';
            $zipPath = $generatedPath . $zipFilename;
            
            // Crear archivo ZIP
            $zip = new \ZipArchive();
            
            if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
                throw new \Exception('No se pudo crear el archivo ZIP');
            }
            
            // Agregar cada PDF al ZIP
            foreach ($files as $file) {
                $filename = basename($file);
                $zip->addFile($file, $filename);
            }
            
            $zip->close();
            
            // Verificar que se creó el archivo
            if (!file_exists($zipPath)) {
                throw new \Exception('Error al crear el archivo ZIP');
            }
            
            // Limpiar búfer de salida
            if (ob_get_level()) {
                ob_end_clean();
            }
            
            // Enviar archivo ZIP
            header('Content-Type: application/zip');
            header('Content-Disposition: attachment; filename="' . $zipFilename . '"');
            header('Content-Length: ' . filesize($zipPath));
            header('Cache-Control: private, max-age=0, must-revalidate');
            header('Pragma: public');
            
            readfile($zipPath);
            
            // Eliminar el archivo ZIP temporal después de enviarlo
            unlink($zipPath);
            
            exit;
            
        } catch (\Exception $e) {
            error_log("Error en downloadAllZip: " . $e->getMessage());
            header("HTTP/1.0 500 Internal Server Error");
            echo "Error al crear el archivo ZIP: " . $e->getMessage();
        }
    }
    
    /**
     * Enviar certificado por email
     */
    public function sendEmail() {
        header('Content-Type: application/json');
        
        try {
            $participantId = $_POST['participant_id'] ?? null;
            
            if (!$participantId) {
                throw new \Exception('ID de participante requerido');
            }
            
            $participant = $this->participantModel->getById($participantId);
            
            if (!$participant) {
                throw new \Exception('Participante no encontrado');
            }
            
            if (!$participant['certificate_generated']) {
                throw new \Exception('El certificado aún no ha sido generado');
            }
            
            // Buscar el archivo del certificado
            $generatedPath = __DIR__ . '/../../generated/';
            $normalizedName = str_replace(' ', '_', strtolower($participant['name']));
            $pattern = $generatedPath . 'certificado_' . $normalizedName . '_*.pdf';
            $files = glob($pattern);
            
            if (empty($files)) {
                throw new \Exception('No se encontró el archivo del certificado');
            }
            
            // Usar el archivo más reciente
            usort($files, function($a, $b) {
                return filemtime($b) - filemtime($a);
            });
            
            $certificatePath = $files[0];
            
            // Enviar email
            $result = $this->certificateModel->sendByEmail($participant, $certificatePath);
            
            if ($result['success']) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Certificado enviado correctamente a ' . $participant['email']
                ]);
            } else {
                throw new \Exception($result['error']);
            }
            
        } catch (\Exception $e) {
            error_log("Error en sendEmail: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Probar configuración de email
     */
    public function testEmail() {
        header('Content-Type: application/json');
        
        try {
            if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
                throw new \Exception('PHPMailer no está instalado');
            }
            
            $emailService = new \App\Services\EmailService();
            $result = $emailService->testConnection();
            
            echo json_encode($result);
            
        } catch (\Exception $e) {
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Enviar emails en lote
     */
    public function sendBatchEmails() {
        error_reporting(E_ALL);
        ini_set('display_errors', 0);
        
        header('Content-Type: application/json');
        
        try {
            ob_start();
            error_log("=== INICIO sendBatchEmails ===");
            
            $participantIds = [];
            
            if (isset($_POST['participant_ids']) && is_array($_POST['participant_ids'])) {
                $participantIds = array_values($_POST['participant_ids']);
            }
            
            $participantIds = array_filter(array_map('trim', $participantIds));
            
            if (empty($participantIds)) {
                throw new \Exception('No se seleccionaron participantes');
            }
            
            error_log("IDs de participantes para envío de email: " . implode(', ', $participantIds));
            
            $sent = 0;
            $errors = [];
            $emails = [];
            $generatedPath = __DIR__ . '/../../generated/';
            
            foreach ($participantIds as $id) {
                error_log("Procesando envío de email para ID: " . $id);
                $participant = $this->participantModel->getById($id);
                
                if ($participant) {
                    // Verificar que tenga certificado generado
                    if (!$participant['certificate_generated']) {
                        $errors[] = "{$participant['name']}: Certificado no generado aún";
                        error_log("Participante ID {$id} no tiene certificado generado");
                        continue;
                    }
                    
                    try {
                        // Buscar el archivo del certificado
                        $normalizedName = str_replace(' ', '_', strtolower($participant['name']));
                        $pattern = $generatedPath . 'certificado_' . $normalizedName . '_*.pdf';
                        $files = glob($pattern);
                        
                        if (empty($files)) {
                            $errors[] = "{$participant['name']}: Archivo de certificado no encontrado";
                            error_log("No se encontró archivo para ID {$id}");
                            continue;
                        }
                        
                        // Usar el archivo más reciente
                        usort($files, function($a, $b) {
                            return filemtime($b) - filemtime($a);
                        });
                        
                        $certificatePath = $files[0];
                        
                        // Enviar email
                        $result = $this->certificateModel->sendByEmail($participant, $certificatePath);
                        
                        if ($result['success']) {
                            $sent++;
                            $emails[] = [
                                'name' => $participant['name'],
                                'email' => $participant['email'],
                                'status' => 'sent'
                            ];
                            error_log("Email enviado exitosamente a ID {$id} ({$participant['email']})");
                        } else {
                            $error = "{$participant['name']}: " . ($result['error'] ?? 'Error desconocido');
                            $errors[] = $error;
                            error_log($error);
                        }
                        
                    } catch (\Exception $e) {
                        $error = "{$participant['name']}: " . $e->getMessage();
                        $errors[] = $error;
                        error_log($error);
                    }
                } else {
                    $error = "Participante ID {$id} no encontrado";
                    $errors[] = $error;
                    error_log($error);
                }
                
                // Pequeña pausa entre envíos para no saturar el servidor SMTP
                usleep(500000); // 0.5 segundos
            }
            
            $response = [
                'success' => true,
                'message' => "Se enviaron {$sent} de " . count($participantIds) . " emails" . 
                           (count($errors) > 0 ? " con " . count($errors) . " errores" : ""),
                'sent' => $sent,
                'total' => count($participantIds),
                'emails' => $emails,
                'errors' => $errors
            ];
            
            error_log("Respuesta final envío masivo: " . print_r($response, true));
            
            if (ob_get_level() > 0) {
                ob_end_clean();
            }
            
            echo json_encode($response);
            error_log("=== FIN sendBatchEmails ===");
            
        } catch (\Exception $e) {
            error_log("Error en sendBatchEmails: " . $e->getMessage());
            
            if (ob_get_level() > 0) {
                ob_end_clean();
            }
            
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error al enviar emails: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Enviar emails a todos los participantes con certificados generados
     */
    public function sendAllEmails() {
        header('Content-Type: application/json');
        
        try {
            // Obtener todos los participantes con certificados generados
            $participants = $this->participantModel->getAllWithCertificates();
            
            if (empty($participants)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'No hay participantes con certificados generados'
                ]);
                return;
            }
            
            $sent = 0;
            $errors = [];
            $generatedPath = __DIR__ . '/../../generated/';
            
            foreach ($participants as $participant) {
                try {
                    // Buscar el archivo del certificado
                    $normalizedName = str_replace(' ', '_', strtolower($participant['name']));
                    $pattern = $generatedPath . 'certificado_' . $normalizedName . '_*.pdf';
                    $files = glob($pattern);
                    
                    if (empty($files)) {
                        continue;
                    }
                    
                    usort($files, function($a, $b) {
                        return filemtime($b) - filemtime($a);
                    });
                    
                    $certificatePath = $files[0];
                    
                    // Enviar email
                    $result = $this->certificateModel->sendByEmail($participant, $certificatePath);
                    
                    if ($result['success']) {
                        $sent++;
                    } else {
                        $errors[] = "{$participant['name']}: {$result['error']}";
                    }
                    
                } catch (\Exception $e) {
                    $errors[] = "{$participant['name']}: " . $e->getMessage();
                }
                
                usleep(500000); // 0.5 segundos entre envíos
            }
            
            echo json_encode([
                'success' => true,
                'message' => "Se enviaron {$sent} de " . count($participants) . " emails",
                'sent' => $sent,
                'total' => count($participants),
                'errors' => $errors
            ]);
            
        } catch (\Exception $e) {
            error_log("Error en sendAllEmails: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }
}