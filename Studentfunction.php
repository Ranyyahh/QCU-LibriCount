<?php
header('Content-Type: application/json');

/* ========= SET TIMEZONE ========= */
date_default_timezone_set('Asia/Manila');

/* ========= DATABASE CONNECTION ========= */
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "QCULibriCount";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(['error' => 'Database connection failed']));
}

/* ========= LINKED LIST NODE ========= */
class Node {
    public $data;
    public $next;

    public function __construct($data) {
        $this->data = $data;
        $this->next = null;
    }
}

/* ========= STUDENT FUNCTION LINKED LIST ========= */
class StudentFunctionList {
    public $head = null;

    public function addNode($data) {
        $newNode = new Node($data);
        if ($this->head === null) {
            $this->head = $newNode;
        } else {
            $current = $this->head;
            while ($current->next !== null) {
                $current = $current->next;
            }
            $current->next = $newNode;
        }
    }
}

/* ========= INITIALIZE LIST ========= */
$responseList = new StudentFunctionList();

/* ========= GET POST DATA ========= */
$studentNo = isset($_POST['studentNo']) ? trim($_POST['studentNo']) : '';
$action = isset($_POST['action']) ? $_POST['action'] : '';

if (empty($studentNo)) {
    $responseList->addNode(['error' => 'Student number is required']);
} else {

    /* ========= GET STUDENT ID ========= */
    $sql = "SELECT student_id FROM students WHERE student_number = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $studentNo);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $responseList->addNode(['error' => 'Student not found']);
    } else {

        $student = $result->fetch_assoc();
        $student_id = $student['student_id'];
        $now = date('Y-m-d H:i:s');

        /* ========= TIME IN ========= */
        if ($action === 'timein') {

            // ✅ CHECK FOR EXISTING ACTIVE TIME IN
            $checkTimeIn = $conn->prepare(
                "SELECT * FROM attendance_logs 
                 WHERE student_id = ? AND status = 'inside' AND time_out IS NULL"
            );
            $checkTimeIn->bind_param("i", $student_id);
            $checkTimeIn->execute();
            $checkResult = $checkTimeIn->get_result();

            if ($checkResult->num_rows > 0) {
                // DOUBLE TIME IN ATTEMPT
                $responseList->addNode([
                    'error' => 'ANO DALAWA KATAWAN MO????? TIME IN KA NA NASA LOOB KA PA???'
                ]);
            } else {
                // wala pang time in, pwede mag time in
                $insert = $conn->prepare(
                    "INSERT INTO attendance_logs (student_id, time_in, status) VALUES (?, ?, 'inside')"
                );
                $insert->bind_param("is", $student_id, $now);
                $insert->execute();

                $responseList->addNode([
                    'success' => true,
                    'message' => 'Time IN recorded'
                ]);
            }
        }

        /* ========= TIME OUT ========= */
        if ($action === 'timeout') {

            // sanaol may validation
            $check = $conn->prepare(
                "SELECT * FROM attendance_logs 
                 WHERE student_id = ? AND status = 'inside' AND time_out IS NULL
                 ORDER BY time_in DESC LIMIT 1"
            );
            $check->bind_param("i", $student_id);
            $check->execute();
            $checkResult = $check->get_result();

            if ($checkResult->num_rows === 0) {
                // HOY MAG TIME IN KA MUNA BAGO KA MAG TIME OUT
                $responseList->addNode([
                    'error' => "TIME OUT GUSTO????? TIME IN AYAW??? TIME IN KA MUNA!"
                ]);
            } else {
                // i2 kapag meron time in, no problem na mag time out
                $update = $conn->prepare(
                    "UPDATE attendance_logs 
                     SET time_out = ?, status = 'exited', 
                         session_duration = TIMESTAMPDIFF(MINUTE, time_in, ?) 
                     WHERE student_id = ? AND status = 'inside' 
                     ORDER BY time_in DESC LIMIT 1"
                );
                $update->bind_param("ssi", $now, $now, $student_id);
                $update->execute();

                $responseList->addNode([
                    'success' => true,
                    'message' => 'Time OUT recorded'
                ]);
            }
        }
    }
}

/* ========= TRAVERSE LINKED LIST ========= */
$response = [];
$current = $responseList->head;

while ($current !== null) {
    $response = $current->data;
    $current = $current->next;
}

/* ========= OUTPUT ========= */
echo json_encode($response);

$conn->close();
?>
