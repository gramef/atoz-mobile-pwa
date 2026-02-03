@if (!isset($hideSubmit))
@endif
<hr>
<div class="hhddd">
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
                {{ Form::select('to_language_id', $languages, null, ['class' => 'input input--select form__input', 'required' => 'required', 'placeholder' => 'Choose language...']) }}
            </fieldset>
            <fieldset class="form__field">
                {{ Form::label('skill_id', 'Interpreter details:', ['class' => 'label form__label required']) }}
                {{ Form::select('skill_id', $skills, null, [
                    'class' => 'input input--select form__input',
                    'required' => 'required',
                    'placeholder' => 'Choose service type'
                ]) }}
                {{ Form::select(
                    'require_qualified',
                    [
                        1 => 'Court Qualified Interpreter',
                        2 => 'Community interpreter',
                        3 => 'Level 2,3,4 Community interpreter',
                        4 => 'Qualified Translator',
                    ],
                    null,
                    [
                        'class' => 'input input--select form__input',
                        'required' => 'required',
                        'placeholder' => 'Qualified or Community'
                    ]
                ) }}



                {{ Form::select('security_type_id', $security_types, null, ['class' => 'input input--select form__input', 'required' => 'required', 'placeholder' => 'Choose Security Type', 'id' => 'security_type_id']) }}


                {{ Form::select('gender', config('enums.genders'), null, [
                    'class' => 'input input--select form__input form__input--small',
                    'required' => 'required',
                    'placeholder' => 'Select gender'
                ]) }}
            </fieldset>
            <div id="initial_fieldset">
                <fieldset class="form__field">
                    {{ Form::label('appointment_date', 'Appointment date and time:', ['class' => 'label form__label required']) }}
                    <div class="row">
                        <div class="col-sm-6">
                            {{ Form::text(
                                'appointment_date',
                                isset($interpreterJob) ? $interpreterJob->appointment_date->toDateString() : null,
                                [
                                    'class' => 'input form__input',
                                    'placeholder' => 'dd/mm/yyyy',
                                    'required' => 'required'
                                ]
                            ) }}
                        </div>
                        <div class="col-sm-6">
                            {{ Form::text('start_time', null, [
                                'class' => 'input form__input form__input--time',
                                'placeholder' => '00:00',
                                'required' => 'required',
                                'id' => 'start_time'
                            ]) }}
                        </div>
                    </div>
                </fieldset>
                <fieldset class="form__field">
                    {{ Form::label('duration_hours', 'Appointment duration:', ['class' => 'label form__label required']) }}
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form__input relative">
                                {{ Form::number('duration_hours', null, [
                                    'min' => 0,
                                    'placeholder' => '0',
                                    'class' => 'input'
                                ]) }}
                                <span class="form__after-input">Hours</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form__input relative">
                                {{ Form::number('duration_minutes', isset($interpreterJob) ? $interpreterJob->duration_minutes : 0, [
                                    'min' => 0,
                                    'max' => 59,
                                    'placeholder' => '00',
                                    'class' => 'input'
                                ]) }}
                                <span class="form__after-input">Minutes</span>
                            </div>
                        </div>
                    </div>
                </fieldset>
            </div>

        </div>
        <div class="col-lg-4">
            <fieldset class="form__field">
                {{ Form::label('address_line_1', 'Appointment address (optional):', ['class' => 'label form__label']) }}

                {{ Form::text('address_line_1', null, ['class' => 'input form__input', 'placeholder' => 'Enter address line 1']) }}
                <div id="suggestions-address_line_1"></div>
                {{ Form::text('address_line_2', null, ['class' => 'input form__input', 'placeholder' => 'Enter address line 2']) }}
                <div id="suggestions-address_line_2"></div>
                {{ Form::text('county', null, ['class' => 'input form__input', 'placeholder' => 'County']) }}
                <div id="suggestions-county"></div>
                {{ Form::text('postcode', null, ['class' => 'input form__input form__input--small', 'placeholder' => 'Postcode']) }}
                <div id="suggestions-postcode"></div>
            </fieldset>
            <fieldset class="form__field">
                {{ Form::label('department', 'Department (optional):', ['class' => 'label form__label']) }}
                {{ Form::text('department', null, ['class' => 'input form__input', 'placeholder' => 'Enter department name']) }}
            </fieldset>
            <fieldset class="form__field" id="contact-info">
                {{ Form::label('contact_information', 'Contact information of person on arrival:', ['class' => 'label form__label required']) }}
                <div class="d-flex align-items-center">
                    {{ Form::hidden('contact_information_is_same_as_account', 0, ['id' => 'hidden_contact_information_is_same_as_account']) }}
                    {{ Form::checkbox('contact_information_is_same_as_account', 1, null, ['id' => 'contact_information_is_same_as_account']) }}
                    {{ Form::label('contact_information_is_same_as_account', 'Tick here if same as client account', [
                        'class' => 'ml-2 label form__label text-gray m-0 user-select-none'
                    ]) }}
                </div>
                {{ Form::textarea('contact_information', null, [
                    'class' => 'input input--textarea form__input mt-3',
                    'placeholder' => 'Please enter name and contact info'
                ]) }}
            </fieldset>
        </div>
        <div class="col-lg-4">
		
            <fieldset class="form__field" id="service-user-inputs">
                {{ Form::label('user_title', 'Service user details', ['class' => 'label form__label']) }}
                <div class="d-flex align-items-center">
                    {{ Form::hidden('service_user_required', 0, ['id' => 'hidden_service_user_required']) }}
                    {{ Form::checkbox('service_user_required', 1, null, ['id' => 'service_user_required']) }}
                    {{ Form::label('service_user_required', 'Tick here if not required', ['class' => 'ml-2 label form__label text-gray m-0 user-select-none']) }}
                </div>
                <div class="form__inputs mt-3">
                    {{ Form::select('user_title', config('enums.titles'), null, [
                        'class' => 'input input--select form__input form__input--title',
                        'placeholder' => 'Title'
                    ]) }}
                    {{ Form::text('user_first_name', null, ['class' => 'input form__input', 'placeholder' => 'First name']) }}
                </div>
                {{ Form::text('user_last_name', null, ['class' => 'input form__input', 'placeholder' => 'Last name']) }}
                {{ Form::text('personal_identity_number', null, ['class' => 'input form__input', 'placeholder' => 'PID Number (if applicable)']) }}
                {{ Form::text('file_reference', null, ['class' => 'input form__input', 'placeholder' => 'File Reference Number']) }}
                {{ Form::text(
                    'date_of_birth',
                    isset($interpreterJob) ? optional($interpreterJob->date_of_birth)->toDateString() : null,
                    [
                        'class' => 'input form__input',
                        'placeholder' => 'Date of birth',
                        'id' => 'date_of_birth'
                    ]
                ) }}
            </fieldset>
			
            <fieldset class="form__field">
                {{ Form::label('special_requirements', 'Special instructions (optional):', ['class' => 'label form__label']) }}
                {{ Form::textarea('special_requirements', null, [
                    'class' => 'input input--textarea form__input mt-3',
                    'placeholder' => 'Please enter the name of interpreter if required or any other special instructions…'
                ]) }}
            </fieldset>
			
            <fieldset class="form__field">
                {{ Form::label('client_reference', 'Client Reference / PO / Cost Code (optional):', ['class' => 'label form__label']) }}
                {{ Form::text('client_reference', null, ['class' => 'input form__input', 'placeholder' => 'Please enter...']) }}
            </fieldset>

            <fieldset class="form__field">
                {{ Form::label('requested_agent_id', 'Request Specific Interpreter', ['class' => 'label form__label']) }}
                {{ Form::select('requested_agent_id', [], null, [
                    'class' => 'input input--select form__input agent--select',
                    'placeholder' => 'Select Interpreter',
                    'disabled' => 'disabled',
                    'data-job-type' => 'interpreter'
                ]) }}
            </fieldset>


            @php $hidePast=false; @endphp
            @if (!isset($hideSubmit))
                <fieldset class="form__field form__field--no-mb">

                    @if (isset($interpreterJob))
                        @can('update', $interpreterJob)
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
                var $serviceType = $('#skill_id'),
                    $contactInfo = $('#contact-info')

                $contactInfo.toggleClass('d-none', Boolean($serviceType.val() && $serviceType.val() != 1))

                $serviceType.change(function() {
                    $contactInfo.toggleClass('d-none', Boolean($(this).val() && $(this).val() != 1))
                })

                $('#service-user-inputs select, #service-user-inputs input').not('#service_user_required').attr(
                    'disabled', $('#service_user_required').prop('checked'))

                $('#service_user_required').change(function() {
                    $('#service-user-inputs select, #service-user-inputs input').not($(this)).attr('disabled',
                        $(this).prop('checked'))
                })

                $('#contact_information').attr('disabled', $('#contact_information_is_same_as_account').prop('checked'))

                $('#contact_information_is_same_as_account').change(function() {
                    $('#contact_information').attr('disabled', $(this).prop('checked'))
                })

                $('#start_time').flatpickr({
                    enableTime: true,
                    noCalendar: true,
                    dateFormat: "H:i:ss",
                    altFormat: 'H:i',
                    altInput: true,
                })

                $('#date_of_birth').flatpickr({
                    allowInput: true,
                    altInput: true,
                    altFormat: 'd/m/Y',
                })

                $('#appointment_date').flatpickr({
                    allowInput: true,
                    altInput: true,
                    altFormat: 'd/m/Y'
                    @if ($hidePast)
                        ,
                        disable: [
                            function(selectedDate) {
                                var today = new Date();
                                today.setHours(0, 0, 0, 0);
                                // return (selectedDate < today);
                                return false;
                            }
                        ]
                    @endif
                })
            })

            $('document').ready(function() {
                $('#company-name').hide()
                @if (isset($interpreterJob) && $interpreterJob->client->organisation)
                    loadCompany({{ $interpreterJob->client_id }});
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
            $(document).ready(function() {
                // Event listener for input fields
                $('input[name="address_line_1"], input[name="address_line_2"], input[name="county"], input[name="postcode"]')
                    .on('input', function() {
                        let query = $(this).val();
                        let field = $(this).attr('name'); // Get the name of the input field
                        let suggestionsDiv = `#suggestions-${field}`;

                        if (query.length > 1) {
                            $.ajax({
                                url: "{{ route('getaddress') }}", // Ensure this route is correctly defined in your routes file
                                method: "GET",
                                data: {
                                    query: query,
                                    field: field
                                },
                                success: function(data) {
                                    let suggestions = data.map(address =>
                                        `<div class="suggestion-item" data-address='${JSON.stringify(address)}'>
                                ${address.address_line_1} ${address.address_line_2} ${address.county} ${address.postcode}
                            </div>`
                                    );
                                    $(suggestionsDiv).html(suggestions.join(''));
                                }
                            });
                        } else {
                            $(suggestionsDiv).html('');
                        }
                    });

                // Event listener for suggestion clicks
                $(document).on('click', '.suggestion-item', function() {
                    let address = $(this).data('address'); // Get the address data
                    $('input[name="address_line_1"]').val(address.address_line_1 || '');
                    $('input[name="address_line_2"]').val(address.address_line_2 || '');
                    $('input[name="county"]').val(address.county || '');
                    $('input[name="postcode"]').val(address.postcode || '');
                    $(this).parent().html(''); // Clear suggestions
                });
            });
        </script>
    @endpush
