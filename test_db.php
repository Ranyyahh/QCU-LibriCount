<?php
// test_update.php
require_once 'config.php';

try {
    $pdo = getDBConnection();
    
    echo "<h2>Test Manual Update</h2>";
    
    // Test update to 60
    $new_capacity = 60;
    $admin_id = 1;
    
    $sql = "INSERT INTO system_settings (setting_name, setting_value, admin_id, changed_at) 
            VALUES ('max_capacity', ?, ?, NOW())
            ON DUPLICATE KEY UPDATE 
            setting_value = VALUES(setting_value),
            admin_id = VALUES(admin_id),
            changed_at = NOW()";
    
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute([$new_capacity, $admin_id])) {
        echo "<p style='color:green;'>✅ Update successful!</p>";
    } else {
        echo "<p style='color:red;'>❌ Update failed</p>";
    }
    
    // Check current value
    $check_sql = "SELECT * FROM system_settings WHERE setting_name = 'max_capacity'";
    $check_stmt = $pdo->query($check_sql);
    $result = $check_stmt->fetch();
    
    echo "<h3>Current value in database:</h3>";
    echo "<pre>";
    print_r($result);
    echo "</pre>";
    
} catch (PDOException $e) {
    echo "<p style='color:red;'>Error: " . $e->getMessage() . "</p>";
}
?>