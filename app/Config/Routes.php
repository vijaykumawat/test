<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
// Employee Authentication Routes (no filter)
$routes->get('/employee/login', 'Auth::loginForm');
$routes->post('/employee/login', 'Auth::login');
$routes->get('/employee/logout', 'Auth::logout');
$routes->get('/register', 'Auth::register');
$routes->post('/auth/employee-add', 'Auth::employeeAdd');

$routes->get('/tcpdfexample/quote', 'tcpdfexample::quote');
$routes->get('/Tcpdfexample/test', 'Tcpdfexample::test');
$routes->match(['get','post'], '/Tcpdfexample/quote', 'Tcpdfexample::quote');


// Protected Employee Routes
$routes->group('employee', ['filter' => ['authEmployee', 'sessionExpire']], function($routes) {
    $routes->get('dashboard', 'Employee::dashboard');
    $routes->get('dashboard/(:any)', 'Employee::dashboard/$1');
    $routes->post('save', 'Employee::save');
    $routes->post('save-ajax', 'Employee::saveAjax');
    $routes->post('toggle-star-ajax', 'Employee::toggleStarAjax');
    $routes->post('upload-policy', 'Employee::uploadPolicyPost');
    $routes->post('upload-policy-ajax', 'Employee::uploadPolicyPostAjax');
    $routes->get('edit-policy-view/(:num)', 'Employee::editPolicyView/$1');
    $routes->get('policies-sold', 'Employee::policiesSold');
    $routes->get('renewals', 'Employee::renewals');
    $routes->post('saveFieldSettings', 'Employee::saveFieldSettings');
    $routes->get('renewals/(:any)', 'Employee::renewals/$1');
    $routes->get('all-data', 'Employee::allData');
    $routes->get('timesheet', 'Employee::timesheet');
    $routes->get('expiry-data', 'Employee::expiryData');
    $routes->get('expiry-data-api', 'Employee::expiryDataApi');
    $routes->post('save-expiry-date', 'Employee::saveExpiryDate');
    $routes->post('skip-expiry-date', 'Employee::skipExpiryDate');
    $routes->get('nextRecord/(:any)', 'Employee::nextRecord/$1');
    $routes->get('starRecord/(:any)/(:any)', 'Employee::starRecord/$1/$2');
    $routes->get('allStarRecord', 'Employee::allStarRecord');
    $routes->get('download-policy/(:num)', 'Employee::downloadPolicy/$1');
    $routes->get('preview-policy/(:num)', 'Employee::previewPolicy/$1');
    $routes->get('prevRecord/(:any)', 'Employee::previousRecord/$1');
    $routes->get('forwardRecord/(:any)', 'Employee::forwardRecord/$1');
    
    $routes->get('(:any)', 'Employee::viewEmployee/$1');
    $routes->post('uploadProfilePhoto', 'Employee::uploadProfilePhoto'); 
    $routes->post('employee-update', 'Employee::updateEmployee');
    $routes->post('policy-update', 'Employee::postUpdatePolicy');
    
});


// Protected Admin Routes
$routes->group('admin', ['filter' => 'authAdmin'], function($routes) {
    $routes->get('/', 'Admin::index');
    $routes->get('subscription', 'Admin::subscriptionDetails');    
    $routes->get('upload', 'Admin::uploadPolicy');
    $routes->post('upload', 'Admin::uploadPolicyPost');
    $routes->get('data-loader', 'Admin::dataLoader');
    $routes->get('generic-data-loader', 'Admin::genericDataLoader');
    $routes->post('generic-upload-data', 'Admin::genericDataLoaderPost');
    $routes->post('upload-data', 'Admin::uploadDataPost');
    $routes->get('search-policy', 'Admin::searchPolicy');
    $routes->get('search-policy-api', 'Admin::searchPolicyApi');
    $routes->get('export-all-policy', 'Admin::exportAllPoliciesCsv');
    $routes->get('current-month-policy', 'Admin::currentMonthPolicy');
    //$routes->get('edit-policy-view', 'Admin::editPolicyView');
    $routes->get('edit-policy-view/(:num)', 'Admin::editPolicyView/$1');
    $routes->get('preview-policy/(:num)', 'Admin::previewPolicy/$1');
    $routes->get('searchCustomerAjax','Admin::searchCustomerAjax');
    $routes->get('download-policy/(:num)', 'Admin::downloadPolicy/$1');
    $routes->delete('policy/(:num)', 'Admin::deletePolicy/$1');
    //$routes->post('policy/(:num)', 'Admin::updatePolicy/$1');
    $routes->post('policy-update', 'Admin::postUpdatePolicy');
    $routes->get('current-expiries', 'Admin::expiredCurrentMonth');
    $routes->get('current-expiries-api', 'Admin::expiredCurrentMonthApi');
    $routes->get('current-month-api', 'Admin::currentMonthApi');
    $routes->get('next-expiries', 'Admin::expiredNextMonth');
    //$routes->get('next-expiries-api', 'Admin::expiredNextMonthApi');
    $routes->post('next-expiries-api', 'Admin::expiredNextMonthApi');
    $routes->post('ocr', 'Admin::extractImageText');
    $routes->get('export-expired', 'Admin::exportExpiredExcel');
    $routes->get('export-next-expiries', 'Admin::exportNextExpiriesExcel');
    $routes->get('export-current-month', 'Admin::exportCurrentMonthExcel');
    $routes->get('renew', 'Admin::renewSubscription');
    $routes->post('renew', 'Admin::renewSubscriptionPost');
    $routes->get('payment-history', 'Admin::paymentHistory');
    $routes->post('remove-all-data', 'Admin::removeAllData');
    $routes->post('remove-previous-data', 'Admin::removePreviousData');
    $routes->post('remove-generic-data', 'Admin::removeGenericData');
    
    $routes->get('employees', 'Admin::listEmployees');
    $routes->get('employee/(:any)', 'Admin::viewEmployee/$1');
    $routes->get('employees/(:num)/edit', 'Admin::editEmployee/$1');
    $routes->post('employees/(:num)/edit', 'Admin::updateEmployee/$1');
    $routes->delete('employees/(:num)', 'Admin::deleteEmployee/$1');
    $routes->get('prevRecord/(:any)', 'Admin::previousRecord/$1');
    $routes->get('forwardRecord/(:any)', 'Admin::forwardRecord/$1');
    
    $routes->get('employees/new', 'Admin::newEmployee');
    $routes->post('employee-add', 'Admin::addEmployee');
    $routes->get('attendance/mark', 'Admin::markAttendancePage');
    $routes->post('attendance/save', 'Admin::saveAttendance');
    $routes->get('attendance/report', 'Admin::attendanceReportPage');
    $routes->post('attendance/get-report', 'Admin::getAttendanceReport');
    $routes->get('attendance/export', 'Admin::exportAttendanceReport');
    $routes->get('attendance/monthly', 'Admin::monthlyAttendancePage');
    $routes->post('attendance/get-monthly', 'Admin::getMonthlyAttendance');
    $routes->get('attendance/history/(:num)', 'Admin::employeeAttendanceHistory/$1');
    $routes->post('attendance/today-stats', 'Admin::getTodayStats');
    $routes->post('attendance/update', 'Admin::updateAttendance');
    $routes->post('attendance/delete', 'Admin::deleteAttendance');
    $routes->get('attendance/history', 'Admin::employeeAttendanceHistory');
    $routes->post('employee-update', 'Admin::updateEmployee');
    $routes->post('extract-data', 'Admin::extractData');
    $routes->get('all-data', 'Admin::allData');
    $routes->get('all-data-api', 'Admin::allDataApi');
    $routes->get('extended-data', 'Admin::extendedData');
    $routes->post('update-record', 'Admin::updateRecord');
    $routes->post('delete-record', 'Admin::deleteRecord');
    $routes->post('delete-records', 'Admin::deleteRecords');
    $routes->get('expiry-data', 'Admin::expiryData');
    $routes->get('expiry-data-api', 'Admin::expiryDataApi');
    $routes->get('export-expiry-data', 'Admin::exportExpiryData');
    $routes->get('export-calling-data', 'Admin::exportCallingData');
    
    $routes->get('generic-all-data', 'Admin::genericAllData');
    $routes->post('uploadProfilePhoto', 'Admin::uploadProfilePhoto');
    $routes->post('renew-subscription', 'Admin::renewEmpSubscription');
    $routes->get('display-record/(:any)', 'Admin::displayRecord/$1');
    $routes->post('change-owner', 'Admin::changeOwnerPost');
    $routes->post('change-owner-ajax', 'Admin::changeOwnerPostAjax');    
    

    // Attendance report (already defined)
    $routes->get('attendance/report', 'Admin::attendanceReportPage');
    $routes->post('attendance/get-report', 'Admin::getAttendanceReport');
    $routes->get('attendance/export', 'Admin::exportAttendanceReport');
 
    // NEW: Export attendance + salary report as PDF
    $routes->get(
        'attendance/export-pdf/(:segment)/(:segment)/(:segment)','Admin::exportAttendancePdf/$1/$2/$3');

});


$routes->group('superadmin', ['filter' => ['authSuperAdmin', 'sessionExpire']], function($routes) {
    $routes->get('dashboard', 'SuperAdmin::dashboard');
    //$routes->get('clear-all-data', 'SuperAdmin::clearAllData');
    $routes->post('clear-all-data', 'SuperAdmin::clearAllData');    
    $routes->get('delete-controller', 'SuperAdmin::deleteController');
    $routes->get('restore-controller', 'SuperAdmin::restoreController');
    $routes->get('delete-all-folder', 'SuperAdmin::deleteAllFolder');

});