<?php
include 'config.php';

// Check if user is logged in
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

// Get update logs
$sql = "SELECT ul.*, s.name as student_name, s.roll_no 
        FROM update_logs ul 
        JOIN students s ON ul.student_id = s.id 
        ORDER BY ul.updated_at DESC";
        
$logs = [];
if($stmt = $pdo->prepare($sql)){
    if($stmt->execute()){
        $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Logs - SF Due Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">SF Due Portal</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="dashboard.php"><i class="bi bi-arrow-left"></i> Back to Dashboard</a>
                <span class="navbar-text me-3">
                    Welcome, <?php echo htmlspecialchars($_SESSION["username"]); ?>
                </span>
                <a class="nav-link" href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h2>Update Logs</h2>
        <p class="text-muted">Track all changes made to student records</p>
        
        <?php if(empty($logs)): ?>
            <div class="alert alert-info">No update logs found.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Date & Time</th>
                            <th>Student</th>
                            <th>Roll No</th>
                            <th>Field</th>
                            <th>Old Value</th>
                            <th>New Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($logs as $log): ?>
                            <tr>
                                <td><?php echo date('M j, Y g:i A', strtotime($log['updated_at'])); ?></td>
                                <td><?php echo htmlspecialchars($log['student_name']); ?></td>
                                <td><?php echo htmlspecialchars($log['roll_no']); ?></td>
                                <td>
                                    <span class="badge bg-info">
                                        <?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $log['field_name']))); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if(is_numeric($log['old_value'])): ?>
                                        ₹<?php echo number_format($log['old_value'], 2); ?>
                                    <?php else: ?>
                                        <?php echo htmlspecialchars($log['old_value']); ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if(is_numeric($log['new_value'])): ?>
                                        ₹<?php echo number_format($log['new_value'], 2); ?>
                                    <?php else: ?>
                                        <?php echo htmlspecialchars($log['new_value']); ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
