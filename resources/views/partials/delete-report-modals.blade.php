{{-- Delete & Report Modals Partial --}}
{{-- Place in resources/views/partials/delete-report-modals.blade.php --}}

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel">Delete Post</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <p class="mb-0">Are you sure you want to delete this post? This action cannot be undone.</p>
                <div class="text-muted small mt-2">This will remove the post and any associated media.</div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" action="" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Report Modal -->
<div class="modal fade" id="reportModal" tabindex="-1" aria-labelledby="reportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="reportModalLabel">Report Post</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form id="reportForm" method="POST" action="">
                    @csrf

                    <fieldset class="form-group mb-3">
                        <legend class="visually-hidden">Reason for report</legend>
                        <label class="fw-bold d-block mb-2">Choose a reason</label>

                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="reason" id="reasonSpam" value="spam" required>
                            <label class="form-check-label" for="reasonSpam">Spam or misleading</label>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="reason" id="reasonViolence" value="violence">
                            <label class="form-check-label" for="reasonViolence">Violence or harmful content</label>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="reason" id="reasonHate" value="hate_speech">
                            <label class="form-check-label" for="reasonHate">Hate speech or discrimination</label>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="reason" id="reasonMisinfo" value="misinformation">
                            <label class="form-check-label" for="reasonMisinfo">Misinformation or false claims</label>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="reason" id="reasonOther" value="other">
                            <label class="form-check-label" for="reasonOther">Other</label>
                        </div>
                    </fieldset>

                    <div class="small text-muted mb-3">
                        Select the category that best matches the issue. Admins will review reports manually.
                    </div>

                    <div class="text-end">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning" id="reportSubmitBtn">Submit Report</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Success Toast Notification -->
<div class="position-fixed top-0 end-0 p-3" style="z-index: 9999">
    <div id="successToast" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="toastMessage">
                Action completed successfully!
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // DELETE modal: set action dynamically
    const deleteModal = document.getElementById('deleteModal');
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const postId = button.getAttribute('data-id');
            
            if (!postId) {
                console.warn('deleteModal: no data-id on trigger element');
                return;
            }
            
            const deleteForm = document.getElementById('deleteForm');
            deleteForm.setAttribute('action', '/posts/' + postId);
            deleteForm.setAttribute('data-post-id', postId);
        });

        deleteModal.addEventListener('hidden.bs.modal', function () {
            const deleteForm = document.getElementById('deleteForm');
            deleteForm.setAttribute('action', '');
            deleteForm.removeAttribute('data-post-id');
        });
    }

    // REPORT modal: set action dynamically
    const reportModal = document.getElementById('reportModal');
    if (reportModal) {
        reportModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const postId = button.getAttribute('data-id');
            
            if (!postId) {
                console.warn('reportModal: no data-id on trigger element');
                return;
            }
            
            const reportForm = document.getElementById('reportForm');
            reportForm.setAttribute('action', '/posts/' + postId + '/report');
            
            // Clear any previously selected radio buttons
            const radios = reportForm.querySelectorAll('input[name="reason"]');
            radios.forEach(radio => radio.checked = false);
        });

        reportModal.addEventListener('hidden.bs.modal', function () {
            const reportForm = document.getElementById('reportForm');
            reportForm.setAttribute('action', '');
            
            const radios = reportForm.querySelectorAll('input[name="reason"]');
            radios.forEach(radio => radio.checked = false);
        });
    }

    // Function to show toast notification
    function showToast(message, isSuccess = true) {
        const toastEl = document.getElementById('successToast');
        const toastMessage = document.getElementById('toastMessage');
        
        toastMessage.textContent = message;
        
        // Change color based on success/error
        toastEl.classList.remove('bg-success', 'bg-danger');
        toastEl.classList.add(isSuccess ? 'bg-success' : 'bg-danger');
        
        const toast = new bootstrap.Toast(toastEl, {
            autohide: true,
            delay: 3000
        });
        toast.show();
    }

    // Handle DELETE form submission with AJAX
    const deleteForm = document.getElementById('deleteForm');
    if (deleteForm) {
        deleteForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formAction = this.getAttribute('action');
            const postId = this.getAttribute('data-post-id');
            const formData = new FormData(this);
            
            fetch(formAction, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                // Close modal
                const modalInstance = bootstrap.Modal.getInstance(deleteModal);
                modalInstance.hide();
                
                if (data.success) {
                    // Remove post from DOM
                    const postCard = document.getElementById('post-' + postId);
                    if (postCard) {
                        postCard.style.transition = 'opacity 0.3s, transform 0.3s';
                        postCard.style.opacity = '0';
                        postCard.style.transform = 'scale(0.9)';
                        
                        setTimeout(() => {
                            postCard.remove();
                        }, 300);
                    }
                    
                    showToast(data.message || 'Post deleted successfully!', true);
                } else {
                    showToast(data.message || 'Failed to delete post.', false);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                const modalInstance = bootstrap.Modal.getInstance(deleteModal);
                modalInstance.hide();
                showToast('An error occurred. Please try again.', false);
            });
        });
    }

    // Handle REPORT form submission with AJAX
    const reportForm = document.getElementById('reportForm');
    if (reportForm) {
        reportForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formAction = this.getAttribute('action');
            const formData = new FormData(this);
            
            fetch(formAction, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                // Close modal
                const modalInstance = bootstrap.Modal.getInstance(reportModal);
                modalInstance.hide();
                
                if (data.success) {
                    showToast(data.message || 'Report submitted successfully!', true);
                } else {
                    showToast(data.message || 'Failed to submit report.', false);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                const modalInstance = bootstrap.Modal.getInstance(reportModal);
                modalInstance.hide();
                showToast('An error occurred. Please try again.', false);
            });
        });
    }
});
</script>