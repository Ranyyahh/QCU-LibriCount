<?php
// admin_ajax.php
session_start();
require_once 'config.php'; // Include your config file

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// Initialize response
$response = [
    'success' => false,
    'message' => '',
    'message_type' => '',
    'max_capacity' => 50,
    'current_count' => 0
];

try {
    // Get PDO connection from your config
    $pdo = getDBConnection();
    
    // Handle GET request for initial data
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get_data') {
        // Get current capacity from database
        $sql = "SELECT max_capacity FROM admin WHERE admin_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$_SESSION['admin_id']]);
        $row = $stmt->fetch();
        
        if ($row) {
            $response['max_capacity'] = $row['max_capacity'];
        }
        
        // Get current count from database
        $sql_count = "SELECT COUNT(*) as count FROM attendance_logs WHERE time_out IS NULL";
        $stmt_count = $pdo->query($sql_count);
        $row_count = $stmt_count->fetch();
        
        if ($row_count) {
            $response['current_count'] = $row_count['count'];
        }
        
        $response['success'] = true;
        
        // Check for session messages
        if (isset($_SESSION['message'])) {
            $response['message'] = $_SESSION['message'];
            $response['message_type'] = $_SESSION['message_type'] ?? 'info';
            unset($_SESSION['message']);
            unset($_SESSION['message_type']);
        }
    }
    
    // Handle POST requests
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? '';
        
        switch ($action) {
            case 'update_capacity':
                if (isset($_POST['max_capacity'])) {
                    $new_capacity = (int)$_POST['max_capacity'];
                    
                    if ($new_capacity >= 1 && $new_capacity <= 100) {
                        // UPDATE using PDO
                        $sql_update = "UPDATE admin SET max_capacity = ? WHERE admin_id = ?";
                        $stmt_update = $pdo->prepare($sql_update);
                        
                        if ($stmt_update->execute([$new_capacity, $_SESSION['admin_id']])) {
                            $response['success'] = true;
                            $response['message'] = "Maximum capacity updated to $new_capacity successfully!";
                            $response['message_type'] = "success";
                            $response['max_capacity'] = $new_capacity;
                        } else {
                            $response['message'] = "Failed to update capacity!";
                            $response['message_type'] = "error";
                        }
                    } else {
                        $response['message'] = "Capacity must be between 1 and 100!";
                        $response['message_type'] = "error";
                    }
                }
                break;
                
            case 'reset_count':
                $reset_sql = "UPDATE attendance_logs SET time_out = NOW() WHERE time_out IS NULL";
                $stmt_reset = $pdo->query($reset_sql);
                
                if ($stmt_reset) {
                    $response['success'] = true;
                    $response['message'] = "Library count has been reset! All students logged out.";
                    $response['message_type'] = "info";
                    $response['current_count'] = 0;
                } else {
                    $response['message'] = "Failed to reset count!";
                    $response['message_type'] = "error";
                }
                break;
                
            case 'clear_logs':
                $clear_sql = "TRUNCATE TABLE attendance_logs";
                $stmt_clear = $pdo->query($clear_sql);
                
                if ($stmt_clear) {
                    $response['success'] = true;
                    $response['message'] = "All attendance logs have been cleared!";
                    $response['message_type'] = "info";
                } else {
                    $response['message'] = "Failed to clear logs!";
                    $response['message_type'] = "error";
                }
                break;
                
            default:
                $response['message'] = "Invalid action!";
                $response['message_type'] = "error";
                break;
        }
    }
    
} catch (PDOException $e) {
    $response['message'] = "Database error: " . $e->getMessage();
    $response['message_type'] = "error";
    error_log("Database error in admin_ajax.php: " . $e->getMessage());
}

// Send JSON response
header('Content-Type: application/json');
echo json_encode($response);
exit();
?>