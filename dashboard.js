$(document).ready(function() {
    // Update student modal
    $('.update-student-btn').click(function() {
        const studentId = $(this).data('id');
        const name = $(this).data('name');
        const rollNo = $(this).data('roll_no');
        const deptId = $(this).data('dept_id');
        const year1Due = $(this).data('year1_due');
        const year2Due = $(this).data('year2_due');
        const year3Due = $(this).data('year3_due');
        const paid = $(this).data('paid');
        
        $('#student_id').val(studentId);
        $('#update_name').val(name);
        $('#update_roll_no').val(rollNo);
        $('#update_dept_id').val(deptId);
        $('#update_year1_due').val(year1Due);
        $('#update_year2_due').val(year2Due);
        $('#update_year3_due').val(year3Due);
        $('#update_paid').val(paid);
        
        $('#updateStudentModal').modal('show');
    });
    
    // Save student changes
    $('#saveStudentChanges').click(function() {
        const formData = $('#updateStudentForm').serialize();
        
        $.ajax({
            url: 'update_student.php',
            type: 'POST',
            data: formData,
            success: function(response) {
                const result = JSON.parse(response);
                if(result.success) {
                    $('#updateStudentModal').modal('hide');
                    location.reload(); // Reload to show updated data
                } else {
                    alert('Error updating student: ' + result.message);
                }
            },
            error: function() {
                alert('Error updating student. Please try again.');
            }
        });
    });
    
    // Remove student
    $('.remove-student-btn').click(function() {
        const studentId = $(this).data('id');
        const studentName = $(this).closest('.student-card').find('.card-title').text();
        
        if(confirm(`Are you sure you want to remove ${studentName} from the dashboard?`)) {
            $.ajax({
                url: 'remove_student.php',
                type: 'POST',
                data: { student_id: studentId },
                success: function(response) {
                    const result = JSON.parse(response);
                    if(result.success) {
                        // Hide the student card with animation
                        $(`button[data-id="${studentId}"]`).closest('.col-md-6').fadeOut(300, function() {
                            $(this).remove();
                        });
                        
                        // Show undo button
                        showUndoButton(studentId, studentName);
                    } else {
                        alert('Error removing student: ' + result.message);
                    }
                },
                error: function() {
                    alert('Error removing student. Please try again.');
                }
            });
        }
    });
    
    // Show undo button
    function showUndoButton(studentId, studentName) {
        const undoHtml = `
            <div class="alert alert-warning alert-dismissible fade show mt-3" role="alert" id="undo-alert-${studentId}">
                Student "${studentName}" has been removed from dashboard.
                <button type="button" class="btn btn-sm btn-success ms-2 undo-remove-btn" data-id="${studentId}">
                    <i class="bi bi-arrow-counterclockwise"></i> Undo
                </button>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        $('.container .row .col-12').first().after(undoHtml);
        
        // Auto remove alert after 10 seconds
        setTimeout(() => {
            $(`#undo-alert-${studentId}`).alert('close');
        }, 10000);
    }
    
    // Undo remove
    $(document).on('click', '.undo-remove-btn', function() {
        const studentId = $(this).data('id');
        
        $.ajax({
            url: 'undo_remove.php',
            type: 'POST',
            data: { student_id: studentId },
            success: function(response) {
                const result = JSON.parse(response);
                if(result.success) {
                    $(`#undo-alert-${studentId}`).alert('close');
                    location.reload(); // Reload to show the student again
                } else {
                    alert('Error restoring student: ' + result.message);
                }
            },
            error: function() {
                alert('Error restoring student. Please try again.');
            }
        });
    });
    
    // AJAX polling for real-time updates (every 10 seconds)
    setInterval(function() {
        $.ajax({
            url: 'get_updated_students.php',
            type: 'GET',
            success: function(response) {
                // This would update the dashboard with new data
                // For simplicity, we're just logging for now
                console.log('Polling for updates...');
            }
        });
    }, 10000);
});
