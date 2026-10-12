<?php
require_once __DIR__ . '/../src/Core/Database.php';
require_once __DIR__ . '/../src/Core/Auth.php';
require_once __DIR__ . '/../src/Core/View.php';
require_once __DIR__ . '/../src/Models/Ticket.php';

use NimbusDesk\Core\Auth;
use NimbusDesk\Core\View;
use NimbusDesk\Models\Ticket;

Auth::requireRole('admin');

// Fetch global IT help desk statistics
$stats = Ticket::getStats();

// Filters from GET request
$status = $_GET['status'] ?? 'active';
$urgency = $_GET['urgency'] ?? '';
$category = $_GET['category'] ?? '';
$terminal = trim($_GET['terminal'] ?? '');
$search = trim($_GET['search'] ?? '');

$filters = [
    'status'   => $status,
    'urgency'  => $urgency,
    'category' => $category,
    'terminal' => $terminal,
    'search'   => $search
];

// Retrieve filtered tickets
$tickets = Ticket::getAllWithFilters($filters);

View::render('admin/dashboard', 'admin', [
    'title'      => 'IT Operations Dashboard - NimbusDesk',
    'pageHeader' => 'IT Help Desk Incident Queue',
    'activeMenu' => 'dashboard',
    'stats'      => $stats,
    'tickets'    => $tickets,
    'filters'    => $filters
]);
