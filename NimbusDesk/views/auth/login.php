<div class="card p-5 login-card" style="width: 100%; max-width: 450px;">
    <div class="text-center mb-4">
        <i class="fa-solid fa-cloud" style="font-size: 3rem; color: var(--nimbus-pink);"></i>
        <h2 class="fw-bold mt-2" style="color: var(--nimbus-maroon); letter-spacing: 1px;">NIMBUSDESK</h2>
        <p class="text-muted">Technical Support and Help Desk</p>
    </div>
    
    <form action="/NimbusDesk/index.php" method="GET">
        <div class="mb-3">
            <label for="email" class="form-label fw-bold" style="color: var(--nimbus-dark);">Email address</label>
            <input type="email" class="form-control" id="email" placeholder="name@airline.com" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label fw-bold" style="color: var(--nimbus-dark);">Password</label>
            <input type="password" class="form-control" id="password" required>
        </div>
        
        <div class="mb-4">
            <label class="form-label fw-bold" style="color: var(--nimbus-dark);">Login As (Demo Feature)</label>
            <select name="role" class="form-select" required>
                <option value="employee">Aviation Employee (Ticket Creator)</option>
                <option value="admin">IT Admin / Technician</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary w-100 py-2 fs-5">Sign In</button>
    </form>
</div>
