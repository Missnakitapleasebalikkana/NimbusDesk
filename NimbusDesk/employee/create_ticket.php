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

$errors = [];
$old = [
    'terminal'    => 'Terminal 1',
    'location'    => '',
    'subject'     => '',
    'category'    => '',
    'urgency'     => 'Medium',
    'description' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Auth::verifyCsrf($_POST['csrf_token'] ?? '')) {
        $errors['csrf'] = 'Security session expired. Please refresh the page and try again.';
    }

    $terminal = trim($_POST['terminal'] ?? 'Terminal 1');
    $location = trim($_POST['location'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $urgency = trim($_POST['urgency'] ?? 'Medium');
    $description = trim($_POST['description'] ?? '');

    $old['terminal'] = $terminal;
    $old['location'] = $location;
    $old['subject'] = $subject;
    $old['category'] = $category;
    $old['urgency'] = $urgency;
    $old['description'] = $description;

    $allowedTerminals = ['Terminal 1', 'Terminal 2', 'Terminal 3', 'Domestic Cargo', 'General Aviation / Apron'];
    $allowedCategories = ['Hardware', 'Software', 'Network', 'Facilities', 'Other'];
    $allowedUrgencies = ['Low', 'Medium', 'High', 'Critical'];

    if (!in_array($terminal, $allowedTerminals, true)) {
        $errors['terminal'] = 'Please select a valid airport terminal or zone.';
    }

    if (empty($location) || mb_strlen($location) < 2) {
        $errors['location'] = 'Please specify the exact location (e.g., Gate 4, Counter 12, Carousel 3).';
    }

    if (empty($subject) || mb_strlen($subject) < 3) {
        $errors['subject'] = 'Please enter a clear subject (at least 3 characters).';
    }

    if (!in_array($category, $allowedCategories, true)) {
        $errors['category'] = 'Please select a valid category.';
    }

    if (!in_array($urgency, $allowedUrgencies, true)) {
        $errors['urgency'] = 'Please select a valid urgency level.';
    }

    if (empty($description) || mb_strlen($description) < 5) {
        $errors['description'] = 'Please describe the incident in detail (at least 5 characters).';
    }

    // Handle file upload
    $attachmentPath = null;
    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] !== UPLOAD_ERR_NO_FILE) {
        $file = $_FILES['attachment'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors['attachment'] = 'File upload failed with error code: ' . $file['error'];
        } else {
            $maxBytes = 8 * 1024 * 1024; // 8MB
            if ($file['size'] > $maxBytes) {
                $errors['attachment'] = 'Attachment is too large. Maximum size is 8MB.';
            } else {
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'txt', 'log', 'doc', 'docx'];

                if (!in_array($ext, $allowedExts, true)) {
                    $errors['attachment'] = 'Invalid file type. Allowed formats: PNG, JPG, PDF, TXT, LOG, DOCX.';
                } else {
                    $uploadDir = __DIR__ . '/../uploads/tickets/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }

                    $fileName = 'tkt_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                    $targetPath = $uploadDir . $fileName;

                    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                        $attachmentPath = 'uploads/tickets/' . $fileName;
                    } else {
                        $errors['attachment'] = 'Could not save the uploaded file. Check folder permissions.';
                    }
                }
            }
        }
    }

    if (empty($errors)) {
        $ticketId = Ticket::create([
            'user_id'     => $currentUser['id'],
            'terminal'    => $terminal,
            'location'    => $location,
            'subject'     => $subject,
            'category'    => $category,
            'urgency'     => $urgency,
            'description' => $description,
            'attachment'  => $attachmentPath
        ]);

        $ticket = Ticket::findById($ticketId);
        $ticketCode = $ticket['ticket_code'] ?? "TKT-{$ticketId}";

        // Add initial timeline event
        TicketNote::add(
            $ticketId,
            $currentUser['id'],
            'Incident ticket reported by ' . $currentUser['name'] . ' (' . ($currentUser['department'] ?? 'Aviation Staff') . ') at ' . $terminal . ' - ' . $location . '.',
            'Ticket Opened'
        );

        Auth::setFlash('success', "Ticket {$ticketCode} was created successfully! IT dispatch has been notified.");
        header('Location: /NimbusDesk/employee/view_ticket.php?id=' . $ticketId);
        exit;
    }
}

View::render('employee/create_ticket', 'employee', [
    'title'      => 'Submit Ticket - NimbusDesk',
    'pageHeader' => 'Report an IT Incident',
    'activeMenu' => 'create',
    'errors'     => $errors,
    'old'        => $old
]);
