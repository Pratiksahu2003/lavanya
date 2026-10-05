<?php
/**
 * NOVAHOMZ — Database Configuration
 * PDO connection with error handling
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'u817968670_lavanya');
define('DB_USER', 'u817968670_lavanya');
define('DB_PASS', 'Uj@AKN6^d');
define('DB_CHARSET', 'utf8mb4');

define('BASE_URL', 'https://lavanyaacreation.in/');
define('UPLOAD_PATH', __DIR__ . '/../uploads/');
define('UPLOAD_URL', BASE_URL . '/uploads/');
 
function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            error_log('LAVANYAA CREATION DB Error: ' . $e->getMessage());
            die('<div style="font-family:sans-serif;padding:40px;text-align:center;"><h2>Database connection error.</h2><p>Please contact the system administrator.</p></div>');
        }
    }
    return $pdo;
}

function ensureCmsSchema(PDO $db): void {
    $statements = [
        "ALTER TABLE subcategories ADD COLUMN IF NOT EXISTS image VARCHAR(255) DEFAULT NULL AFTER slug",
        "ALTER TABLE reviews ADD COLUMN IF NOT EXISTS company_name VARCHAR(150) DEFAULT NULL AFTER name",
        "ALTER TABLE reviews ADD COLUMN IF NOT EXISTS designation VARCHAR(150) DEFAULT NULL AFTER company_name",
        "ALTER TABLE reviews ADD COLUMN IF NOT EXISTS city VARCHAR(100) DEFAULT NULL AFTER designation",
        "ALTER TABLE reviews ADD COLUMN IF NOT EXISTS sort_order INT NOT NULL DEFAULT 0 AFTER status",
        "ALTER TABLE reviews MODIFY product_id INT UNSIGNED NULL DEFAULT NULL",
    ];

    foreach ($statements as $sql) {
        try {
            $db->exec($sql);
        } catch (PDOException $e) {
            error_log('LAVANYAA CREATION Schema warning: ' . $e->getMessage());
        }
    }
}

ensureCmsSchema(getDB());
