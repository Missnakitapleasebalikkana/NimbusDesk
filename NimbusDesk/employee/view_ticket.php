<?php
require_once __DIR__ . '/../src/Core/Database.php';
require_once __DIR__ . '/../src/Core/Auth.php';
require_once __DIR__ . '/../src/Core/View.php';
require_once __DIR__ . '/../src/Models/Ticket.php';
require_once __DIR__ . '/../src/Models/TicketNote.php';

use NimbusDesk\Core\Auth;
use NimbusDesk\Core\View;
use NimbusDesk\Models\Ticket;
use NimbusDesk\Models\TicketNote;

Auth::requireRole(['employee', 'admin']);
$currentUser = Auth::user();

$ticketId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$ticket = Ticket::findById($ticketId);

if (!$ticket) {
    Auth::setFlash('danger', 'Ticket not found.');
    header('Location: /NimbusDesk/employee/index.php');
    exit;
}

// Ensure employee can only view their own tickets (admins can view any)
if ($currentUser['role'] === 'employee' && (int)$ticket['user_id'] !== (int)$currentUser['id']) {
    Auth::setFlash('danger', 'You do not have permission to view that ticket.');
    header('Location: /NimbusDesk/employee/index.php');
    exit;
}

// Handle employee follow-up message / comment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reply_note'])) {
    if (!Auth::verifyCsrf($_POST['csrf_token'] ?? '')) {
        Auth::setFlash('danger', 'Security validation failed. Please try submitting again.');
        header('Location: /NimbusDesk/employee/view_ticket.php?id=' . $ticket['id']);
        exit;
    }

    $noteText = trim($_POST['reply_note']);
    if (!empty($noteText)) {
        // Handle optional attachment on reply
        $attachmentPath = null;
        if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['attachment'];
            $maxBytes = 8 * 1024 * 1024;
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'txt', 'log', 'docx'];

            if ($file['size'] <= $maxBytes && in_array($ext, $allowedExts, true)) {
                $uploadDir = __DIR__ . '/../uploads/tickets/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $fileName = 'reply_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                if (move_uploaded_file($file['tmp_name'], $uploadDir . $fileName)) {
                    $attachmentPath = 'uploads/tickets/' . $fileName;
                }
            }
        }

        TicketNote::add(
            $ticket['id'],
            $currentUser['id'],
            $noteText,
            null,
            false,
            $attachmentPath
        );
        Auth::setFlash('success', 'Your update was posted to the ticket timeline.');
        header('Location: /NimbusDesk/employee/view_ticket.php?id=' . $ticket['id']);
        exit;
    }
}

// Fetch notes with $includeInternal = false so employee never sees confidential IT notes
$notes = TicketNote::getForTicket($ticket['id'], false);

View::render('employee/view_ticket', 'employee', [
    'title'      => "{$ticket['ticket_code']} - NimbusDesk",
    'pageHeader' => "Ticket {$ticket['ticket_code']}",
    'activeMenu' => 'dashboard',
    'ticket'     => $ticket,
    'notes'      => $notes
]);
