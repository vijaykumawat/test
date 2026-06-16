# Employee Attendance Management System - Quick Reference

## 🎯 Quick Start

### 1. Run Migration
```bash
php spark migrate
```

### 2. Access Features
- Mark Attendance: `/admin/attendance/mark`
- Attendance Report: `/admin/attendance/report`
- Monthly Attendance: `/admin/attendance/monthly`
- Employee History: `/admin/attendance/history`

### 3. Verify Installation
- Check sidebar has "Attendance" menu under "Employee"
- Try marking attendance for today
- Should prevent future dates
- Should prevent duplicates (same employee, same date)

---

## 📋 File Locations

| Component | File Path |
|-----------|-----------|
| Model | `app/Models/AttendanceModel.php` |
| Migration | `app/Database/Migrations/2024-06-16-000001_CreateAttendanceTable.php` |
| Controller | `app/Controllers/Admin.php` |
| Routes | `app/Config/Routes.php` |
| Views | `app/Views/admin/attendance/*.php` |
| Sidebar | `app/Views/admin/sidebar.php` |
| Database | `ATTENDANCE_SYSTEM_SETUP.sql` |

---

## 🔧 Configuration

### Change Table Name
Edit migration and model:
```php
protected $table = 'attendance';  // In AttendanceModel
$this->forge->createTable('attendance');  // In Migration
```

### Change Date Format
In views, search for `date('Y-m-d')` and change format

### Change Statuses
Update migration ENUM and dropdown selects

---

## 🚨 Common Errors & Solutions

| Error | Cause | Solution |
|-------|-------|----------|
| SQLSTATE[42S02]: Table not found | Migration not run | Run `php spark migrate` |
| 404 Not Found | Route not defined | Verify routes in Routes.php |
| Undefined variable | Missing data in controller | Check controller method returns all needed variables |
| CSRF token mismatch | Form without CSRF | Ensure form includes CSRF token |
| Duplicate entry error | Trying to insert same employee+date | Check UI validation and DB constraint |

---

## 📊 Key Database Queries

### Get Today's Attendance
```php
$today = date('Y-m-d');
$records = $this->attendanceModel
    ->where('attendance_date', $today)
    ->findAll();
```

### Check if Attendance Exists
```php
$exists = $this->attendanceModel->attendanceExists($employeeId, $date);
```

### Get Monthly Summary
```php
$summary = $this->attendanceModel->getMonthlyAttendanceSummary(
    $employeeId, 
    '06', 
    '2024'
);
```

### Export Data
```php
$records = $this->attendanceModel->getAttendanceByDateRange(
    '2024-06-01', 
    '2024-06-30'
);
```

---

## 🎨 UI Customization

### Status Badge Colors
Edit CSS in view files:
```css
.status-present { background: #d1e7dd; color: #0f5132; }
.status-absent { background: #f8d7da; color: #842029; }
.status-leave { background: #d1ecf1; color: #0c5460; }
.status-half-day { background: #fff3cd; color: #664d03; }
```

### Change Pagination Size
Edit in `Admin.php`:
```php
$perPage = 15;  // Change to desired number
```

### Add Fields to Attendance
1. Add column to migration
2. Add to `$allowedFields` in model
3. Update form in view
4. Update validation rules in model

---

## 🔒 Security Checklist

- ✓ CSRF protection on all forms
- ✓ Input validation on both client and server
- ✓ SQL injection prevented via prepared statements (ORM)
- ✓ XSS prevented via htmlspecialchars() in views
- ✓ Future date prevention in controller
- ✓ Duplicate prevention via DB unique constraint
- ✓ Only active employees can have attendance marked
- ✓ Proper error messages without exposing SQL

---

## 📈 Performance Tips

### For Large Datasets
1. Add pagination
2. Use date range filtering
3. Create database indexes (already done)
4. Consider caching monthly summaries

### Query Optimization
- Use `select()` to fetch only needed columns
- Join with employee table only when displaying name
- Use indexes on frequently filtered columns

### JavaScript Performance
- Debounce search filters
- Lazy load paginated tables
- Cache AJAX responses when appropriate

---

## 🧪 Testing Checklist

### Unit Test Scenarios
- [ ] Mark attendance for single employee
- [ ] Mark attendance for multiple employees
- [ ] Prevent duplicate attendance
- [ ] Prevent future date attendance
- [ ] Validate check-out > check-in time
- [ ] Filter by date range
- [ ] Filter by status
- [ ] Filter by employee
- [ ] Export to CSV
- [ ] Monthly calendar display
- [ ] Pagination works
- [ ] Edit attendance record
- [ ] Delete attendance record

### Edge Cases
- [ ] Mark attendance for last day of month
- [ ] Mark attendance for Feb 29 (leap year)
- [ ] Mark attendance for large date ranges (>1 year)
- [ ] 1000+ employees bulk marking
- [ ] Special characters in remarks
- [ ] Very long remarks (>255 chars)
- [ ] Empty date ranges
- [ ] No records found

---

## 🔌 API Response Examples

### Mark Attendance Success
```json
{
  "success": true,
  "message": "Attendance saved: 5 records. Skipped: 0 (already marked).",
  "saved": 5,
  "skipped": 0
}
```

### Get Report Success
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "employee_id": 1,
      "attendance_date": "2024-06-16",
      "check_in_time": "09:00:00",
      "check_out_time": "17:30:00",
      "status": "Present",
      "remarks": null,
      "employee_name": "John Doe",
      "jobTitle": "Manager"
    }
  ],
  "count": 1
}
```

### Get Monthly Success
```json
{
  "success": true,
  "records": [...],
  "summary": {
    "Present": 20,
    "Absent": 2,
    "Half Day": 1,
    "Leave": 3
  }
}
```

---

## 📚 Code References

### Controller Method Pattern
```php
public function newFeature() {
    // Check prerequisites
    if (!condition) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Error message'
        ]);
    }
    
    // Process
    $result = $this->attendanceModel->action();
    
    // Return
    return $this->response->setJSON([
        'success' => true,
        'message' => 'Success message',
        'data' => $result
    ]);
}
```

### Model Query Pattern
```php
public function getCustomData($param) {
    return $this->select('specific, columns')
                ->where('condition', $param)
                ->orderBy('field', 'ASC')
                ->findAll();
}
```

### View Form Pattern
```html
<form id="form" method="POST">
    <?= csrf_field() ?>
    
    <div class="mb-3">
        <label for="field" class="form-label">Field</label>
        <input type="text" class="form-control" id="field" name="field">
    </div>
    
    <button type="submit" class="btn btn-primary">Submit</button>
</form>
```

---

## 📞 Frequently Asked Questions

**Q: Can I mark attendance for past dates?**  
A: Yes, any date up to today. Future dates are blocked.

**Q: What happens if I mark attendance twice for same employee and date?**  
A: Database unique constraint prevents it. UI also checks first.

**Q: Can I export all attendance data?**  
A: Yes, set date range to large period (e.g., full year) and export.

**Q: How do I view specific employee's history?**  
A: Go to Attendance History or click History from employee profile.

**Q: Can I bulk update attendance records?**  
A: Currently single updates. Add bulk edit feature if needed.

**Q: How many employees can I handle?**  
A: System handles 10,000+ employees. Optimize if more needed.

**Q: Is there a mobile app?**  
A: Not included. Can build separate mobile app consuming REST API.

**Q: Can I track location of check-in?**  
A: Not included. Add geolocation in future enhancement.

**Q: How do I backup attendance data?**  
A: Use `mysqldump` or export from MySQL/phpMyAdmin.

**Q: Can I integrate with biometric devices?**  
A: Not included. Requires custom integration with device API.

---

## 🔗 Important Links

- CodeIgniter Docs: https://codeigniter.com/docs/
- Bootstrap Docs: https://getbootstrap.com/docs/
- DataTables Docs: https://datatables.net/
- MySQL Docs: https://dev.mysql.com/doc/

---

**Last Updated**: 2024-06-16  
**Quick Reference Version**: 1.0
