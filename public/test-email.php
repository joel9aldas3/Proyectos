<?php
require_once '../vendor/autoload.php';

use App\Services\EmailService;
use App\Config\EmailConfig;

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prueba de Configuración de Email</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 50px 0;
        }
        .test-card {
            max-width: 800px;
            margin: 0 auto;
        }
        .config-item {
            padding: 10px;
            margin: 5px 0;
            background: #f8f9fa;
            border-radius: 5px;
        }
        .status-badge {
            font-size: 1.2em;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card test-card shadow-lg">
            <div class="card-header bg-primary text-white">
                <h3 class="mb-0">
                    <i class="fas fa-envelope-open-text"></i> Prueba de Configuración de Email
                </h3>
            </div>
            <div class="card-body">
                
                <!-- Configuración actual -->
                <h5><i class="fas fa-cog"></i> Configuración Actual:</h5>
                <div class="config-item">
                    <strong>Servidor SMTP:</strong> <?php echo EmailConfig::SMTP_HOST; ?>:<?php echo EmailConfig::SMTP_PORT; ?>
                </div>
                <div class="config-item">
                    <strong>Usuario:</strong> <?php echo EmailConfig::SMTP_USERNAME; ?>
                </div>
                <div class="config-item">
                    <strong>Contraseña configurada:</strong> 
                    <?php 
                    if (EmailConfig::isConfigured()) {
                        echo '<span class="badge bg-success">✓ SÍ</span>';
                    } else {
                        echo '<span class="badge bg-danger">✗ NO</span>';
                    }
                    ?>
                </div>
                <div class="config-item">
                    <strong>Remitente:</strong> <?php echo EmailConfig::FROM_NAME; ?> &lt;<?php echo EmailConfig::FROM_EMAIL; ?>&gt;
                </div>
                
                <hr class="my-4">
                
                <!-- Prueba de conexión -->
                <h5><i class="fas fa-plug"></i> Prueba de Conexión SMTP:</h5>
                
                <?php
                if (!EmailConfig::isConfigured()) {
                    echo '<div class="alert alert-warning">
                            <strong>⚠️ Configuración incompleta</strong><br>
                            Edita <code>src/Config/EmailConfig.php</code> y configura:
                            <ul class="mb-0 mt-2">
                                <li>SMTP_USERNAME (tu email)</li>
                                <li>SMTP_PASSWORD (contraseña de aplicación de Gmail)</li>
                            </ul>
                          </div>';
                } else {
                    try {
                        $emailService = new EmailService();
                        $result = $emailService->testConnection();
                        
                        if ($result['success']) {
                            echo '<div class="alert alert-success">
                                    <h4 class="alert-heading">
                                        <i class="fas fa-check-circle"></i> ¡Conexión exitosa!
                                    </h4>
                                    <p class="mb-0">' . $result['message'] . '</p>
                                    <hr>
                                    <p class="mb-0">
                                        <strong>Estado:</strong> El sistema puede enviar correos correctamente ✓
                                    </p>
                                  </div>';
                        } else {
                            echo '<div class="alert alert-danger">
                                    <h4 class="alert-heading">
                                        <i class="fas fa-times-circle"></i> Error de conexión
                                    </h4>
                                    <p><strong>Mensaje:</strong> ' . htmlspecialchars($result['error']) . '</p>
                                    <hr>
                                    <p class="mb-0"><strong>Posibles soluciones:</strong></p>
                                    <ul>
                                        <li>Verifica que la contraseña de aplicación sea correcta</li>
                                        <li>Asegúrate de tener verificación en 2 pasos activada</li>
                                        <li>Revisa que el email sea correcto</li>
                                        <li>Verifica que tu firewall no bloquee el puerto 587</li>
                                    </ul>
                                  </div>';
                        }
                    } catch (Exception $e) {
                        echo '<div class="alert alert-danger">
                                <h4 class="alert-heading">
                                    <i class="fas fa-exclamation-triangle"></i> Error al probar
                                </h4>
                                <p><strong>Error:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>
                                <hr>
                                <p class="mb-0">
                                    <strong>Verifica que:</strong><br>
                                    - Composer esté instalado correctamente<br>
                                    - PHPMailer esté disponible (ejecuta: composer install)
                                </p>
                              </div>';
                    }
                }
                ?>
                
                <hr class="my-4">
                
                <!-- Instrucciones -->
                <h5><i class="fas fa-book"></i> ¿Cómo obtener la contraseña de aplicación?</h5>
                <ol class="mt-3">
                    <li>Ve a <a href="https://myaccount.google.com/security" target="_blank">Seguridad de Google</a></li>
                    <li>Activa <strong>"Verificación en 2 pasos"</strong> si no lo está</li>
                    <li>Busca <strong>"Contraseñas de aplicaciones"</strong></li>
                    <li>Selecciona <strong>Correo</strong> y <strong>Otro</strong></li>
                    <li>Escribe: <em>"Sistema Certificados"</em></li>
                    <li>Copia la contraseña de 16 caracteres (sin espacios)</li>
                    <li>Pégala en <code>EmailConfig.php</code> en <code>SMTP_PASSWORD</code></li>
                </ol>
                
                <div class="alert alert-info mt-3">
                    <strong><i class="fas fa-info-circle"></i> Nota:</strong> 
                    Si es una cuenta de Google Workspace (institucional), el administrador debe habilitar 
                    las contraseñas de aplicación en la consola de administración.
                </div>
                
            </div>
            <div class="card-footer">
                <a href="index.php" class="btn btn-primary">
                    <i class="fas fa-arrow-left"></i> Volver al Sistema
                </a>
                <button onclick="location.reload()" class="btn btn-secondary">
                    <i class="fas fa-sync"></i> Probar Nuevamente
                </button>
            </div>
        </div>
    </div>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>