<?php
require_once __DIR__ . '/../src/Core/View.php';
use NimbusDesk\Core\View;

View::render('employee/create_ticket', 'employee', [
    'title' => 'New Ticket - Employee',
    'pageHeader' => 'Submit New Ticket',
    'activeMenu' => 'create'
]);
