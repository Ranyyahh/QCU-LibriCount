<?php
session_start();
require_once 'config.php';


if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit();
}


$max_capacity = 50;
$current_count = 0;
$percentage = 0;


$message = $_SESSION['message'] ?? '';
$message_type = $_SESSION['message_type'] ?? '';


unset($_SESSION['message']);
unset($_SESSION['message_type']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    if (isset($_POST['update_capacity'])) {
        $new_capacity = (int)$_POST['max_capacity'];
        
        if ($new_capacity >= 1 && $new_capacity <= 500) {
            try {
                $pdo = getDBConnection();
                $admin_id = $_SESSION['admin_id'];
                
                // Update capacity
                $sql = "INSERT INTO system_settings (setting_name, setting_value, admin_id, changed_at) 
                        VALUES ('max_capacity', ?, ?, NOW())
                        ON DUPLICATE KEY UPDATE 
                        setting_value = VALUES(setting_value),
                        admin_id = VALUES(admin_id),
                        changed_at = NOW()";
                
                $stmt = $pdo->prepare($sql);
                
                if ($stmt->execute([$new_capacity, $admin_id])) {
      
                    $log_sql = "INSERT INTO system_logs (action_type, action_details, admin_id) 
                               VALUES ('UPDATE_CAPACITY', 'Updated max capacity to $new_capacity', ?)";
                    $log_stmt = $pdo->prepare($log_sql);
                    $log_stmt->execute([$admin_id]);
                    
                    $_SESSION['message'] = "✅ Maximum capacity updated to $new_capacity";
                    $_SESSION['message_type'] = "success";
                } else {
                    $_SESSION['message'] = "❌ Failed to update capacity";
                    $_SESSION['message_type'] = "error";
                }
            } catch (PDOException $e) {
                $_SESSION['message'] = "❌ Database error: " . $e->getMessage();
                $_SESSION['message_type'] = "error";
            }
        } else {
            $_SESSION['message'] = "❌ Capacity must be between 1 and 500!";
            $_SESSION['message_type'] = "error";
        }
        
      
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    }
    

    if (isset($_POST['reset_count'])) {
        try {
            $pdo = getDBConnection();
            $admin_id = $_SESSION['admin_id'];
            
            $reset_sql = "UPDATE attendance_logs 
                         SET time_out = NOW(), status = 'exited', session_duration = TIMESTAMPDIFF(MINUTE, time_in, NOW())
                         WHERE status = 'inside'";
            $stmt_reset = $pdo->query($reset_sql);
            
            if ($stmt_reset) {
        
                $count_sql = "SELECT COUNT(*) as count FROM attendance_logs WHERE time_out = NOW() AND status = 'exited'";
                $count_stmt = $pdo->query($count_sql);
                $count_row = $count_stmt->fetch();
                $students_count = $count_row ? $count_row['count'] : 0;
                
                $log_sql = "INSERT INTO system_logs (action_type, action_details, admin_id) 
                           VALUES ('RESET_COUNT', 'Reset library count - $students_count students logged out', ?)";
                $log_stmt = $pdo->prepare($log_sql);
                $log_stmt->execute([$admin_id]);
                
                $_SESSION['message'] = "✅ Library count has been reset! $students_count students logged out.";
                $_SESSION['message_type'] = "success";
            } else {
                $_SESSION['message'] = "❌ Failed to reset count!";
                $_SESSION['message_type'] = "error";
            }
        } catch (PDOException $e) {
            $_SESSION['message'] = "❌ Database error: " . $e->getMessage();
            $_SESSION['message_type'] = "error";
        }
        
        // REDIRECT IMMEDIATELY AFTER PROCESSING
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    }
    
    // Handle clear logs
    if (isset($_POST['clear_logs'])) {
        try {
            $pdo = getDBConnection();
            $admin_id = $_SESSION['admin_id'];
            
            // Get count before clearing for logging
            $count_sql = "SELECT COUNT(*) as count FROM attendance_logs";
            $count_stmt = $pdo->query($count_sql);
            $count_row = $count_stmt->fetch();
            $logs_count = $count_row ? $count_row['count'] : 0;
            
            $clear_sql = "DELETE FROM attendance_logs";
            $stmt_clear = $pdo->query($clear_sql);
            
            if ($stmt_clear) {
                $log_sql = "INSERT INTO system_logs (action_type, action_details, admin_id) 
                           VALUES ('CLEAR_LOGS', 'Cleared $logs_count attendance logs', ?)";
                $log_stmt = $pdo->prepare($log_sql);
                $log_stmt->execute([$admin_id]);
                
                $_SESSION['message'] = "✅ All attendance logs cleared! ($logs_count logs removed)";
                $_SESSION['message_type'] = "success";
            } else {
                $_SESSION['message'] = "❌ Failed to clear logs!";
                $_SESSION['message_type'] = "error";
            }
        } catch (PDOException $e) {
            $_SESSION['message'] = "❌ Database error: " . $e->getMessage();
            $_SESSION['message_type'] = "error";
        }
        
        // REDIRECT IMMEDIATELY AFTER PROCESSING
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    }
}

//Ito yung responsible sa pagkuha ng data from database, so dbali lagi itong naka run
try {
    $pdo = getDBConnection();
    $admin_id = $_SESSION['admin_id'];
    
    // Get max capacity from system_settings
    $sql = "SELECT setting_value FROM system_settings WHERE setting_name = 'max_capacity'";
    $stmt = $pdo->query($sql);
    $row = $stmt->fetch();
    
    if ($row) {
        $max_capacity = $row['setting_value'];
    } else {
        // If no record exists, create one
        $sql = "INSERT INTO system_settings (setting_name, setting_value, admin_id) 
                VALUES ('max_capacity', '50', ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$admin_id]);
    }
    
    //Real-time current count ng mga student na nasa loob ng database HAHAHAAHAHAH
    $sql_count = "SELECT COUNT(*) as count FROM attendance_logs WHERE status = 'inside'";
    $stmt_count = $pdo->query($sql_count);
    $row_count = $stmt_count->fetch();
    
    if ($row_count) {
        $current_count = $row_count['count'];
    }
    
    $percentage = ($max_capacity > 0) ? min(($current_count  / $max_capacity * 100), 100) : 0;
    
} catch (PDOException $e) {
    $error_message = "Database error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QCU LibriCount - Settings</title>
    <link rel="stylesheet" href="ADMIN_SETTINGS.css">
</head>

<body>
    <?php if ($message): ?>
        <div class="message-popup <?php echo $message_type; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
        
        <script>
            // Scene ng ano message pop-up
            setTimeout(() => {
                const message = document.querySelector('.message-popup');
                if (message) {
                    message.style.opacity = '0';
                    message.style.transform = 'translateX(100%)';
                    message.style.transition = 'opacity 0.3s, transform 0.3s';
                    setTimeout(() => message.remove(), 300);
                }
            }, 5000);
        </script>
    <?php endif; ?>

    <?php if (isset($error_message)): ?>
        <div class="message error">
            <?php echo htmlspecialchars($error_message); ?>
        </div>
    <?php endif; ?>

    <header class="header">
        <div class="logo-wrap">
            <img src="Images/libricount-logo.png" class="logo" alt="LibriCount Logo">
            <h1>QCU LibriCount</h1>
        </div>

        <div class="Nav-bar">
            <nav>
                <ul>
                    <li><a href="ADMIN_DASHBOARD.html">Dashboard</a></li>
                    <li><a href="ADMIN_LOGS.html">Logs</a></li>
                    <li><a href="ADMIN_SETTINGS.php" class="active">Settings</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container">
  
        <div class="card">
            <h2>System Configuration</h2>
            <form method="POST" action="">
                <label class="label">Max Capacity:</label>
                <input type="number" id="maxCapInput" name="max_capacity" 
                       value="<?php echo htmlspecialchars($max_capacity); ?>" 
                       min="1" max="500" class="input-box" disabled>
                <div class="btn-groupCRUD">
                    <button type="button" id="editBtn" class="btn btn-gray">Edit</button>
                    <button type="submit" id="Savebttn" name="update_capacity" class="btn btn-red" disabled>Save</button>
                </div>
            </form>
        </div>

    
        <div class="card">
            <h2>System Status</h2>
            <div class="status-info">
                <p>Current Count: <b>
                    <span id="currentCount"><?php echo htmlspecialchars($current_count); ?></span> /
                    <span id="maxCapacity"><?php echo htmlspecialchars($max_capacity); ?></span>
                </b></p>
                <div class="progress-bar">
                    <div class="progress-fill" id="progressFill" 
                         style="width: <?php echo $percentage; ?>%; 
                         background: <?php 
                            if ($percentage >= 90) echo 'linear-gradient(135deg, #dc3545 0%, #c82333 100%)';
                            elseif ($percentage >= 70) echo 'linear-gradient(135deg, #ffc107 0%, #e0a800 100%)';
                            else echo 'linear-gradient(135deg, #28a745 0%, #218838 100%)';
                         ?>;">
                    </div>
                </div>
                <p id="datetime"><?php 
                    date_default_timezone_set('Asia/Manila');
                    echo "As of " . date("F j, Y \a\\t g:i:s A"); 
                ?></p>
                <p>Status: <span id="systemStatus" style="color: <?php
                    if ($percentage >= 90) echo '#dc3545';
                    elseif ($percentage >= 70) echo '#ffc107';
                    else echo '#28a745';
                ?>;">
                    <?php 
                    if ($percentage >= 90) echo '🔴 Wala na tuloy space, bobo ka kasi eh';
                    elseif ($percentage >= 70) echo '🟡 Malapit na mapuno, bawasan mo na sila HAHAHAHAH';
                    else echo '🟢 Daming space ya';
                    ?>
                </span></p>
            </div>
        </div>

        <div class="card2">
            <h2>Reset Tools</h2>
            <form method="POST" action="" onsubmit="return confirmAction(this)">
                <div class="btn-group">
                    <button type="submit" name="reset_count" class="btn btn-gray">Reset Count</button>
                    <button type="submit" name="clear_logs" class="btn btn-red">Clear Logs</button>
                </div>
            </form>
        </div>

        <div class="card">
            <h2>About</h2>
            <p>
                Quezon City University <br>
                Library Capacity Monitoring System v1.0
            </p>
            <p class="italic">
                Developed by SBIT – 2D <br>
                Elevator Etiquette Group
            </p>
            <form method="POST" action="logout.php" onsubmit="return confirm('Are you sure you want to log out?')">
                <button type="submit" class="btn btn-red logout">Log out</button>
            </form>
        </div>
    </div>

    <script>
        //confirmation noitf lang naman ito
    function confirmAction(form) {
        const button = window.event.submitter;
        
        if (button.name === 'reset_count') {
            return confirm('Are you sure you want to reset the library count? This will log out all current students.');
        }
        
        if (button.name === 'clear_logs') {
            return confirm('Are you sure you want to clear all attendance logs? This action cannot be undone!');
        }
        
        return true;
    }
    
    // Edit button functionality na ito
    document.addEventListener('DOMContentLoaded', function() {
        const editBtn = document.getElementById('editBtn');
        const maxCapInput = document.getElementById('maxCapInput');
        const saveBtn = document.getElementById('Savebttn');
        
        if (editBtn && maxCapInput && saveBtn) {
            maxCapInput.disabled = true;
            saveBtn.disabled = true;
            
            editBtn.addEventListener('click', function() {
                if (maxCapInput.disabled) {
                    // Enable editing mode
                    maxCapInput.disabled = false;
                    maxCapInput.focus();
                    maxCapInput.select();
                    
                    // Change button states
                    editBtn.textContent = 'Cancel';
                    editBtn.classList.remove('btn-gray');
                    editBtn.classList.add('btn-red');
                    
                    saveBtn.disabled = false;
                } else {
                    // Cancel editing mode
                    maxCapInput.disabled = true;
                    maxCapInput.value = document.getElementById('maxCapacity').textContent;
                    
                    // Reset button states yeaa
                    editBtn.textContent = 'Edit';
                    editBtn.classList.remove('btn-red');
                    editBtn.classList.add('btn-gray');
                    
                    saveBtn.disabled = true;
                }
            });
            
            // Optional: Disable Save button if no changes were made
            maxCapInput.addEventListener('input', function() {
                const currentValue = document.getElementById('maxCapacity').textContent;
                if (maxCapInput.value === currentValue) {
                    saveBtn.disabled = true;
                } else {
                    saveBtn.disabled = false;
                }
            });
        }
    });
    </script>
</body>
</html>