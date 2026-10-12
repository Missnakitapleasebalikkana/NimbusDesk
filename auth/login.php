<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';

$error = '';
$info = '';

// Handle quick-switch or login submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_GET['quick_role'])) {
    $email = trim($_POST['email'] ?? '');
    $role = trim($_POST['role'] ?? ($_GET['quick_role'] ?? ''));

    try {
        $pdo = Database::getConnection();

        if (!empty($role) && empty($email)) {
            // Quick role selector login (Alice = employee, Dave = technician, Sarah = admin)
            $map = [
                'employee' => 'alice@airline.com',
                'technician' => 'dave@airline.com',
                'admin' => 'sarah@airline.com',
            ];
            $email = $map[$role] ?? 'alice@airline.com';
        }

        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            $_SESSION['user'] = [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'role' => $user['role'],
                'department' => $user['department'],
                'badge_number' => $user['badge_number']
            ];

            // Redirect to appropriate role folder
            header('Location: ' . url($user['role'] . '/index.php'));
            exit;
        } else {
            $error = 'User account not found for ' . htmlspecialchars($email);
        }
    } catch (Exception $e) {
        $error = 'Database error: ' . $e->getMessage();
    }
}

// If already logged in, redirect to their role home
if (isAuthenticated()) {
    header('Location: ' . url(currentUser()['role'] . '/index.php'));
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NimbusDesk - Aviation IT Support Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= url('assets/css/global.css') ?>">
    <style>
        body {
            background: linear-gradient(135deg, #1e0914 0%, #3a0d1f 50%, #601a35 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            padding: 20px;
        }
        .login-card {
            background: rgba(255, 255, 255, 0.98);
            border-radius: 16px;
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.4);
            max-width: 480px;
            width: 100%;
            border: 1px solid rgba(241, 145, 181, 0.3);
            overflow: hidden;
        }
        .login-header {
            background: #601a35;
            color: #fff;
            padding: 30px 25px;
            text-align: center;
        }
        .brand-badge {
            background: rgba(241, 145, 181, 0.2);
            color: #f191b5;
            font-size: 0.8rem;
            font-weight: 700;
            padding: 4px 12px;
            border-radius: 20px;
            letter-spacing: 1px;
            text-transform: uppercase;
            display: inline-block;
            margin-bottom: 12px;
        }
        .role-pill {
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 10px 14px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            text-decoration: none;
            color: #334155;
            transition: all 0.2s ease;
            background: #f8fafc;
        }
        .role-pill:hover {
            border-color: #f191b5;
            background: #fdf2f7;
            transform: translateX(4px);
            color: #601a35;
        }
        .role-icon {
            width: 38px;
            height: 38px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            font-size: 1.1rem;
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="login-header">
        <div class="brand-badge"><i class="fa-solid fa-plane-departure me-1"></i> Aviation Incident Center</div>
        <div class="d-flex align-items-center justify-content-center gap-2 mb-2">
            <i class="fa-solid fa-cloud" style="font-size: 2.2rem; color: #f191b5;"></i>
            <h2 class="fw-bold mb-0" style="letter-spacing: 1.5px; color: #fff;">NIMBUSDESK</h2>
        </div>
        <p class="mb-0 text-white-50 small">Centralized IT Incident Management & Help Desk</p>
    </div>

    <div class="p-4 p-md-5">
        <?php if ($error): ?>
            <div class="alert alert-danger py-2 small mb-3">
                <i class="fa-solid fa-triangle-exclamation me-1"></i> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form action="<?= url('auth/login.php') ?>" method="POST" class="mb-4">
            <div class="mb-3">
                <label for="email" class="form-label fw-bold text-dark small">Staff Email</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="fa-solid fa-envelope text-muted"></i></span>
                    <input type="email" name="email" id="email" class="form-control" placeholder="name@airline.com" required value="alice@airline.com">
                </div>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label fw-bold text-dark small">Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="fa-solid fa-lock text-muted"></i></span>
                    <input type="password" name="password" id="password" class="form-control" value="password123" required>
                </div>
                <div class="form-text text-muted small">Default demo password: <code>password123</code></div>
            </div>

            <button type="submit" class="btn w-100 py-2 fw-bold text-white" style="background-color: #601a35;">
                <i class="fa-solid fa-right-to-bracket me-2"></i> Sign In to Portal
            </button>
        </form>

        <div class="position-relative text-center my-3">
            <hr class="text-muted">
            <span class="position-absolute top-50 start-50 translate-middle bg-white px-3 text-muted small fw-bold">OR QUICK DEMO ACCESS</span>
        </div>

        <div class="role-selector mt-3">
            <a href="<?= url('auth/login.php?quick_role=employee') ?>" class="role-pill">
                <div class="role-icon bg-info bg-opacity-10 text-info">
                    <i class="fa-solid fa-id-badge"></i>
                </div>
                <div>
                    <div class="fw-bold">Aviation Employee Portal</div>
                    <div class="small text-muted">Alice Morgan (Gate Services) &bull; Submit Tickets</div>
                </div>
                <i class="fa-solid fa-chevron-right ms-auto text-muted"></i>
            </a>

            <a href="<?= url('auth/login.php?quick_role=technician') ?>" class="role-pill">
                <div class="role-icon bg-warning bg-opacity-10 text-warning">
                    <i class="fa-solid fa-screwdriver-wrench"></i>
                </div>
                <div>
                    <div class="fw-bold">IT Technician Portal</div>
                    <div class="small text-muted">Dave Miller (IT Field Support) &bull; Fix Issues</div>
                </div>
                <i class="fa-solid fa-chevron-right ms-auto text-muted"></i>
            </a>

            <a href="<?= url('auth/login.php?quick_role=admin') ?>" class="role-pill">
                <div class="role-icon bg-danger bg-opacity-10 text-danger">
                    <i class="fa-solid fa-user-shield"></i>
                </div>
                <div>
                    <div class="fw-bold">IT Supervisor / Admin Portal</div>
                    <div class="small text-muted">Sarah Jenkins (IT Ops) &bull; Assign & Logs</div>
                </div>
                <i class="fa-solid fa-chevron-right ms-auto text-muted"></i>
            </a>
        </div>
    </div>
</div>

</body>
</html>
