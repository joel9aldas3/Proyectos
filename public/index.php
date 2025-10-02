<?php
// Verificar si vendor/autoload.php existe
if (file_exists('../vendor/autoload.php')) {
    require_once '../vendor/autoload.php';
} else {
    // Autoload manual temporal para probar sin composer
    spl_autoload_register(function ($className) {
        // Convertir namespace a ruta de archivo
        $className = str_replace('App\\', '', $className);
        $className = str_replace('\\', DIRECTORY_SEPARATOR, $className);
        $file = '../src/' . $className . '.php';
        
        if (file_exists($file)) {
            require_once $file;
        }
    });
    
    // Mostrar aviso
    echo '<div style="background: #fff3cd; color: #856404; padding: 10px; margin: 10px; border-radius: 5px;">
            <strong>⚠️ Aviso:</strong> Ejecuta <code>composer install</code> para instalar todas las dependencias.
            <br>Funcionalidades limitadas: No se pueden generar PDFs ni enviar emails.
          </div>';
}

use App\Controllers\HomeController;
use App\Controllers\UploadController;
use App\Controllers\CertificateController;

// Router simple
$action = $_GET['action'] ?? 'home';

try {
    switch ($action) {
        case 'home':
        default:
            $controller = new HomeController();
            $controller->index();
            break;
            
        case 'participants':
            $controller = new HomeController();
            $controller->participants();
            break;
            
        case 'get-participants':
            $controller = new HomeController();
            $controller->getParticipantsJson();
            break;
            
        case 'upload-csv':
            $controller = new UploadController();
            $controller->uploadCsv();
            break;
            
        case 'generate-certificate':
            if (!class_exists('TCPDF')) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Instalar dependencias: composer install']);
                exit;
            }
            $controller = new CertificateController();
            $controller->generate();
            break;
            
        case 'generate-batch':
            if (!class_exists('TCPDF')) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Instalar dependencias: composer install']);
                exit;
            }
            $controller = new CertificateController();
            $controller->generateBatch();
            break;
            
        case 'download':
            $controller = new CertificateController();
            $controller->download();
            break;
            
        case 'get-generated-certificates':
            $controller = new CertificateController();
            $controller->getGeneratedCertificates();
            break;
            
        case 'count-generated-certificates':
            $controller = new CertificateController();
            $controller->countGeneratedCertificates();
            break;
            
        case 'download-all-zip':
            $controller = new CertificateController();
            $controller->downloadAllZip();
            break;
            
        case 'send-email':
            $controller = new CertificateController();
            $controller->sendEmail();
            break;
            
        case 'send-batch-emails':
            $controller = new CertificateController();
            $controller->sendBatchEmails();
            break;
            
        case 'send-all-emails':
            $controller = new CertificateController();
            $controller->sendAllEmails();
            break;
            
        case 'test-email':
            $controller = new CertificateController();
            $controller->testEmail();
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error interno del servidor: ' . $e->getMessage()]);
}