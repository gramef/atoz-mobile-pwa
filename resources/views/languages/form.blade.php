<div class="row">
    <div class="col-lg-4">
        <fieldset class="form__field">
            {{ Form::label('name', 'Name', ['class' => 'label form__label required']) }}
            {{ Form::text('name', null, ['class' => 'input form__input', 'placeholder' => 'Name', 'required' => 'required']) }}
        </fieldset>
    </div>

    <div class="col-lg-4">
        <fieldset class="form-group text-right">
            {{ Form::submit('Submit', ['class' => 'btn btn--primary form__btn']) }}
        </fieldset>
    </div>
</div>
@if(isset($language))
    @if($language->agents()->count())
        <h2>Agents</h2>
        <section class="content__table">
            <table class="table">
                <thead class="table__header">
                    <tr>
                        <th class="table__heading">Agent Name</th>
                        <th class="table__heading">Agent type</th>
                        <th class="table__heading">Email</th>
                        <th class="table__heading">Contact Number</th>
                        <th class="table__heading">Languages</th>
                        <th class="table__heading"></th>
                    </tr>
                </thead>
                <tbody>

                    @foreach ($language->agents as $agent)
                        <tr class="table__row">
                            <td class="table__data">{{ $agent->user->getFullName() }}</td>
                            <td class="table__data">{{ $agent->getAgentType() }}</td>
                            <td class="table__data">
                                <a class="table__link" href="mailto:{{ $agent->user->email }}">
                                    {{ $agent->user->email }}
                                </a>
                            </td>
                            <td class="table__data">{{ $agent->contact_number }}</td>
                            <td class="table__data">{{ implode(', ', $agent->languages->pluck('name')->all()) }}</td>
                            <td class="table__data table__data--actions">
                                <a class="btn btn--secondary table__btn" href="{{ route('agents.edit', $agent->id) }}">View</a>
                            </td>

                        </tr>
                    @endforeach


                </tbody>
            </table>
        </section>
    @endif

    @if($language->toInterpreterJob()->count() || $language->fromInterpreterJob()->count())
        <h2>Interpreter Jobs</h2>
        <section class="content__table">
            <table class="table">
                <thead class="table__header">
                <tr>
                    <th class="table__heading">Ref</th>
                    <th class="table__heading">Submitted</th>
                    <th class="table__heading">Appt. Date</th>
                    <th class="table__heading">Appt. Time</th>
                    <th class="table__heading">Duration</th>
                    <th class="table__heading">Language</th>
                    <th class="table__heading">Company Name</th>
                    <th class="table__heading">Posted By</th>
                    <th class="table__heading">Agent Details</th>
                    <th class="table__heading">Status</th>
                    <th class="table__heading"></th>
                </tr>
                </thead>
                <tbody>
                    @foreach ($language->toInterpreterJob as $job)
                        <tr class="table__row">
                            <td class="table__data">{{ $job->reference }}</td>
                            <td class="table__data">{{ $job->created_at->format('d/m/Y') }}</td>
                            <td class="table__data">{{ $job->appointment_date->format('d/m/Y') }}</td>
                            <td class="table__data">{{ $job->start_time }}</td>
                            <td class="table__data">{{ $job->formattedDuration }}</td>
                            <td class="table__data">
                                {{ $job->toLanguage->name }}
                                @if($job->toLanguage->trashed())
                                    <br/><small>Deleted</small>
                                @endif
                            </td>
                            <td class="table__data">{{ $job->client->organisation->company->name ?? 'N/A' }}</td>
                            <td class="table__data">{{ $job->client->user->getFullName() }}</td>
                            <td class="table__data">
                                @if ($job->agent)
                                    {{ $job->agent->user->getFullName() }}
                                @endif
                            </td>
                            <td class="table__data status status--{{ $job->getStatusForAgent() ?? $job->statusName }}">
                                {{ $job->statusName }}
                            </td>
                            <td class="table__data table__data--actions">
                                <a class="btn btn--secondary table__btn ml-1" href="{{ route('interpreter-jobs.edit', $job) }}">View</a>
                            </td>
                        </tr>
                    @endforeach
                    @foreach ($language->fromInterpreterJob as $job)
                        <tr class="table__row">
                            <td class="table__data">{{ $job->reference }}</td>
                            <td class="table__data">{{ $job->created_at->format('d/m/Y') }}</td>
                            <td class="table__data">{{ $job->appointment_date->format('d/m/Y') }}</td>
                            <td class="table__data">{{ $job->start_time }}</td>
                            <td class="table__data">{{ $job->formattedDuration }}</td>
                            <td class="table__data">
                                {{ $job->toLanguage->name }}
                                @if($job->toLanguage->trashed())
                                    <br/><small>Deleted</small>
                                @endif
                            </td>
                            <td class="table__data">{{ $job->client->organisation->company->name ?? 'N/A' }}</td>
                            <td class="table__data">{{ $job->client->user->getFullName() }}</td>
                            <td class="table__data">
                                @if ($job->agent)
                                    {{ $job->agent->user->getFullName() }}
                                @endif
                            </td>
                            <td class="table__data status status--{{ $job->getStatusForAgent() ?? $job->statusName }}">
                                {{ $job->statusName }}
                            </td>
                            <td class="table__data table__data--actions">
                                <a class="btn btn--secondary table__btn ml-1" href="{{ route('interpreter-jobs.edit', $job) }}">View</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>
    @endif

    @if($language->toTranslatorJob()->count() || $language->fromTranslatorJob()->count())
        <h2>Translator Jobs</h2>
        <section class="content__table">
            <table class="table">
                <thead class="table__header">
                    <tr>
                        <th class="table__heading">Ref</th>
                        <th class="table__heading">Submitted</th>
                        <th class="table__heading">Delivery Date</th>
                        <th class="table__heading">Word Count</th>
                        <th class="table__heading">Languages</th>
                        <th class="table__heading">Affirmation</th>
                        <th class="table__heading">Affidavit</th>
                        <th class="table__heading">Company Name</th>
                        <th class="table__heading">Posted By</th>
                        <th class="table__heading">Status</th>
                        <th class="table__heading"></th>
                    </tr>
                </thead>
                <tbody>
                @foreach ($language->toTranslatorJob as $job)
                    <tr class="table__row">
                        <td class="table__data">{{ $job->reference }}</td>
                        <td class="table__data">{{ $job->created_at->format('d/m/Y') }}</td>
                        <td class="table__data">{{ $job->target_date->format('d/m/Y') }}</td>
                        <td class="table__data">{{ $job->word_count }}</td>
                        <td class="table__data">
                            {{ $job->fromLanguage->name }}
                            @if($job->fromLanguage->trashed())
                                <small>Deleted</small>
                            @endif
                            - {{ $job->toLanguage->name }}
                            @if($job->toLanguage->trashed())
                                <small>Deleted</small>
                            @endif
                        </td>
                        <td class="table__data">{{ $job->affirmation ? 'Yes' : 'No' }}</td>
                        <td class="table__data">{{ $job->affidavit ? 'Yes' : 'No' }}</td>
                        <td class="table__data">{{ $job->client->organisation->company->name ?? 'N/A' }}</td>
                        <td class="table__data">{{ $job->client->user->getFullName() }}</td>
                        <td class="table__data status status--{{ $job->getStatusForAgent() ?? $job->statusName }}">
                            {{ $job->statusName }}
                        </td>
                        <td class="table__data table__data--actions">
                            <a class="btn btn--secondary table__btn" href="{{ route('translator-jobs.edit', $job) }}">View</a>
                        </td>
                    </tr>
                @endforeach
                @foreach ($language->fromTranslatorJob as $job)
                    <tr class="table__row">
                        <td class="table__data">{{ $job->reference }}</td>
                        <td class="table__data">{{ $job->created_at->format('d/m/Y') }}</td>
                        <td class="table__data">{{ $job->target_date->format('d/m/Y') }}</td>
                        <td class="table__data">{{ $job->word_count }}</td>
                        <td class="table__data">
                            {{ $job->fromLanguage->name }}
                            @if($job->fromLanguage->trashed())
                                <small>Deleted</small>
                            @endif
                            - {{ $job->toLanguage->name }}
                            @if($job->toLanguage->trashed())
                                <small>Deleted</small>
                            @endif
                        </td>
                        <td class="table__data">{{ $job->affirmation ? 'Yes' : 'No' }}</td>
                        <td class="table__data">{{ $job->affidavit ? 'Yes' : 'No' }}</td>
                        <td class="table__data">{{ $job->client->organisation->company->name ?? 'N/A' }}</td>
                        <td class="table__data">{{ $job->client->user->getFullName() }}</td>
                        <td class="table__data status status--{{ $job->getStatusForAgent() ?? $job->statusName }}">
                            {{ $job->statusName }}
                        </td>
                        <td class="table__data table__data--actions">
                            <a class="btn btn--secondary table__btn" href="{{ route('translator-jobs.edit', $job) }}">View</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </section>
    @endif
@endif
