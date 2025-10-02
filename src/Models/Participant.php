<?php

namespace App\Models;

use PDO;
use Exception;

class Participant {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function create($data) {
        $sql = "INSERT INTO participants (name, email, course, date_completed) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$data['name'], $data['email'], $data['course'], $data['date_completed']]);
    }
    
    public function getAll() {
        $sql = "SELECT * FROM participants ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getById($id) {
        $sql = "SELECT * FROM participants WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function updateCertificateStatus($id) {
        $sql = "UPDATE participants SET certificate_generated = 1 WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
    
    public function getAllWithCertificates() {
        $sql = "SELECT * FROM participants WHERE certificate_generated = 1 ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function validateData($data) {
        $errors = [];
        
        if (empty($data['name'])) {
            $errors[] = "El nombre es obligatorio";
        }
        
        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Email inválido";
        }
        
        if (empty($data['course'])) {
            $errors[] = "El curso es obligatorio";
        }
        
        if (empty($data['date_completed']) || !strtotime($data['date_completed'])) {
            $errors[] = "Fecha de finalización inválida";
        }
        
        return $errors;
    }
    
    public function bulkInsert($participants) {
        $this->db->beginTransaction();
        
        try {
            $sql = "INSERT INTO participants (name, email, course, date_completed) VALUES (?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            
            $inserted = 0;
            $errors = [];
            
            foreach ($participants as $index => $participant) {
                $validationErrors = $this->validateData($participant);
                
                if (empty($validationErrors)) {
                    try {
                        $stmt->execute([
                            $participant['name'],
                            $participant['email'],
                            $participant['course'],
                            $participant['date_completed']
                        ]);
                        $inserted++;
                    } catch (Exception $e) {
                        $errors[] = "Fila " . ($index + 1) . ": Error en base de datos - " . $e->getMessage();
                    }
                } else {
                    $errors[] = "Fila " . ($index + 1) . ": " . implode(', ', $validationErrors);
                }
            }
            
            $this->db->commit();
            return ['inserted' => $inserted, 'errors' => $errors];
            
        } catch (Exception $e) {
            $this->db->rollBack();
            throw new Exception("Error en transacción: " . $e->getMessage());
        }
    }
}