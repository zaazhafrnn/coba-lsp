<?php
require_once 'db_connection.php';
include 'layout.php';

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

$vendorsList = [];
$selectedItem = $_POST['items'] ?? $_GET['items'] ?? null;

$initialVendorsStmt = $pdo->query("SELECT * FROM vendor");
$initialVendorsList = $initialVendorsStmt->fetchAll(PDO::FETCH_ASSOC);

$vendorsStmt = $pdo->prepare("SELECT * FROM vendor WHERE FIND_IN_SET(?, REPLACE(items, ', ', ',')) > 0");
$vendorsStmt->execute([$selectedItem]);
$vendorsList = $vendorsStmt->fetchAll(PDO::FETCH_ASSOC);

$storageStmt = $pdo->query("SELECT * FROM storage");
$storageLocations = $storageStmt->fetchAll(PDO::FETCH_ASSOC);

$message = null;
$error = null;

if (isset($_POST['action']) && $_POST['action'] == 'create') {
    try {

        $stmt = $pdo->prepare("INSERT INTO inventory (items, stock, id_storage) VALUES (?, ?, ?)");
        $stmt->execute([
            $_POST['items'],
            $_POST['stock'],
            $_POST['id_storage']
        ]);
        $message = "Inventory item added successfully!";

        $_POST = [];
    } catch (Exception $e) {
        $error = "Error adding inventory item: " . $e->getMessage();
    }
}

if (isset($_POST['action']) && $_POST['action'] == 'update') {
    try {

        $stmt = $pdo->prepare("UPDATE inventory SET items = ?, stock = ?, id_storage = ? WHERE id = ?");
        $stmt->execute([
            $_POST['items'],
            $_POST['stock'],
            $_POST['id_storage'],
            $_POST['id']
        ]);
        $message = "Inventory item updated successfully!";
    } catch (Exception $e) {
        $error = "Error updating inventory item: " . $e->getMessage();
    }
}

if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM inventory WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $message = "Inventory item deleted successfully!";
    } catch (PDOException $e) {
        $error = "Error deleting inventory item: " . $e->getMessage();
    }
}

$stmt = $pdo->query("
    SELECT i.*, s.name AS storage_name 
    FROM inventory i 
    LEFT JOIN storage s ON i.id_storage = s.id
");
$inventories = $stmt->fetchAll(PDO::FETCH_ASSOC);

$editInventory = null;
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM inventory WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $editInventory = $stmt->fetch(PDO::FETCH_ASSOC);

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
    <title>Inventory - Management</title>
</head>

<body class="bg-gray-100">
    <div class="container mx-auto p-6">
        <h1 class="text-3xl font-bold mb-6 text-gray-800">Inventory Management</h1>

        <?php if ($message): ?>
            <div class="bg-green-100 text-green-700 p-4 rounded-lg mb-4"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="bg-red-100 text-red-700 p-4 rounded-lg mb-4"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" class="bg-white p-6 rounded-lg shadow-md mb-6">
            <input type="hidden" name="action" value="<?= $editInventory ? 'update' : 'create' ?>">
            <?php if ($editInventory): ?>
                <input type="hidden" name="id" value="<?= $editInventory['id'] ?>">
            <?php endif; ?>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <select id="items-select" name="items" class="p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" onchange="updateVendors()" required>
                    <option value="">Select Item</option>
                    <?php foreach ($uniqueItems as $item): ?>
                        <option value="<?= htmlspecialchars($item) ?>"
                            <?= ($editInventory && $editInventory['items'] == $item) ||
                                ($selectedItem && $selectedItem == $item) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($item) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <input type="text" name="stock" class="p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Stock"
                    value="<?= $editInventory ? htmlspecialchars($editInventory['stock']) : '' ?>" required>

                <select name="id_storage" class="p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    <option value="">Select Storage</option>
                    <?php foreach ($storageLocations as $storage): ?>
                        <option value="<?= $storage['id'] ?>"
                            <?= $editInventory && $editInventory['id_storage'] == $storage['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($storage['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mt-4">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-300">
                    <?= $editInventory ? 'Update' : 'Add' ?> Inventory Item
                </button>
                <?php if ($editInventory): ?>
                    <a href="?"
                        class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-300 ml-2">Cancel</a>
                <?php endif; ?>
            </div>
        </form>

        <table class="min-w-full bg-white border border-gray-300 rounded-lg shadow-md">
            <thead>
                <tr class="bg-gray-200">
                    <th class="py-3 px-4 border-b text-left">#</th>
                    <th class="py-3 px-4 border-b text-left">Item</th>
                    <th class="py-3 px-4 border-b text-left">Stock</th>
                    <th class="py-3 px-4 border-b text-left">Storage</th>
                    <th class="py-3 px-4 border-b text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($inventories as $index => $inventory): ?>
                    <tr class="<?= $inventory['stock'] <= 0 ? 'bg-red-300' : 'hover:bg-gray-100' ?> transition duration-200">
                        <td class="py-3 px-4 border-b"><?= $index + 1 ?></td>
                        <td class="py-3 px-4 border-b"><?= htmlspecialchars($inventory['items']) ?></td>
                        <td class="py-3 px-4 border-b"><?= htmlspecialchars($inventory['stock']) ?></td>
                        <td class="py-3 px-4 border-b"><?= htmlspecialchars($inventory['storage_name']) ?></td>
                        <td class="py-3 px-4 border-b"> <a class=" bg-blue-600 hover:bg-blue-700 text-white font-semibold py-1 px-3 rounded-lg transition duration-300" href="?action=edit&id=<?= $inventory['id'] ?>">Edit</a>
                            <a class="bg-red-600 hover:bg-red-700 text-white font-semibold py-1 px-3 rounded-lg transition duration-300 ml-2" href="?action=delete&id=<?= $inventory['id'] ?>" onclick="return confirm('Are you sure you want to delete this inventory item?');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>

</html>