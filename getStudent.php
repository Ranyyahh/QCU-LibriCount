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

if(isset($_GET['studentNo']) && !empty($_GET['studentNo'])){
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

    if($result->num_rows > 0){
        $studentFound = null;

    
        while($row = $result->fetch_assoc()){
            if($row['student_number'] === $studentNo){
                $studentFound = [
                    "firstName" => $row['firstName'],
                    "middleName" => $row['middleName'],
                    "lastName" => $row['lastName'],
                    "course" => $row['course'],
                    "yearLvl" => $row['yearLvl']
                ];
                break; 
            }
        }

        if($studentFound){
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
