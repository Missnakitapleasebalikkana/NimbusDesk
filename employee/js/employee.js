/**
 * Employee Portal Frontend Script
 */

document.addEventListener('DOMContentLoaded', () => {
    // Ticket creation form AJAX submission
    const form = document.getElementById('createTicketForm');
    if (form) {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i> Submitting...';

            const formData = new FormData(form);

            try {
                const response = await fetch('/NimbusDesk/employee/api/create_ticket.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();

                if (result.success) {
                    showToast(`Ticket #${result.ticket_code} created successfully!`, 'success');
                    setTimeout(() => {
                        window.location.href = '/NimbusDesk/employee/index.php';
                    }, 1200);
                } else {
                    showToast(result.message || 'Submission failed', 'danger');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            } catch (err) {
                showToast('Network error submitting ticket', 'danger');
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        });
    }

    // Status filter dropdown
    const statusFilter = document.getElementById('employeeStatusFilter');
    if (statusFilter) {
        statusFilter.addEventListener('change', () => {
            const val = statusFilter.value;
            window.location.href = `/NimbusDesk/employee/index.php?status=${encodeURIComponent(val)}`;
        });
    }
});
