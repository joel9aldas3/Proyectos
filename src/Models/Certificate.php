<?php

namespace App\Models;

use TCPDF;

class Certificate {
    private $templatesPath;
    private $outputPath;
    
    public function __construct() {
        $this->templatesPath = __DIR__ . '/../../certificates/';
        $this->outputPath = __DIR__ . '/../../generated/';
        
        // Crear directorios si no existen
        if (!is_dir($this->templatesPath)) {
            mkdir($this->templatesPath, 0755, true);
        }
        if (!is_dir($this->outputPath)) {
            mkdir($this->outputPath, 0755, true);
        }
    }
    
    /**
     * Obtener template según el curso
     */
    private function getTemplateForCourse($courseName) {
        // Normalizar nombre del curso para buscar archivo
        $normalizedCourse = strtolower(preg_replace('/[^a-z0-9]+/i', '_', $courseName));
        
        // Buscar template específico para el curso
        $specificTemplate = $this->templatesPath . 'template_' . $normalizedCourse . '.png';
        
        if (file_exists($specificTemplate)) {
            error_log("Usando template específico: " . $specificTemplate);
            return $specificTemplate;
        }
        
        // Template por categoría (detectar palabras clave)
        $courseCategories = [
            'programacion' => ['php', 'javascript', 'python', 'java', 'programacion', 'desarrollo'],
            'diseno' => ['photoshop', 'illustrator', 'diseno', 'diseño', 'grafico'],
            'base_datos' => ['mysql', 'sql', 'base', 'datos', 'oracle', 'mongodb'],
            'web' => ['html', 'css', 'web', 'frontend', 'backend'],
            'office' => ['excel', 'word', 'powerpoint', 'office', 'ofimatica']
        ];
        
        $courseLower = strtolower($courseName);
        
        foreach ($courseCategories as $category => $keywords) {
            foreach ($keywords as $keyword) {
                if (strpos($courseLower, $keyword) !== false) {
                    $categoryTemplate = $this->templatesPath . 'template_' . $category . '.png';
                    if (file_exists($categoryTemplate)) {
                        error_log("Usando template de categoría: " . $categoryTemplate);
                        return $categoryTemplate;
                    }
                }
            }
        }
        
        // Template por defecto
        $defaultTemplate = $this->templatesPath . 'template.png';
        if (file_exists($defaultTemplate)) {
            error_log("Usando template por defecto");
            return $defaultTemplate;
        }
        
        // Si no hay ninguno, lanzar error
        throw new \Exception('No se encontró ningún template de certificado. Coloca al menos template.png en la carpeta certificates/');
    }
    
    /**
     * Obtener configuración de posición del texto según el template
     */
    private function getTemplateConfig($templatePath) {
        $templateName = basename($templatePath, '.png');
        
        // Configuraciones predefinidas por template
        $configs = [
            'template' => [ // Template por defecto
                'name_x' => 50,
                'name_y' => 95,
                'name_size' => 36,
                'course_y' => 115,
                'course_size' => 24,
                'date_y' => 170,
                'date_size' => 14
            ],
            'template_programacion' => [
                'name_x' => 50,
                'name_y' => 95,
                'name_size' => 40,
                'course_y' => 140,
                'course_size' => 26,
                'date_y' => 175,
                'date_size' => 16
            ],
            'template_diseno' => [
                'name_x' => 50,
                'name_y' => 105,
                'name_size' => 38,
                'course_y' => 150,
                'course_size' => 22,
                'date_y' => 172,
                'date_size' => 14
            ],
            // Agregar más configuraciones según necesites
        ];
        
        return $configs[$templateName] ?? $configs['template'];
    }
    
    public function generate($participantData) {
        try {
            error_log("Iniciando generación de certificado para: " . $participantData['name']);
            
            // Obtener template según el curso
            $templatePath = $this->getTemplateForCourse($participantData['course']);
            $config = $this->getTemplateConfig($templatePath);
            
            // Crear PDF
            $pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);
            
            // Configuración del documento
            $pdf->SetCreator('Sistema de Certificados');
            $pdf->SetAuthor('Instituto Vida Nueva');
            $pdf->SetTitle('Certificado de Participación - ' . $participantData['name']);
            
            // Remover header y footer
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);
            
            // Optimizaciones
            $pdf->setRasterizeVectorImages(false);
            $pdf->SetCompression(true);
            $pdf->setJPEGQuality(90);
            
            // Agregar página
            $pdf->AddPage();
            
            // Insertar imagen de fondo (template)
            $pdf->Image($templatePath, 0, 0, 297, 210, 'PNG', '', '', false, 300, '', false, false, 0);
            
            // Configurar fuente para el nombre
            $pdf->SetFont('helvetica', 'B', $config['name_size']);
            $pdf->SetTextColor(0, 0, 0);
            
            // Posición del nombre
            $pdf->SetXY($config['name_x'], $config['name_y']);
            $pdf->Cell(197, 15, $participantData['name'], 0, 1, 'C');
            
            // Curso
            $pdf->SetFont('helvetica', '', 18);
            $pdf->SetXY(50, $config['course_y'] - 15);
            $pdf->Cell(197, 10, 'Por su participación en el curso:', 0, 1, 'C');
            
            $pdf->SetFont('helvetica', 'B', $config['course_size']);
            $pdf->SetXY(50, $config['course_y']);
            $pdf->Cell(197, 10, $participantData['course'], 0, 1, 'C');
            
            /*/ Fecha
            $pdf->SetFont('helvetica', '', $config['date_size']);
            $pdf->SetXY(50, $config['date_y']);
            $pdf->Cell(197, 10, 'Fecha de finalización: ' . date('d/m/Y', strtotime($participantData['date_completed'])), 0, 1, 'C'); */
            
            // Generar nombre del archivo único
            $filename = 'certificado_' . str_replace(' ', '_', strtolower($participantData['name'])) . '_' . uniqid() . '.pdf';
            $fullPath = $this->outputPath . $filename;
            
            // Guardar PDF
            $pdf->Output($fullPath, 'F');
            
            // Limpiar memoria
            $pdf->Close();
            unset($pdf);
            
            if (!file_exists($fullPath)) {
                throw new \Exception('No se pudo guardar el archivo PDF');
            }
            
            $fileSize = filesize($fullPath);
            error_log("Certificado generado exitosamente: " . $filename . " usando template: " . basename($templatePath));
            
            return [
                'filename' => $filename,
                'path' => $fullPath,
                'success' => true,
                'size' => $fileSize,
                'template_used' => basename($templatePath)
            ];
            
        } catch (\Exception $e) {
            error_log("Error generando certificado para {$participantData['name']}: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    public function generateBatch($participants) {
        $results = [
            'success' => true,
            'generated' => 0,
            'errors' => [],
            'certificates' => []
        ];
        
        foreach ($participants as $index => $participant) {
            error_log("Procesando lote: " . ($index + 1) . "/" . count($participants));
            
            $result = $this->generate($participant);
            
            if ($result['success']) {
                $results['generated']++;
                $results['certificates'][] = [
                    'participant' => $participant['name'],
                    'filename' => $result['filename'],
                    'path' => $result['path'],
                    'template' => $result['template_used'] ?? 'default'
                ];
                error_log("OK: Certificado " . ($index + 1) . " generado para {$participant['name']}");
            } else {
                $error = "Error generando certificado para {$participant['name']}: {$result['error']}";
                $results['errors'][] = $error;
                error_log("ERROR: " . $error);
            }
            
            // Pequeña pausa para evitar sobrecarga
            usleep(100000); // 0.1 segundos
        }
        
        return $results;
    }
    
    public function download($filename) {
        $fullPath = $this->outputPath . $filename;
        
        if (file_exists($fullPath)) {
            // Limpiar cualquier salida previa
            if (ob_get_level()) {
                ob_end_clean();
            }
            
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . basename($filename) . '"');
            header('Content-Length: ' . filesize($fullPath));
            header('Cache-Control: private, max-age=0, must-revalidate');
            header('Pragma: public');
            
            readfile($fullPath);
            exit;
        }
        
        return false;
    }
    
    /**
     * Enviar certificado por email
     */
    public function sendByEmail($participantData, $certificatePath) {
        try {
            // Verificar si PHPMailer está disponible
            if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
                throw new \Exception('PHPMailer no está instalado. Ejecuta: composer install');
            }
            
            $emailService = new \App\Services\EmailService();
            return $emailService->sendCertificate($participantData, $certificatePath);
            
        } catch (\Exception $e) {
            error_log("Error enviando email: " . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Listar templates disponibles
     */
    public function getAvailableTemplates() {
        $templates = glob($this->templatesPath . 'template*.png');
        $result = [];
        
        foreach ($templates as $template) {
            $name = basename($template, '.png');
            $displayName = str_replace('template_', '', $name);
            $displayName = str_replace('_', ' ', $displayName);
            $displayName = ucwords($displayName);
            
            if ($name === 'template') {
                $displayName = 'Por Defecto';
            }
            
            $result[] = [
                'filename' => basename($template),
                'name' => $displayName,
                'path' => $template
            ];
        }
        
        return $result;
    }
}