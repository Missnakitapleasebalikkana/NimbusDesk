<?php
$user = currentUser();
$activePage = $activePage ?? 'dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'IT Systems Administration') ?> - NimbusDesk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= url('assets/css/global.css') ?>">
    <link rel="stylesheet" href="<?= url('admin/css/admin.css') ?>">
</head>
<body>

<div class="app-container">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-brand">
            <i class="fa-solid fa-cloud text-warning fs-3"></i>
            <div>
                <h5>NIMBUSDESK</h5>
                <span class="sidebar-badge admin-badge">IT Supervisor</span>
            </div>
        </div>

        <ul class="sidebar-menu">
            <li class="nav-item">
                <a href="<?= url('admin/index.php') ?>" class="nav-link <?= $activePage === 'dashboard' ? 'active' : '' ?>">
                    <i class="fa-solid fa-gauge-high"></i>
                    <span>All IT Incidents</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= url('admin/index.php?page=history') ?>" class="nav-link <?= $activePage === 'history' ? 'active' : '' ?>">
                    <i class="fa-solid fa-book-journal-whills"></i>
                    <span>Incident History Log</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= url('admin/index.php?page=reports') ?>" class="nav-link <?= $activePage === 'reports' ? 'active' : '' ?>">
                    <i class="fa-solid fa-chart-line"></i>
                    <span>System Analytics</span>
                </a>
            </li>
        </ul>

        <div class="sidebar-user">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle bg-danger text-white d-flex align-items-center justify-content-center fw-bold" style="width: 40px; height: 40px;">
                    <?= strtoupper(substr($user['name'], 0, 1)) ?>
                </div>
                <div class="overflow-hidden flex-grow-1">
                    <div class="fw-bold text-truncate text-white small"><?= htmlspecialchars($user['name']) ?></div>
                    <div class="text-white-50 text-truncate" style="font-size: 0.75rem;">System Administrator</div>
                </div>
                <a href="<?= url('auth/logout.php') ?>" class="text-danger" title="Logout">
                    <i class="fa-solid fa-right-from-bracket"></i>
                </a>
            </div>
        </div>
    </aside>

    <!-- Main Wrapper -->
    <div class="main-wrapper">
        <header class="topbar">
            <div class="d-flex align-items-center gap-3">
                <button id="sidebarToggle" class="btn btn-sm btn-outline-secondary border-0">
                    <i class="fa-solid fa-bars fs-5"></i>
                </button>
                <div class="d-none d-md-block">
                    <span class="text-muted small">Airline IT Incident Command & Dispatch Control</span>
                </div>
            </div>

            <div class="d-flex align-items-center gap-3">
                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1 small">
                    <i class="fa-solid fa-circle-dot me-1"></i> Core Systems Active
                </span>
                <div class="dropdown">
                    <button class="btn btn-sm btn-light border dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="fa-solid fa-shield-halved me-1 text-danger"></i> <?= htmlspecialchars($user['name']) ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                        <li><span class="dropdown-item-text text-muted small">Admin ID: <?= htmlspecialchars($user['badge_number']) ?></span></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="<?= url('auth/logout.php') ?>"><i class="fa-solid fa-arrow-right-from-bracket me-2"></i>Logout</a></li>
                    </ul>
                </div>
            </div>
        </header>

        <main class="content-body">
