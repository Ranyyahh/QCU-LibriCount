<?php
// config.php - Oracle PL/SQL version for QCU LibriCount

// Oracle database credentials
define('DB_USER', 'libri_user');        // Palitan ng actual username ninyo sa Oracle
define('DB_PASS', 'libri_pass');        // Palitan ng actual password
define('DB_HOST', 'localhost');         // or 192.168.x.x (IP ng Oracle server)
define('DB_PORT', '1521');              // Default port ng Oracle
define('DB_SERVICE', 'XE');             // or 'ORCL' depende sa Oracle setup ninyo

// Connection string
function getDBConnection() {
    $conn_string = "(DESCRIPTION=(ADDRESS=(PROTOCOL=TCP)(HOST=" . DB_HOST . ")(PORT=" . DB_PORT . "))(CONNECT_DATA=(SERVICE_NAME=" . DB_SERVICE . ")))";
    
    $conn = oci_connect(DB_USER, DB_PASS, $conn_string, 'AL32UTF8');
    
    if (!$conn) {
        $e = oci_error();
        die("Connection failed: " . $e['message']);
    }
    
    return $conn;
}

// Para magamit sa buong system
$conn = getDBConnection();
?>
