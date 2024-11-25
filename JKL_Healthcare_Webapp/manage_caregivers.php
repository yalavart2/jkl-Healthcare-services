<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$dsn = "sqlite:jkl_healthcare.db";
try {
    // Connect to the database
    $db = new PDO($dsn);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create tables if they do not exist
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
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Add Caregiver
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_caregiver'])) {
    $name = $_POST['name'];
    $availability = $_POST['availability'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];

    try {
        $stmt = $db->prepare("INSERT INTO caregivers (name, availability, email, phone_number) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $availability, $email, $phone_number]);
        $message = "Caregiver added successfully.";
    } catch (PDOException $e) {
        $message = "Error: " . $e->getMessage();
    }
}

// Update Caregiver
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_caregiver'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $availability = $_POST['availability'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];

    try {
        $stmt = $db->prepare("UPDATE caregivers SET name = ?, availability = ?, email = ?, phone_number = ? WHERE id = ?");
        $stmt->execute([$name, $availability, $email, $phone_number, $id]);
        $message = "Caregiver updated successfully.";
    } catch (PDOException $e) {
        $message = "Error: " . $e->getMessage();
    }
}

// Delete Caregiver
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    try {
        $stmt = $db->prepare("DELETE FROM caregivers WHERE id = ?");
        $stmt->execute([$id]);
        $message = "Caregiver deleted successfully.";
    } catch (PDOException $e) {
        $message = "Error: " . $e->getMessage();
    }
}

// Fetch caregivers
try {
    $stmt = $db->query("SELECT * FROM caregivers");
    $caregivers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching caregivers: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Caregivers</title>
    <style>
        /* Existing styles */
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: #121212;
            color: #fff;
        }

        header {
            background-color: #00ffff;
            color: #000;
            text-align: center;
            padding: 15px;
            font-size: 36px;
            font-weight: bold;
        }

        .dashboard-container {
            width: 80%;
            max-width: 1000px;
            margin: 30px auto;
            padding: 20px;
            background-color: rgba(0, 0, 0, 0.8);
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 255, 255, 0.8);
        }

        h2 {
            font-size: 24px;
            color: #00ffff;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-bottom: 20px;
        }

        input, select, button {
            padding: 12px;
            font-size: 16px;
            border: 2px solid #00ffff;
            border-radius: 5px;
            background-color: transparent;
            color: #00ffff;
            outline: none;
            transition: all 0.3s ease;
        }

        input:hover, select:hover {
            box-shadow: 0 0 10px #00ffff, 0 0 15px #00ffff;
        }

        button {
            cursor: pointer;
            background-color: #00ffff;
            color: #000;
            font-weight: bold;
        }

        button:hover {
            background-color: #00cc99;
            box-shadow: 0 0 15px #00ffff, 0 0 25px #00ffff;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border: 1px solid #00ffff;
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

        .action-buttons a {
            padding: 6px 12px;
            border-radius: 5px;
            background-color: #00ffff;
            color: #000;
            font-weight: bold;
            text-decoration: none;
        }

        .action-buttons a:hover {
            background-color: #00cc99;
            box-shadow: 0 0 15px #00ffff, 0 0 25px #00ffff;
        }

        .back-button {
            text-align: center;
            margin-top: 20px;
        }

        .back-button a {
            padding: 10px 20px;
            background-color: #00ffff;
            color: #000;
            font-weight: bold;
            text-decoration: none;
            border-radius: 5px;
        }

        .back-button a:hover {
            background-color: #00cc99;
        }

        footer {
            text-align: center;
            margin-top: auto;
            padding: 10px;
            font-size: 14px;
            color: #00ffff;
        }
    </style>
</head>
<body>
    <header>
        Manage Caregivers
    </header>

    <div class="dashboard-container">
        <h2>Add New Caregiver</h2>
        <form method="POST">
            <input type="text" name="name" placeholder="Caregiver Name" required>
            <select name="availability">
                <option value="available">Available</option>
                <option value="unavailable">Unavailable</option>
            </select>
            <input type="email" name="email" placeholder="Caregiver Email" required>
            <input type="text" name="phone_number" placeholder="Phone Number" required>
            <button type="submit" name="add_caregiver">Add Caregiver</button>
        </form>

        <?php if (isset($message)) echo "<p style='color: #00ffff;'>$message</p>"; ?>

        <h2>Existing Caregivers</h2>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Availability</th>
                    <th>Email</th>
                    <th>Phone Number</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($caregivers as $caregiver): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($caregiver['name']); ?></td>
                        <td><?php echo htmlspecialchars($caregiver['availability']); ?></td>
                        <td><?php echo htmlspecialchars($caregiver['email']); ?></td>
                        <td><?php echo htmlspecialchars($caregiver['phone_number']); ?></td>
                        <td class="action-buttons">
                            <a href="edit_caregiver.php?id=<?php echo $caregiver['id']; ?>">Edit</a>
                            <a href="?delete=<?php echo $caregiver['id']; ?>" onclick="return confirm('Are you sure you want to delete this caregiver?');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="back-button">
        <a href="admin_dashboard.php">Back to Dashboard</a>
    </div>

    <footer>
        &copy; 2024 JKL Healthcare Services
    </footer>
</body>
</html>
