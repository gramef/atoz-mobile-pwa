<div class="modal fade" id="agentCardModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">
            <span>&times;</span>
          </button>
        </div>
        <div class="modal-body py-4">
          <img src="" alt="Agent Profile Picture" class="img-fluid modal-picture" id="agentProfilePicture">
          <div class="mx-auto mt-4" style="max-width: 250px">
            <h2 class="modal-name text-left mb-3" id="agentName"></h2>
              <div class="mb-4">
                  <p class="label mb-2">Telephone:</p>
                  <p class="label agent__detail" id="agentTel"></p>
              </div>
              <div class="mb-4">
                  <p class="label mb-2">Email:</p>
                  <p class="label agent__detail" id="agentEmail"></p>
              </div>
              <div class="mb-4">
                  <p class="label mb-2">DBS Expiry Date:</p>
                  <p class="label agent__detail" id="agentDBSExpiryDate"></p>
              </div>
            <div class="mb-4">
              <p class="label mb-2">DBS Number:</p>
              <p class="label agent__detail" id="agentDBSNumber"></p>
            </div>
            <div class="mb-4">
              <p class="label mb-2">Induction Date:</p>
              <p class="label agent__detail" id="agentInductionDate"></p>
            </div>
            <div>
              <p class="label mb-2">DBS Update Reference Number:</p>
              <p class="label agent__detail mb-0" id="agentDBSRef"></p>
            </div>
          </div>
        </div>
        <div class="modal-footer justify-content-center">
          <button type="button" class="btn btn--grey section__btn" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
</div>

@push('scripts')
  <script>
    window.addEventListener('load', function() {
      $('#agentCardModal').on('show.bs.modal', function (event) {
        var $button = $(event.relatedTarget)

        $('#agentName').text($button.data('name'))
        $('#agentProfilePicture').attr('src', $button.data('picture'))
        $('#agentDBSExpiryDate').text($button.data('dbs-expiry-date') || 'n/a')
        $('#agentDBSNumber').text($button.data('dbs-number') || 'n/a')
        $('#agentInductionDate').text($button.data('induction-date') || 'n/a')
        $('#agentDBSRef').text($button.data('dbs-ref') || 'n/a')
        $('#agentEmail').text($button.data('email') || 'n/a')
        $('#agentTel').text($button.data('tel') || 'n/a')
      })
    })
  </script>
@endpush
