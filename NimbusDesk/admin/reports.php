<?php
require_once __DIR__ . '/../src/Core/Database.php';
require_once __DIR__ . '/../src/Core/Auth.php';
require_once __DIR__ . '/../src/Core/View.php';
require_once __DIR__ . '/../src/Models/Ticket.php';

use NimbusDesk\Core\Auth;
use NimbusDesk\Core\View;
use NimbusDesk\Models\Ticket;

// Administrators and Technicians can access operational reports
Auth::requireRole(['admin', 'technician']);
$currentUser = Auth::user();

// Retrieve analytics data to fulfill Objective #3 of NimbusDesk
$stats = Ticket::getStats();
$categories = Ticket::getCategoryBreakdown();
$urgencies = Ticket::getUrgencyBreakdown();
$terminals = Ticket::getTerminalBreakdown();
$statuses = Ticket::getStatusBreakdown();
$mttr = Ticket::getMeanTimeToResolution();
$recurring = Ticket::getRecurringFailures();

// Calculate resolution rate
$total = $stats['total'] ?: 1;
$resolutionRate = round(($stats['resolved'] / $total) * 100, 1);

$layout = ($currentUser['role'] === 'technician') ? 'technician' : 'admin';

View::render('admin/reports', $layout, [
    'title'          => 'Incident Analytics & Reports - NimbusDesk',
    'pageHeader'     => 'Airport Incident Analytics & Frequency Reports',
    'activeMenu'     => 'reports',
    'stats'          => $stats,
    'categories'     => $categories,
    'urgencies'      => $urgencies,
    'terminals'      => $terminals,
    'statuses'       => $statuses,
    'mttr'           => $mttr,
    'resolutionRate' => $resolutionRate,
    'recurring'      => $recurring
]);
