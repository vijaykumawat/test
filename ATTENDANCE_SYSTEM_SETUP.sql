-- =====================================================
-- EMPLOYEE ATTENDANCE MANAGEMENT SYSTEM
-- Complete SQL Setup and Documentation
-- =====================================================

-- =====================================================
-- 1. CREATE ATTENDANCE TABLE
-- =====================================================
CREATE TABLE `attendance` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `employee_id` INT NOT NULL,
  `attendance_date` DATE NOT NULL,
  `check_in_time` TIME DEFAULT NULL,
  `check_out_time` TIME DEFAULT NULL,
  `status` ENUM('Present', 'Absent', 'Half Day', 'Leave') DEFAULT 'Present',
  `remarks` TEXT DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`employee_id`) REFERENCES `employee`(`employeeId`) ON DELETE CASCADE ON UPDATE CASCADE,
  UNIQUE KEY `unique_employee_date` (`employee_id`, `attendance_date`),
  INDEX `idx_attendance_date` (`attendance_date`),
  INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 2. SAMPLE DATA (Optional - for testing)
-- =====================================================

-- Insert sample attendance records
INSERT INTO `attendance` (`employee_id`, `attendance_date`, `check_in_time`, `check_out_time`, `status`, `remarks`)
VALUES 
  (1, '2024-06-10', '09:00:00', '17:30:00', 'Present', NULL),
  (1, '2024-06-11', '09:15:00', '17:45:00', 'Present', 'Slight delay'),
  (1, '2024-06-12', NULL, NULL, 'Leave', 'Medical leave'),
  (2, '2024-06-10', '09:00:00', '14:00:00', 'Half Day', 'Afternoon off'),
  (2, '2024-06-11', NULL, NULL, 'Absent', NULL);

-- =====================================================
-- 3. USEFUL QUERIES
-- =====================================================

-- Get attendance for a specific employee and date
SELECT * FROM attendance 
WHERE employee_id = ? AND attendance_date = ? 
LIMIT 1;

-- Get monthly attendance summary
SELECT 
  status, 
  COUNT(*) as count 
FROM attendance 
WHERE employee_id = ? 
  AND MONTH(attendance_date) = ? 
  AND YEAR(attendance_date) = ? 
GROUP BY status;

-- Get attendance for date range
SELECT 
  a.*, 
  e.name as employee_name, 
  e.jobTitle 
FROM attendance a 
LEFT JOIN employee e ON e.employeeId = a.employee_id 
WHERE a.attendance_date BETWEEN ? AND ? 
ORDER BY a.attendance_date DESC, e.name ASC;

-- Get today's attendance statistics
SELECT 
  status, 
  COUNT(*) as count 
FROM attendance 
WHERE attendance_date = CURDATE() 
GROUP BY status;

-- Get employees who are absent today
SELECT 
  e.employeeId, 
  e.name, 
  e.jobTitle 
FROM employee e 
LEFT JOIN attendance a ON e.employeeId = a.employee_id 
  AND a.attendance_date = CURDATE() 
WHERE e.isActive = 1 
  AND (a.status = 'Absent' OR a.id IS NULL);

-- Get monthly calendar for employee
SELECT 
  attendance_date,
  check_in_time,
  check_out_time,
  status,
  remarks
FROM attendance
WHERE employee_id = ?
  AND MONTH(attendance_date) = ?
  AND YEAR(attendance_date) = ?
ORDER BY attendance_date ASC;

-- Calculate attendance percentage
SELECT 
  e.name,
  COUNT(CASE WHEN a.status = 'Present' THEN 1 END) as days_present,
  COUNT(CASE WHEN a.status = 'Absent' THEN 1 END) as days_absent,
  COUNT(CASE WHEN a.status = 'Half Day' THEN 1 END) as days_half,
  COUNT(CASE WHEN a.status = 'Leave' THEN 1 END) as days_leave,
  ROUND((COUNT(CASE WHEN a.status = 'Present' THEN 1 END) / COUNT(*)) * 100, 2) as attendance_percentage
FROM employee e
LEFT JOIN attendance a ON e.employeeId = a.employee_id
  AND MONTH(a.attendance_date) = MONTH(CURDATE())
  AND YEAR(a.attendance_date) = YEAR(CURDATE())
WHERE e.isActive = 1
GROUP BY e.employeeId, e.name;

-- =====================================================
-- 4. VALIDATION RULES IN APPLICATION
-- =====================================================

/*
Business Rules Implementation:
1. Duplicate Prevention: Unique key on (employee_id, attendance_date)
2. Future Dates: Attendance cannot be marked for future dates (validated in Controller)
3. Check-out Validation: Check-out time must be > check-in time (validated in Model)
4. Status Validation: Only 'Present', 'Absent', 'Half Day', 'Leave' allowed
5. Bulk Operations: Support marking attendance for multiple employees on same date
6. Soft Delete: Not implemented - hard delete is used
*/

-- =====================================================
-- 5. DATABASE INDEXES FOR OPTIMIZATION
-- =====================================================

-- These indexes are already created in the migration, but here's what was created:
-- - Primary Key on `id`
-- - Foreign Key on `employee_id` (with CASCADE delete/update)
-- - Unique Index on (employee_id, attendance_date) - prevents duplicate entries
-- - Index on `attendance_date` - speeds up date range queries
-- - Index on `status` - speeds up status filtering

-- =====================================================
-- 6. FEATURE CHECKLIST
-- =====================================================

/*
✓ Mark Attendance
  - Display all active employees
  - Bulk selection and marking
  - Prevent duplicate entries
  - Default status is Present
  - Optional check-in/out times
  
✓ Attendance Report
  - Filter by employee, date range, status
  - Display with check-in/out times
  - Export to CSV
  - Responsive DataTables

✓ Monthly Attendance
  - Calendar grid view
  - Visual indicators (P/A/H/L)
  - Summary statistics
  - Detailed records listing

✓ Employee Attendance History
  - Complete history with pagination
  - Duration calculation (check-out - check-in)
  - Edit/Delete capabilities
  - Today's statistics display

✓ Dashboard Widgets (Integrated)
  - Total employees
  - Present today
  - Absent today
  - On leave today

✓ Validation
  - Attendance date cannot be in future
  - Check-out > check-in time
  - Duplicate entry prevention
  - Required field validation
*/

-- =====================================================
-- 7. INSTALLATION INSTRUCTIONS
-- =====================================================

/*
1. Run Migration:
   php spark migrate --all
   
2. Access Features via Admin Panel:
   - Sidebar > Employee > Attendance > Mark Attendance
   - Sidebar > Employee > Attendance > Attendance Report
   - Sidebar > Employee > Attendance > Monthly Attendance
   - Sidebar > Employee > Attendance > Attendance History

3. API Endpoints:
   GET  /admin/attendance/mark          - Mark attendance page
   POST /admin/attendance/save          - Save attendance records
   GET  /admin/attendance/report        - Attendance report page
   POST /admin/attendance/get-report    - Get report data
   GET  /admin/attendance/export        - Export to CSV
   GET  /admin/attendance/monthly       - Monthly view page
   POST /admin/attendance/get-monthly   - Get monthly data
   GET  /admin/attendance/history/:id   - Employee history
   POST /admin/attendance/update        - Update record
   POST /admin/attendance/delete        - Delete record
   POST /admin/attendance/today-stats   - Get today stats
*/

-- =====================================================
-- 8. DATABASE BACKUP COMMAND
-- =====================================================

/*
Backup the attendance table:
mysqldump -u root -p gbinsurance attendance > attendance_backup.sql

Restore:
mysql -u root -p gbinsurance < attendance_backup.sql
*/
