<div class="modal fade" id="messageModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">
          <span>&times;</span>
        </button>
      </div>
      <div class="modal-body py-4">
        <h2 class="modal-name d-none" id="cancellationMessage"></h2>
      </div>
      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn--cancel section__btn" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

@push('scripts')
  <script>
    window.addEventListener('load', function() {
      $('#messageModal').on('show.bs.modal', function (event) {
        $(this).find('#cancellationMessage')
          .removeClass('d-none')
          .text(
            $(event.relatedTarget).data('msg')
          )
      })
    })
  </script>
@endpush