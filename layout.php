<?php
$isLoginPage = false;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body>
    <?php if (!$isLoginPage): ?>
        <nav class="bg-gray-800 py-6 w-full px-28">
            <div class="flex justify-between items-center w-full">
                <a href="index.php" class="text-xl text-white font-bold">Inventory Management</a>
                <ul class="flex items-center space-x-4">
                    <li><a href="index.php" class="text-white hover:text-gray-300 transition duration-300">Dashboard</a></li>
                    <li><a href="inventory.php" class="text-white hover:text-gray-300 transition duration-300">Inventory</a></li>
                    <li><a href="vendor.php" class="text-white hover:text-gray-300 transition duration-300">Vendors</a></li>
                    <li><a href="storage.php" class="text-white hover:text-gray-300 transition duration-300">Storage</a></li>
                </ul>
            </div>
        </nav>
    <?php endif; ?>
</body>

</html>