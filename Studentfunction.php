<?php
header('Content-Type: application/json');

// SET TIMEZONE FIRST
date_default_timezone_set('Asia/Manila');  // For Philippines time kahapon kasi naka ano eh UTC Timezone

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "QCULibriCount";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(['error' => 'Database connection failed']));
}

// Get POST data
$studentNo = isset($_POST['studentNo']) ? trim($_POST['studentNo']) : '';
$action = isset($_POST['action']) ? $_POST['action'] : ''; // 'timein' or 'timeout'

if(empty($studentNo)){
    echo json_encode(['error' => 'Student number is required']);
    exit;
}

// Get student_id from students table
$sql = "SELECT student_id FROM students WHERE student_number = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $studentNo);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows === 0){
    echo json_encode(['error' => 'Student not found']);
    exit;
}

$student = $result->fetch_assoc();
$student_id = $student['student_id'];
$now = date('Y-m-d H:i:s');

// Insert Time In
if($action === 'timein'){
    $insert = $conn->prepare("INSERT INTO attendance_logs (student_id, time_in, status) VALUES (?, ?, 'inside')");
    $insert->bind_param("is", $student_id, $now);
    $insert->execute();
    echo json_encode(['success' => true, 'message' => 'Time IN recorded']);
}

// Insert/Update Time Out
if($action === 'timeout'){
    $update = $conn->prepare("UPDATE attendance_logs 
                              SET time_out = ?, status = 'exited', session_duration = TIMESTAMPDIFF(MINUTE, time_in, ?) 
                              WHERE student_id = ? AND status = 'inside' 
                              ORDER BY time_in DESC LIMIT 1");
    $update->bind_param("ssi", $now, $now, $student_id);
    $update->execute();
    echo json_encode(['success' => true, 'message' => 'Time OUT recorded']);
}

$conn->close();
?>
