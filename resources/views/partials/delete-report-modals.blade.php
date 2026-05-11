{{-- Delete & Report Modals Partial - STYLED & CONSISTENT --}}
<style>
    /* === GLOBAL MODAL STYLING === */
    .modal-content {
        border-radius: 16px; /* consistent rounded corners */
        border: none;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        overflow: hidden;
    }

    .modal-header {
        border-bottom: 1px solid #f0f0f0;
        background: #fff; /* Clean white header */
        padding: 1.25rem 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .modal-title {
        font-weight: 700;
        font-size: 1.1rem;
        color: #333;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .modal-body {
        padding: 1.5rem;
    }

    .modal-footer {
        background: #f8f9fa;
        border-top: 1px solid #f0f0f0;
        padding: 1rem 1.5rem;
    }

    /* Fixed Close Button (No white background) */
    .btn-close {
        background: transparent url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23333'%3e%3cpath d='M.293.293a1 1 0 011.414 0L8 6.586 14.293.293a1 1 0 111.414 1.414L9.414 8l6.293 6.293a1 1 0 01-1.414 1.414L8 9.414l-6.293 6.293a1 1 0 01-1.414-1.414L6.586 8 .293 1.707a1 1 0 010-1.414z'/%3e%3c/svg%3e") center/1em auto no-repeat !important;
        opacity: 0.5;
        transition: opacity 0.2s;
        margin: 0;
        padding: 0.8rem;
    }
    .btn-close:hover {
        opacity: 1;
        box-shadow: none;
    }

    /* === REPORT MODAL SPECIFIC (Cards Layout) === */
    .report-options-list {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .report-option {
        position: relative;
        cursor: pointer;
        margin: 0;
    }

    /* Hide actual radio */
    .report-option input[type="radio"] {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    /* Card styling */
    .option-content {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 16px;
        background: #fbfbfb; /* Light gray */
        border: 1px solid transparent;
        border-radius: 12px;
        transition: all 0.2s ease;
        color: #555;
        font-size: 14px;
        font-weight: 600;
    }
    
    .option-content .material-icons-outlined {
        color: #888;
        font-size: 20px;
    }

    /* Hover */
    .report-option:hover .option-content {
        background: #f0f0f0;
    }

    /* Checked State */
    .report-option input:checked + .option-content {
        background: #fff5f8; /* Tint of accent color */
        border-color: #CF0F47; /* --accent */
        color: #CF0F47;
    }
    
    .report-option input:checked + .option-content .material-icons-outlined {
        color: #CF0F47;
    }

    /* Checkmark icon */
    .check-icon {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #CF0F47;
        font-size: 20px;
        opacity: 0;
        transition: opacity 0.2s;
    }
    .report-option input:checked ~ .check-icon {
        opacity: 1;
    }

    /* Submit Button */
    #reportSubmitBtn {
        background: #CF0F47; /* --accent */
        color: white;
        border: none;
        padding: 8px 24px;
        border-radius: 20px;
    }
    #reportSubmitBtn:hover {
        background: #b00c3b;
    }

    /* === DELETE MODAL SPECIFIC === */
    .delete-icon-wrapper {
        display: flex;
        justify-content: center;
        margin-bottom: 1rem;
    }
    .delete-icon-circle {
        width: 60px;
        height: 60px;
        background: #ffebee;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #dc3545;
    }
    .btn-danger-styled {
        background: #dc3545;
        border: none;
        padding: 8px 24px;
        border-radius: 20px;
        font-weight: 600;
    }
</style>

{{-- DELETE MODAL --}}
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger" id="deleteModalLabel">
                    Delete Post
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body text-center">
                <div class="delete-icon-wrapper">
                    <div class="delete-icon-circle">
                        <span class="material-icons" style="font-size: 32px;">delete_forever</span>
                    </div>
                </div>
                <h6 class="fw-bold mb-2">Are you sure?</h6>
                <p class="text-muted mb-0">This action cannot be undone. The post and its media will be permanently removed.</p>
            </div>

            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-link text-muted text-decoration-none" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" action="" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-danger-styled">Yes, Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- REPORT MODAL --}}
<div class="modal fade" id="reportModal" tabindex="-1" aria-labelledby="reportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reportModalLabel">
                    <span class="material-icons text-warning me-2">flag</span> Report Post
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form id="reportForm" method="POST" action="">
                    @csrf

                    <p class="small text-muted mb-3">Please select the reason that best applies:</p>

                    <div class="report-options-list">
                        <label class="report-option">
                            <input type="radio" name="reason" value="spam" required>
                            <span class="option-content">
                                <span class="material-icons-outlined">sentiment_dissatisfied</span>
                                <span>Spam or misleading</span>
                            </span>
                            <span class="check-icon material-icons">check_circle</span>
                        </label>

                        <label class="report-option">
                            <input type="radio" name="reason" value="violence">
                            <span class="option-content">
                                <span class="material-icons-outlined">report_problem</span>
                                <span>Violence or harmful</span>
                            </span>
                            <span class="check-icon material-icons">check_circle</span>
                        </label>

                        <label class="report-option">
                            <input type="radio" name="reason" value="hate_speech">
                            <span class="option-content">
                                <span class="material-icons-outlined">record_voice_over</span>
                                <span>Hate speech</span>
                            </span>
                            <span class="check-icon material-icons">check_circle</span>
                        </label>

                        <label class="report-option">
                            <input type="radio" name="reason" value="misinformation">
                            <span class="option-content">
                                <span class="material-icons-outlined">fact_check</span>
                                <span>Misinformation</span>
                            </span>
                            <span class="check-icon material-icons">check_circle</span>
                        </label>

                        <label class="report-option">
                            <input type="radio" name="reason" value="other">
                            <span class="option-content">
                                <span class="material-icons-outlined">more_horiz</span>
                                <span>Other</span>
                            </span>
                            <span class="check-icon material-icons">check_circle</span>
                        </label>
                    </div>

                    {{-- Hidden footer inside form to handle submit --}}
                    <div class="modal-footer mt-4 pb-0 px-0">
                        <button type="button" class="btn btn-link text-muted text-decoration-none" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn" id="reportSubmitBtn">Submit Report</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- TOAST NOTIFICATION --}}
<div class="position-fixed top-0 end-0 p-3" style="z-index: 9999">
    <div id="successToast" class="toast align-items-center text-white bg-success border-0 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body d-flex align-items-center gap-2" id="toastMessage">
                <span class="material-icons" style="font-size: 18px;">check_circle</span>
                <span>Action completed successfully!</span>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // === 1. DELETE MODAL LOGIC ===
    const deleteModal = document.getElementById('deleteModal');
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const postId = button.getAttribute('data-id');
            
            if (!postId) return;
            
            const deleteForm = document.getElementById('deleteForm');
            // Ensure this route matches your Laravel routes (e.g., /posts/123)
            deleteForm.setAttribute('action', '/posts/' + postId);
            deleteForm.setAttribute('data-post-id', postId);
        });
    }

    // === 2. REPORT MODAL LOGIC ===
    const reportModal = document.getElementById('reportModal');
    if (reportModal) {
        reportModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const postId = button.getAttribute('data-id');
            
            if (!postId) return;
            
            const reportForm = document.getElementById('reportForm');
            // Ensure this route matches your Laravel routes (e.g., /posts/123/report)
            reportForm.setAttribute('action', '/posts/' + postId + '/report');
            
            // Reset selection
            const radios = reportForm.querySelectorAll('input[name="reason"]');
            radios.forEach(radio => radio.checked = false);
        });
    }

    // === 3. SHARED AJAX & TOAST LOGIC ===
    function showToast(message, isSuccess = true) {
        const toastEl = document.getElementById('successToast');
        const toastMessage = document.getElementById('toastMessage');
        
        // Update text
        // Keep the icon span, just update text node or innerHTML safely
        toastMessage.innerHTML = `<span class="material-icons" style="font-size: 18px;">${isSuccess ? 'check_circle' : 'error'}</span> <span>${message}</span>`;
        
        // Update color
        toastEl.className = `toast align-items-center text-white border-0 shadow-lg ${isSuccess ? 'bg-success' : 'bg-danger'}`;
        
        const toast = new bootstrap.Toast(toastEl, { autohide: true, delay: 3000 });
        toast.show();
    }

    // Generic Form Submit Handler (Works for both Delete & Report)
    function handleFormSubmit(formId, modalId) {
        const form = document.getElementById(formId);
        if (!form) return;

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formAction = this.getAttribute('action');
            const formData = new FormData(this);
            const modalEl = document.getElementById(modalId);
            
            // Disable button to prevent double submit
            const btn = this.querySelector('button[type="submit"]');
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = 'Processing...';

            fetch(formAction, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    // CSRF is handled by the @csrf input in the form
                }
            })
            .then(response => response.json())
            .then(data => {
                // Close modal
                const modalInstance = bootstrap.Modal.getInstance(modalEl);
                if(modalInstance) modalInstance.hide();
                
                if (data.success) {
                    showToast(data.message || 'Success!', true);
                    
                    // Specific logic for delete
                    if (formId === 'deleteForm') {
                        const postId = this.getAttribute('data-post-id');
                        const postCard = document.getElementById('post-' + postId);
                        if (postCard) {
                            postCard.style.transition = 'all 0.3s ease';
                            postCard.style.opacity = '0';
                            postCard.style.transform = 'scale(0.9)';
                            setTimeout(() => postCard.remove(), 300);
                        }
                    }
                } else {
                    showToast(data.message || 'Action failed.', false);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                const modalInstance = bootstrap.Modal.getInstance(modalEl);
                if(modalInstance) modalInstance.hide();
                showToast('An error occurred. Please try again.', false);
            })
            .finally(() => {
                // Reset button
                btn.disabled = false;
                btn.innerHTML = originalText;
            });
        });
    }

    // Initialize handlers
    handleFormSubmit('deleteForm', 'deleteModal');
    handleFormSubmit('reportForm', 'reportModal');
});
</script>