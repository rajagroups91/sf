<?php
include 'config.php';

// Check if user is logged in
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

// Get all departments
$departments = [];
$sql = "SELECT * FROM departments";
if($stmt = $pdo->prepare($sql)){
    if($stmt->execute()){
        $departments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

// Get filter values
$name_filter = isset($_GET['name']) ? $_GET['name'] : '';
$dept_filter = isset($_GET['department']) ? $_GET['department'] : '';
$class_filter = isset($_GET['class']) ? $_GET['class'] : '';

// Build query for students
$sql = "SELECT s.*, d.name as dept_name FROM students s 
        JOIN departments d ON s.dept_id = d.id 
        WHERE s.hidden = 0";

$params = [];

if(!empty($name_filter)) {
    $sql .= " AND s.name LIKE :name";
    $params[':name'] = '%' . $name_filter . '%';
}

if(!empty($dept_filter)) {
    $sql .= " AND s.dept_id = :dept_id";
    $params[':dept_id'] = $dept_filter;
}

if(!empty($class_filter)) {
    $sql .= " AND s.roll_no LIKE :class";
    $params[':class'] = $class_filter . '%';
}

$sql .= " ORDER BY s.dept_id, s.roll_no";

$students = [];
if($stmt = $pdo->prepare($sql)){
    foreach($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    if($stmt->execute()){
        $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - SF Due Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .student-card {
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            padding: 1rem;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
        }
        .student-card:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        .due-amount {
            font-weight: bold;
        }
        .year-due {
            background-color: #f8f9fa;
            padding: 0.5rem;
            border-radius: 0.25rem;
            margin-bottom: 0.5rem;
        }
        .nav-tabs .nav-link.active {
            font-weight: bold;
        }
        .filter-section {
            background-color: #f8f9fa;
            padding: 1rem;
            border-radius: 0.375rem;
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">SF Due Portal</a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3">
                    Welcome, <?php echo htmlspecialchars($_SESSION["username"]); ?>
                </span>
                <a class="nav-link" href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <h2>Student Due Dashboard</h2>
                
                <!-- Filter Section -->
                <div class="filter-section">
                    <form method="GET" action="">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="name" class="form-label">Student Name</label>
                                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($name_filter); ?>" placeholder="Search by name">
                            </div>
                            <div class="col-md-4">
                                <label for="department" class="form-label">Department</label>
                                <select class="form-select" id="department" name="department">
                                    <option value="">All Departments</option>
                                    <?php foreach($departments as $dept): ?>
                                        <option value="<?php echo $dept['id']; ?>" <?php echo ($dept_filter == $dept['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($dept['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="class" class="form-label">Class/Year</label>
                                <input type="text" class="form-control" id="class" name="class" value="<?php echo htmlspecialchars($class_filter); ?>" placeholder="e.g., CS, EC">
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">Apply Filters</button>
                                <a href="dashboard.php" class="btn btn-secondary">Clear Filters</a>
                                <a href="logs.php" class="btn btn-outline-info float-end">View Update Logs</a>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Department Tabs -->
                <ul class="nav nav-tabs" id="deptTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab">All Students</button>
                    </li>
                    <?php foreach($departments as $dept): ?>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="<?php echo strtolower(str_replace(' ', '-', $dept['name'])); ?>-tab" 
                                data-bs-toggle="tab" 
                                data-bs-target="#<?php echo strtolower(str_replace(' ', '-', $dept['name'])); ?>" 
                                type="button" role="tab">
                            <?php echo htmlspecialchars($dept['name']); ?>
                        </button>
                    </li>
                    <?php endforeach; ?>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content mt-3" id="deptTabContent">
                    <!-- All Students Tab -->
                    <div class="tab-pane fade show active" id="all" role="tabpanel">
                        <div class="row" id="students-container">
                            <?php if(empty($students)): ?>
                                <div class="col-12">
                                    <div class="alert alert-info">No students found matching your criteria.</div>
                                </div>
                            <?php else: ?>
                                <?php foreach($students as $student): ?>
                                    <div class="col-md-6 col-lg-4 mb-3" data-dept="<?php echo $student['dept_id']; ?>">
                                        <?php include 'student_card.php'; ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Department-specific Tabs -->
                    <?php foreach($departments as $dept): ?>
                    <div class="tab-pane fade" id="<?php echo strtolower(str_replace(' ', '-', $dept['name'])); ?>" role="tabpanel">
                        <div class="row" id="dept-<?php echo $dept['id']; ?>-students">
                            <?php 
                            $dept_students = array_filter($students, function($s) use ($dept) {
                                return $s['dept_id'] == $dept['id'];
                            });
                            ?>
                            <?php if(empty($dept_students)): ?>
                                <div class="col-12">
                                    <div class="alert alert-info">No students found in this department.</div>
                                </div>
                            <?php else: ?>
                                <?php foreach($dept_students as $student): ?>
                                    <div class="col-md-6 col-lg-4 mb-3">
                                        <?php include 'student_card.php'; ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Update Student Modal -->
    <div class="modal fade" id="updateStudentModal" tabindex="-1" aria-labelledby="updateStudentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateStudentModalLabel">Update Student Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="updateStudentForm">
                        <input type="hidden" id="student_id" name="student_id">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="update_name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="roll_no" class="form-label">Roll No</label>
                            <input type="text" class="form-control" id="update_roll_no" name="roll_no" required>
                        </div>
                        <div class="mb-3">
                            <label for="dept_id" class="form-label">Department</label>
                            <select class="form-select" id="update_dept_id" name="dept_id" required>
                                <?php foreach($departments as $dept): ?>
                                    <option value="<?php echo $dept['id']; ?>"><?php echo htmlspecialchars($dept['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="year1_due" class="form-label">Year 1 Due</label>
                                <input type="number" step="0.01" class="form-control" id="update_year1_due" name="year1_due" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="year2_due" class="form-label">Year 2 Due</label>
                                <input type="number" step="0.01" class="form-control" id="update_year2_due" name="year2_due" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="year3_due" class="form-label">Year 3 Due</label>
                                <input type="number" step="0.01" class="form-control" id="update_year3_due" name="year3_due" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="paid" class="form-label">Amount Paid</label>
                            <input type="number" step="0.01" class="form-control" id="update_paid" name="paid" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveStudentChanges">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="dashboard.js"></script>
</body>
</html>
