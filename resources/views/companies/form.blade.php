<div class="row">
    <div class="col-lg-4">
        <fieldset class="form__field">
            {{ Form::label('name', 'Company Name', ['class' => 'label form__label required']) }}
            {{ Form::text('name', null, ['class' => 'input form__input', 'placeholder' => 'Name', 'required' => 'required']) }}
        </fieldset>
        <fieldset class="form__field">
            {{ Form::label('company_number', 'Company Number', ['class' => 'label form__label']) }}
            {{ Form::text('company_number', null, ['class' => 'input form__input', 'placeholder' => 'Company Number']) }}
        </fieldset>
        <fieldset class="form__field">
            {{ Form::label('vat_number', 'VAT Number', ['class' => 'label form__label']) }}
            {{ Form::text('vat_number', null, ['class' => 'input form__input', 'placeholder' => 'VAT Number']) }}
        </fieldset>
        <fieldset class="form__field">
            {{ Form::label('email', 'Email Address', ['class' => 'label form__label']) }}
            {{ Form::email('email', null, ['class' => 'input form__input', 'placeholder' => 'Email']) }}
        </fieldset>
    </div>

    <div class="col-lg-4">
        <fieldset class="form__field">
            {{ Form::label('address_line_1', 'Company Address', ['class' => 'label form__label']) }}

            {{ Form::text('address_line_1', null, [
                    'class' => 'input form__input',
                    'placeholder' => 'Address line 1',
                    'id' => 'address_line_1'
                ])
            }}

            {{ Form::text('address_line_2', null, [
                    'class' => 'input form__input',
                    'placeholder' => 'Address line 2',
                    'id' => 'address_line_2'
                ])
            }}

            {{ Form::text('county', null, [
                    'class' => 'input form__input',
                    'placeholder' => 'County',
                    'id' => 'county'
                ])
            }}

            {{ Form::text('postcode', null, [
                    'class' => 'input form__input w-50',
                    'placeholder' => 'Postcode',
                    'id' => 'postcode'
                ])
            }}
        </fieldset>

        <fieldset class="form-group text-right">
            {{ Form::submit('Submit', ['class' => 'btn btn--primary form__btn']) }}
        </fieldset>
    </div>
</div>
