<div class="card p-4">
    <form>
        <div class="mb-3">
            <label for="subject" class="form-label fw-bold">Issue Subject</label>
            <input type="text" class="form-control" id="subject" placeholder="Briefly describe the issue">
        </div>
        
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="category" class="form-label fw-bold">Category</label>
                <select class="form-select" id="category">
                    <option selected disabled>Select a category...</option>
                    <option value="software">Software / Application</option>
                    <option value="hardware">Hardware / Equipment</option>
                    <option value="network">Network / Internet</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label for="urgency" class="form-label fw-bold">Urgency</label>
                <select class="form-select" id="urgency">
                    <option selected disabled>Select urgency...</option>
                    <option value="low">Low (Minor annoyance)</option>
                    <option value="medium">Medium (Impairing work)</option>
                    <option value="high">High (Unable to work)</option>
                    <option value="critical">Critical (System down, impacting flights)</option>
                </select>
            </div>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label fw-bold">Detailed Description</label>
            <textarea class="form-control" id="description" rows="5" placeholder="Please provide as much detail as possible..."></textarea>
        </div>

        <div class="mb-3">
            <label for="attachment" class="form-label fw-bold">Attachment (Optional)</label>
            <input class="form-control" type="file" id="attachment">
        </div>

        <div class="text-end">
            <a href="/NimbusDesk/employee/index.php" class="btn btn-secondary me-2">Cancel</a>
            <button type="button" class="btn btn-primary">Submit Ticket</button>
        </div>
    </form>
</div>
