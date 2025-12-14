<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "QCULibriCount";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(["error" => "Database connection failed"]);
    exit();
}

class Node {
    public $data;
    public $next;

    public function __construct($data) {
        $this->data = $data;
        $this->next = null;
    }
}
//student linked list
class Student {
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

if (isset($_GET['studentNo']) && !empty($_GET['studentNo'])) {

    $studentNo = $conn->real_escape_string($_GET['studentNo']);

    $sql = "SELECT 
                firstname AS firstName, 
                middlename AS middleName, 
                lastname AS lastName, 
                course AS course, 
                year_level AS yearLvl,
                student_number
            FROM students";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {

        $studentList = new Student();
        while ($row = $result->fetch_assoc()) {
            $studentList->addNode($row);
        }

        $studentFound = null;
        $current = $studentList->head;

        while ($current !== null) {
            if ($current->data['student_number'] === $studentNo) {
                $studentFound = [
                    "firstName" => $current->data['firstName'],
                    "middleName" => $current->data['middleName'],
                    "lastName" => $current->data['lastName'],
                    "course" => $current->data['course'],
                    "yearLvl" => $current->data['yearLvl']
                ];
                break;
            }
            $current = $current->next;
        }

        if ($studentFound) {
            echo json_encode($studentFound);
        } else {
            echo json_encode(["error" => "Student not found"]);
        }

    } else {
        echo json_encode(["error" => "No students in the database"]);
    }

} else {
    echo json_encode(["error" => "Student number not provided"]);
}

$conn->close();
?>
