<?php
require_once __DIR__ . '/../src/Core/View.php';
use NimbusDesk\Core\View;

View::render('employee/history', 'employee', [
    'title' => 'My History - Employee',
    'pageHeader' => 'My Resolved Tickets',
    'activeMenu' => 'history'
]);
