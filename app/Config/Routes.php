<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
// Employee Authentication Routes (no filter)
$routes->get('/employee/login', 'Auth::loginForm');
$routes->post('/employee/login', 'Auth::login');
$routes->get('/employee/logout', 'Auth::logout');

// Protected Employee Routes
$routes->group('employee', ['filter' => 'authEmployee'], function($routes) {
    $routes->get('dashboard', 'Employee::dashboard');
    $routes->get('dashboard/(:any)', 'Employee::dashboard/$1');
    $routes->post('save', 'Employee::save');
    $routes->get('all-data', 'Employee::allData');
    $routes->get('nextRecord/(:any)', 'Employee::nextRecord/$1');
    $routes->get('starRecord/(:any)/(:any)', 'Employee::starRecord/$1/$2');
    $routes->get('allStarRecord', 'Employee::allStarRecord');
});

// Protected Admin Routes
$routes->group('admin', ['filter' => 'authAdmin'], function($routes) {
    $routes->get('/', 'Admin::index');
    $routes->get('upload', 'Admin::uploadPolicy');
    $routes->post('upload', 'Admin::uploadPolicyPost');
    $routes->get('data-loader', 'Admin::dataLoader');
    $routes->post('upload-data', 'Admin::uploadDataPost');
    $routes->get('search-policy', 'Admin::searchPolicy');
    $routes->get('search-policy-api', 'Admin::searchPolicyApi');
    $routes->get('download-policy/(:num)', 'Admin::downloadPolicy/$1');
    $routes->delete('policy/(:num)', 'Admin::deletePolicy/$1');
    $routes->post('policy/(:num)', 'Admin::updatePolicy/$1');
    $routes->get('current-expiries', 'Admin::expiredCurrentMonth');
    $routes->get('current-expiries-api', 'Admin::expiredCurrentMonthApi');
    $routes->get('next-expiries', 'Admin::expiredNextMonth');
    $routes->get('next-expiries-api', 'Admin::expiredNextMonthApi');
    $routes->post('ocr', 'Admin::extractImageText');
    $routes->get('export-expired', 'Admin::exportExpiredExcel');
    $routes->get('export-next-expiries', 'Admin::exportNextExpiriesExcel');
    $routes->get('renew', 'Admin::renewSubscription');
    $routes->post('renew', 'Admin::renewSubscriptionPost');
    $routes->get('payment-history', 'Admin::paymentHistory');
    $routes->post('remove-all-data', 'Admin::removeAllData');
    $routes->get('employees', 'Admin::listEmployees');
    $routes->get('employee/(:any)', 'Admin::viewEmployee/$1');
    $routes->get('employees/(:num)/edit', 'Admin::editEmployee/$1');
    $routes->post('employees/(:num)/edit', 'Admin::updateEmployee/$1');
    $routes->delete('employees/(:num)', 'Admin::deleteEmployee/$1');
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
});
