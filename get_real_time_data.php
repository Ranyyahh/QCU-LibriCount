<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "QCULibriCount";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed"]));
}

// Get max capacity
$maxCapacity = 50;
$result = $conn->query("SELECT setting_value FROM system_settings WHERE setting_name = 'max_capacity'");
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $maxCapacity = (int)$row['setting_value'];
}

// Get current count
$sql = "SELECT COUNT(*) as current_count FROM attendance_logs WHERE status = 'inside' AND time_out IS NULL";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$currentCount = (int)$row['current_count'];

// Calculate percentage
$percentage = ($maxCapacity > 0) ? round(($currentCount / $maxCapacity) * 100) : 0;

// Get recent activity (last 12)
$sql = "SELECT 
            al.time_in,
            al.time_out,
            s.firstname,
            s.lastname
        FROM attendance_logs al
        JOIN students s ON al.student_id = s.student_id
        ORDER BY al.time_in DESC 
        LIMIT 12";
$result = $conn->query($sql);
$activities = [];

while($row = $result->fetch_assoc()) {
    if ($row['time_out'] === null) {
        // Entry
        $activities[] = [
            'type' => '+1 Entry',
            'student' => $row['firstname'] . ' ' . substr($row['lastname'], 0, 1) . '.',
            'time' => date('h:i A', strtotime($row['time_in'])),
            'date' => date('m/d/y', strtotime($row['time_in']))
        ];
    } else {
        // Exit
        $activities[] = [
            'type' => '-1 Entry',
            'student' => $row['firstname'] . ' ' . substr($row['lastname'], 0, 1) . '.',
            'time' => date('h:i A', strtotime($row['time_out'])),
            'date' => date('m/d/y', strtotime($row['time_out']))
        ];
    }
}

// Prepare response
echo json_encode([
    'success' => true,
    'current' => $currentCount,
    'max' => $maxCapacity,
    'percentage' => $percentage,
    'activity' => $activities,
    'timestamp' => date('Y-m-d H:i:s')
]);

$conn->close();
?>