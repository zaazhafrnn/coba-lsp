<?php

$host = '127.0.0.1';
$dbname = "inventory_db";
$username = "user-baru";
$password = "password";

$pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
