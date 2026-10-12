<?php
require_once __DIR__ . '/../src/Core/Database.php';
require_once __DIR__ . '/../src/Core/Auth.php';
require_once __DIR__ . '/../src/Core/View.php';
require_once __DIR__ . '/../src/Models/User.php';
require_once __DIR__ . '/../src/Models/Ticket.php';
require_once __DIR__ . '/../src/Models/TicketNote.php';

use NimbusDesk\Core\Auth;
use NimbusDesk\Core\View;
use NimbusDesk\Models\User;
use NimbusDesk\Models\Ticket;
use NimbusDesk\Models\TicketNote;

Auth::requireRole(['technician', 'admin']);
$currentUser = Auth::user();

$ticketId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$ticket = Ticket::findById($ticketId);

if (!$ticket) {
    Auth::setFlash('danger', 'Ticket not found.');
    $fallbackRedirect = ($currentUser['role'] === 'technician') ? '/NimbusDesk/technician/index.php' : '/NimbusDesk/admin/index.php';
    header("Location: {$fallbackRedirect}");
    exit;
}

// Available technicians for assignment
$technicians = User::getTechnicians();

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Auth::verifyCsrf($_POST['csrf_token'] ?? '')) {
        Auth::setFlash('danger', 'Security validation failed. Please refresh and try again.');
        header('Location: /NimbusDesk/admin/ticket.php?id=' . $ticket['id']);
        exit;
    }

    $action = $_POST['action'] ?? '';

    if ($action === 'update_assignment_status') {
        $newStatus = trim($_POST['status'] ?? $ticket['status']);
        $newAssignee = !empty($_POST['assigned_to']) ? (int)$_POST['assigned_to'] : null;

        $statusChanged = ($newStatus !== $ticket['status']);
        $assigneeChanged = ($newAssignee !== (int)$ticket['assigned_to']);

        if ($statusChanged) {
            Ticket::updateStatus($ticket['id'], $newStatus);
            TicketNote::add(
                $ticket['id'],
                $currentUser['id'],
                "Status updated to {$newStatus}.",
                "Status: {$newStatus}"
            );
        }

        if ($assigneeChanged) {
            Ticket::assignTechnician($ticket['id'], $newAssignee);
            $techName = 'Unassigned';
            if ($newAssignee) {
                foreach ($technicians as $t) {
                    if ((int)$t['id'] === $newAssignee) {
                        $techName = $t['name'];
                        break;
                    }
                }
            }
            TicketNote::add(
                $ticket['id'],
                $currentUser['id'],
                "Assigned technician changed to: {$techName}.",
                "Assignment Updated"
            );
        }

        Auth::setFlash('success', 'Ticket status and technician assignment updated.');
        header('Location: /NimbusDesk/admin/ticket.php?id=' . $ticket['id']);
        exit;
    }

    if ($action === 'add_resolution_note') {
        $noteText = trim($_POST['note'] ?? '');
        $isInternal = isset($_POST['is_internal']) && $_POST['is_internal'] === '1';
        $markStatus = trim($_POST['status_update'] ?? '');

        if (!empty($noteText)) {
            $statusChangeDesc = null;
            if (!empty($markStatus) && $markStatus !== $ticket['status']) {
                Ticket::updateStatus($ticket['id'], $markStatus);
                $statusChangeDesc = "Status changed to {$markStatus}";
            }

            // Handle optional attachment on note
            $attachmentPath = null;
            if (isset($_FILES['note_attachment']) && $_FILES['note_attachment']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['note_attachment'];
                $maxBytes = 8 * 1024 * 1024;
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'txt', 'log', 'docx'];

                if ($file['size'] <= $maxBytes && in_array($ext, $allowedExts, true)) {
                    $uploadDir = __DIR__ . '/../uploads/tickets/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }
                    $fileName = 'tech_note_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                    if (move_uploaded_file($file['tmp_name'], $uploadDir . $fileName)) {
                        $attachmentPath = 'uploads/tickets/' . $fileName;
                    }
                }
            }

            TicketNote::add(
                $ticket['id'],
                $currentUser['id'],
                $noteText,
                $statusChangeDesc,
                $isInternal,
                $attachmentPath
            );

            Auth::setFlash('success', 'Resolution note added to incident record.');
            header('Location: /NimbusDesk/admin/ticket.php?id=' . $ticket['id']);
            exit;
        } else {
            Auth::setFlash('danger', 'Please enter a note before submitting.');
        }
    }
}

// Reload notes (technicians/admins can see both public & internal notes)
$notes = TicketNote::getForTicket($ticket['id'], true);

// Select appropriate layout for the user
$layout = ($currentUser['role'] === 'technician') ? 'technician' : 'admin';

View::render('admin/ticket_detail', $layout, [
    'title'       => "Manage {$ticket['ticket_code']} - NimbusDesk IT",
    'pageHeader'  => "Manage Incident {$ticket['ticket_code']}",
    'activeMenu'  => 'dashboard',
    'ticket'      => $ticket,
    'technicians' => $technicians,
    'notes'       => $notes
]);
