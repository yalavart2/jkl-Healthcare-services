<?php
require 'db.php';
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_patient'])) {
        $stmt = $db->prepare("INSERT INTO patients (name, address, medical_records) VALUES (?, ?, ?)");
        $stmt->execute([$_POST['name'], $_POST['address'], $_POST['medical_records']]);
    } elseif (isset($_POST['remove_patient'])) {
        $stmt = $db->prepare("DELETE FROM patients WHERE id = ?");
        $stmt->execute([$_POST['patient_id']]);
    } elseif (isset($_POST['edit_patient'])) {
        $stmt = $db->prepare("UPDATE patients SET name = ?, address = ?, medical_records = ? WHERE id = ?");
        $stmt->execute([$_POST['name'], $_POST['address'], $_POST['medical_records'], $_POST['patient_id']]);
    }
}

$patients = $db->query("SELECT * FROM patients")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Management</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: #000;
            color: #fff;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            min-height: 100vh;
        }

        header {
            background-color: #000;
            width: 100%;
            padding: 15px 0;
            text-align: center;
            font-size: 36px;
            font-weight: bold;
            color: #00ffff; /* Neon color */
        }

        .container {
            width: 90%;
            max-width: 800px;
            background: rgba(0, 0, 0, 0.9);
            border-radius: 10px;
            box-shadow: 0 0 10px #00ffff, 0 0 20px #00ffff;
            padding: 20px;
            margin-top: 20px;
        }

        h1 {
            text-align: center;
            font-size: 28px;
            margin-bottom: 20px;
            color: #00ffff;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-bottom: 20px;
        }

        input, textarea, button {
            padding: 10px;
            font-size: 16px;
            border: 1px solid #00ffff;
            border-radius: 5px;
            background: transparent;
            color: #00ffff;
            outline: none;
            transition: all 0.3s ease;
        }

        input:hover, textarea:hover {
            box-shadow: 0 0 10px #00ffff, 0 0 15px #00ffff;
        }

        button {
            cursor: pointer;
            background: #00ffff;
            color: #000;
            font-weight: bold;
            text-transform: uppercase;
        }

        button:hover {
            background: #005f5f;
            box-shadow: 0 0 15px #00ffff, 0 0 25px #00ffff;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #00ffff;
        }

        th, td {
            padding: 10px;
            text-align: left;
            color: #fff;
        }

        th {
            background-color: #00ffff;
            color: #000;
        }

        tr:nth-child(even) {
            background-color: rgba(0, 0, 0, 0.8);
        }

        .action-buttons {
            display: flex;
            gap: 10px;
        }

        .back-button {
            margin-top: 20px;
            display: flex;
            justify-content: center;
        }

        footer {
            text-align: center;
            margin-top: auto;
            padding: 10px 0;
            font-size: 14px;
            color: #00ffff;
        }
    </style>
</head>
<body>
    <header>
        JKL Healthcare Services - Patient Management
    </header>

    <div class="container">
        <h1>Patient Management</h1>
        
        <!-- Add Patient Form -->
        <form method="POST">
            <h3>Add New Patient</h3>
            <input type="text" name="name" placeholder="Patient Name" required>
            <input type="text" name="address" placeholder="Patient Address" required>
            <textarea name="medical_records" placeholder="Medical Records" rows="4" required></textarea>
            <button type="submit" name="add_patient">Add Patient</button>
        </form>

        <!-- Back to Dashboard Button -->
        <div class="back-button">
            <a href="admin_dashboard.php">
                <button>Back to Dashboard</button>
            </a>
        </div>

        <!-- Patient List in Table -->
        <h3>Existing Patients</h3>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Address</th>
                    <th>Medical Records</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($patients as $patient): ?>
                    <tr>
                        <td><?= htmlspecialchars($patient['name']) ?></td>
                        <td><?= htmlspecialchars($patient['address']) ?></td>
                        <td><?= htmlspecialchars($patient['medical_records']) ?></td>
                        <td>
                            <div class="action-buttons">
                                <!-- Edit Form -->
                                <form method="POST" style="margin: 0;">
                                    <input type="hidden" name="patient_id" value="<?= $patient['id'] ?>">
                                    <input type="hidden" name="name" value="<?= htmlspecialchars($patient['name']) ?>">
                                    <input type="hidden" name="address" value="<?= htmlspecialchars($patient['address']) ?>">
                                    <input type="hidden" name="medical_records" value="<?= htmlspecialchars($patient['medical_records']) ?>">
                                    <button type="submit" name="edit_patient">Edit</button>
                                </form>

                                <!-- Remove Form -->
                                <form method="POST" style="margin: 0;">
                                    <input type="hidden" name="patient_id" value="<?= $patient['id'] ?>">
                                    <button type="submit" name="remove_patient">Remove</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <footer>
        &copy; <?= date("Y") ?> JKL Healthcare Services. All Rights Reserved.
    </footer>
</body>
</html>
