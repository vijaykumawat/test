<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');

$routes->get('/admin', 'Admin::index');

// Upload routes
$routes->get('/admin/upload', 'Admin::uploadPolicy');
$routes->post('/admin/upload', 'Admin::uploadPolicyPost');
//$routes->get('/admin/data-loader', 'Admin::dataLoader');
$routes->get('/admin/data-loader', 'Admin::dataLoader');
$routes->post('/admin/upload-data', 'Admin::uploadDataPost');
// Search routes
$routes->get('/admin/search-policy', 'Admin::searchPolicy');
$routes->get('/admin/search-policy-api', 'Admin::searchPolicyApi');
$routes->get('/admin/download-policy/(:num)', 'Admin::downloadPolicy/$1');
$routes->delete('/admin/policy/(:num)', 'Admin::deletePolicy/$1');
$routes->post('/admin/policy/(:num)', 'Admin::updatePolicy/$1');

// Expired policies routes
$routes->get('/admin/current-expiries', 'Admin::expiredCurrentMonth');
$routes->get('/admin/current-expiries-api', 'Admin::expiredCurrentMonthApi');
$routes->get('/admin/next-expiries', 'Admin::expiredNextMonth');
$routes->get('/admin/next-expiries-api', 'Admin::expiredNextMonthApi');

// Image OCR route
$routes->post('/admin/ocr', 'Admin::extractImageText');

// Excel export routes
$routes->get('/admin/export-expired', 'Admin::exportExpiredExcel');
$routes->get('/admin/export-next-expiries', 'Admin::exportNextExpiriesExcel');
// Renew subscription
$routes->get('/admin/renew', 'Admin::renewSubscription');
$routes->post('/admin/renew', 'Admin::renewSubscriptionPost');
$routes->get('/admin/payment-history', 'Admin::paymentHistory');

// remove data loader route if not needed
$routes->post('/admin/remove-all-data', 'Admin::removeAllData');

//Employee routes
$routes->get('/admin/employees', 'Admin::listEmployees');
$routes->get('/admin/employee/(:any)', 'Admin::viewEmployee/$1');
$routes->get('/admin/employees/(:num)/edit', 'Admin::editEmployee/$1');
$routes->post('/admin/employees/(:num)/edit', 'Admin::updateEmployee/$1');
$routes->delete('/admin/employees/(:num)', 'Admin::deleteEmployee/$1');
$routes->get('/admin/employees/new', 'Admin::newEmployee');
$routes->post('/admin/employee-add', 'Admin::addEmployee');

// Attendance routes
$routes->get('/admin/attendance/mark', 'Admin::markAttendancePage');
$routes->post('/admin/attendance/save', 'Admin::saveAttendance');
$routes->get('/admin/attendance/report', 'Admin::attendanceReportPage');
$routes->post('/admin/attendance/get-report', 'Admin::getAttendanceReport');
$routes->get('/admin/attendance/export', 'Admin::exportAttendanceReport');
$routes->get('/admin/attendance/monthly', 'Admin::monthlyAttendancePage');
$routes->post('/admin/attendance/get-monthly', 'Admin::getMonthlyAttendance');
$routes->get('/admin/attendance/history/(:num)', 'Admin::employeeAttendanceHistory/$1');
$routes->post('/admin/attendance/today-stats', 'Admin::getTodayStats');
$routes->post('/admin/attendance/update', 'Admin::updateAttendance');
$routes->post('/admin/attendance/delete', 'Admin::deleteAttendance');
$routes->get('/admin/attendance/history', 'Admin::employeeAttendanceHistory');
$routes->post('/admin/employee-update', 'Admin::updateEmployee');
$routes->post('/admin/extract-data', 'Admin::extractData');


// Employee Authentication Routes
$routes->get('/employee/login', 'Auth::loginForm');       // Show login form
$routes->post('/employee/login', 'Auth::login');         // Handle login POST
$routes->get('/employee/logout', 'Auth::logout'); 

$routes->get('/employee/dashboard', 'Employee::dashboard'); // Employee dashboard route
