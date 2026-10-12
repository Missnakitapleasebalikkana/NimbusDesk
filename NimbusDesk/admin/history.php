<?php
require_once __DIR__ . '/../src/Core/Database.php';
require_once __DIR__ . '/../src/Core/Auth.php';
require_once __DIR__ . '/../src/Core/View.php';
require_once __DIR__ . '/../src/Models/Ticket.php';

use NimbusDesk\Core\Auth;
use NimbusDesk\Core\View;
use NimbusDesk\Models\Ticket;

Auth::requireRole('admin');

$search = trim($_GET['search'] ?? '');
$category = trim($_GET['category'] ?? '');
$terminal = trim($_GET['terminal'] ?? '');
$status = trim($_GET['status'] ?? 'Resolved');

$filters = [
    'search'   => $search,
    'category' => $category,
    'terminal' => $terminal,
    'status'   => $status
];

$historyTickets = Ticket::getAllWithFilters($filters);

View::render('admin/history', 'admin', [
    'title'          => 'Incident History Archive - NimbusDesk IT',
    'pageHeader'     => 'Airport Incident History & Audit Log',
    'activeMenu'     => 'history',
    'historyTickets' => $historyTickets,
    'filters'        => $filters
]);
