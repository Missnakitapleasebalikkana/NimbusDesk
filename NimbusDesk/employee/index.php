<?php
require_once __DIR__ . '/../src/Core/View.php';
use NimbusDesk\Core\View;

View::render('employee/dashboard', 'employee', [
    'title' => 'Dashboard - Employee',
    'pageHeader' => 'Employee Dashboard',
    'activeMenu' => 'dashboard'
]);
