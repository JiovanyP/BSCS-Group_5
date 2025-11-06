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
              Are you sure you want to delete this post? This action cannot be undone.
          </div>
          <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
              <form id="deleteForm" method="POST" action="">
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
          <div class="modal-header bg-warning text-dark">
              <h5 class="modal-title" id="reportModalLabel">Report Post</h5>
              <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
              </button>
          </div>
          <div class="modal-body">
              <form id="reportForm" method="POST" action="">
                  @csrf
                  <div class="form-group">
                      <label for="reportReason">Reason</label>
                      <textarea id="reportReason" name="reason" class="form-control" rows="3" required></textarea>
                  </div>
                  <div class="text-end mt-3">
                      <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                      <button type="submit" class="btn btn-warning">Submit Report</button>
                  </div>
              </form>
          </div>
      </div>
  </div>
</div>
