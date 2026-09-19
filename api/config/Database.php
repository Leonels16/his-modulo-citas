<?php
class Database {
    private static $pdo = null;

    public static function getConnection() {
        if (self::$pdo === null) {
            $dbPath = __DIR__ . '/../../database.sqlite';
            $isNew = !file_exists($dbPath);

            self::$pdo = new PDO('sqlite:' . $dbPath);
            self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            if ($isNew) {
                self::initTables();
            }
        }
        return self::$pdo;
    }

    private static function initTables() {
        $sql = "
        CREATE TABLE IF NOT EXISTS doctores (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            nombre TEXT NOT NULL,
            especialidad TEXT NOT NULL
        );

        CREATE TABLE IF NOT EXISTS pacientes (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            nombre TEXT NOT NULL,
            telefono TEXT NOT NULL
        );

        CREATE TABLE IF NOT EXISTS citas (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            doctor_id INTEGER NOT NULL,
            paciente_id INTEGER NOT NULL,
            fecha_inicio TEXT NOT NULL,
            fecha_fin TEXT NOT NULL,
            motivo TEXT NOT NULL,
            estado TEXT DEFAULT 'pendiente',
            FOREIGN KEY (doctor_id) REFERENCES doctores(id),
            FOREIGN KEY (paciente_id) REFERENCES pacientes(id)
        );

        INSERT INTO doctores (nombre, especialidad) VALUES 
        ('Dr. Carlos Mendoza', 'Medicina General'),
        ('Dra. Sofia Ramos', 'Pediatría'),
        ('Dr. Roberto Gómez', 'Cardiología');

        INSERT INTO pacientes (nombre, telefono) VALUES 
        ('Oscar Perez', '55512345'),
        ('Maria Lopez', '55598765'),
        ('Juan Ramirez', '55533221');
        ";
        self::$pdo->exec($sql);
    }
}