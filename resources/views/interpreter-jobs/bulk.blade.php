@if (!isset($hideSubmit))
    <fieldset class="form__field form__field--no-mb">
        @if (isset($interpreterJob))
            @can('update', $interpreterJob)
                @php $hidePast=true; @endphp
            @endcan
        @else
            @php $hidePast=true; @endphp


            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <!-- jQuery to handle visibility -->
        @endif

    </fieldset>
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
                    'placeholder' => 'Choose service type',
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
                        'placeholder' => 'Qualified or Community',
                        'id' => 'require_qualified',
                    ],
                ) }}



                {{ Form::select('security_type_id', $security_types, null, ['class' => 'input input--select form__input', 'required' => 'required', 'placeholder' => 'Choose Security Type', 'id' => 'security_type_id']) }}


                {{ Form::select('gender', config('enums.genders'), null, [
                    'class' => 'input input--select form__input form__input--small',
                    'required' => 'required',
                    'placeholder' => 'Select gender',
                    'id' => 'gender',
                ]) }}
            </fieldset>

            <div id="initial_fieldset_for_bulk_job">
                <!-- Container to append additional duplicates -->
                <div id="duplicate_fields"></div>
                <button type="button" class="btn btn--secondary form__btn" style="width:100%" onclick="addInputs()">Add
                    More</button>
                <hr>
                <!-- Include JavaScript -->
                <script>
                    let inputCount = 0;
                    const duplicateCount = document.getElementById('bulk_number');


                    function addInputs() {
                        const container = document.getElementById('duplicate_fields');
                        inputCount++;
                        const html = `
                         <div class="input-group">
                                <fieldset class="form__field">
                                        <label for="appointment_date_${inputCount}" class="label form__label required">Appointment date and time:</label>
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <input type="text" name="appointment_date[]" id="appointment_date_${inputCount}" class="input form__input date-picker appointment_date" placeholder="dd/mm/yyyy" required="required" />
                                            </div>
                                            <div class="col-sm-6">
                                                <input type="text" name="start_time[]" id="start_time_${inputCount}" class="input form__input form__input--time time-picker start_time" placeholder="00:00" required="required" />
                                            </div>
                                        </div>
                                    </fieldset>
                                    <fieldset class="form__field">
                                        <label for="duration_hours_${inputCount}" class="label form__label required">Appointment duration:</label>
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="form__input relative">
                                                    <input type="number" name="duration_hours[]" id="duration_hours_${inputCount}" min="0" placeholder="0" class="input duration_hours" />
                                                    <span class="form__after-input">Hours</span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form__input relative">
                                                    <input type="number" name="duration_minutes[]" id="duration_minutes_${inputCount}" min="0" max="59" value="0" placeholder="00" class="input duration_minutes" />
                                                    <span class="form__after-input">Minutes</span>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                               <label for="requested_agent_id" class="label form__label">Request Specific Interpreter</label>
                                        <select name="requested_agent_id[]" class="input input--select form__input agent--select select2"
                                            data-job-type="interpreter" style="width:100%;">
                                            <option value="">Select Interpreter</option>
                                            ${[...Array(29).keys()].map(i => `<option value="${i + 2}">${i + 2}</option>`).join('')}
                                        </select>
                                            </div>
                                        </div>
                                
                                       
                                    </fieldset>
                                  
                                      
                                        
                        </div>
                                        <hr>`;
                        container.insertAdjacentHTML('beforeend', html);

                        initializeSelect2();
                        initializeFlatpickr();
                    }

                    function displayValues(button) {
                        const group = button.parentElement;
                        const inputs = group.querySelectorAll("input");
                        const select = group.querySelector("select");
                        alert(`Date: ${inputs[0].value}, Time: ${inputs[1].value}, Selected: ${select.value}`);
                    }

                    function initializeSelect2() {
                        $(".select2").each(function() {
                            let selectElement = this;
                            $(selectElement).select2({
                                width: "100%",
                                // placeholder: "Select Interpreter",
                                // allowClear: true

                                width: "100%",
                                ajax: {
                                    delay: 500,
                                    url: "/api/available-interpreters/interpreter",
                                    data: function(params) {
                                        const group = $(selectElement).closest(".input-group");

                                        let to_language_id = document.getElementById('to_language_id')?.value || "";
                                        let skill_id = document.getElementById('skill_id')?.value || "";
                                        let require_qualified = document.getElementById('require_qualified')
                                            ?.value || "";
                                        let security_type_id = document.getElementById('security_type_id')
                                            ?.value || "";
                                        let gender = document.getElementById('gender')?.value || "";
                                        let appointment_date = group.find(".appointment_date").val() || "";
                                        let start_time = group.find(".start_time").val() || "";
                                        let duration_hours = group.find(".duration_hours").val() || "";
                                        let duration_minutes = group.find(".duration_minutes").val() || "";

                                        // Convert values
                                        to_language_id = to_language_id ? parseInt(to_language_id) : null;
                                        skill_id = skill_id ? parseInt(skill_id) : null;
                                        require_qualified = require_qualified ? parseInt(require_qualified) : null;
                                        gender = gender ? parseInt(gender) : null;
                                        duration_hours = duration_hours ? parseInt(duration_hours) : 0;
                                        duration_minutes = duration_minutes ? parseInt(duration_minutes) : 0;

                                        // Validate required fields
                                        if (!to_language_id || !skill_id || !require_qualified || gender > 2) {
                                            alert("Language, skill, qualification and gender are required");
                                            return false;
                                        }
                                        // Validate required fields
                                        if (!appointment_date || !start_time) {
                                            alert("Appointment date and time are required!");
                                            return false;
                                        }


                                        return {
                                            to_language_id,
                                            skill_id,
                                            require_qualified,
                                            security_type_id,
                                            gender,
                                            appointment_date,
                                            start_time,
                                            duration_hours,
                                            duration_minutes,
                                            search: params.term || "",
                                            page: params.page || 1
                                        };
                                    },
                                    processResults: function(data, params) {
                                        params.page = params.page || 1;
                                        console.log(data)
                                        return {
                                            results: data.data.map(item => ({
                                                id: item.id,
                                                text: item.user.fullName
                                            })),
                                            pagination: {
                                                more: data.last_page !== data.current_page
                                            }
                                        };
                                    }
                                }

                            });

                            // Attach event listener for select2:open
                            $(selectElement).on("select2:open", function() {

                            });
                        });

                    }

                    function initializeFlatpickr() {
                        document.querySelectorAll(".date-picker").forEach(el => {
                            flatpickr(el, {
                                dateFormat: "Y-m-d"
                            });
                        });
                        document.querySelectorAll(".time-picker").forEach(el => {
                            flatpickr(el, {
                                enableTime: true,
                                noCalendar: true,
                                dateFormat: "H:i:ss",
                                altFormat: 'H:i',
                            });
                        });
                    }

                    function fetchInterpreter(selectElement) {
                        const to_language_id = document.getElementById('to_language_id')?.value;
                        const skill_id = document.getElementById('skill_id')?.value;
                        const require_qualified = document.getElementById('require_qualified')?.value;
                        const gender = document.getElementById('gender')?.value;

                        // Find closest group
                        const group = selectElement.closest(".input-group");
                        const appointment_date = group?.querySelector(".appointment_date")?.value;
                        const start_time = group?.querySelector(".start_time")?.value;
                        const duration_hours = group?.querySelector(".duration_hours")?.value;
                        const duration_minutes = group?.querySelector(".duration_minutes")?.value;
                        const selectedOption = selectElement.value;

                        console.log({
                            to_language_id,
                            skill_id,
                            require_qualified,
                            gender,
                            appointment_date,
                            start_time,
                            duration_hours,
                            duration_minutes,
                        });
                        //   const page = query.page || 1;


                    }

                    // Add the first input group on page load
                    window.onload = function() {
                        addInputs();
                    };
                </script>
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
                        'class' => 'ml-2 label form__label text-gray m-0 user-select-none',
                    ]) }}
                </div>
                {{ Form::textarea('contact_information', null, [
                    'class' => 'input input--textarea form__input mt-3',
                    'placeholder' => 'Please enter name and contact info',
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
                        'placeholder' => 'Title',
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
                        'id' => 'date_of_birth',
                    ],
                ) }}
            </fieldset>
            <fieldset class="form__field">
                {{ Form::label('special_requirements', 'Special instructions (optional):', ['class' => 'label form__label']) }}
                {{ Form::textarea('special_requirements', null, [
                    'class' => 'input input--textarea form__input mt-3',
                    'placeholder' => 'Please enter the name of interpreter if required or any other special instructions…',
                ]) }}
            </fieldset>
            <fieldset class="form__field">
                {{ Form::label('client_reference', 'Client Reference / PO / Cost Code (optional):', ['class' => 'label form__label']) }}
                {{ Form::text('client_reference', null, ['class' => 'input form__input', 'placeholder' => 'Please enter...']) }}
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
