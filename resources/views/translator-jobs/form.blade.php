<div class="row">
    <div class="col-lg-4">

        @role('admin')
            <fieldset class="form__field">
                {{ Form::label('client_id', 'Client:', ['class' => 'label form__label required']) }}
                {{ Form::select('client_id', $clients, null, ['class' => 'input input--select form__input', 'required' => 'required', 'placeholder' => 'Choose a client']) }}
                <div id="company-name"><strong>Company:</strong> <span class="name"></span></div>
            </fieldset>
        @endrole

        <fieldset class="form__field">
            {{ Form::label('to_language_id', 'Languages:', ['class' => 'label form__label required']) }}
            {{ Form::select('to_language_id', $languages, null, ['class' => 'input input--select form__input', 'required' => 'required', 'placeholder' => 'To']) }}
            {{ Form::select('from_language_id', $languages, null, ['class' => 'input input--select form__input', 'required' => 'required', 'placeholder' => 'From']) }}
        </fieldset>
        <fieldset class="form__field">
            {{ Form::label('skill_id', 'Service type:', ['class' => 'label form__label required']) }}
            {{ Form::select('skill_id', $skills, null, ['class' => 'input input--select form__input', 'required' => 'required', 'placeholder' => 'Choose service type']) }}
        </fieldset>
        <fieldset class="form__field">
            {{ Form::label('word_count', 'Word count:', ['class' => 'label form__label required']) }}
            {{ Form::number('word_count', null, ['class' => 'input form__input form__input--small', 'placeholder' => 'Eg. 250', 'min' => 0, 'required' => 'required']) }}
        </fieldset>
        <fieldset class="form__field">
            {{ Form::label('target_date', 'Delivery date:', ['class' => 'label form__label required']) }}
            {{ Form::text('target_date', isset($translatorJob) ? $translatorJob->target_date->toDateString() : null, [
                'class' => 'input input--select form__input',
                'placeholder' => 'Select date',
                'required' => 'required',
            ]) }}
        </fieldset>
    </div>
    <div class="col-lg-4">
        <fieldset class="form__field">
            {{ Form::label('notes', 'Special instructions/ notes', ['class' => 'label form__label']) }}
            {{ Form::textarea('notes', null, ['class' => 'input input--textarea form__input form__special-instructions', 'placeholder' => 'Please enter the name of interpreter if required or any other special instructions…']) }}
        </fieldset>
        <fieldset class="form__field">
            {{ Form::label('affirmation', 'Affirmation required:', ['class' => 'label form__label required']) }}
            {{ Form::select('affirmation', [1 => 'Yes', 0 => 'No'], null, ['class' => 'input input--select form__input', 'required' => 'required', 'placeholder' => 'Yes or No']) }}
        </fieldset>
        <fieldset class="form__field">
            {{ Form::label('affidavit', 'Sworn affidavit required:', ['class' => 'label form__label required']) }}
            {{ Form::select('affidavit', [1 => 'Yes', 0 => 'No'], null, ['class' => 'input input--select form__input', 'required' => 'required', 'placeholder' => 'Yes or No']) }}
        </fieldset>
    </div>
    <div class="col-lg-4">
        <fieldset class="form__field">

            @if (isset($translatorJob))
                <translator-job-dropzone
                    :existing-documents="{{ $translatorJob->documents }}"></translator-job-dropzone>
            @else
                <translator-job-dropzone></translator-job-dropzone>
            @endif

        </fieldset>
        <fieldset class="form__field">
            {{ Form::label('client_reference', 'Client Reference / Cost Code:', ['class' => 'label form__label']) }}
            {{ Form::text('client_reference', null, ['class' => 'input form__input', 'placeholder' => 'Please enter']) }}
        </fieldset>

        <fieldset class="form__field">
            {{ Form::label('requested_agent_id', 'Request Specific Translator', ['class' => 'label form__label']) }}
            {{ Form::select('requested_agent_id', [], null, [
                'class' => 'input input--select form__input agent--select',
                'placeholder' => 'Select Translator',
                'disabled' => 'disabled',
                'data-job-type' => 'translator',
            ]) }}
        </fieldset>
        @php $hidePast=false; @endphp

        @if (!isset($hideSubmit))
            <fieldset class="form__field form__field--no-mb">

                @if (isset($translatorJob))
                    @can('update', $translatorJob)
                        @php $hidePast=true; @endphp
                        {{ Form::submit('Update Job', ['class' => 'btn btn--primary form__btn']) }}
                    @endcan
                @else
                    @php $hidePast=true; @endphp
                    {{ Form::submit('Submit Job', ['class' => 'btn btn--primary form__btn']) }}
                @endif

            </fieldset>
        @endif

    </div>
</div>

@push('scripts')
    <script src="{{ asset('js/interpreter-select.js') }}"></script>
    <script>
        window.addEventListener('load', function() {
            $('#target_date').flatpickr({
                allowInput: true,
                altInput: true,
                altFormat: 'd/m/Y'
                @if ($hidePast)
                    ,
                    disable: [
                        function(selectedDate) {
                            var today = new Date();
                            today.setHours(0, 0, 0, 0);
                            return (selectedDate < today);
                        }
                    ]
                @endif
            })
        })

        $('document').ready(function() {
            $('#company-name').hide()
            @if (isset($translatorJob) && $translatorJob->client->organisation)
                loadCompany({{ $translatorJob->client_id }});
            @endif
        });

        function loadCompany(client_id) {
            let baseUrl = "{{ route('clients.show', ['client' => '*']) }}";

            $.ajax({
                    method: "POST",
                    url: baseUrl.replace('*', client_id),
                })
                .done(function(data) {
                    $('#company-name .name').html(data.company);
                    if (data.company != '') {
                        $('#company-name').show()
                    } else {
                        $('#company-name').hide()
                    }
                });
        }
        $('#client_id').change(function() {
            loadCompany($('#client_id').val());
        });
    </script>
@endpush
