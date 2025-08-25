<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Define database credentials
define('DB_HOST', '127.0.0.1');
define('DB_USER', 'root');
define('DB_PASS', '8TyUN=cV[-Xy.ERB$H}|');
define('DB_NAME', 'kokan renge');

// Attempt to connect to MySQL using PDO
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME;
    $pdo = new PDO($dsn, DB_USER, DB_PASS);
    
    // Set PDO attributes to throw exceptions on errors
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<h1>Connected successfully to MySQL database: '" . DB_NAME . "'</h1>";
    
} catch (PDOException $e) {
    // If the connection fails, display an error message
    echo "<h1>Database connection failed: " . $e->getMessage() . "</h1>";
}

?>