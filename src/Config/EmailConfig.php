<?php

namespace App\Config;

class EmailConfig {
    // Configuración SMTP de Gmail
    const SMTP_HOST = 'smtp.gmail.com';
    const SMTP_PORT = 587; // Puerto TLS
    const SMTP_SECURE = 'tls'; // o 'ssl' para puerto 465
    
    // Credenciales del correo institucional
    const SMTP_USERNAME = 'biblioteca.campusnorte@istvidanueva.edu.ec'; // Tu correo institucional
    const SMTP_PASSWORD = 'nrht wqja ywsu qaii'; // Contraseña de aplicación (NO la contraseña normal)
    
    // Información del remitente
    const FROM_EMAIL = 'biblioteca.campusnorte@istvidanueva.edu.ec';
    const FROM_NAME = 'Instituto Vida Nueva - Certificaciones';
    
    // Configuraciones adicionales
    const REPLY_TO_EMAIL = 'biblioteca.campusnorte@istvidanueva.edu.ec';
    const REPLY_TO_NAME = 'Instituto Vida Nueva';
    
    // Plantilla de email
    const EMAIL_SUBJECT = 'Tu Certificado de Participación - Instituto Vida Nueva';
    
    /**
     * Obtener configuración completa
     */
    public static function getConfig() {
        return [
            'host' => self::SMTP_HOST,
            'port' => self::SMTP_PORT,
            'secure' => self::SMTP_SECURE,
            'username' => self::SMTP_USERNAME,
            'password' => self::SMTP_PASSWORD,
            'from_email' => self::FROM_EMAIL,
            'from_name' => self::FROM_NAME,
            'reply_to_email' => self::REPLY_TO_EMAIL,
            'reply_to_name' => self::REPLY_TO_NAME,
        ];
    }
    
    /**
     * Validar si la configuración está completa
     */
    public static function isConfigured() {
        return !empty(self::SMTP_USERNAME) && !empty(self::SMTP_PASSWORD);
    }
}