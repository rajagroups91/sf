<?php
include 'config.php';

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $student_id = $_POST['student_id'];
    $name = $_POST['name'];
    $roll_no = $_POST['roll_no'];
    $dept_id = $_POST['dept_id'];
    $year1_due = $_POST['year1_due'];
    $year2_due = $_POST['year2_due'];
    $year3_due = $_POST['year3_due'];
    $paid = $_POST['paid'];
    
    // Calculate totals
    $total_due = $year1_due + $year2_due + $year3_due;
    $balance = $total_due - $paid;
    
    // Get old values for logging
    $old_values_sql = "SELECT * FROM students WHERE id = :id";
    $old_values_stmt = $pdo->prepare($old_values_sql);
    $old_values_stmt->bindParam(":id", $student_id);
    $old_values_stmt->execute();
    $old_student = $old_values_stmt->fetch(PDO::FETCH_ASSOC);
    
    // Update student
    $sql = "UPDATE students SET name = :name, roll_no = :roll_no, dept_id = :dept_id, 
            year1_due = :year1_due, year2_due = :year2_due, year3_due = :year3_due, 
            total_due = :total_due, paid = :paid, balance = :balance 
            WHERE id = :id";
    
    if($stmt = $pdo->prepare($sql)){
        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":roll_no", $roll_no);
        $stmt->bindParam(":dept_id", $dept_id);
        $stmt->bindParam(":year1_due", $year1_due);
        $stmt->bindParam(":year2_due", $year2_due);
        $stmt->bindParam(":year3_due", $year3_due);
        $stmt->bindParam(":total_due", $total_due);
        $stmt->bindParam(":paid", $paid);
        $stmt->bindParam(":balance", $balance);
        $stmt->bindParam(":id", $student_id);
        
        if($stmt->execute()){
            // Log changes
            logChanges($pdo, $student_id, $old_student, [
                'name' => $name,
                'roll_no' => $roll_no,
                'dept_id' => $dept_id,
                'year1_due' => $year1_due,
                'year2_due' => $year2_due,
                'year3_due' => $year3_due,
                'paid' => $paid
            ]);
            
            echo json_encode(["success" => true]);
        } else{
            echo json_encode(["success" => false, "message" => "Error updating student."]);
        }
    }
    
    unset($pdo);
}

function logChanges($pdo, $student_id, $old_values, $new_values) {
    $fields = ['name', 'roll_no', 'dept_id', 'year1_due', 'year2_due', 'year3_due', 'paid'];
    
    foreach($fields as $field) {
        if($old_values[$field] != $new_values[$field]) {
            $sql = "INSERT INTO update_logs (student_id, field_name, old_value, new_value) 
                    VALUES (:student_id, :field_name, :old_value, :new_value)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(":student_id", $student_id);
            $stmt->bindParam(":field_name", $field);
            $stmt->bindParam(":old_value", $old_values[$field]);
            $stmt->bindParam(":new_value", $new_values[$field]);
            $stmt->execute();
        }
    }
}
?>
