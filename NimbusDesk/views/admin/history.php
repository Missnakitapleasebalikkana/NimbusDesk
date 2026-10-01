<div class="d-flex justify-content-between align-items-center mb-3">
    <div class="d-flex gap-2">
        <input type="text" class="form-control form-control-sm" placeholder="Search by ID or Subject...">
        <select class="form-select form-select-sm">
            <option>All Categories</option>
            <option>Software</option>
            <option>Hardware</option>
            <option>Network</option>
        </select>
        <button class="btn btn-sm btn-outline-secondary">Filter</button>
    </div>
    <button class="btn btn-sm btn-primary"><i class="fa-solid fa-download me-1"></i> Export</button>
</div>

<div class="table-responsive">
    <table class="table table-hover table-bordered align-middle">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Subject</th>
                <th>Requester</th>
                <th>Category</th>
                <th>Resolved Date</th>
                <th>Resolved By</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>#10019</td>
                <td>Check-in software keeps freezing</td>
                <td>Bob (Agent)</td>
                <td>Software</td>
                <td>Sep 25, 2026</td>
                <td>Tech Admin 1</td>
                <td><button class="btn btn-sm btn-outline-primary">View</button></td>
            </tr>
            <tr>
                <td>#10015</td>
                <td>No internet connection at Gate 2</td>
                <td>Dave (Agent)</td>
                <td>Network</td>
                <td>Sep 15, 2026</td>
                <td>Tech Admin 2</td>
                <td><button class="btn btn-sm btn-outline-primary">View</button></td>
            </tr>
        </tbody>
    </table>
</div>
