<?php
require_once __DIR__ . '/../src/Core/View.php';
use NimbusDesk\Core\View;

$ticketId = $_GET['id'] ?? '10024';

View::render('admin/ticket_detail', 'admin', [
    'title' => 'Manage Ticket #' . htmlspecialchars($ticketId),
    'pageHeader' => 'Manage Ticket',
    'activeMenu' => 'dashboard' // keep dashboard active since it's a sub-page
]);
