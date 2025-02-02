<?php
require_once __DIR__ . '/../includes/Database.php';

// Get database connection
$connect = Database::getConnection();

// For backward compatibility
$host = "localhost";
$user = "root";
$password = "";
$dbname = "phpstore";