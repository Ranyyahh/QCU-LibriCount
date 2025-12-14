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

// Get recent activity
$sql = "SELECT 
            al.time_in,
            al.time_out,
            CASE 
                WHEN al.time_out IS NULL THEN '+1 Entry'
                ELSE '-1 Entry'
            END as type,
            CASE 
                WHEN al.time_out IS NULL THEN 'Time in'
                ELSE 'Time out'
            END as action,
            CASE 
                WHEN al.time_out IS NULL THEN al.time_in
                ELSE al.time_out
            END as event_time
        FROM attendance_logs al
        ORDER BY al.time_in DESC";
        
$result = $conn->query($sql);
$activities = [];

while($row = $result->fetch_assoc()) {
    $activities[] = [
        'type' => $row['type'],
        'action' => $row['action'],
        'time' => date('h:i A', strtotime($row['event_time'])),
        'date' => date('m/d/y', strtotime($row['event_time']))
    ];
}

echo json_encode([
    'success' => true,
    'activity' => $activities
]);

$conn->close();
?>