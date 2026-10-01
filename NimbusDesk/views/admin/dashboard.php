<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-center bg-danger text-white">
            <div class="card-body">
                <h5 class="card-title">Critical</h5>
                <h2 class="display-5 fw-bold">1</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center bg-warning text-dark">
            <div class="card-body">
                <h5 class="card-title">Open</h5>
                <h2 class="display-5 fw-bold">12</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center bg-info text-white">
            <div class="card-body">
                <h5 class="card-title">In Progress</h5>
                <h2 class="display-5 fw-bold">8</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center bg-success text-white">
            <div class="card-body">
                <h5 class="card-title">Resolved (Today)</h5>
                <h2 class="display-5 fw-bold">15</h2>
            </div>
        </div>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Active Support Tickets</h4>
    <div class="d-flex gap-2">
        <input type="text" class="form-control form-control-sm" placeholder="Search tickets...">
        <select class="form-select form-select-sm">
            <option>All Status</option>
            <option>Open</option>
            <option>In Progress</option>
        </select>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-hover table-bordered align-middle">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Subject</th>
                <th>Requester</th>
                <th>Category</th>
                <th>Urgency</th>
                <th>Status</th>
                <th>Assigned To</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>#10024</td>
                <td>Gate 4 boarding scanner broken</td>
                <td>Alice (Agent)</td>
                <td>Hardware</td>
                <td><span class="badge bg-danger">Critical</span></td>
                <td><span class="badge ticket-status-open">Open</span></td>
                <td>Unassigned</td>
                <td><a href="/NimbusDesk/admin/ticket.php?id=10024" class="btn btn-sm btn-primary">Manage</a></td>
            </tr>
            <tr>
                <td>#10023</td>
                <td>Cannot access airline internal portal</td>
                <td>Bob (Crew)</td>
                <td>Network</td>
                <td><span class="badge bg-warning text-dark">Medium</span></td>
                <td><span class="badge ticket-status-open">Open</span></td>
                <td>Unassigned</td>
                <td><a href="/NimbusDesk/admin/ticket.php?id=10023" class="btn btn-sm btn-primary">Manage</a></td>
            </tr>
            <tr>
                <td>#10022</td>
                <td>Printer in Terminal 2 not responding</td>
                <td>Charlie (Desk)</td>
                <td>Hardware</td>
                <td><span class="badge bg-info">Low</span></td>
                <td><span class="badge ticket-status-progress">In Progress</span></td>
                <td>Tech Admin 1</td>
                <td><a href="/NimbusDesk/admin/ticket.php?id=10022" class="btn btn-sm btn-primary">Manage</a></td>
            </tr>
        </tbody>
    </table>
</div>
