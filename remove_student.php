<?php
include 'config.php';

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $student_id = $_POST['student_id'];
    
    $sql = "UPDATE students SET hidden = 1 WHERE id = :id";
    
    if($stmt = $pdo->prepare($sql)){
        $stmt->bindParam(":id", $student_id);
        
        if($stmt->execute()){
            echo json_encode(["success" => true]);
        } else{
            echo json_encode(["success" => false, "message" => "Error removing student."]);
        }
    }
    
    unset($pdo);
}
?>
