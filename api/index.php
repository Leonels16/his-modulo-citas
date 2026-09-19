<?php
header('Content-Type: application/json; charset=utf-8');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, PATCH, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/models/CitaRepository.php';
$repo = new CitaRepository();

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Rutas auxiliares de doctores y pacientes
if ($uri === '/api/doctores' && $method === 'GET') {
    echo json_encode($repo->getDoctores());
    exit;
}

if ($uri === '/api/pacientes' && $method === 'GET') {
    echo json_encode($repo->getPacientes());
    exit;
}

// Rutas Citas (Listar y Crear)
if ($uri === '/api/citas') {
    if ($method === 'GET') {
        $doctor = $_GET['doctor_id'] ?? null;
        $desde = $_GET['desde'] ?? null;
        $hasta = $_GET['hasta'] ?? null;
        echo json_encode($repo->getAll($doctor, $desde, $hasta));
        exit;
    }

    if ($method === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['doctor_id']) || empty($data['paciente_id']) || empty($data['fecha_inicio']) || empty($data['fecha_fin']) || empty($data['motivo'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Todos los campos son obligatorios [RQF-08]']);
            exit;
        }

        // Validación de no solapamiento / doble reserva (RQF-03, RQNF-07, RQNF-03)
        if ($repo->existeConflicto($data['doctor_id'], $data['fecha_inicio'], $data['fecha_fin'])) {
            http_response_code(409);
            echo json_encode(['error' => 'Conflicto: El doctor ya tiene una cita programada en ese horario']);
            exit;
        }

        $id = $repo->create($data);
        http_response_code(201);
        echo json_encode(['id' => $id, 'mensaje' => 'Cita creada exitosamente']);
        exit;
    }
}

// Cambiar estado de la cita (PATCH /api/citas/{id}/estado)
if (preg_match('#^/api/citas/(\d+)/estado$#', $uri, $matches)) {
    if ($method === 'PATCH') {
        $id = $matches[1];
        $data = json_decode(file_get_contents('php://input'), true);
        $estadosPermitidos = ['pendiente', 'confirmada', 'cancelada', 'atendida'];

        if (empty($data['estado']) || !in_array($data['estado'], $estadosPermitidos)) {
            http_response_code(400);
            echo json_encode(['error' => 'Estado no valido. Debe ser: pendiente, confirmada, cancelada o atendida']);
            exit;
        }

        $repo->cambiarEstado($id, $data['estado']);
        echo json_encode(['mensaje' => 'Estado actualizado exitosamente [RQF-05]']);
        exit;
    }
}

// Detalle y Reprogramar cita (PUT /api/citas/{id})
if (preg_match('#^/api/citas/(\d+)$#', $uri, $matches)) {
    $id = $matches[1];

    if ($method === 'GET') {
        $cita = $repo->getById($id);
        if (!$cita) {
            http_response_code(404);
            echo json_encode(['error' => 'Cita no encontrada']);
            exit;
        }
        echo json_encode($cita);
        exit;
    }

    if ($method === 'PUT') {
        $data = json_decode(file_get_contents('php://input'), true);
        if (empty($data['fecha_inicio']) || empty($data['fecha_fin'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Fechas requeridas']);
            exit;
        }

        $citaActual = $repo->getById($id);
        if (!$citaActual) {
            http_response_code(404);
            echo json_encode(['error' => 'Cita no encontrada']);
            exit;
        }

        // Validación de conflicto al reprogramar excluyendo la misma cita
        if ($repo->existeConflicto($citaActual['doctor_id'], $data['fecha_inicio'], $data['fecha_fin'], $id)) {
            http_response_code(409);
            echo json_encode(['error' => 'Conflicto: El doctor no tiene disponibilidad en ese horario']);
            exit;
        }

        $repo->updateHorario($id, $data['fecha_inicio'], $data['fecha_fin']);
        echo json_encode(['mensaje' => 'Horario actualizado exitosamente [RQF-04]']);
        exit;
    }
}

http_response_code(404);
echo json_encode(['error' => 'Ruta no encontrada']);