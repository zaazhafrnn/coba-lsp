<?php
require_once 'db_connection.php';
include 'layout.php';

if (isset($_POST['action']) && $_POST['action'] == 'create') {
    try {
        $stmt = $pdo->prepare("INSERT INTO storage (name, location) VALUES (?, ?)");
        $stmt->execute([
            $_POST['name'],
            $_POST['location'],
        ]);
        $message = "Storage added successfully!";
    } catch (PDOException $e) {
        $error = "Error adding storage: " . $e->getMessage();
    }
}

if (isset($_POST['action']) && $_POST['action'] == 'update') {
    try {
        $stmt = $pdo->prepare("UPDATE storage SET name = ?, location = ? WHERE id = ?");
        $stmt->execute([
            $_POST['name'],
            $_POST['location'],
            $_POST['id']
        ]);
        $message = "Storage updated successfully!";
    } catch (PDOException $e) {
        $error = "Error updating storage: " . $e->getMessage();
    }
}

if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM storage WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $message = "Storage deleted successfully!";
    } catch (PDOException $e) {
        $error = "Error deleting storage: " . $e->getMessage();
    }
}

$stmt = $pdo->query("SELECT * FROM storage");
$storages = $stmt->fetchAll(PDO::FETCH_ASSOC);

$editStorage = null;
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM storage WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $editStorage = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Storage - Inventory Management</title>
</head>

<body class="bg-gray-100">
    <div class="container mx-auto p-6">
        <h1 class="text-3xl font-bold mb-6 text-gray-800">Storage Management</h1>

        <?php if (isset($message)): ?>
            <p class="text-green-600 mb-4"><?= $message ?></p>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <p class="text-red-600 mb-4"><?= $error ?></p>
        <?php endif; ?>

        <form method="POST" class="bg-white p-6 rounded-lg shadow-md mb-6">
            <input type="hidden" name="action" value="<?= $editStorage ? 'update' : 'create' ?>">
            <?php if ($editStorage): ?>
                <input type="hidden" name="id" value="<?= $editStorage['id'] ?>">
            <?php endif; ?>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <input type="text" name="name" class="p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Storage Name"
                    value="<?= $editStorage ? $editStorage['name'] : '' ?>" required>

                <input type="text" name="location" class="p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Location"
                    value="<?= $editStorage ? $editStorage['location'] : '' ?>" required>
            </div>

            <div class="mt-4">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-300">
                    <?= $editStorage ? 'Update' : 'Add' ?> Storage
                </button>
                <?php if ($editStorage): ?>
                    <a class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-300 ml-2" href="?">Cancel</a>
                <?php endif; ?>
            </div>
        </form>

        <table class="min-w-full bg-white border border-gray-300 rounded-lg shadow-md">
            <thead>
                <tr class="bg-gray-200">
                    <th class="py-3 px-4 border-b text-left">#</th>
                    <th class="py-3 px-4 border-b text-left">Name</th>
                    <th class="py-3 px-4 border-b text-left">Location</th>
                    <th class="py-3 px-4 border-b text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($storages as $index => $storage): ?>
                    <tr class="hover:bg-gray-100 transition duration-200">
                        <td class="py-3 px-4 border-b"><?= $index + 1 ?></td>
                        <td class="py-3 px-4 border-b"><?= $storage['name'] ?></td>
                        <td class="py-3 px-4 border-b"><?= $storage['location'] ?></td>
                        <td class="py-3 px-4 border-b">
                            <a class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-1 px-3 rounded-lg transition duration-300" href="?action=edit&id=<?= $storage['id'] ?>">Edit</a>
                            <a class="bg-red-600 hover:bg-red-700 text-white font-semibold py-1 px-3 rounded-lg transition duration-300 ml-2" href="?action=delete&id=<?= $storage['id'] ?>" onclick="return confirm('Are you sure you want to delete this storage?');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>

</html>