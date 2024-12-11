<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Inventory Management Dashboard</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            max-width: 800px; 
            margin: 0 auto; 
            padding: 20px; 
            text-align: center;
        }
        .dashboard-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 20px;
            margin-top: 50px;
        }
        .dashboard-item {
            border: 1px solid #ddd;
            padding: 20px;
            text-decoration: none;
            color: #333;
            background-color: #f4f4f4;
            transition: background-color 0.3s ease;
        }
        .dashboard-item:hover {
            background-color: #e0e0e0;
        }
        h1 {
            color: #333;
        }
    </style>
</head>
<body>
    <h1>Inventory Management System</h1>

    <div class="dashboard-grid">
        <a href="inventory.php" class="dashboard-item">
            <h2>Inventory</h2>
            <p>Manage Product Inventory</p>
        </a>

        <a href="vendor.php" class="dashboard-item">
            <h2>Vendors</h2>
            <p>Manage Vendor Information</p>
        </a>

        <a href="storage.php" class="dashboard-item">
            <h2>Storage</h2>
            <p>Manage Storage Locations</p>
        </a>
    </div>
</body>
</html>