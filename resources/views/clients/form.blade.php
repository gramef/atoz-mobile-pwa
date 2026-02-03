<div class="row">

{{--    @if (isset($client))--}}
{{--        {{ Form::hidden('is_organisation', $client->organisation ? 1 : 0) }}--}}
{{--    @else--}}
        <div class="col-12">
            <fieldset class="form__field mb-5">

                {{ Form::label('is_organisation', 'Type of client', ['class' => 'label form__label mb-3 required']) }}

                <ul class="list d-flex" id="radioButtons">
                    <li class="form__item mr-3">

                        @if(isset($client))
                            {{ Form::radio('is_organisation', 0, !request('organisation'),
                            ['id' => 'isNotOrganisation', 'class' => 'form__radio', 'data-link' => route('clients.edit',['client' => $client->id, 'organisation' => false])]) }}
                        @else
                            {{ Form::radio('is_organisation', 0, !request('organisation'),
                            ['id' => 'isNotOrganisation', 'class' => 'form__radio', 'data-link' => route('clients.create') ]) }}
                        @endif
                        {{ Form::label('isNotOrganisation', 'Individual', ['class' => 'label form__label form__label--radio text-gray']) }}

                    </li>
                    <li class="form__item">

                        @if(isset($client))
                            {{ Form::radio('is_organisation', 1, request('organisation'), ['id' => 'isOrganisation', 'class' => 'form__radio', 'data-link' => route('clients.edit', ['client' => $client->id, 'organisation' => true])]) }}
                        @else
                            {{ Form::radio('is_organisation', 1, request('organisation'), ['id' => 'isOrganisation', 'class' => 'form__radio', 'data-link' => route('clients.create', ['organisation' => true])]) }}
                        @endif
                        {{ Form::label('isOrganisation', 'Company', ['class' => 'label form__label form__label--radio text-gray']) }}

                    </li>
                </ul>
            </fieldset>
        </div>
{{--    @endif--}}

    <div class="col-lg-4">
        <fieldset class="form__field">

            {{ Form::label('title', 'Profile information', ['class' => 'label form__label required']) }}

            <div class="form__inputs">

                {{ Form::select('title', config('enums.titles'), isset($client) ? $client->user->title : null, [
                        'class' => 'input input--select form__input form__input--title',
                        'placeholder' => 'Title',
                        'required' => 'required'
                    ])
                }}

                {{ Form::text('first_name', isset($client) ? $client->user->first_name : null, [
                        'class' => 'input form__input',
                        'placeholder' => 'First name',
                        'required' => 'required'
                    ])
                }}

            </div>

            {{ Form::text('last_name', isset($client) ? $client->user->last_name : null, [
                    'class' => 'input form__input',
                    'placeholder' => 'Last name',
                    'required' => 'required'
                ])
            }}

            {{ Form::email('email', isset($client) ? $client->user->email : null, [
                    'class' => 'input form__input',
                    'placeholder' => 'Email address',
                    'required' => 'required'
                ])
            }}

            @role('client')
                {{ Form::password('password', ['class' => 'input form__input', 'placeholder' => 'Password']) }}
            @endrole

            {{ Form::text('contact_number', null, [
                    'class' => 'input form__input',
                    'placeholder' => 'Contact number',
                    'required' => 'required'
                ])
            }}
        </fieldset>
        <fieldset class="form__field">
            {{ Form::label('client_address_line_1', 'Account address', ['class' => 'label form__label required']) }}

            {{ Form::text('client_address_line_1', null, [
                    'class' => 'input form__input',
                    'placeholder' => 'Address line 1',
                    'required' => 'required',
                    'id' => 'client_address_line_1'
                ])
            }}

            {{ Form::text('client_address_line_2', null, [
                    'class' => 'input form__input',
                    'placeholder' => 'Address line 2',
                    'id' => 'client_address_line_2'
                ])
            }}

            {{ Form::text('client_county', null, [
                    'class' => 'input form__input',
                    'placeholder' => 'County',
                    'required' => 'required',
                    'id' => 'client_county'
                ])
            }}

            {{ Form::text('client_postcode', null, [
                    'class' => 'input form__input w-50',
                    'placeholder' => 'Postcode',
                    'required' => 'required',
                    'id' => 'client_postcode'
                ])
            }}

        </fieldset>
    </div>
    <div class="col-lg-4">

        @if (request('organisation'))

            <fieldset class="form__field">
                {{ Form::label('organisation_company', 'Company name', ['class' => 'label form__label required']) }}
                {{ Form::text('organisation_company', isset($client) && isset($client->organisation) ? $client->organisation->organisation_company : null, [
                        'class' => 'input form__input',
                        'placeholder' => 'Company name',
                        'required' => 'required'
                    ])
                }}
            </fieldset>
            <fieldset class="form__field">
                {{ Form::label('vat_number', 'VAT number', ['class' => 'label form__label']) }}
                {{ Form::text('vat_number', isset($client) && isset($client->organisation) ? $client->organisation->vat_number : null, [
                        'class' => 'input form__input',
                        'placeholder' => 'VAT Number'
                    ])
                }}
            </fieldset>
            <fieldset class="form__field">
                {{ Form::label('company_number', 'Company number', ['class' => 'label form__label required']) }}
                {{ Form::text('company_number', isset($client) && isset($client->organisation) ? $client->organisation->company_number : null, [
                        'class' => 'input form__input',
                        'placeholder' => 'Company number',
                        'required' => 'required'
                    ])
                }}
            </fieldset>

            @role('admin')
                <fieldset class="form__field">
                    {{ Form::label('company_id', 'Assign to a company group', ['class' => 'label form__label']) }}
                    {{ Form::select('company_id', $companies, isset($client) && isset($client->organisation) ? $client->organisation->company_id : null, [
                            'class' => 'input input--select form__input',
                            'placeholder' => 'Select Company group'
                        ])
                    }}
                </fieldset>
            @endrole

        @else
            <fieldset class="form__field">

                {{ Form::label('contact_method', 'Contact preferences', [
                        'class' => 'label form__label mb-3'
                    ])
                }}

                <ul class="list d-flex">
                    @foreach ($contactMethods as $key => $contactMethod)
                        <li class="form__item form__item--method {{ ($key + 1) != count($contactMethods) ? 'mr-3' : '' }}">

                            {{ Form::checkbox('contact_method[]', $contactMethod->id, isset($client) ? $client->contactMethods->contains($contactMethod) : null, ['class' => 'form__radio', 'id' => 'contact_method_' . $key]) }}

                            {{ Form::label('contact_method_' . $key, $contactMethod->contact_method, [
                                    'class' => 'label form__label form__label--radio'
                                ])
                            }}

                        </li>
                    @endforeach
                </ul>

            </fieldset>
            <fieldset class="form__field">
                {{ Form::label('always_requires_a_quote', 'Quote preference', ['class' => 'label mb-3 form__label']) }}
                <div class="d-flex align-items-center">
                    {{ Form::hidden('always_requires_a_quote', '0', ['id' => 'hidden_quote']) }}
                    {{ Form::checkbox('always_requires_a_quote', '1', null, [
                            'class' => 'form__radio',
                            'id' => 'always_requires_a_quote'
                        ])
                    }}
                    {{ Form::label('always_requires_a_quote', 'Always require a quote', ['class' => 'label m-0']) }}
                </div>
            </fieldset>
        @endif

    </div>

    @if (request('organisation'))
        <div class="col-lg-4">
            <fieldset class="form__field" id="invoiceInputs">

                <div class="d-flex justify-content-between form__label">
                    {{ Form::label('sms_method', 'Invoice address', ['class' => 'label form__label mb-0 required']) }}
                    <div class="d-flex align-items-center">
                        {{ Form::hidden('invoice_details_same_as_account', 0, null, ['id' => 'hidden_invoice_details_same_as_account']) }}
                        {{ Form::label('invoice_details_same_as_account', 'Same as account', ['class' => 'label text-gray mb-0 mr-3 form__label']) }}
                        {{ Form::checkbox('invoice_details_same_as_account', 1, null) }}
                    </div>
                </div>

                {{ Form::text('organisation_address_line_1', isset($client) && isset($client->organisation) ? $client->organisation->organisation_address_line_1 : null, [
                        'class' => 'input form__input',
                        'placeholder' => 'Address line 1',
                        'required' => 'required',
                        'id' => 'organisation_address_line_1'
                    ])
                }}

                {{ Form::text('organisation_address_line_2', isset($client) && isset($client->organisation) ? $client->organisation->organisation_address_line_2 : null, [
                        'class' => 'input form__input',
                        'placeholder' => 'Address line 2',
                        'id' => 'organisation_address_line_2'
                    ])
                }}

                {{ Form::text('organisation_county', isset($client) && isset($client->organisation) ? $client->organisation->organisation_county : null, [
                        'class' => 'input form__input',
                        'placeholder' => 'County',
                        'required' => 'required',
                        'id' => 'organisation_county'
                    ])
                }}

                {{ Form::text('organisation_postcode', isset($client) && isset($client->organisation) ? $client->organisation->organisation_postcode : null, [
                        'class' => 'input form__input w-50',
                        'placeholder' => 'Postcode',
                        'required' => 'required',
                        'id' => 'organisation_postcode'
                    ])
                }}

            </fieldset>
            <fieldset class="form__field">
                <div class="d-flex justify-content-between form__label">
                    {{ Form::label('sms_method', 'Invoice email address', ['class' => 'label form__label mb-0 required']) }}
                    <div class="d-flex align-items-center">
                        {{ Form::hidden('invoice_email_same_as_account', 0, null, ['id' => 'hidden_invoice_email_same_as_account']) }}
                        {{ Form::label('invoice_email_same_as_account', 'Same as account', ['class' => 'label text-gray mb-0 mr-3 form__label']) }}
                        {{ Form::checkbox('invoice_email_same_as_account', 1, null) }}
                    </div>
                </div>

                {{ Form::email('organisation_email', isset($client) && isset($client->organisation) ? $client->organisation->organisation_email : null, [
                        'class' => 'input form__input',
                        'placeholder' => 'Invoice Email address',
                        'required' => 'required',
                        'id' => 'invoiceEmailAddress'
                    ])
                }}
            </fieldset>
            <fieldset class="form__field">

                {{ Form::label('contact_method', 'Contact preferences', [
                        'class' => 'label form__label mb-3'
                    ])
                }}

                <ul class="list d-flex">
                    @foreach ($contactMethods as $key => $contactMethod)
                        <li class="form__item form__item--method {{ ($key + 1) != count($contactMethods) ? 'mr-3' : '' }}">

                            {{ Form::checkbox('contact_method[]', $contactMethod->id, isset($client) && isset($client->organisation) ? $client->contactMethods->contains($contactMethod) : null, ['class' => 'form__radio', 'id' => 'contact_method_' . $key]) }}

                            {{ Form::label('contact_method_' . $key, $contactMethod->contact_method, [
                                    'class' => 'label form__label form__label--radio'
                                ])
                            }}

                        </li>
                    @endforeach
                </ul>

            </fieldset>
            <fieldset class="form__field">
                {{ Form::label('always_requires_a_quote', 'Quote preference', ['class' => 'label mb-3 form__label']) }}
                <div class="d-flex align-items-center">
                    {{ Form::hidden('always_requires_a_quote', '0', ['id' => 'hidden_quote']) }}
                    {{ Form::checkbox('always_requires_a_quote', '1', null, [
                            'class' => 'form__radio',
                            'id' => 'always_requires_a_quote'
                        ])
                    }}
                    {{ Form::label('always_requires_a_quote', 'Always require a quote', ['class' => 'label m-0']) }}
                </div>
            </fieldset>
        </div>
    @endif

</div>

@push('scripts')
    <script>
        window.addEventListener('load', function() {
            $('#invoiceInputs input').not($('#invoice_details_same_as_account')).attr('disabled', $('#invoice_details_same_as_account').prop('checked'))
            $('#invoice_details_same_as_account').change(function() {
                $('#invoiceInputs input').not($(this)).attr('disabled', $(this).prop('checked'))
            })

            $('#invoiceEmailAddress').attr('disabled', $('#invoice_email_same_as_account').prop('checked'))
            $('#invoice_email_same_as_account').change(function() {
                $('#invoiceEmailAddress').attr('disabled', $(this).prop('checked'))
            })
        })

        $('#company_id').change(function() {
            if(confirm("Do you want to update the details with data for that Company Group.")) {

                let baseUrl = "{{ route('companies.show', ['company' => '*']) }}";

                $.ajax({
                    method: "POST",
                    url: baseUrl.replace('*', $('#company_id').val()),
                })
                .done(function(data) {
                    $('#company_number').val(data.company_number);
                    $('#organisation_address_line_1').val(data.address_line_1);
                    $('#organisation_address_line_2').val(data.address_line_2);
                    $('#organisation_county').val(data.county);
                    $('#organisation_postcode').val(data.postcode);

                    $('#client_address_line_1').val(data.address_line_1);
                    $('#client_address_line_2').val(data.address_line_2);
                    $('#client_county').val(data.county);
                    $('#client_postcode').val(data.postcode);


                    $('#vat_number').val(data.vat_number);
                    $('#invoiceEmailAddress').val(data.email);
                    $('#organisation_company').val(data.name);
                });
            }
        });
    </script>
@endpush
