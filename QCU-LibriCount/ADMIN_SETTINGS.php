<?php
session_start();
require_once 'config.php'; // Include your config file

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit();
}

// Get initial data from database
try {
    $pdo = getDBConnection();
    
    // Get max capacity
    $max_capacity = 50;
    $sql = "SELECT max_capacity FROM admin WHERE admin_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$_SESSION['admin_id']]);
    $row = $stmt->fetch();
    
    if ($row) {
        $max_capacity = $row['max_capacity'];
    }
    
    // Get current count
    $current_count = 0;
    $sql_count = "SELECT COUNT(*) as count FROM attendance_logs WHERE time_out IS NULL";
    $stmt_count = $pdo->query($sql_count);
    $row_count = $stmt_count->fetch();
    
    if ($row_count) {
        $current_count = $row_count['count'];
    }
    
} catch (PDOException $e) {
    // Handle error
    $max_capacity = 50;
    $current_count = 0;
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
    <!-- Message Display -->
    <div id="message-container"></div>

    <?php if (isset($error_message)): ?>
        <div class="message error" style="text-align: center; margin: 10px; padding: 10px; background: #ffcccc; color: #cc0000; border-radius: 5px;">
            <?php echo $error_message; ?>
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
                    <li><a href="ADMIN_DASHBOARD.php">Dashboard</a></li>
                    <li><a href="ADMIN_LOGS.php">Logs</a></li>
                    <li><a href="ADMIN_SETTINGS.php" class="active">Settings</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container">
        <!-- System Configuration -->
        <div class="card">
            <h2>System Configuration</h2>
            <form id="capacityForm">
                <label class="label">Max Capacity:</label>
                <!-- Load max capacity from PHP directly -->
                <input type="number" id="maxCapInput" name="max_capacity" 
                       value="<?php echo htmlspecialchars($max_capacity); ?>" 
                       min="1" max="100" class="input-box" disabled>
                <div class="btn-group">
                    <button type="button" class="btn btn-gray" id="editBtn">Edit</button>
                    <button type="button" class="btn btn-red" id="saveBtn">Save</button>
                </div>
            </form>
        </div>

        <!-- System Status -->
        <div class="card">
            <h2>System Status</h2>
            <div class="status-info">
                <!-- Load data from PHP directly -->
                <p>Current Count: <b><span id="currentCount"><?php echo htmlspecialchars($current_count); ?></span>/<span id="maxCapacity"><?php echo htmlspecialchars($max_capacity); ?></span></b></p>
                <div class="progress-bar">
                    <div class="progress-fill" id="progressFill" 
                         style="width: <?php echo ($max_capacity > 0) ? ($current_count / $max_capacity * 100) : 0; ?>%;"></div>
                </div>
                <p id="datetime">Loading date & time...</p>
                <p>Status: <span id="systemStatus">🟢 Online</span></p>
            </div>
        </div>

        <!-- Reset Tools -->
        <div class="card">
            <h2>Reset Tools</h2>
            <div class="btn-group">
                <button type="button" class="btn btn-gray" id="resetBtn">Reset Count</button>
                <button type="button" class="btn btn-red" id="clearLogsBtn">Clear Logs</button>
            </div>
        </div>

        <!-- About Section -->
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
            <button id="logoutBtn" class="btn btn-red logout">Log out</button>
        </div>
    </div>

    <script src="ADMIN_SETTINGS.js"></script>
</body>
</html>