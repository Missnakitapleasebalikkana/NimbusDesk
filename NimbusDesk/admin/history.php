<?php
require_once __DIR__ . '/../src/Core/View.php';
use NimbusDesk\Core\View;

View::render('admin/history', 'admin', [
    'title' => 'Incident History - Admin',
    'pageHeader' => 'Incident History Log',
    'activeMenu' => 'history'
]);
