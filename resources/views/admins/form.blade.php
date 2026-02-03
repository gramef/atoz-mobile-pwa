<div class="row">
    <div class="col-lg-4">
        <fieldset class="form__field">
            <div class="form__inputs">

                {{ Form::select('title', config('enums.titles'), null, ['class' => 'input input--select form__input form__input--title', 'required' => 'required', 'placeholder' => 'Title']) }}

                {{ Form::text('first_name', null, ['class' => 'input form__input', 'placeholder' => 'First name', 'required' => 'required']) }}

            </div>

            {{ Form::text('last_name', null, ['class' => 'input form__input', 'placeholder' => 'Last name', 'required' => 'required']) }}

            {{ Form::email('email', null, ['class' => 'input form__input', 'placeholder' => 'Email address', 'required' => 'required']) }}

        </fieldset>
        <fieldset class="form__field">

            {{ Form::submit('Submit', ['class' => 'btn btn--primary form__btn']) }}

        </fieldset>
    </div>
</div>