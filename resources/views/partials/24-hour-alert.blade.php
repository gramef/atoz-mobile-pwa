<div class="modal fade" id="24HourModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">24 hour alert</h5>
            <button type="button" class="close" data-dismiss="modal">
            <span>&times;</span>
            </button>
        </div>
        <div class="modal-body">
            The start time of this job is less than 24 hours away. If you choose to continue with the cancellation charges may apply if an interpreter is booked as per your terms and conditions.
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn--cancel section__btn" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn--secondary section__btn" data-dismiss="modal" id="continueButton">Continue</button>
        </div>
    </div>
  </div>
</div>

@push('scripts')
  <script>
    window.addEventListener('load', function() {
      $('#24HourModal').on('show.bs.modal', function (event) {
        $('#continueButton').click(function() {
          $('#cancelModal').modal('show', {
            route: event.relatedTarget.route
          })
        })
      })
    })
  </script>
@endpush
