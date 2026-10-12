<div class="card p-4 p-md-5 login-card" style="width: 100%; max-width: 460px;">
    <div class="text-center mb-4">
        <img src="/NimbusDesk/assets/images/icon.png" alt="NimbusDesk Logo" width="80" height="80" class="rounded-circle shadow-sm mb-2 border border-2 border-white">
        <h2 class="fw-bold mt-2 mb-0" style="color: var(--nimbus-maroon); letter-spacing: 1.5px;">NIMBUSDESK</h2>
        <p class="text-muted small mt-1">Aviation IT Help Desk & Incident Management</p>
    </div>

    <?php if (!empty($flashSuccess)): ?>
        <div class="alert alert-success alert-dismissible fade show py-2" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i><?= htmlspecialchars($flashSuccess) ?>
            <button type="button" class="btn-close py-2" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($flashDanger)): ?>
        <div class="alert alert-danger alert-dismissible fade show py-2" role="alert">
            <i class="fa-solid fa-triangle-exclamation me-2"></i><?= htmlspecialchars($flashDanger) ?>
            <button type="button" class="btn-close py-2" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <form action="/NimbusDesk/index.php" method="POST" autocomplete="on">
        <input type="hidden" name="csrf_token" value="<?= \NimbusDesk\Core\Auth::csrfToken() ?>">

        <div class="mb-3">
            <label for="email" class="form-label fw-semibold" style="color: var(--nimbus-dark);">Email Address</label>
            <div class="input-group">
                <span class="input-group-text bg-white"><i class="fa-solid fa-envelope text-muted"></i></span>
                <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($oldEmail ?? '') ?>" placeholder="name@airline.com" required autofocus>
            </div>
        </div>

        <div class="mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <label for="password" class="form-label fw-semibold mb-0" style="color: var(--nimbus-dark);">Password</label>
                <a href="/NimbusDesk/forgot_password.php" class="small text-decoration-none fw-semibold" style="color: var(--nimbus-maroon);">Forgot Password?</a>
            </div>
            <div class="input-group mt-1">
                <span class="input-group-text bg-white"><i class="fa-solid fa-lock text-muted"></i></span>
                <input type="password" class="form-control" id="password" name="password" placeholder="••••••••" required>
            </div>
        </div>

        <button type="submit" class="btn btn-primary w-100 py-2 fs-5 shadow-sm">
            <i class="fa-solid fa-right-to-bracket me-2"></i>Sign In
        </button>

        <div class="text-center mt-3 pt-2 border-top">
            <span class="small text-muted">Need airport access? Contact your </span>
            <span class="small fw-bold" style="color: var(--nimbus-maroon);">IT Administrator</span>
        </div>
    </form>
</div>
