<?php
require_once 'config.php';

// CRUD Operations for Vendor Table
// Create
if (isset($_POST['action']) && $_POST['action'] == 'create') {
    try {
        $stmt = $pdo->prepare("INSERT INTO vendor (name, contact, items) VALUES (?, ?, ?)");
        $stmt->execute([
            $_POST['name'],
            $_POST['contact'],
            $_POST['items']
        ]);
        $message = "Vendor added successfully!";
    } catch (PDOException $e) {
        $error = "Error adding vendor: " . $e->getMessage();
    }
}

// Update
if (isset($_POST['action']) && $_POST['action'] == 'update') {
    try {
        $stmt = $pdo->prepare("UPDATE vendor SET name = ?, contact = ?, items = ? WHERE id = ?");
        $stmt->execute([
            $_POST['name'],
            $_POST['contact'],
            $_POST['items'],
            $_POST['id']
        ]);
        $message = "Vendor updated successfully!";
    } catch (PDOException $e) {
        $error = "Error updating vendor: " . $e->getMessage();
    }
}

// Delete
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM vendor WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $message = "Vendor deleted successfully!";
    } catch (PDOException $e) {
        $error = "Error deleting vendor: " . $e->getMessage();
    }
}

// Read - Fetch Vendors
$stmt = $pdo->query("SELECT * FROM vendor");
$vendors = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Edit Mode
$editVendor = null;
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM vendor WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $editVendor = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Vendor Management</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table,
        th,
        td {
            border: 1px solid #ddd;
        }

        th,
        td {
            padding: 8px;
            text-align: left;
        }

        form {
            background: #f4f4f4;
            padding: 20px;
            margin-bottom: 20px;
        }

        .message {
            color: green;
        }

        .error {
            color: red;
        }

        nav {
            margin-bottom: 20px;
        }

        nav a {
            margin-right: 10px;
            text-decoration: none;
            color: blue;
        }
    </style>
</head>

<body>
    <h1>Vendor Management</h1>

    <!-- Navigation -->
    <nav>
        <a href="index.php">Dashboard</a> |
        <a href="inventory.php">Inventory</a> |
        <a href="vendor.php">Vendors</a> |
        <a href="storage.php">Storage</a>
    </nav>

    <!-- Display Messages -->
    <?php if (isset($message)): ?>
        <p class="message"><?= $message ?></p>
    <?php endif; ?>
    <?php if (isset($error)): ?>
        <p class="error"><?= $error ?></p>
    <?php endif; ?>

    <!-- Create/Update Form -->
    <form method="POST">
        <input type="hidden" name="action" value="<?= $editVendor ? 'update' : 'create' ?>">
        <?php if ($editVendor): ?>
            <input type="hidden" name="id" value="<?= $editVendor['id'] ?>">
        <?php endif; ?>

        <input type="text" name="name" placeholder="Vendor Name"
            value="<?= $editVendor ? $editVendor['name'] : '' ?>" required>

        <input type="text" name="contact" placeholder="Contact"
            value="<?= $editVendor ? $editVendor['contact'] : '' ?>" required>

        <input type="text" name="items" placeholder="Items"
            value="<?= $editVendor ? $editVendor['items'] : '' ?>" required>

        <button type="submit"><?= $editVendor ? 'Update' : 'Add' ?> Vendor</button>
        <?php if ($editVendor): ?>
            <a href="?">Cancel</a>
        <?php endif; ?>
    </form>

    <!-- Vendors Table -->
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Contact</th>
                <th>Items</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($vendors as $vendor): ?>
                <tr>
                    <td><?= $vendor['id'] ?></td>
                    <td><?= $vendor['name'] ?></td>
                    <td><?= $vendor['contact'] ?></td>
                    <td><?= $vendor['items'] ?></td>
                    <td>
                        <a href="?action=edit&id=<?= $vendor['id'] ?>">Edit</a>
                        <a href="?action=delete&id=<?= $vendor['id'] ?>"
                            onclick="return confirm('Are you sure you want to delete this vendor?');">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>

</html>