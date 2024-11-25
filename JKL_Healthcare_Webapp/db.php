<?php
$dsn = "sqlite:jkl_healthcare.db";

try {
    $db = new PDO($dsn);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create tables
    $db->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT UNIQUE NOT NULL,
            password TEXT NOT NULL,
            role TEXT NOT NULL -- 'admin' or 'caregiver'
        );

        CREATE TABLE IF NOT EXISTS patients (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            address TEXT NOT NULL,
            medical_records TEXT NOT NULL
        );

        CREATE TABLE IF NOT EXISTS caregivers (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            availability TEXT NOT NULL,  -- 'available' or 'unavailable'
            email TEXT NOT NULL,
            phone_number TEXT NOT NULL
        );

        CREATE TABLE IF NOT EXISTS assignments (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            patient_id INTEGER NOT NULL,
            caregiver_id INTEGER NOT NULL,
            FOREIGN KEY (patient_id) REFERENCES patients (id),
            FOREIGN KEY (caregiver_id) REFERENCES caregivers (id)
        );

        CREATE TABLE IF NOT EXISTS appointments (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            patient_id INTEGER NOT NULL,
            caregiver_id INTEGER NOT NULL,
            appointment_time TEXT NOT NULL,
            status TEXT DEFAULT 'scheduled',
            FOREIGN KEY (patient_id) REFERENCES patients (id),
            FOREIGN KEY (caregiver_id) REFERENCES caregivers (id)
        );

        CREATE TABLE IF NOT EXISTS notifications (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            caregiver_id INTEGER NOT NULL,
            message TEXT NOT NULL,
            is_read INTEGER DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (caregiver_id) REFERENCES caregivers (id)
        );
    ");


    $stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
    $stmt->execute(['admin']);
    $adminExists = $stmt->fetchColumn();

    if (!$adminExists) {
        // Insert default admin user with hashed password
        $password = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $db->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $stmt->execute(['admin', $password, 'admin']);
    }

} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
