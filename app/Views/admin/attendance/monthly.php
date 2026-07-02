<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Monthly Attendance - Admin Panel</title>
    <link rel="shortcut icon" type="image/png" href="<?= base_url('/assets/images/logos/favicon.png') ?>" />
    <link rel="stylesheet" href="<?= base_url('/assets/css/styles.min.css') ?>" />
    <style>
        html, body { margin: 0; padding: 0; }
        #main-wrapper, .page-wrapper { padding-top: 0 !important; }
        .body-wrapper, .body-wrapper-inner { margin-top: 0 !important; padding-top: 0 !important; }
        .app-header, .navbar { top: 0 !important; margin-top: 0 !important; padding-top: 0 !important; }
        #main-wrapper[data-layout="vertical"][data-sidebar-position="fixed"] .left-sidebar { top: 0 !important; }

        .attendance-calendar {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 8px;
            margin-bottom: 30px;
        }

        .calendar-header {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 8px;
            margin-bottom: 10px;
            font-weight: 600;
            text-align: center;
        }

        .calendar-day-header {
            padding: 8px;
            background: #f8f9fa;
            border-radius: 4px;
            font-size: 12px;
        }

        .calendar-day {
            min-height: 50px;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            text-align: center;
            font-size: 12px;
            position: relative;
            cursor: pointer;
            transition: all 0.2s;
        }

        .calendar-day:hover {
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .calendar-day.empty {
            background: #f8f9fa;
            cursor: not-allowed;
        }

        .calendar-day.present {
            background: #d1e7dd;
            border-color: #0f5132;
        }

        .calendar-day.absent {
            background: #f8d7da;
            border-color: #842029;
        }

        .calendar-day.leave {
            background: #d1ecf1;
            border-color: #0c5460;
        }

        .calendar-day.half-day {
            background: #fff3cd;
            border-color: #664d03;
        }

        .calendar-day-number {
            font-weight: 600;
            margin-bottom: 2px;
        }

        .calendar-status-indicator {
            font-size: 11px;
            font-weight: 600;
        }

        .summary-card {
            padding: 15px;
            border-radius: 4px;
            text-align: center;
            margin-bottom: 10px;
        }

        .summary-card h6 {
            margin-bottom: 8px;
            font-weight: 600;
        }

        .summary-card .count {
            font-size: 24px;
            font-weight: 700;
        }

        .summary-present {
            background: #d1e7dd;
            color: #0f5132;
            border-left: 4px solid #0f5132;
        }

        .summary-absent {
            background: #f8d7da;
            color: #842029;
            border-left: 4px solid #842029;
        }

        .summary-leave {
            background: #d1ecf1;
            color: #0c5460;
            border-left: 4px solid #0c5460;
        }

        .summary-half-day {
            background: #fff3cd;
            color: #664d03;
            border-left: 4px solid #664d03;
        }

        .filter-section {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">

         <?= $this->include('admin/sidebar'); ?>
       
        <div class="body-wrapper">
         <?= $this->include('admin/header'); ?>
            <div class="body-wrapper-inner">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title mb-4">
                                        <i class="ti ti-calendar-month me-2"></i>Monthly Attendance
                                    </h4>

                                    <div id="alertBox"></div>

                                    <!-- Filter Section -->
                                    <div class="filter-section">
                                        <form id="filterForm">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="mb-3">
                                                        <label for="employeeSelect" class="form-label">Select Employee <span class="text-danger">*</span></label>
                                                        <select class="form-select" id="employeeSelect" required>
                                                            <option value="">-- Choose Employee --</option>
                                                            <?php foreach ($employees as $emp): ?>
                                                                <option value="<?= $emp['employeeId'] ?>">
                                                                    <?= htmlspecialchars($emp['name']) ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="mb-3">
                                                        <label for="monthSelect" class="form-label">Month</label>
                                                        <select class="form-select" id="monthSelect">
                                                            <?php for ($m = 1; $m <= 12; $m++): ?>
                                                                <option value="<?= $m ?>" <?= $m == $currentMonth ? 'selected' : '' ?>>
                                                                    <?= date('F', mktime(0, 0, 0, $m, 1)) ?>
                                                                </option>
                                                            <?php endfor; ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="mb-3">
                                                        <label for="yearSelect" class="form-label">Year</label>
                                                        <select class="form-select" id="yearSelect">
                                                            <?php for ($y = date('Y') - 2; $y <= date('Y'); $y++): ?>
                                                                <option value="<?= $y ?>" <?= $y == $currentYear ? 'selected' : '' ?>>
                                                                    <?= $y ?>
                                                                </option>
                                                            <?php endfor; ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="mb-3">
                                                        <label class="form-label">&nbsp;</label>
                                                        <button type="submit" class="btn btn-primary w-100">
                                                            <i class="ti ti-search"></i> Load
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>

                                    <div id="attendanceContent" style="display:none;">
                                        <!-- Summary Section -->
                                        <div class="row mb-4">
                                            <div class="col-md-3">
                                                <div class="summary-card summary-present">
                                                    <h6>Present</h6>
                                                    <div class="count" id="presentCount">0</div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="summary-card summary-absent">
                                                    <h6>Absent</h6>
                                                    <div class="count" id="absentCount">0</div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="summary-card summary-leave">
                                                    <h6>Leave</h6>
                                                    <div class="count" id="leaveCount">0</div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="summary-card summary-half-day">
                                                    <h6>Half Day</h6>
                                                    <div class="count" id="halfDayCount">0</div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Calendar Section -->
                                        <div class="card bg-light mb-4">
                                            <div class="card-body">
                                                <h6 class="mb-3" id="monthYearTitle"></h6>
                                                
                                                <div class="calendar-header">
                                                    <div class="calendar-day-header">Sun</div>
                                                    <div class="calendar-day-header">Mon</div>
                                                    <div class="calendar-day-header">Tue</div>
                                                    <div class="calendar-day-header">Wed</div>
                                                    <div class="calendar-day-header">Thu</div>
                                                    <div class="calendar-day-header">Fri</div>
                                                    <div class="calendar-day-header">Sat</div>
                                                </div>

                                                <div class="attendance-calendar" id="attendanceCalendar"></div>
                                            </div>
                                        </div>

                                        <!-- Detailed Records -->
                                        <div class="card">
                                            <div class="card-body">
                                                <h6 class="mb-3">Detailed Records</h6>
                                                <div class="table-responsive">
                                                    <table class="table table-sm">
                                                        <thead>
                                                            <tr>
                                                                <th>Date</th>
                                                                <th>Check In</th>
                                                                <th>Check Out</th>
                                                                <th>Status</th>
                                                                <th>Remarks</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="detailedRecords">
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
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
        const alertBox = document.getElementById('alertBox');
        const filterForm = document.getElementById('filterForm');
        const attendanceContent = document.getElementById('attendanceContent');

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

        function getStatusClass(status) {
            const map = {
                'Present': 'present',
                'Absent': 'absent',
                'Leave': 'leave',
                'Half Day': 'half-day'
            };
            return map[status] || '';
        }

        async function loadMonthlyAttendance(e) {
            e.preventDefault();

            const employeeId = document.getElementById('employeeSelect').value;
            const month = document.getElementById('monthSelect').value;
            const year = document.getElementById('yearSelect').value;

            if (!employeeId) {
                setAlert('Please select an employee', 'error');
                return;
            }

            try {
                const response = await fetch('<?= site_url('admin/attendance/get-monthly') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({
                        employee_id: employeeId,
                        month: month,
                        year: year
                    })
                });

                const data = await response.json();

                if (data.success) {
                    displayMonthlyAttendance(data.records, data.summary, month, year);
                    attendanceContent.style.display = 'block';
                } else {
                    setAlert(data.message || 'Error loading attendance', 'error');
                    attendanceContent.style.display = 'none';
                }
            } catch (error) {
                setAlert('Error: ' + error.message, 'error');
                attendanceContent.style.display = 'none';
            }
        }

        function displayMonthlyAttendance(records, summary, month, year) {
            // Update summary
            document.getElementById('presentCount').textContent = summary['Present'] || 0;
            document.getElementById('absentCount').textContent = summary['Absent'] || 0;
            document.getElementById('leaveCount').textContent = summary['Leave'] || 0;
            document.getElementById('halfDayCount').textContent = summary['Half Day'] || 0;

            // Create month/year title
            const monthName = new Date(year, month - 1).toLocaleString('default', { month: 'long' });
            document.getElementById('monthYearTitle').textContent = `${monthName} ${year}`;

            // Create calendar
            const firstDay = new Date(year, month - 1, 1);
            const lastDay = new Date(year, month, 0);
            const daysInMonth = lastDay.getDate();
            const startingDayOfWeek = firstDay.getDay();

            // Create attendance map
            const attendanceMap = {};
            records.forEach(record => {
                attendanceMap[record.attendance_date] = record;
            });

            const calendar = document.getElementById('attendanceCalendar');
            calendar.innerHTML = '';

            // Empty cells before month starts
            for (let i = 0; i < startingDayOfWeek; i++) {
                const emptyDay = document.createElement('div');
                emptyDay.className = 'calendar-day empty';
                calendar.appendChild(emptyDay);
            }

            // Days of month
            for (let day = 1; day <= daysInMonth; day++) {
                const dateStr = `${year}-${String(month).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                const attendance = attendanceMap[dateStr];
                
                const dayElement = document.createElement('div');
                dayElement.className = `calendar-day ${attendance ? getStatusClass(attendance.status) : ''}`;
                
                let content = `<div class="calendar-day-number">${day}</div>`;
                if (attendance) {
                    content += `<div class="calendar-status-indicator">${attendance.status.substring(0, 3).toUpperCase()}</div>`;
                }
                
                dayElement.innerHTML = content;
                dayElement.title = attendance ? `${attendance.status}: ${attendance.check_in_time || ''} - ${attendance.check_out_time || ''}` : dateStr;
                calendar.appendChild(dayElement);
            }

            // Display detailed records
            const detailedRecords = document.getElementById('detailedRecords');
            detailedRecords.innerHTML = '';

            if (records.length === 0) {
                detailedRecords.innerHTML = '<tr><td colspan="5" class="text-center text-muted">No records found</td></tr>';
            } else {
                records.forEach(record => {
                    const row = `
                        <tr>
                            <td>${record.attendance_date}</td>
                            <td>${record.check_in_time || '-'}</td>
                            <td>${record.check_out_time || '-'}</td>
                            <td><span class="badge bg-${getStatusClass(record.status).replace('-', '')}">${record.status}</span></td>
                            <td>${record.remarks || '-'}</td>
                        </tr>
                    `;
                    detailedRecords.innerHTML += row;
                });
            }
        }

        filterForm.addEventListener('submit', loadMonthlyAttendance);
    </script>
</body>

</html>
