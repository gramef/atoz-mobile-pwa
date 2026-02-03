@extends('layouts.app')

@section('content')

    <nav class="nav">
        <h1 class="nav__heading">Job Report</h1>
        <span class="badge bg-light text-dark text-bold">Total Jobs: {{ $count }}</span>
        <span class="badge bg-light text-dark text-bold">Total Interpreter Jobs: {{ $countInterpreterJob }}</span>
        <span class="badge bg-light text-dark text-bold">Total Tranlator Jobs: {{ $countTranslatorJob }}</span>
        <ul class="links">
            <li class="links__item">
                <div class="links__text">All Jobs</div>
            </li>

        </ul>
        <section class="job__actions ">
            @role('admin')
                <a href="{{ route('report-export', request()->query()) }}" class="btn btn--secondary nav__btn">Export
                    CSV</a>
            @endrole

        </section>
    </nav>

    @include('partials.job-filter-form')

    <section class="content__table">
        <table class="table">
            {{-- <thead class="table__header">
                <tr>
                    <th class="table__heading">Ref</th>
                    <th class="table__heading">Submitted</th>
                    <th class="table__heading">Appt. Date</th>
                    <th class="table__heading">Appt. Time</th>
                    <th class="table__heading">Duration</th>
                    <th class="table__heading">Language</th>

                    @role('admin')
                        <th class="table__heading">Company Name</th>
                        <th class="table__heading">Posted By</th>
                    @endrole

                    @hasanyrole('admin|client')
                        <th class="table__heading">Agent Details</th>
                    @endhasanyrole

                    <th class="table__heading">Status</th>
                </tr>
            </thead> --}}
            <thead class="table__header">
                <tr>
                    <th class="table__heading">Ref</th>
                    <th class="table__heading">Job Type</th>
                    <th class="table__heading">Submitted</th>
                    <th class="table__heading">Appt./Delivery Date</th>
                    <th class="table__heading">Appt. Time</th>
                    <th class="table__heading">Word Count</th>
                    <th class="table__heading">Duration</th>
                    <th class="table__heading">Language</th>
                    {{-- <th class="table__heading">Bulk ID</th> --}}
                    @role('admin')
                        <th class="table__heading">Company Name</th>
                        <th class="table__heading">Posted By</th>
                    @endrole
                    @hasanyrole('admin|client')
                        <th class="table__heading">Agent Details</th>
                    @endhasanyrole
                    <th class="table__heading">Status</th>
                </tr>
            </thead>

            {{-- <tbody>

                @forelse ($jobs as $job)
                    <tr class="table__row">
                        <td class="table__data">{{ $job->reference }}{{ $job->client->show_agents }}</td>
                        <td class="table__data">{{ $job->created_at->format('d/m/Y') }}</td>
                        <td class="table__data">{{ $job->appointment_date->format('d/m/Y') }}</td>
                        <td class="table__data">{{ $job->start_time }}</td>
                        <td class="table__data">{{ $job->formattedDuration }}</td>
                        <td class="table__data">
                            {{ $job->toLanguage->name }}
                            @role('admin')
                                @if ($job->toLanguage->trashed())
                                    <br /><small>Deleted</small>
                                @endif
                            @endrole
                        </td>

                        @role('admin')
                            <td class="table__data">{{ $job->client->organisation->company->name ?? 'N/A' }}</td>
                            <td class="table__data">{{ $job->client->user->getFullName() }}</td>
                        @endrole

                        @hasanyrole('admin|client')
                            <td class="table__data">
                                @if ($job->agent && $job->client->show_agents)
                                    <button type="button" class="table__agent-name" data-toggle="modal"
                                        data-target="#agentCardModal" data-picture="{{ $job->agent->getProfilePicture() }}"
                                        data-name="{{ $job->agent->user ? $job->agent->user->getFullName() : 'DELETED USER' }}"
                                        data-dbs-expiry-date="{{ optional($job->agent->dbs_expiry_date)->format('d/m/Y') }}"
                                        data-dbs-number="{{ $job->agent->dbs_number }}"
                                        data-induction-date="{{ optional($job->agent->induction_date)->format('d/m/Y') }}"
                                        data-dbs-ref="{{ $job->agent->dbs_update_reference_number ?? 'n/a' }}"
                                        data-tel="{{ ($job->skill_id == 3 || $job->skill_id == 4) && $job->agent->contact_number
                                            ? $job->agent->contact_number
                                            : 'n/a' }}"
                                        data-email="{{ $job->skill_id == 4 && $job->agent->user->email ? $job->agent->user->email : 'n/a' }}">
                                        {{ $job->agent->user ? $job->agent->user->getFullName() : 'DELETED USER' }}
                                    </button>
                                @else
                                    ...
                                @endif

                            </td>
                        @endhasanyrole

                        <td class="table__data status status--{{ $job->getStatusForAgent() ?? $job->statusName }}">

                            @role('agent')
                                {{ $job->getStatusForAgent() ?? $job->statusName }}
                            @else
                                {{ $job->statusName }}

                                @if ($job->cancellation)
                                    <a class="table__agent-name align-bottom" data-toggle="modal" data-target="#messageModal"
                                        data-msg="{{ $job->cancellation->message }}">
                                        <img src="/img/cancellations.svg" alt="Agent Cancellation">
                                    </a>
                                @endif
                            @endrole

                        </td>
                        <td class="table__data table__data--actions">









                        </td>
                    </tr>
                @empty
                    <tr class="table__row">
                        <td class="table__data table__data--grey" colspan="10">
                            There are no jobs
                        </td>
                    </tr>
                @endforelse

            </tbody> --}}

            <tbody>
                @forelse ($jobs as $job)
                    <tr class="table__row">
                        <td class="table__data">{{ $job->id }}</td>
                        <td class="table__data">{{ $job->job_type }}</td>
                        <td class="table__data"> @isset($job->created_at)
                                {{ $job->created_at->format('d/m/Y') }}
                            @endisset
                        </td>
                        <td class="table__data">
                            @isset($job->appointment_date)
                                {{ $job->appointment_date->format('d/m/Y') }}
                            @endisset
                        </td>
                        <td class="table__data">{{ $job->start_time ?? 'N/A' }}</td>
                        <td class="table__data">{{ $job->word_count ?? 'N/A' }}</td>
                        @if ($job->job_type === 'interpreter')
                            <td class="table__data">{{ $job->duration_hours }} hours {{ $job->duration_minutes }} minutes
                            </td>
                        @else
                            <td class="table__data">N/A</td>
                        @endif
                        <td class="table__data">
                            @if ($job->job_type === 'interpreter')
                                {{ $job->toLanguage->name }}
                            @else
                                {{ $job->fromLanguage->name }}
                                @role('admin')
                                    @if ($job->fromLanguage->trashed())
                                        <small>Deleted</small>
                                    @endif
                                @endrole
                                - {{ $job->toLanguage->name }}
                                @role('admin')
                                    @if ($job->toLanguage->trashed())
                                        <small>Deleted</small>
                                    @endif
                                @endrole
                            @endif
                        </td>
                        {{-- <td class="table__data">
                            @if ($job->job_type === 'interpreter')
                                {{ $job->bulk_id }}
                            @else
                                --
                            @endif
                        </td> --}}
                        @role('admin')
                            <td class="table__data">{{ $job->client->organisation->company->name ?? 'N/A' }}</td>
                            <td class="table__data">{{ $job->client->user->getFullName() }}</td>
                        @endrole

                        @hasanyrole('admin|client')
                            <td class="table__data">
                                {{-- @if ($job->agent && $job->agent->user)
                                    {{ $job->agent->user->fullName }}
                                @else
                                    --
                                @endif --}}

                                @if ($job->agent && $job->client->show_agents)
                                    <button type="button" class="table__agent-name" data-toggle="modal"
                                        data-target="#agentCardModal" data-picture="{{ $job->agent->getProfilePicture() }}"
                                        data-name="{{ $job->agent->user ? $job->agent->user->getFullName() : 'DELETED USER' }}"
                                        data-dbs-expiry-date="{{ optional($job->agent->dbs_expiry_date)->format('d/m/Y') }}"
                                        data-dbs-number="{{ $job->agent->dbs_number }}"
                                        data-induction-date="{{ optional($job->agent->induction_date)->format('d/m/Y') }}"
                                        data-dbs-ref="{{ $job->agent->dbs_update_reference_number ?? 'n/a' }}"
                                        data-tel="{{ ($job->skill_id == 3 || $job->skill_id == 4) && $job->agent->contact_number
                                            ? $job->agent->contact_number
                                            : 'n/a' }}"
                                        data-email="{{ $job->skill_id == 4 && $job->agent->user->email ? $job->agent->user->email : 'n/a' }}">
                                        {{ $job->agent->user ? $job->agent->user->getFullName() : 'DELETED USER' }}
                                    </button>
                                @else
                                    ...
                                @endif
                            </td>
                        @endhasanyrole

                        <td class="table__data status status--{{ $job->getStatusForAgent() ?? $job->statusName }}">

                            @role('agent')
                                {{ $job->getStatusForAgent() ?? $job->status }}
                            @else
                                {{ $job->statusName }}

                                @if ($job->cancellation)
                                    <a class="table__agent-name align-bottom" data-toggle="modal" data-target="#messageModal"
                                        data-msg="{{ $job->cancellation->message }}">
                                        <img src="/img/cancellations.svg" alt="Agent Cancellation">
                                    </a>
                                @endif
                            @endrole

                        </td>
                    </tr>
                @empty
                    <tr class="table__row">
                        <td class="table__data table__data--grey" colspan="11">
                            There are no jobs
                        </td>
                    </tr>
                @endforelse
            </tbody>

        </table>
    </section>

    {{ $jobs->appends(request()->all())->links() }}

    @include('partials.cancel-modal')

    @role('admin|client')
        @include('partials.message-modal')
        @include('partials.agent-card-modal')
    @endrole

@endsection
