<?php
// library_linked_list.php
require_once 'config.php';

class StudentNode {
    public $studentData;
    public $next;
    
    public function __construct($studentData) {
        $this->studentData = $studentData;
        $this->next = null;
    }
}

class LibraryLinkedList {
    private $head;
    private $tail;
    private $count;
    private $maxCapacity;
    
    public function __construct($maxCapacity = 50) {
        $this->head = null;
        $this->tail = null;
        $this->count = 0;
        $this->maxCapacity = $maxCapacity;
    }
    
    public function studentEnters($studentData) {
        // Check if library is at capacity
        if ($this->count >= $this->maxCapacity) {
            return [
                'success' => false,
                'message' => 'Library is at maximum capacity! Please wait for someone to leave.',
                'action' => 'capacity_full'
            ];
        }
        
        // Check if student is already inside
        if ($this->isStudentInside($studentData['student_id'])) {
            return [
                'success' => false,
                'message' => 'You are already inside the library!',
                'action' => 'already_inside'
            ];
        }
        
        // Add time_in to student data
        $studentData['time_in'] = date('Y-m-d H:i:s');
        
        // Create new node
        $newNode = new StudentNode($studentData);
        
        // If list is empty
        if ($this->head === null) {
            $this->head = $newNode;
            $this->tail = $newNode;
        } else {
            // Add to end of list
            $this->tail->next = $newNode;
            $this->tail = $newNode;
        }
        
        $this->count++;
        
        // Log to database
        $this->logTimeIn($studentData['student_id']);
        
        return [
            'success' => true,
            'message' => 'Time In successful! Welcome to the library.',
            'action' => 'time_in',
            'current_count' => $this->count,
            'student' => $studentData
        ];
    }
    
    public function studentLeaves($studentId) {
        // If list is empty
        if ($this->head === null) {
            return [
                'success' => false,
                'message' => 'No students in library!',
                'action' => 'empty_library'
            ];
        }
        
        // If head needs to be removed
        if ($this->head->studentData['student_id'] == $studentId) {
            $removedStudent = $this->head->studentData;
            $this->head = $this->head->next;
            
            // If list becomes empty
            if ($this->head === null) {
                $this->tail = null;
            }
            
            $this->count--;
            
            // Log to database
            $this->logTimeOut($studentId);
            
            return [
                'success' => true,
                'message' => 'Time Out successful! Thank you for visiting.',
                'action' => 'time_out',
                'current_count' => $this->count,
                'student' => $removedStudent
            ];
        }
        
        // Search for student in the list
        $current = $this->head;
        $prev = null;
        
        while ($current !== null && $current->studentData['student_id'] != $studentId) {
            $prev = $current;
            $current = $current->next;
        }
        
        // Student not found
        if ($current === null) {
            return [
                'success' => false,
                'message' => 'You are not currently inside the library!',
                'action' => 'not_found'
            ];
        }
        
        // Remove the node
        $prev->next = $current->next;
        
        // If removing tail
        if ($current === $this->tail) {
            $this->tail = $prev;
        }
        
        $this->count--;
        
        // Log to database
        $this->logTimeOut($studentId);
        
        return [
            'success' => true,
            'message' => 'Time Out successful! Thank you for visiting.',
            'action' => 'time_out',
            'current_count' => $this->count,
            'student' => $current->studentData
        ];
    }
    
    private function isStudentInside($studentId) {
        $current = $this->head;
        
        while ($current !== null) {
            if ($current->studentData['student_id'] == $studentId) {
                return true;
            }
            $current = $current->next;
        }
        
        return false;
    }
    
    public function checkStudentStatus($studentId) {
        return [
            'is_inside' => $this->isStudentInside($studentId),
            'current_count' => $this->count,
            'max_capacity' => $this->maxCapacity
        ];
    }
    
    public function getCurrentOccupancy() {
        return [
            'current_count' => $this->count,
            'max_capacity' => $this->maxCapacity,
            'available_spots' => $this->maxCapacity - $this->count,
            'percentage' => round(($this->count / $this->maxCapacity) * 100, 1),
            'is_full' => $this->count >= $this->maxCapacity
        ];
    }
    
    public function displayCurrentStudents() {
        $students = [];
        $current = $this->head;
        $position = 1;
        
        while ($current !== null) {
            $students[] = [
                'position' => $position++,
                'student_id' => $current->studentData['student_id'],
                'student_number' => $current->studentData['student_number'],
                'name' => trim($current->studentData['Firstname'] . ' ' . 
                         $current->studentData['Middlename'] . ' ' . 
                         $current->studentData['Lastname']),
                'time_in' => $current->studentData['time_in']
            ];
            $current = $current->next;
        }
        
        return $students;
    }
    
    public function clearLibrary() {
        $clearedCount = $this->count;
        $students = $this->displayCurrentStudents();
        
        // Log all students out
        foreach ($students as $student) {
            $this->logTimeOut($student['student_id']);
        }
        
        $this->head = null;
        $this->tail = null;
        $this->count = 0;
        
        return $clearedCount;
    }
    
    private function logTimeIn($studentId) {
        $conn = Database::getInstance();
        $timeIn = date('Y-m-d H:i:s');
        $sql = "INSERT INTO attendance_logs (student_id, time_in) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $studentId, $timeIn);
        $stmt->execute();
        $stmt->close();
    }
    
    private function logTimeOut($studentId) {
        $conn = Database::getInstance();
        $timeOut = date('Y-m-d H:i:s');
        $sql = "UPDATE attendance_logs SET time_out = ? 
                WHERE student_id = ? AND time_out IS NULL 
                ORDER BY log_id DESC LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $timeOut, $studentId);
        $stmt->execute();
        $stmt->close();
    }
    
    public static function getStudentByNumber($studentNumber) {
        $conn = Database::getInstance();
        $sql = "SELECT * FROM students WHERE student_number = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $studentNumber);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $student = $result->fetch_assoc();
            $stmt->close();
            return $student;
        }
        
        $stmt->close();
        return null;
    }
}
?>