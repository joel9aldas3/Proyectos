<?php

namespace App\Controllers;

use App\Models\Participant;

class HomeController {
    private $participantModel;
    
    public function __construct() {
        $this->participantModel = new Participant();
    }
    
    public function index() {
        $this->render('home');
    }
    
    public function participants() {
        $participants = $this->participantModel->getAll();
        $this->render('participants', ['participants' => $participants]);
    }
    
    public function getParticipantsJson() {
        header('Content-Type: application/json');
        $participants = $this->participantModel->getAll();
        
        // Formatear datos para DataTables
        $data = [];
        foreach ($participants as $participant) {
            // Construir botones de acción
            $actions = '';
            
            if ($participant['certificate_generated']) {
                // Si ya está generado, mostrar botón de descarga
                $certificateFile = $this->findCertificateFile($participant['id'], $participant['name']);
                
                if ($certificateFile) {
                    $actions .= '<button class="btn btn-sm btn-primary download-cert" 
                                   onclick="downloadCertificate(\'' . htmlspecialchars($certificateFile) . '\')" 
                                   title="Descargar certificado">
                                   <i class="fas fa-download"></i>
                                 </button> ';
                    
                    // Botón enviar por email
                    $actions .= '<button class="btn btn-sm btn-info send-email-cert" 
                                   data-id="' . $participant['id'] . '" 
                                   data-email="' . htmlspecialchars($participant['email']) . '"
                                   title="Enviar por email">
                                   <i class="fas fa-envelope"></i>
                                 </button> ';
                }
                
                $actions .= '<span class="badge bg-success">✓ Generado</span>';
            } else {
                // Si no está generado, mostrar botón de generar
                $actions .= '<button class="btn btn-sm btn-success generate-cert" 
                               data-id="' . $participant['id'] . '" 
                               title="Generar certificado">
                               <i class="fas fa-certificate"></i> Generar
                             </button>';
            }
            
            $data[] = [
                $participant['id'],
                htmlspecialchars($participant['name']),
                htmlspecialchars($participant['email']),
                htmlspecialchars($participant['course']),
                date('d/m/Y', strtotime($participant['date_completed'])),
                $actions
            ];
        }
        
        echo json_encode(['data' => $data]);
    }
    
    /**
     * Buscar el archivo de certificado más reciente para un participante
     */
    private function findCertificateFile($participantId, $participantName) {
        $generatedPath = __DIR__ . '/../../generated/';
        
        if (!is_dir($generatedPath)) {
            return null;
        }
        
        // Normalizar nombre para búsqueda
        $normalizedName = str_replace(' ', '_', strtolower($participantName));
        
        // Buscar archivos que coincidan con el patrón
        $pattern = $generatedPath . 'certificado_' . $normalizedName . '_*.pdf';
        $files = glob($pattern);
        
        if (empty($files)) {
            return null;
        }
        
        // Ordenar por fecha de modificación (más reciente primero)
        usort($files, function($a, $b) {
            return filemtime($b) - filemtime($a);
        });
        
        // Retornar solo el nombre del archivo (no la ruta completa)
        return basename($files[0]);
    }
    
    private function render($view, $data = []) {
        extract($data);
        include __DIR__ . '/../Views/layout.php';
    }
}