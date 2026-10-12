/**
 * Admin Portal JavaScript
 */

document.addEventListener('DOMContentLoaded', () => {
    // Admin ticket update form
    const updateForm = document.getElementById('adminUpdateForm');
    if (updateForm) {
        updateForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const submitBtn = updateForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i> Saving...';

            const formData = new FormData(updateForm);

            try {
                const response = await fetch('/NimbusDesk/admin/api/update_ticket.php', {
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
                    showToast(result.message || 'Update failed', 'danger');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            } catch (err) {
                showToast('Network error updating ticket', 'danger');
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        });
    }

    // Quick assignment modal form
    const assignForm = document.getElementById('adminAssignForm');
    if (assignForm) {
        assignForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const submitBtn = assignForm.querySelector('button[type="submit"]');
            submitBtn.disabled = true;

            const formData = new FormData(assignForm);

            try {
                const response = await fetch('/NimbusDesk/admin/api/assign_ticket.php', {
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
                    showToast(result.message || 'Assignment failed', 'danger');
                    submitBtn.disabled = false;
                }
            } catch (err) {
                showToast('Network error during technician assignment', 'danger');
                submitBtn.disabled = false;
            }
        });
    }

    // Search and filter inputs on dashboard
    const filterForm = document.getElementById('adminFilterForm');
    if (filterForm) {
        filterForm.querySelectorAll('select').forEach(sel => {
            sel.addEventListener('change', () => filterForm.submit());
        });
    }
});
