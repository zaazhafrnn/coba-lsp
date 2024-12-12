<?php
require_once 'db_connection.php';
include 'layout.php';

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

if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM vendor WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $message = "Vendor deleted successfully!";
    } catch (PDOException $e) {
        $error = "Error deleting vendor: " . $e->getMessage();
    }
}

$stmt = $pdo->query("SELECT * FROM vendor");
$vendors = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
    <title>Vendor - Inventory Dashboard</title>
</head>

<body class="bg-gray-100">
    <div class="container mx-auto p-6">
        <h1 class="text-3xl font-bold mb-6 text-gray-800">Vendor Management</h1>

        <?php if (isset($message)): ?>
            <p class="text-green-600 mb-4"><?= $message ?></p>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <p class="text-red-600 mb-4"><?= $error ?></p>
        <?php endif; ?>

        <form method="POST" class="bg-white p-6 rounded-lg shadow-md mb-6">
            <input type="hidden" name="action" value="<?= $editVendor ? 'update' : 'create' ?>">
            <?php if ($editVendor): ?>
                <input type="hidden" name="id" value="<?= $editVendor['id'] ?>">
            <?php endif; ?>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <input type="text" class="p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" name="name" placeholder="Vendor Name"
                    value="<?= $editVendor ? $editVendor['name'] : '' ?>" required>

                <input type="text" class="p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" name="contact" placeholder="Contact"
                    value="<?= $editVendor ? $editVendor['contact'] : '' ?>" required>

                <input type="text" class="p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" name="items" placeholder="Items"
                    value="<?= $editVendor ? $editVendor['items'] : '' ?>" required>
            </div>

            <div class="mt-4">
                <button class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-300" type="submit">
                    <?= $editVendor ? 'Update' : 'Add ' ?> Vendor
                </button>
                <?php if ($editVendor): ?>
                    <a class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-300 ml-2" href="?">Cancel</a>
                <?php endif; ?>
            </div>
        </form>

        <table class="min-w-full bg-white border border-gray-300 rounded-lg shadow-md">
            <thead>
                <tr class="bg-gray-200 text-center">
                    <th class="py-3 px-4 border-b text-left">#</th>
                    <th class="py-3 px-4 border-b text-left">Name</th>
                    <th class="py-3 px-4 border-b text-left">Contact</th>
                    <th class="py-3 px-4 border-b text-left">Items</th>
                    <th class="py-3 px-4 border-b text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($vendors as $index => $vendor): ?>
                    <tr class="hover:bg-gray-100 transition duration-200">
                        <td class="py-3 px-4 border-b"><?= $index + 1 ?></td>
                        <td class="py-3 px-4 border-b"><?= $vendor['name'] ?></td>
                        <td class="py-3 px-4 border-b"><?= $vendor['contact'] ?></td>
                        <td class="py-3 px-4 border-b"><?= $vendor['items'] ?></td>
                        <td class="py-3 px-4 border-b">
                            <a class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-1 px-3 rounded-lg transition duration-300" href="?action=edit&id=<?= $vendor['id'] ?>">Edit</a>
                            <a class="bg-red-600 hover:bg-red-700 text-white font-semibold py-1 px-3 rounded-lg transition duration-300 ml-2" href="?action=delete&id=<?= $vendor['id'] ?>" onclick="return confirm('Are you sure you want to delete this vendor?');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>

</html>