<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mark Attendance - Admin Panel</title>
    <link rel="shortcut icon" type="image/png" href="<?= base_url('/assets/images/logos/favicon.png') ?>" />
    <link rel="stylesheet" href="<?= base_url('/assets/css/styles.min.css') ?>" />
    <style>
        html, body { margin: 0; padding: 0; }
        #main-wrapper, .page-wrapper { padding-top: 0 !important; }
        .body-wrapper, .body-wrapper-inner { margin-top: 0 !important; padding-top: 0 !important; }
        .app-header, .navbar { top: 0 !important; margin-top: 0 !important; padding-top: 0 !important; }
        #main-wrapper[data-layout="vertical"][data-sidebar-position="fixed"] .left-sidebar { top: 0 !important; }

        .employee-checkbox {
            margin-bottom: 0.5rem;
        }

        .employee-list {
            max-height: 400px;
            overflow-y: auto;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 10px;
            background: #f8f9fa;
        }

        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
        }

        .time-input {
            max-width: 120px;
        }

        .select-controls {
            margin-bottom: 15px;
        }

        .btn-group-sm .btn {
            padding: 6px 12px;
            font-size: 12px;
        }
    </style>
</head>

<body>
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">

        <?php include '../sidebar.php'; ?>

        <div class="body-wrapper">
            <?php include '../header.php'; ?>

            <div class="body-wrapper-inner">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title mb-4">
                                        <i class="ti ti-calendar-check me-2"></i>Mark Attendance
                                    </h4>

                                    <div id="alertBox"></div>

                                    <form id="markAttendanceForm">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="attendanceDate" class="form-label">Attendance Date <span class="text-danger">*</span></label>
                                                    <input type="date" class="form-control" id="attendanceDate" name="attendance_date" 
                                                           value="<?= $today ?>" max="<?= $today ?>" required>
                                                    <small class="text-muted">Select date for marking attendance (cannot be future date)</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Quick Actions</label>
                                                    <div class="btn-group w-100" role="group">
                                                        <button type="button" class="btn btn-outline-primary btn-sm" id="selectAllBtn">
                                                            <i class="ti ti-check-all"></i> Select All
                                                        </button>
                                                        <button type="button" class="btn btn-outline-secondary btn-sm" id="clearAllBtn">
                                                            <i class="ti ti-x"></i> Clear All
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-12">
                                                <div class="mb-3">
                                                    <label class="form-label">Select Employees <span class="text-danger">*</span></label>
                                                    <div class="employee-list">
                                                        <?php foreach ($employees as $employee): ?>
                                                            <div class="form-check employee-checkbox">
                                                                <input class="form-check-input employee-checkbox-input" type="checkbox" 
                                                                       value="<?= $employee['employeeId'] ?>" 
                                                                       id="emp_<?= $employee['employeeId'] ?>"
                                                                       data-name="<?= htmlspecialchars($employee['name']) ?>">
                                                                <label class="form-check-label" for="emp_<?= $employee['employeeId'] ?>">
                                                                    <strong><?= htmlspecialchars($employee['name']) ?></strong>
                                                                    <small class="text-muted ms-2"><?= $employee['jobTitle'] ?? 'N/A' ?></small>
                                                                </label>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                    <small class="text-muted d-block mt-2">Total: <?= count($employees) ?> active employees</small>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row" id="attendanceDetailsRow" style="display:none;">
                                            <div class="col-12">
                                                <div class="card bg-light">
                                                    <div class="card-body">
                                                        <h6 class="card-title mb-3">Attendance Details</h6>
                                                        <div id="attendanceDetailsContainer"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mt-3">
                                            <div class="col-12">
                                                <button type="submit" class="btn btn-success" id="submitBtn">
                                                    <i class="ti ti-check"></i> Submit Attendance
                                                </button>
                                                <a href="<?= site_url('/admin/attendance/report') ?>" class="btn btn-secondary">
                                                    <i class="ti ti-arrow-left"></i> Back
                                                </a>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="<?= base_url('/assets/libs/jquery/dist/jquery.min.js') ?>"></script>
    <script src="<?= base_url('/assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?= base_url('/assets/js/sidebarmenu.js') ?>"></script>
    <script src="<?= base_url('/assets/js/app.min.js') ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>

    <script>
        const attendanceDetailsContainer = document.getElementById('attendanceDetailsContainer');
        const attendanceDetailsRow = document.getElementById('attendanceDetailsRow');
        const checkboxes = document.querySelectorAll('.employee-checkbox-input');
        const alertBox = document.getElementById('alertBox');
        const form = document.getElementById('markAttendanceForm');

        function setAlert(message, type) {
            const alertClass = type === 'success' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger';
            alertBox.innerHTML = `
                <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                    <i class="ti ${type === 'success' ? 'ti-circle-check' : 'ti-alert-circle'} me-2"></i>
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
        }

        // Update attendance details when checkboxes change
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateAttendanceDetails);
        });

        function updateAttendanceDetails() {
            const selected = document.querySelectorAll('.employee-checkbox-input:checked');
            
            if (selected.length === 0) {
                attendanceDetailsRow.style.display = 'none';
                attendanceDetailsContainer.innerHTML = '';
                return;
            }

            attendanceDetailsRow.style.display = 'block';
            let html = '';

            selected.forEach(checkbox => {
                const employeeId = checkbox.value;
                const employeeName = checkbox.dataset.name;
                
                html += `
                    <div class="row mb-3 pb-3 border-bottom">
                        <div class="col-md-3">
                            <strong>${employeeName}</strong>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <select class="form-select form-select-sm" name="status_${employeeId}" data-employee="${employeeId}">
                                <option value="Present">Present</option>
                                <option value="Absent">Absent</option>
                                <option value="Half Day">Half Day</option>
                                <option value="Leave">Leave</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Check In</label>
                            <input type="time" class="form-control form-control-sm time-input" 
                                   name="check_in_${employeeId}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Check Out</label>
                            <input type="time" class="form-control form-control-sm time-input" 
                                   name="check_out_${employeeId}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Remarks</label>
                            <input type="text" class="form-control form-control-sm" 
                                   name="remarks_${employeeId}" placeholder="Optional">
                        </div>
                    </div>
                `;
            });

            attendanceDetailsContainer.innerHTML = html;
        }

        // Select all
        document.getElementById('selectAllBtn').addEventListener('click', function(e) {
            e.preventDefault();
            checkboxes.forEach(checkbox => {
                checkbox.checked = true;
            });
            updateAttendanceDetails();
        });

        // Clear all
        document.getElementById('clearAllBtn').addEventListener('click', function(e) {
            e.preventDefault();
            checkboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
            updateAttendanceDetails();
        });

        // Form submission
        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            const attendanceDate = document.getElementById('attendanceDate').value;
            const selected = document.querySelectorAll('.employee-checkbox-input:checked');

            if (!attendanceDate) {
                setAlert('Please select attendance date', 'error');
                return;
            }

            if (selected.length === 0) {
                setAlert('Please select at least one employee', 'error');
                return;
            }

            const formData = new FormData();
            formData.append('attendance_date', attendanceDate);

            selected.forEach(checkbox => {
                const employeeId = checkbox.value;
                formData.append('employees[]', employeeId);
                
                const statusField = document.querySelector(`select[name="status_${employeeId}"]`);
                const checkInField = document.querySelector(`input[name="check_in_${employeeId}"]`);
                const checkOutField = document.querySelector(`input[name="check_out_${employeeId}"]`);
                const remarksField = document.querySelector(`input[name="remarks_${employeeId}"]`);

                if (statusField) formData.append(`status_${employeeId}`, statusField.value);
                if (checkInField?.value) formData.append(`check_in_${employeeId}`, checkInField.value);
                if (checkOutField?.value) formData.append(`check_out_${employeeId}`, checkOutField.value);
                if (remarksField?.value) formData.append(`remarks_${employeeId}`, remarksField.value);
            });

            try {
                document.getElementById('submitBtn').disabled = true;
                document.getElementById('submitBtn').textContent = 'Processing...';

                const response = await fetch('<?= site_url('admin/attendance/save') ?>', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    setAlert(data.message, 'success');
                    setTimeout(() => {
                        form.reset();
                        checkboxes.forEach(cb => cb.checked = false);
                        updateAttendanceDetails();
                    }, 1500);
                } else {
                    setAlert(data.message || 'Error saving attendance', 'error');
                }
            } catch (error) {
                setAlert('Error: ' + error.message, 'error');
            } finally {
                document.getElementById('submitBtn').disabled = false;
                document.getElementById('submitBtn').textContent = 'Submit Attendance';
            }
        });
    </script>
</body>

</html>
