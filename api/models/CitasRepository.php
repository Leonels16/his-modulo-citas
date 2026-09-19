<?php
require_once __DIR__ . '/../config/Database.php';

class CitaRepository {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getAll($doctorId = null, $desde = null, $hasta = null) {
        $sql = "SELECT c.*, d.nombre as doctor_nombre, p.nombre as paciente_nombre 
                FROM citas c
                JOIN doctores d ON c.doctor_id = d.id
                JOIN pacientes p ON c.paciente_id = p.id
                WHERE 1=1";
        $params = [];

        if (!empty($doctorId)) {
            $sql .= " AND c.doctor_id = ?";
            $params[] = $doctorId;
        }
        if (!empty($desde)) {
            $sql .= " AND c.fecha_inicio >= ?";
            $params[] = $desde;
        }
        if (!empty($hasta)) {
            $sql .= " AND c.fecha_fin <= ?";
            $params[] = $hasta;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT c.*, d.nombre as doctor_nombre, p.nombre as paciente_nombre 
                                     FROM citas c
                                     JOIN doctores d ON c.doctor_id = d.id
                                     JOIN pacientes p ON c.paciente_id = p.id 
                                     WHERE c.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($data) {
        $stmt = $this->db->prepare("INSERT INTO citas (doctor_id, paciente_id, fecha_inicio, fecha_fin, motivo, estado) VALUES (?, ?, ?, ?, ?, 'pendiente')");
        $stmt->execute([
            $data['doctor_id'],
            $data['paciente_id'],
            $data['fecha_inicio'],
            $data['fecha_fin'],
            $data['motivo']
        ]);
        return $this->db->lastInsertId();
    }

    public function updateHorario($id, $fechaInicio, $fechaFin) {
        $stmt = $this->db->prepare("UPDATE citas SET fecha_inicio = ?, fecha_fin = ? WHERE id = ?");
        return $stmt->execute([$fechaInicio, $fechaFin, $id]);
    }

    public function getDoctores() {
        return $this->db->query("SELECT * FROM doctores")->fetchAll();
    }

    public function getPacientes() {
        return $this->db->query("SELECT * FROM pacientes")->fetchAll();
    }
}