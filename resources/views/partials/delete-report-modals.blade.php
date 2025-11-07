{{-- Delete & Report Modals Partial --}}
{{-- Place in resources/views/partials/delete-report-modals.blade.php --}}

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
          <div class="modal-header bg-danger text-white">
              <h5 class="modal-title" id="deleteModalLabel">Delete Post</h5>
              <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
              </button>
          </div>

          <div class="modal-body">
              <p class="mb-0">Are you sure you want to delete this post? This action cannot be undone.</p>
              <div class="text-muted small mt-2">This will remove the post and any associated media.</div>
          </div>

          <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>

              {{-- Action is set dynamically when modal shown --}}
              <form id="deleteForm" method="POST" action="">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-danger">Delete</button>
              </form>
          </div>
      </div>
  </div>
</div>

<!-- Report Modal (Option 2: Predefined Reasons) -->
<div class="modal fade" id="reportModal" tabindex="-1" role="dialog" aria-labelledby="reportModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
          <div class="modal-header bg-warning text-dark">
              <h5 class="modal-title" id="reportModalLabel">Report Post</h5>
              <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
              </button>
          </div>

          <div class="modal-body">
              {{-- Form action set dynamically on modal show --}}
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

                  <div class="small text-muted mb-2">
                      Select the category that best matches the issue. Admins will review reports manually.
                  </div>

                  <div class="text-end">
                      <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                      <button type="submit" class="btn btn-warning" id="reportSubmitBtn">Submit Report</button>
                  </div>
              </form>
          </div>
      </div>
  </div>
</div>

{{-- Unobtrusive script: sets form actions from the triggering element's data-id.
     Keeps the modal partial self-contained and works whether you open modal via data-toggle or JS. --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    // DELETE modal: set action to DELETE /posts/{id}
    $('#deleteModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var postId = button.data('id');
        if (!postId) {
            console.warn('deleteModal: no data-id on trigger element');
            return;
        }
        $('#deleteForm').attr('action', '/posts/' + postId);
    });

    $('#deleteModal').on('hidden.bs.modal', function () {
        $('#deleteForm').attr('action', '');
    });

    // REPORT modal: set action to POST /posts/{id}/report
    $('#reportModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var postId = button.data('id');
        if (!postId) {
            console.warn('reportModal: no data-id on trigger element');
            return;
        }
        $('#reportForm').attr('action', '/posts/' + postId + '/report');

        // ensure no radios are pre-checked by default
        $('input[name="reason"]').prop('checked', false);
    });

    $('#reportModal').on('hidden.bs.modal', function () {
        $('#reportForm').attr('action', '');
        $('input[name="reason"]').prop('checked', false);
    });
});
</script>
