<?php
use NimbusDesk\Core\Auth;
Auth::init();
$currentUser = Auth::user() ?? ['name' => 'IT Technician', 'role' => 'technician', 'department' => 'Aviation Support'];
$flashSuccess = Auth::getFlash('success');
$flashDanger = Auth::getFlash('danger');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'NimbusDesk - Technician Workstation' ?></title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="/NimbusDesk/assets/images/icon.png">
    <!-- Bootstrap 5 CSS -->
    <link href="/NimbusDesk/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="/NimbusDesk/assets/vendor/fontawesome/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/NimbusDesk/assets/css/style.css">
</head>
<body>
    <div class="d-flex flex-column vh-100">
        <!-- Top Navbar -->
        <nav class="navbar navbar-expand-lg top-navbar px-3 flex-shrink-0">
            <div class="d-flex align-items-center">
                <button id="sidebarToggle" class="btn-burger me-3" title="Toggle Sidebar"><i class="fa-solid fa-bars"></i></button>
                <a class="navbar-brand d-flex align-items-center m-0" href="/NimbusDesk/technician/index.php">
                    <img src="/NimbusDesk/assets/images/icon.png" alt="NimbusDesk Logo" width="30" height="30" class="me-2 rounded-circle border border-white">
                    NimbusDesk Tech
                </a>
                <span class="badge bg-info text-dark ms-2 text-uppercase">Technician Workbench</span>
            </div>
            <div class="ms-auto dropdown">
                <button class="btn btn-outline-light dropdown-toggle border-0 d-flex align-items-center" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="<?= \NimbusDesk\Core\View::avatarUrl($currentUser['name'], 'f191b5', '601a35') ?>" alt="Avatar" width="32" height="32" class="me-2 rounded-circle">
                    <span class="d-none d-md-inline"><?= htmlspecialchars($currentUser['name']) ?></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                    <li class="px-3 py-1">
                        <small class="text-muted d-block">Signed in as</small>
                        <strong class="d-block text-truncate" style="max-width: 180px;"><?= htmlspecialchars($currentUser['email'] ?? '') ?></strong>
                        <span class="badge bg-light text-dark text-capitalize mt-1"><?= htmlspecialchars($currentUser['department'] ?? 'IT Support') ?></span>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item" href="/NimbusDesk/profile.php">
                            <i class="fa-solid fa-id-badge me-2 text-primary"></i>My Profile
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="/NimbusDesk/logout.php"><i class="fa-solid fa-right-from-bracket me-2"></i>Sign Out</a></li>
                </ul>
            </div>
        </nav>

        <div class="d-flex flex-grow-1 overflow-hidden">
            <!-- Sidebar -->
            <div id="sidebar" class="sidebar-wrapper d-flex flex-column pb-3">
                <div class="sidebar-heading text-center mt-3 mb-2">Technical Workbench</div>
                <ul class="nav flex-column px-3 flex-grow-1">
                    <li class="nav-item">
                        <a class="nav-link <?= ($activeMenu ?? '') === 'dashboard' ? 'active' : '' ?>" href="/NimbusDesk/technician/index.php">
                            <i class="fa-solid fa-screwdriver-wrench me-2"></i> My Assigned Queue
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($activeMenu ?? '') === 'all' ? 'active' : '' ?>" href="/NimbusDesk/technician/all_tickets.php">
                            <i class="fa-solid fa-list-check me-2"></i> Airport Ticket Pool
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($activeMenu ?? '') === 'history' ? 'active' : '' ?>" href="/NimbusDesk/technician/history.php">
                            <i class="fa-solid fa-clock-rotate-left me-2"></i> My Resolved Log
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($activeMenu ?? '') === 'reports' ? 'active' : '' ?>" href="/NimbusDesk/admin/reports.php">
                            <i class="fa-solid fa-chart-pie me-2"></i> Incident Analytics
                        </a>
                    </li>
                </ul>
                <div class="px-3 mt-auto">
                    <a href="/NimbusDesk/logout.php" class="btn btn-sm btn-outline-light w-100">
                        <i class="fa-solid fa-right-from-bracket me-1"></i> Sign Out
                    </a>
                </div>
            </div>

            <!-- Page Content -->
            <div id="page-content-wrapper" class="flex-grow-1 px-4 py-4" style="overflow-y: auto;">
                <?php if (!empty($flashSuccess)): ?>
                    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                        <i class="fa-solid fa-circle-check me-2"></i><?= htmlspecialchars($flashSuccess) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (!empty($flashDanger)): ?>
                    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                        <i class="fa-solid fa-triangle-exclamation me-2"></i><?= htmlspecialchars($flashDanger) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-2 pb-3 mb-4 border-bottom">
                    <h2 class="h3 fw-bold" style="color: var(--nimbus-maroon);"><?= $pageHeader ?? 'Technician Workspace' ?></h2>
                </div>
                
                <?= $content ?>
                
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="/NimbusDesk/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="/NimbusDesk/assets/js/main.js"></script>
</body>
</html>
