<?php
// Calculate totals
$total_due = $student['year1_due'] + $student['year2_due'] + $student['year3_due'];
$balance = $total_due - $student['paid'];
?>
<div class="student-card">
    <div class="d-flex justify-content-between align-items-start mb-2">
        <h5 class="card-title mb-0"><?php echo htmlspecialchars($student['name']); ?></h5>
        <span class="badge bg-secondary"><?php echo htmlspecialchars($student['roll_no']); ?></span>
    </div>
    <p class="text-muted small mb-2"><?php echo htmlspecialchars($student['dept_name']); ?></p>
    
    <div class="year-due">
        <small>Year 1 Due: <span class="due-amount text-warning">₹<?php echo number_format($student['year1_due'], 2); ?></span></small>
    </div>
    <div class="year-due">
        <small>Year 2 Due: <span class="due-amount text-warning">₹<?php echo number_format($student['year2_due'], 2); ?></span></small>
    </div>
    <div class="year-due">
        <small>Year 3 Due: <span class="due-amount text-warning">₹<?php echo number_format($student['year3_due'], 2); ?></span></small>
    </div>
    
    <div class="d-flex justify-content-between mt-3">
        <div>
            <div class="fw-bold">Total Due: ₹<?php echo number_format($total_due, 2); ?></div>
            <div class="text-success">Paid: ₹<?php echo number_format($student['paid'], 2); ?></div>
            <div class="text-danger fw-bold">Balance: ₹<?php echo number_format($balance, 2); ?></div>
        </div>
    </div>
    
    <div class="mt-3">
        <small class="text-muted">Last Updated: <?php echo date('M j, Y g:i A', strtotime($student['last_updated'])); ?></small>
    </div>
    
    <div class="mt-3 d-flex gap-2">
        <button class="btn btn-sm btn-primary update-student-btn" 
                data-id="<?php echo $student['id']; ?>"
                data-name="<?php echo htmlspecialchars($student['name']); ?>"
                data-roll_no="<?php echo htmlspecialchars($student['roll_no']); ?>"
                data-dept_id="<?php echo $student['dept_id']; ?>"
                data-year1_due="<?php echo $student['year1_due']; ?>"
                data-year2_due="<?php echo $student['year2_due']; ?>"
                data-year3_due="<?php echo $student['year3_due']; ?>"
                data-paid="<?php echo $student['paid']; ?>">
            <i class="bi bi-pencil"></i> Update
        </button>
        <button class="btn btn-sm btn-danger remove-student-btn" data-id="<?php echo $student['id']; ?>">
            <i class="bi bi-trash"></i> Remove
        </button>
    </div>
</div>
