<?php
require_once 'config.php';

// Fetch unique items from vendors
$itemsStmt = $pdo->query("SELECT items FROM vendor");
$vendorItems = $itemsStmt->fetchAll(PDO::FETCH_COLUMN);
$uniqueItems = [];

foreach ($vendorItems as $itemList) {
    $items = explode(',', $itemList);
    foreach ($items as $item) {
        $trimmedItem = trim($item);
        if (!in_array($trimmedItem, $uniqueItems) && !empty($trimmedItem)) {
            $uniqueItems[] = $trimmedItem;
        }
    }
}
sort($uniqueItems);

// Fetch vendors based on selected item
$vendorsList = [];
$selectedItem = $_POST['items'] ?? $_GET['items'] ?? null;

// Fetch initial list of vendors (use this when no item is selected)
$initialVendorsStmt = $pdo->query("SELECT * FROM vendor");
$initialVendorsList = $initialVendorsStmt->fetchAll(PDO::FETCH_ASSOC);

$vendorsStmt = $pdo->prepare("SELECT * FROM vendor WHERE FIND_IN_SET(?, REPLACE(items, ', ', ',')) > 0");
$vendorsStmt->execute([$selectedItem]);
$vendorsList = $vendorsStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch storage locations
$storageStmt = $pdo->query("SELECT * FROM storage");
$storageLocations = $storageStmt->fetchAll(PDO::FETCH_ASSOC);

// CRUD Operations for Inventory
$message = null;
$error = null;

// Create
if (isset($_POST['action']) && $_POST['action'] == 'create') {
    try {
        // Validate that idvendor is not empty
        if (empty($_POST['idvendor'])) {
            throw new Exception("Please select a vendor.");
        }

        $stmt = $pdo->prepare("INSERT INTO inventory (items, idvendor, stock, idstorage) VALUES (?, ?, ?, ?)");
        $stmt->execute([
            $_POST['items'],
            $_POST['idvendor'],
            $_POST['stock'],
            $_POST['idstorage']
        ]);
        $message = "Inventory item added successfully!";

        // Clear POST data after successful submission
        $_POST = [];
    } catch (Exception $e) {
        $error = "Error adding inventory item: " . $e->getMessage();
    }
}

// Update (similar to create, with ID check)
if (isset($_POST['action']) && $_POST['action'] == 'update') {
    try {
        // Validate that idvendor is not empty
        if (empty($_POST['idvendor'])) {
            throw new Exception("Please select a vendor.");
        }

        $stmt = $pdo->prepare("UPDATE inventory SET items = ?, idvendor = ?, stock = ?, idstorage = ? WHERE id = ?");
        $stmt->execute([
            $_POST['items'],
            $_POST['idvendor'],
            $_POST['stock'],
            $_POST['idstorage'],
            $_POST['id']
        ]);
        $message = "Inventory item updated successfully!";
    } catch (Exception $e) {
        $error = "Error updating inventory item: " . $e->getMessage();
    }
}

// Delete
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM inventory WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $message = "Inventory item deleted successfully!";
    } catch (PDOException $e) {
        $error = "Error deleting inventory item: " . $e->getMessage();
    }
}

// Fetch Inventory with Vendor and Storage Information
$stmt = $pdo->query("
    SELECT i.*, v.name AS vendor_name, s.name AS storage_name 
    FROM inventory i 
    LEFT JOIN vendor v ON i.idvendor = v.id 
    LEFT JOIN storage s ON i.idstorage = s.id
");
$inventories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Edit Mode
$editInventory = null;
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM inventory WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $editInventory = $stmt->fetch(PDO::FETCH_ASSOC);

    // Fetch vendors for the edit mode's selected item
    if ($editInventory['items']) {
        $vendorsStmt = $pdo->prepare("SELECT * FROM vendor WHERE FIND_IN_SET(?, REPLACE(items, ', ', ',')) > 0");
        $vendorsStmt->execute([$editInventory['items']]);
        $vendorsList = $vendorsStmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Inventory Management</title>
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

        .form-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-bottom: 10px;
        }

        select,
        input {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }

        .message {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
        }

        .success {
            background-color: #dff0d8;
            color: #3c763d;
        }

        .error {
            background-color: #f2dede;
            color: #a94442;
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
    <script>
        function updateVendors() {
            const itemSelect = document.getElementById('items-select');
            const selectedItem = itemSelect.value;

            // If an item is selected, reload the page with the selected item
            if (selectedItem) {
                window.location.href = '?items=' + encodeURIComponent(selectedItem);
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const itemSelect = document.getElementById('items-select');
            const vendorSelect = document.querySelector('select[name="idvendor"]');

            // Disable vendor dropdown initially
            vendorSelect.disabled = true;

            // Enable vendor dropdown when an item is selected
            itemSelect.addEventListener('change', function() {
                if (this.value) {
                    vendorSelect.disabled = false;
                } else {
                    vendorSelect.disabled = true;
                }
            });

            // If editing and an item is pre-selected, ensure vendor is enabled
            if (itemSelect.value) {
                vendorSelect.disabled = false;
            }
        });
    </script>
</head>

<body>
    <h1>Inventory Management</h1>

    <nav>
        <a href="index.php">Dashboard</a> |
        <a href="inventory.php">Inventory</a> |
        <a href="vendor.php">Vendors</a> |
        <a href="storage.php">Storage</a>
    </nav>

    <?php if ($message): ?>
        <div class="message success"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="message error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <!-- Create/Update Form -->
    <form method="POST">
        <div class="form-row">
            <input type="hidden" name="action" value="<?= $editInventory ? 'update' : 'create' ?>">
            <?php if ($editInventory): ?>
                <input type="hidden" name="id" value="<?= $editInventory['id'] ?>">
            <?php endif; ?>

            <select id="items-select" name="items" onchange="updateVendors()" required>
                <option value="">Select Item</option>
                <?php foreach ($uniqueItems as $item): ?>
                    <option value="<?= htmlspecialchars($item) ?>"
                        <?= ($editInventory && $editInventory['items'] == $item) ||
                            ($selectedItem && $selectedItem == $item) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($item) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <select name="idvendor" required>
                <option value="">Select Vendor</option>
                <?php foreach ($vendorsList as $vendor): ?>
                    <option value="<?= $vendor['id'] ?>"
                        <?= ($editInventory && $editInventory['idvendor'] == $vendor['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($vendor['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <input type="text" name="stock" placeholder="Stock"
                value="<?= $editInventory ? htmlspecialchars($editInventory['stock']) : '' ?>" required>

            <select name="idstorage" required>
                <option value="">Select Storage</option>
                <?php foreach ($storageLocations as $storage): ?>
                    <option value="<?= $storage['id'] ?>"
                        <?= $editInventory && $editInventory['idstorage'] == $storage['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($storage['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="action-buttons">
            <button type="submit"><?= $editInventory ? 'Update' : 'Add' ?> Inventory Item</button>
            <?php if ($editInventory): ?>
                <a href="?">Cancel</a>
            <?php endif; ?>
        </div>
    </form>

    <!-- Inventory Table -->
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Item</th>
                <th>Vendor</th>
                <th>Stock</th>
                <th>Storage</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($inventories as $inventory): ?>
                <tr>
                    <td><?= htmlspecialchars($inventory['id']) ?></td>
                    <td><?= htmlspecialchars($inventory['items']) ?></td>
                    <td><?= htmlspecialchars($inventory['vendor_name']) ?></td>
                    <td><?= htmlspecialchars($inventory['stock']) ?></td>
                    <td><?= htmlspecialchars($inventory['storage_name']) ?></td>
                    <td>
                        <a href="?action=edit&id=<?= $inventory['id'] ?>">Edit</a>
                        <a href="?action=delete&id=<?= $inventory['id'] ?>"
                            onclick="return confirm('Are you sure you want to delete this inventory item?');">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>

</html>