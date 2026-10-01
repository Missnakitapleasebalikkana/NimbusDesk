<?php
require_once __DIR__ . '/../src/Core/View.php';
use NimbusDesk\Core\View;

View::render('admin/dashboard', 'admin', [
    'title' => 'IT Dashboard - Admin',
    'pageHeader' => 'Active Support Tickets',
    'activeMenu' => 'dashboard'
]);
