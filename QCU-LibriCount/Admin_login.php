<?php   
session_start();
require_once 'config.php';

function loginAdmin($username, $password) {
    try {
        $pdo = getDBConnection();
        
        // FIXED: Removed extra quote
        $stmt = $pdo->prepare("SELECT admin_id, username, password FROM admin WHERE username = ?");
        
        if (!$stmt) {
            throw new Exception("Failed to prepare SQL statement");
        }
        
        $stmt->execute([$username]);
        $admin = $stmt->fetch();
        
        if ($admin) {
            // For debugging - show what password is stored
            // echo "Stored password: " . $admin['password'] . "<br>";
            // echo "Input password: " . $password . "<br>";
            
            // Check if password matches (plaintext for now)
            if ($password === $admin['password']) {
                // Set session variables
                $_SESSION['admin_id'] = $admin['admin_id'];
                $_SESSION['username'] = $admin['username'];
                $_SESSION['admin_logged_in'] = true;
                
                // Regenerate session ID for security
                session_regenerate_id(true);
                
                return true;
            } else {
                // Password doesn't match
                error_log("Password mismatch for user: $username");
                return false;
            }
        } else {
            // User not found
            error_log("User not found: $username");
            return false;
        }
        
    } catch(PDOException $e) {
        error_log("Database error in loginAdmin: " . $e->getMessage());
        return false;
    } catch(Exception $e) {
        error_log("General error in loginAdmin: " . $e->getMessage());
        return false;
    }
}

// Handle form submission
$error = '';
$debug_info = ''; // For debugging

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Debug: Show what we received
    // $debug_info = "Username: '$username', Password: '$password'";
    
    if (empty($username) || empty($password)) {
        $error = "Please enter username and password";
    } else {
        $login_result = loginAdmin($username, $password);
        
        if ($login_result === true) {
            // Login successful - redirect
            header("Location: ADMIN_DASHBOARD.html");
            exit();
        } else {
            // Login failed - show error
            $error = "Invalid username or password";
            
            // For debugging, you can add more info:
            // $error = "Invalid username or password. Debug: " . $debug_info;
        }
    }
}
?>