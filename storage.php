<?php
require_once 'config.php';

// CRUD Operations for Storage Table
// Create
if (isset($_POST['action']) && $_POST['action'] == 'create') {
    try {
        $stmt = $pdo->prepare("INSERT INTO storage (name, location) VALUES (?, ?)");
        $stmt->execute([
            $_POST['name'], 
            $_POST['location'], 
            // $_POST['capacity']
        ]);
        $message = "Storage added successfully!";
    } catch(PDOException $e) {
        $error = "Error adding storage: " . $e->getMessage();
    }
}

// Update
if (isset($_POST['action']) && $_POST['action'] == 'update') {
    try {
        $stmt = $pdo->prepare("UPDATE storage SET name = ?, location = ? WHERE id = ?");
        $stmt->execute([
            $_POST['name'], 
            $_POST['location'], 
            // $_POST['capacity'], 
            $_POST['id']
        ]);
        $message = "Storage updated successfully!";
    } catch(PDOException $e) {
        $error = "Error updating storage: " . $e->getMessage();
    }
}

// Delete
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM storage WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $message = "Storage deleted successfully!";
    } catch(PDOException $e) {
        $error = "Error deleting storage: " . $e->getMessage();
    }
}

// Read - Fetch Storage
$stmt = $pdo->query("SELECT * FROM storage");
$storages = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Edit Mode
$editStorage = null;
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM storage WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $editStorage = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
.message {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
        }
        .message.success { 
            background-color: #dff0d8; 
            color: #3c763d; 
            border: 1px solid #d6e9c6;
        }
        .message.error { 
            background-color: #f2dede; 
            color: #a94442; 
            border: 1px solid #ebccd1;
        }
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Storage Management</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 1200px; margin: 0 auto; padding: 20px; }
        table { width: 100%; border-collapse: collapse; }
        table, th, td { border: 1px solid #ddd; }
        th, td { padding: 8px; text-align: left; }
        form { background: #f4f4f4; padding: 20px; margin-bottom: 20px; }
        .message { color: green; }
        .error { color: red; }
        nav { margin-bottom: 20px; }
        nav a { margin-right: 10px; text-decoration: none; color: blue; }
    </style>
</head>
<body>
    <h1>Storage Management</h1>

    <!-- Navigation -->
    <nav>
        <a href="index.php">Dashboard</a> |
        <a href="inventory.php">Inventory</a> | 
        <a href="vendor.php">Vendors</a> | 
        <a href="storage.php">Storage</a>
    </nav>

    <!-- Display Messages -->
    <?php if(isset($message)): ?>
        <p class="message"><?= $message ?></p>
    <?php endif; ?>
    <?php if(isset($error)): ?>
        <p class="error"><?= $error ?></p>
    <?php endif; ?>

    <!-- Create/Update Form -->
    <form method="POST">
        <input type="hidden" name="action" value="<?= $editStorage ? 'update' : 'create' ?>">
        <?php if($editStorage): ?>
            <input type="hidden" name="id" value="<?= $editStorage['id'] ?>">
        <?php endif; ?>

        <input type="text" name="name" placeholder="Storage Name" 
               value="<?= $editStorage ? $editStorage['name'] : '' ?>" required>

        <input type="text" name="location" placeholder="Location" 
               value="<?= $editStorage ? $editStorage['location'] : '' ?>" required>

        <!-- <input type="number" name="capacity" placeholder="Capacity" 
               value="<?= $editStorage ? $editStorage['capacity'] : '' ?>" required> -->

        <button type="submit"><?= $editStorage ? 'Update' : 'Add' ?> Storage</button>
        <?php if($editStorage): ?>
            <a href="?">Cancel</a>
        <?php endif; ?>
    </form>

    <!-- Storage Table -->
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Location</th>
                <th>Capacity</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($storages as $storage): ?>
            <tr>
                <td><?= $storage['id'] ?></td>
                <td><?= $storage['name'] ?></td>
                <td><?= $storage['location'] ?></td>
                <!-- <td><?= $storage['capacity'] ?></td> -->
                <td>
                    <a href="?action=edit&id=<?= $storage['id'] ?>">Edit</a>
                    <a href="?action=delete&id=<?= $storage['id'] ?>" 
                       onclick="return confirm('Are you sure you want to delete this storage?');">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>