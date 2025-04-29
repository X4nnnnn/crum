<?php
// Simulating a database with a local array
session_start();

if (!isset($_SESSION['patients'])) {
    $_SESSION['patients'] = [
        ['patient_id' => 1, 'name' => 'John Doe', 'address' => '123 Main St'],
        ['patient_id' => 2, 'name' => 'Jane Smith', 'address' => '456 Oakland St'],
        // Add more dummy data as needed
    ];
}

// Handle patient actions (Add, Edit, Delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        // Add a new patient
        $name = $_POST['name'];
        $address = $_POST['address'];
        $newId = max(array_column($_SESSION['patients'], 'patient_id')) + 1;
        $_SESSION['patients'][] = ['patient_id' => $newId, 'name' => $name, 'address' => $address];
    } elseif (isset($_POST['edit'])) {
        // Update existing patient
        $id = $_POST['id'];
        $name = $_POST['name'];
        $address = $_POST['address'];
        foreach ($_SESSION['patients'] as &$patient) {
            if ($patient['patient_id'] == $id) {
                $patient['name'] = $name;
                $patient['address'] = $address;
                break;
            }
        }
    }
} elseif (isset($_GET['delete'])) {
    // Delete a patient
    $id = $_GET['delete'];
    foreach ($_SESSION['patients'] as $index => $patient) {
        if ($patient['patient_id'] == $id) {
            unset($_SESSION['patients'][$index]);
            break;
        }
    }
}

// Get patient data (either editing an existing patient or adding a new one)
$editingPatient = null;
if (isset($_GET['edit'])) {
    $editingPatient = null;
    foreach ($_SESSION['patients'] as $patient) {
        if ($patient['patient_id'] == $_GET['edit']) {
            $editingPatient = $patient;
            break;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Management System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .container {
            width: 80%;
            margin: 0 auto;
            padding: 20px;
        }
        h1, h2 {
            text-align: center;
        }
        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        th {
            background-color: #f2f2f2;
        }
        .btn {
            padding: 8px 16px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 4px;
            text-decoration: none;
        }
        .btn-danger {
            background-color: #f44336;
        }
        .btn-primary {
            background-color: #2196F3;
        }
        .form-group {
            margin: 10px 0;
        }
        .form-group input, .form-group textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Patient Management System</h1>

    <!-- Patient List Table -->
    <h2>Patient List</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Address</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($_SESSION['patients'] as $patient): ?>
        <tr>
            <td><?= $patient['patient_id'] ?></td>
            <td><?= $patient['name'] ?></td>
            <td><?= $patient['address'] ?></td>
            <td>
                <a href="?edit=<?= $patient['patient_id'] ?>" class="btn btn-primary">Edit</a>
                <a href="?delete=<?= $patient['patient_id'] ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this patient?')">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

    <br>

    <!-- Add/Edit Patient Form -->
    <h2><?= $editingPatient ? 'Edit Patient' : 'Add New Patient' ?></h2>
    <form method="POST">
        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" name="name" id="name" required value="<?= $editingPatient ? $editingPatient['name'] : '' ?>">
        </div>
        <div class="form-group">
            <label for="address">Address:</label>
            <textarea name="address" id="address" required><?= $editingPatient ? $editingPatient['address'] : '' ?></textarea>
        </div>
        <div class="form-group">
            <?php if ($editingPatient): ?>
                <input type="hidden" name="id" value="<?= $editingPatient['patient_id'] ?>">
                <input type="submit" name="edit" value="Update Patient" class="btn">
            <?php else: ?>
                <input type="submit" name="add" value="Add Patient" class="btn">
            <?php endif; ?>
        </div>
    </form>
</div>

</body>
</html>
