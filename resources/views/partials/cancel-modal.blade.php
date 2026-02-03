<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog">
        {{ Form::open() }}
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cancelModalLabel">Please provide a reason for your cancellation</h5>
                    <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    {{ Form::label('message') }}
                    {{ Form::textarea('message', null, ['class' => 'form-control', 'required' => 'required']) }}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--cancel section__btn" data-dismiss="modal">Close</button>
                    {{ Form::submit('Send message', ['class' => 'btn btn--primary section__btn']) }}
                </div>
            </div>
        {{ Form::close() }}
    </div>
</div>

@push('scripts')
    <script>
        window.addEventListener('load', function() {
            $('#cancelModal').on('show.bs.modal', function (event) {
                $(this)
                    .find('form')
                    .attr('action', event.relatedTarget.route)
            })
        })
    </script>
@endpush

@include('partials.24-hour-alert')