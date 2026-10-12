<?php
require_once __DIR__ . '/../src/Core/Database.php';
require_once __DIR__ . '/../src/Core/Auth.php';
require_once __DIR__ . '/../src/Models/Ticket.php';

use NimbusDesk\Core\Auth;
use NimbusDesk\Models\Ticket;

Auth::requireRole(['technician', 'admin']);

$status = $_GET['status'] ?? '';
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

$tickets = Ticket::getAllWithFilters($filters);

$filename = 'nimbusdesk_incident_report_' . date('Ymd_His') . '.csv';

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Pragma: no-cache');
header('Expires: 0');

$output = fopen('php://output', 'w');

// UTF-8 BOM for Excel compatibility
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// CSV Header row
fputcsv($output, [
    'Ticket Code',
    'Terminal',
    'Location / Station',
    'Subject',
    'Requester Name',
    'Department',
    'Category',
    'Urgency',
    'Status',
    'Assigned Technician',
    'Reported Date',
    'Resolved Date'
]);

// Write data rows
foreach ($tickets as $t) {
    fputcsv($output, [
        $t['ticket_code'],
        $t['terminal'] ?? 'Terminal 1',
        $t['location'] ?? 'N/A',
        $t['subject'],
        $t['requester_name'],
        $t['requester_dept'] ?? 'N/A',
        $t['category'],
        $t['urgency'],
        $t['status'],
        $t['technician_name'] ?? 'Unassigned',
        date('Y-m-d H:i:s', strtotime($t['created_at'])),
        !empty($t['resolved_at']) ? date('Y-m-d H:i:s', strtotime($t['resolved_at'])) : 'N/A'
    ]);
}

fclose($output);
exit;
