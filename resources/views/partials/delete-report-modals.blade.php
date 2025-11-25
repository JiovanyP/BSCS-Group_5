{{-- Delete & Report Modals Partial - FULLY FIXED --}}
{{-- Place in resources/views/partials/delete-report-modals.blade.php --}}

<style>
/* Modal close button - NO WHITE BACKGROUND */
.modal .close {
    background: transparent !important;
    border: none !important;
    opacity: 1 !important;
    padding: 0 !important;
    margin: -1rem -1rem -1rem auto !important;
    font-size: 1.5rem;
    font-weight: 700;
    line-height: 1;
    text-shadow: none !important;
    transition: opacity 0.2s ease;
    box-shadow: none !important;
    cursor: pointer;
}

.modal .close:hover,
.modal .close:focus {
    opacity: 0.75;
    background: transparent !important;
    outline: none !important;
    box-shadow: none !important;
}

/* Delete modal styling */
#deleteModal .modal-header {
    background-color: #dc3545;
    color: white;
    border-bottom: none;
}

#deleteModal .modal-header .close {
    color: white !important;
}

#deleteModal .modal-body {
    padding: 1.5rem;
}

#deleteModal .modal-footer {
    background: #f8f9fa;
    border-top: 1px solid #dee2e6;
    padding: 1rem 1.5rem;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}

#deleteModal .btn-secondary {
    background: #6c757d;
    border: none;
    padding: 8px 20px;
    border-radius: 8px;
    font-weight: 600;
}

#deleteModal .btn-danger {
    background: #dc3545;
    border: none;
    padding: 8px 20px;
    border-radius: 8px;
    font-weight: 600;
}

/* Report modal styling */
#reportModal .modal-header {
    background: #ffc107;
    color: #000;
    border-bottom: none;
}

#reportModal .modal-header .close {
    color: #000 !important;
}

#reportModal .form-check {
    padding: 12px;
    border-radius: 8px;
    transition: background 0.2s ease;
}

#reportModal .form-check:hover {
    background: #f8f9fa;
}

#reportModal .modal-footer {
    background: #f8f9fa;
    border-top: 1px solid #dee2e6;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}

#reportModal .btn-secondary {
    background: #6c757d;
    border: none;
    padding: 8px 20px;
    border-radius: 8px;
    font-weight: 600;
}

#reportModal .btn-warning {
    background: #ffc107;
    border: none;
    padding: 8px 20px;
    border-radius: 8px;
    font-weight: 600;
    color: #000;
}
</style>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Delete Post</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <p class="mb-0">Are you sure you want to delete this post? This action cannot be undone.</p>
                <div class="text-muted small mt-2">This will remove the post and any associated media.</div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" action="" style="display: inline; margin: 0;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Report Modal -->
<div class="modal fade" id="reportModal" tabindex="-1" role="dialog" aria-labelledby="reportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reportModalLabel">Report Post</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form id="reportForm" method="POST" action="">
                    @csrf

                    <fieldset class="form-group mb-3">
                        <legend class="sr-only">Reason for report</legend>

                        <label class="font-weight-bold d-block mb-2">Choose a reason</label>

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
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-warning" id="reportSubmitBtn">Submit Report</button>
            </div>
        </div>
    </div>
</div>

<script>
// Ensure modals work properly
$(document).ready(function() {
    // Make sure Bootstrap modal is available
    if ($.fn.modal) {
        // Handle modal close events
        $('.modal .close, .modal [data-dismiss="modal"]').on('click', function() {
            $(this).closest('.modal').modal('hide');
        });

        // Handle report submit button
        $('#reportSubmitBtn').on('click', function() {
            $('#reportForm').submit();
        });

        // Reset forms when modals are hidden
        $('#deleteModal').on('hidden.bs.modal', function() {
            $('#deleteForm').attr('action', '');
        });

        $('#reportModal').on('hidden.bs.modal', function() {
            $('#reportForm').attr('action', '');
            $('input[name="reason"]').prop('checked', false);
        });
    }
});
</script>