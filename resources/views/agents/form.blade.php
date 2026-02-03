<div class="row">
    <div class="col-lg-4" id="userFields">
        <fieldset class="form__field">

            {{ Form::label('title', 'Profile information', ['class' => 'label form__label required']) }}

            <div class="form__inputs">

                {{ Form::select('title', config('enums.titles'), $agent->user->title ?? null, ['class' => 'input input--select form__input form__input--title', 'required' => 'required', 'placeholder' => 'Title']) }}

                @role('new-agent')
                    {{ Form::text('first_name', auth()->user()->first_name ?? null, ['class' => 'input form__input', 'placeholder' => 'First name', 'required' => 'required']) }}
                @else
                    {{ Form::text('first_name', $agent->user->first_name ?? null, ['class' => 'input form__input', 'placeholder' => 'First name', 'required' => 'required']) }}
                @endrole

            </div>

            @role('new-agent')
                {{ Form::text('last_name', auth()->user()->last_name ?? null, ['class' => 'input form__input', 'placeholder' => 'Last name', 'required' => 'required']) }}
                {{ Form::email('email', auth()->user()->email ?? null, ['class' => 'input form__input', 'placeholder' => 'Email address', 'required' => 'required']) }}
            @else
                {{ Form::text('last_name', $agent->user->last_name ?? null, ['class' => 'input form__input', 'placeholder' => 'Last name', 'required' => 'required']) }}
                {{ Form::email('email', $agent->user->email ?? null, ['class' => 'input form__input', 'placeholder' => 'Email address', 'required' => 'required']) }}
            @endrole

            @hasanyrole('new-agent|agent')
                {{ Form::password('password', ['class' => 'input form__input', 'placeholder' => 'Password']) }}
            @endhasanyrole

            {{ Form::text('contact_number', null, ['class' => 'input form__input', 'placeholder' => 'Contact number', 'required' => 'required']) }}

        </fieldset>
        <fieldset class="form__field">

            {{ Form::label('gender', 'Gender', ['class' => 'label form__label required']) }}

            <ul class="choices">
                <li class="choices__choice">
                    {{ Form::radio('gender', 0, null, ['class' => 'choices__radio', 'required' => 'required']) }}
                    {{ Form::label('gender', 'Male', ['class' => 'label choices__label']) }}
                </li>
                <li class="choices__choice choices__choice--last">
                    {{ Form::radio('gender', 1, null, ['class' => 'choices__radio', 'required' => 'required']) }}
                    {{ Form::label('gender', 'Female', ['class' => 'label choices__label']) }}
                </li>
            </ul>
        </fieldset>
        </fieldset>
        <fieldset class="form__field">

            {{ Form::label('address_line_1', 'Address', ['class' => 'label form__label required']) }}

            {{ Form::text('address_line_1', old('address_line_1'), ['class' => 'input form__input', 'placeholder' => 'Address line 1', 'required' => 'required']) }}

            {{ Form::text('address_line_2', old('address_line_2'), ['class' => 'input form__input', 'placeholder' => 'Address line 2']) }}

            {{ Form::text('county', old('county'), ['class' => 'input form__input', 'placeholder' => 'County', 'required' => 'required']) }}

            {{ Form::text('postcode', old('postcode'), ['class' => 'input form__input form__input--small', 'placeholder' => 'Postcode', 'required' => 'required']) }}

        </fieldset>
        <fieldset class="form__field mb-lg-0" id="affidavitAndAffirmation">
            <div class="form__long-field">
                {{ Form::label('can_provide_affidavit', 'Can you provide a sworn affidavit?', ['class' => 'label form__label form__label--long required']) }}
                <ul class="choices">
                    <li class="choices__choice">
                        {{ Form::radio('can_provide_affidavit', 0, null, ['class' => 'choices__radio']) }}
                        {{ Form::label('can_provide_affidavit', 'No', ['class' => 'label choices__label']) }}
                    </li>
                    <li class="choices__choice">
                        {{ Form::radio('can_provide_affidavit', 1, null, ['class' => 'choices__radio']) }}
                        {{ Form::label('can_provide_affidavit', 'Yes', ['class' => 'label choices__label']) }}
                    </li>
                </ul>
            </div>
            <div class="form__long-field">
                {{ Form::label('can_provide_affirmation', 'Can you provide a sworn affirmation?', ['class' => 'label form__label form__label--long required']) }}
                <ul class="choices">
                    <li class="choices__choice">
                        {{ Form::radio('can_provide_affirmation', 0, null, [ 'class' => 'choices__radio' ]) }}
                        {{ Form::label('can_provide_affirmation', 'No', ['class' => 'label choices__label']) }}
                    </li>
                    <li class="choices__choice">
                        {{ Form::radio('can_provide_affirmation', 1, null, [ 'class' => 'choices__radio' ]) }}
                        {{ Form::label('can_provide_affirmation', 'Yes', [ 'class' => 'label choices__label' ]) }}
                    </li>
                </ul>
            </div>
        </fieldset>
    </div>
    <div class="col-lg-4 agent-fields">
        <fieldset class="form__field">

            {{ Form::label('interpreter_types[]', 'Interpreter Type:', ['class' => 'label form__label required']) }}

            @foreach (App\Agent::$agentTypes as $key => $agentType)
                <div class="form__item form__item--method">

                    {{ Form::checkbox('interpreter_types[]', $key, isset($agent) ? $agent->user->getRoleNames()->contains($key) : null, [
                            'class' => 'form__radio interpreter-type'
                        ])
                    }}
                    {{ Form::label('interpreter_types', $agentType, ['class' => 'label form__label form__label--radio']) }}

                </div>
            @endforeach

        </fieldset>
        <fieldset class="form__field skills">

            {{ Form::label('skills[]', 'Skills / Services', ['class' => 'label form__label required']) }}

            <div class="row">

                @foreach ($skills as $skill)
                    <div class="form__item form__item--method col-lg-6 skill" data-type="{{ $skill->getSkillType() }}">
                        {{ Form::checkbox('skills[]', $skill->id, isset($agent) ? $agent->skills->contains($skill) : null, [
                                'class' => 'form__radio',
                            ])
                        }}
                        {{ Form::label('skills', $skill->skill, ['class' => 'label form__label form__label--radio']) }}
                    </div>
                @endforeach

            </div>
        </fieldset>
        <fieldset class="form__field">

            {{ Form::label('languages[]', 'Please add your languages', ['class' => 'label form__label required']) }}
            <p>If you don't see your language you can type the name of the language and press enter to add it</p>
            {{ Form::select('languages[]', $languages, isset($agent) ? $agent->languages : null, [
                    'class' => 'input input--select form__input',
                    'required' => 'required',
                    'id' => 'languagesSelect',
                    'multiple' => 'multiple'
                ])
            }}

        </fieldset>
        <fieldset class="form__field">

            {{ Form::label('skype_details', 'Skype details', [ 'class' => 'label form__label' ]) }}
            {{ Form::textarea('skype_details', null, [ 'class' => 'input input--textarea form__input', 'placeholder' => 'Skype details' ]) }}

        </fieldset>
    </div>
    <div class="col-lg-4 agent-fields">

        <fieldset class="form__field">

            @if (isset($agent))
                <img class="img-fluid" src="{{ $agent->getProfilePicture() }}" alt="Profile Picture">
            @endif

        </fieldset>
        <fieldset class="form__field">

            {{ Form::label('documents', 'Please upload a profile picture', ['class' => 'label form__label required']) }}

            @if (isset($agent))
                <document-dropzone url="/api/documents" input-name="profile_picture" profile-picture="{{ $agent->profile_picture }}"></document-dropzone>
            @else
                <document-dropzone url="/api/documents" input-name="profile_picture"></document-dropzone>
            @endif

        </fieldset>

        <fieldset class="form__field">

            {{ Form::label('contact_method', 'Please select how you would like to be notified', ['class' => 'label form__label']) }}

            @foreach ($contactMethods as $key => $contactMethod)
                <div class="form__item form__item--method">
                    {{ Form::checkbox('contact_method[]', $contactMethod->id, isset($agent) ? $agent->contactMethods->contains($contactMethod) : $key == 0, ['class' => 'form__radio']) }}
                    {{ Form::label('contact_method', $contactMethod->contact_method, ['class' => 'label form__label form__label--radio']) }}
                </div>
            @endforeach

        </fieldset>

        @role('admin')
            <fieldset class="form__field">

                {{ Form::label('restrict_job_notifications', 'Notifications:', ['class' => 'label form__label']) }}

                <div class="form__item form__item--method">
                    {{ Form::hidden('restrict_job_notifications', 0) }}
                    {{ Form::checkbox('restrict_job_notifications', 1, null, ['class' => 'form__radio']) }}
                    {{ Form::label('restrict_job_notifications', 'Restrict job notifications', ['class' => 'label form__label form__label--radio']) }}
                </div>
            </fieldset>
        @endrole

    </div>
</div>

@push('scripts')
    <script>
        window.addEventListener('load', function () {

            var selectedTypes = $('.interpreter-type:checked').map(function(index, element) {
                return $(element).val().indexOf('interpreter') > -1 ? 'interpreter' : $(element).val()
            }).toArray()

            showSkills(selectedTypes)

            $('#affidavitAndAffirmation').toggleClass('d-none', selectedTypes.indexOf('translator') === -1 && selectedTypes.length > 0)

            $('.interpreter-type').change(function() {

                if ($(this).prop('checked')) {
                    selectedTypes.push(
                        $(this).val().indexOf('interpreter') > -1 ? 'interpreter' : $(this).val()
                    )
                } else {
                    selectedTypes.splice(selectedTypes.indexOf($(this).val()), 1)
                }

                $('#affidavitAndAffirmation').toggleClass('d-none', selectedTypes.indexOf('translator') === -1 && selectedTypes.length > 0)

                showSkills(selectedTypes)
            })

            function showSkills(selectedTypes) {
                if (selectedTypes.length === 0) {
                    $('.skill').removeClass('d-none')
                    return false
                }

                $('.skill').each(function(index, element) {
                    var skillWasNotSelected = !(selectedTypes.indexOf($(element).data('type')) > -1)
                    $(element).toggleClass('d-none', skillWasNotSelected)
                })
            }

            @if (auth()->user()->hasRole('agent'))
                $('.agent-fields input, .agent-fields select, .agent-fields textarea, .dz-hidden-input, #can_provide_affidavit, #can_provide_affirmation, .choices input')
                    .attr('disabled', true)
            @endif
        })
    </script>
@endpush
