<div class="card p-4 p-md-5 login-card" style="width: 100%; max-width: 460px;">
    <div class="text-center mb-4">
        <img src="/NimbusDesk/assets/images/icon.png" alt="NimbusDesk Logo" width="75" height="75" class="rounded-circle shadow-sm mb-2 border border-2 border-white">
        <h3 class="fw-bold mt-2 mb-0" style="color: var(--nimbus-maroon); letter-spacing: 1px;">Reset Password</h3>
        <p class="text-muted small mt-1">Airport Security Access Recovery</p>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger py-2 small mb-3">
            <i class="fa-solid fa-triangle-exclamation me-1"></i><?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <?php if ($requested): ?>
        <div class="alert alert-info text-center py-3 mb-4">
            <i class="fa-solid fa-envelope-circle-check fs-2 text-primary d-block mb-2"></i>
            <h6 class="fw-bold mb-1">Password Reset Dispatched</h6>
            <p class="small text-muted mb-0">
                A password reset request for <strong><?= htmlspecialchars($email) ?></strong> has been logged in the system for the <strong>IT Systems Administrator</strong>. Please contact IT Ops at Terminal 1 or wait for your administrator to generate a temporary access key.
            </p>
        </div>
        <a href="/NimbusDesk/index.php" class="btn btn-primary w-100 py-2 fs-6">
            <i class="fa-solid fa-arrow-left me-1"></i> Return to Sign In
        </a>
    <?php else: ?>
        <form action="/NimbusDesk/forgot_password.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?= \NimbusDesk\Core\Auth::csrfToken() ?>">
            <p class="text-muted small mb-3">
                Enter your registered airline work email address. An authorized IT Administrator will verify your airport credentials and issue a new password.
            </p>

            <div class="mb-4">
                <label for="email" class="form-label fw-semibold small text-dark">Airline Work Email</label>
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="fa-solid fa-envelope text-muted"></i></span>
                    <input type="email" class="form-control" id="email" name="email" placeholder="name@airline.com" required autofocus>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2 fs-6 shadow-sm">
                <i class="fa-solid fa-paper-plane me-2"></i>Request Password Reset
            </button>

            <div class="text-center mt-4 pt-2 border-top">
                <a href="/NimbusDesk/index.php" class="small fw-semibold text-decoration-none" style="color: var(--nimbus-maroon);">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back to Sign In
                </a>
            </div>
        </form>
    <?php endif; ?>
</div>
