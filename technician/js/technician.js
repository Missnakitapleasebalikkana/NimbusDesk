/**
 * Technician Portal JavaScript
 */

document.addEventListener('DOMContentLoaded', () => {
    // Status update form submission
    const statusForm = document.getElementById('techStatusForm');
    if (statusForm) {
        statusForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const submitBtn = statusForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i> Saving...';

            const formData = new FormData(statusForm);

            try {
                const response = await fetch('/NimbusDesk/technician/api/update_status.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();

                if (result.success) {
                    showToast(result.message, 'success');
                    setTimeout(() => {
                        window.location.reload();
                    }, 1200);
                } else {
                    showToast(result.message || 'Status update failed', 'danger');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            } catch (err) {
                showToast('Network error while updating status', 'danger');
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        });
    }

    // Add troubleshooting note form
    const noteForm = document.getElementById('techNoteForm');
    if (noteForm) {
        noteForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const submitBtn = noteForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i> Posting...';

            const formData = new FormData(noteForm);

            try {
                const response = await fetch('/NimbusDesk/technician/api/add_note.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();

                if (result.success) {
                    showToast(result.message, 'success');
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    showToast(result.message || 'Could not post note', 'danger');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            } catch (err) {
                showToast('Network error posting note', 'danger');
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        });
    }

    // Filter selector
    const filterSelect = document.getElementById('techStatusFilter');
    if (filterSelect) {
        filterSelect.addEventListener('change', () => {
            window.location.href = `/NimbusDesk/technician/index.php?status=${encodeURIComponent(filterSelect.value)}`;
        });
    }
});
