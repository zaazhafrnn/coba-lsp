<?php
require_once 'db_connection.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Inventory Management Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-6">
        <h1 class="text-3xl font-bold text-center text-gray-800 mb-8">Inventory Management System</h1>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <a href="inventory.php" class="dashboard-item bg-white shadow-md rounded-lg p-6 text-center transition-transform transform hover:scale-105">
                <h2 class="text-xl font-semibold text-gray-800">Inventory</h2>
                <p class="text-gray-600">Manage Product Inventory</p>
            </a>

            <a href="vendor.php" class="dashboard-item bg-white shadow-md rounded-lg p-6 text-center transition-transform transform hover:scale-105">
                <h2 class="text-xl font-semibold text-gray-800">Vendors</h2>
                <p class="text-gray-600">Manage Vendor Information</p>
            </a>

            <a href="storage.php" class="dashboard-item bg-white shadow-md rounded-lg p-6 text-center transition-transform transform hover:scale-105">
                <h2 class="text-xl font-semibold text-gray-800">Storage</h2>
                <p class="text-gray-600">Manage Storage Locations</p>
            </a>
        </div>
    </div>
</body>
</html>