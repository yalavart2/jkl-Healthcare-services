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
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Get caregiver ID from URL
if (!isset($_GET['id'])) {
    die("Caregiver ID is required.");
}

$id = $_GET['id'];

// Fetch caregiver details
$stmt = $db->prepare("SELECT * FROM caregivers WHERE id = ?");
$stmt->execute([$id]);
$caregiver = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$caregiver) {
    die("Caregiver not found.");
}

// Update caregiver
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_caregiver'])) {
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Caregiver</title>
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
        Edit Caregiver
    </header>

    <div class="dashboard-container">
        <h2>Edit Caregiver Details</h2>

        <form method="POST">
            <input type="text" name="name" value="<?php echo htmlspecialchars($caregiver['name']); ?>" placeholder="Caregiver Name" required>
            <select name="availability">
                <option value="available" <?php echo $caregiver['availability'] === 'available' ? 'selected' : ''; ?>>Available</option>
                <option value="unavailable" <?php echo $caregiver['availability'] === 'unavailable' ? 'selected' : ''; ?>>Unavailable</option>
            </select>
            <input type="email" name="email" value="<?php echo htmlspecialchars($caregiver['email']); ?>" placeholder="Caregiver Email" required>
            <input type="text" name="phone_number" value="<?php echo htmlspecialchars($caregiver['phone_number']); ?>" placeholder="Phone Number" required>
            <button type="submit" name="update_caregiver">Update Caregiver</button>
        </form>

        <?php if (isset($message)) echo "<p style='color: #00ffff;'>$message</p>"; ?>

        <div class="back-button">
            <a href="manage_caregivers.php">Back to Caregivers List</a>
        </div>
    </div>

    <footer>
        &copy; 2024 JKL Healthcare Services
    </footer>
</body>
</html>
