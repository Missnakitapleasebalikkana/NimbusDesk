<?php
require_once __DIR__ . '/../src/Core/Database.php';
require_once __DIR__ . '/../src/Core/Auth.php';
require_once __DIR__ . '/../src/Core/View.php';
require_once __DIR__ . '/../src/Models/Ticket.php';

use NimbusDesk\Core\Auth;
use NimbusDesk\Core\View;
use NimbusDesk\Models\Ticket;

Auth::requireRole(['technician', 'admin']);

// Fetch all active/open tickets across the airport
$search = trim($_GET['search'] ?? '');
$urgency = trim($_GET['urgency'] ?? '');
$category = trim($_GET['category'] ?? '');
$terminal = trim($_GET['terminal'] ?? '');

$tickets = Ticket::getAllWithFilters([
    'status'   => 'active',
    'search'   => $search,
    'urgency'  => $urgency,
    'category' => $category,
    'terminal' => $terminal
]);

View::render('technician/all_tickets', 'technician', [
    'title'      => 'Airport Incident Pool - NimbusDesk Tech',
    'pageHeader' => 'Airport-Wide Incident Queue',
    'activeMenu' => 'all',
    'tickets'    => $tickets,
    'search'     => $search,
    'urgency'    => $urgency,
    'category'   => $category,
    'terminal'   => $terminal
]);
