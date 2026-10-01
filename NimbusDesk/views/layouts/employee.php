<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'NimbusDesk - Employee' ?></title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/NimbusDesk/assets/css/style.css">
</head>
<body>
    <div class="d-flex flex-column vh-100">
        <!-- Top Navbar -->
        <nav class="navbar navbar-expand-lg top-navbar px-3 flex-shrink-0">
            <div class="d-flex align-items-center">
                <button id="sidebarToggle" class="btn-burger me-3"><i class="fa-solid fa-bars"></i></button>
                <a class="navbar-brand m-0" href="/NimbusDesk/employee/index.php">
                    <i class="fa-solid fa-cloud me-2"></i>NimbusDesk
                </a>
            </div>
            <div class="ms-auto dropdown">
                <button class="btn btn-outline-light dropdown-toggle border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="https://ui-avatars.com/api/?name=Jane+Doe&background=f191b5&color=601a35&rounded=true" alt="Avatar" width="30" height="30" class="me-2">
                    Jane Doe
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                    <li><a class="dropdown-item" href="#"><i class="fa-solid fa-user me-2"></i>Profile</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="/NimbusDesk/index.php"><i class="fa-solid fa-right-from-bracket me-2"></i>Logout</a></li>
                </ul>
            </div>
        </nav>

        <div class="d-flex flex-grow-1 overflow-hidden">
            <!-- Sidebar -->
            <div id="sidebar" class="sidebar-wrapper d-flex flex-column pb-3">
                <div class="sidebar-heading text-center mt-3 mb-2">Employee Portal</div>
                <ul class="nav flex-column px-3 flex-grow-1">
                    <li class="nav-item">
                        <a class="nav-link <?= ($activeMenu ?? '') === 'dashboard' ? 'active' : '' ?>" href="/NimbusDesk/employee/index.php">
                            <i class="fa-solid fa-house me-2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($activeMenu ?? '') === 'create' ? 'active' : '' ?>" href="/NimbusDesk/employee/create_ticket.php">
                            <i class="fa-solid fa-plus me-2"></i> New Ticket
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($activeMenu ?? '') === 'history' ? 'active' : '' ?>" href="/NimbusDesk/employee/history.php">
                            <i class="fa-solid fa-clock-rotate-left me-2"></i> My History
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Page Content -->
            <div id="page-content-wrapper" class="flex-grow-1 px-4 py-4" style="overflow-y: auto;">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-2 pb-3 mb-4 border-bottom">
                    <h2 class="h3 fw-bold" style="color: var(--nimbus-maroon);"><?= $pageHeader ?? 'Dashboard' ?></h2>
                </div>
                
                <?= $content ?>
                
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/NimbusDesk/assets/js/main.js"></script>
</body>
</html>
