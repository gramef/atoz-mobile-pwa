@extends('layouts.app')

@section('content')

    <nav class="nav">
        <h1 class="nav__heading">Job Board</h1>
        <button type="button" class="btn btn-secondary">Total Jobs: <span
                class="badge bg-light text-dark text-bold">{{ $count }}</span></button>
        <ul class="links">
            <li class="links__item">
                <div class="links__text">Interpreter Jobs</div>
            </li>
            <li class="links__item links__item--last">
                <a href="{{ route('translator-jobs.index') }}" class="links__text links__text--link">Translation Jobs</a>
            </li>
        </ul>
        <section class="job__actions">
            @role('admin')
                <a href="{{ route('interpreter-jobs.export', request()->query()) }}" class="btn btn--secondary nav__btn"
                    style="font-size: 11px">Export
                    CSV</a>
            @endrole
            @unlessrole('agent')
                <a href="{{ route('interpreter-jobs.create') }}" class="btn btn--primary nav__btn"
                    style="padding:2px; font-size:11px; width: 100%">+ Post
                    Interpreter Job</a>
                <a href="{{ route('interpreter-jobs.createbulk') }}" class="btn btn--primary nav__btn"
                    style="padding:2px; font-size:11px; width: 100%">+ Post
                    Bulk Interpreter Job</a>
            @endunlessrole
        </section>
    </nav>

    @include('partials.job-filter-form')

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
                    {{-- <th class="table__heading">Bulk ID</th> --}}

                    @role('admin')
                        <th class="table__heading">Company Name</th>
                        <th class="table__heading">Posted By</th>
                    @endrole

                    @hasanyrole('admin|client')
                        <th class="table__heading">Agent Details</th>
                    @endhasanyrole

                    <th class="table__heading">Status</th>
                    <th class="table__heading">Action</th>
                </tr>
            </thead>
            <tbody>

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
                        {{-- <td class="table__data" style="font-size: 0.7em">

                            @if ($job->bulk_id)
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

                                {{-- {{ $job->agent->user->getFullName() }} --}}

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

                            @if ($job->canBeCompleted())
                                {{ Form::open([
                                    'method' => 'PUT',
                                    'route' => ['interpreter-jobs.complete', $job],
                                    'onsubmit' => 'return confirm("Are you sure you want to complete this job?")',
                                    'class' => 'd-inline-block',
                                ]) }}
                                {{ Form::submit('Complete', ['class' => 'btn btn--primary table__btn']) }}
                                {{ Form::close() }}
                            @endif
                            @if ($job->canBeDna())
                                @role('admin')
                                    {{ Form::Open([
                                        'method' => 'PUT',
                                    
                                        'route' => ['interpreter-jobs.dna', $job],
                                        'onSubmit' => 'return confirm("Are you Sure you want to DNA this job?")',
                                        'class' => 'd-inline-block',
                                    ]) }}
                                    {{ Form::submit('DNA', ['class' => 'btn btn--cancel table__btn']) }}
                                    {{ Form::close() }}
                                @endrole
                            @endif


                            @if ($job->canBeRetrn())
                                @role('admin')
                                    {{ Form::Open([
                                        'method' => 'PUT',
                                    
                                        'route' => ['interpreter-jobs.retrn', $job],
                                        'onSubmit' => 'return confirm("Are you Sure you want to Return this job?")',
                                        'class' => 'd-inline-block',
                                    ]) }}
                                    {{ Form::submit('Return', ['class' => 'btn btn--cancel table__btn']) }}
                                    {{ Form::close() }}
                                @endrole
                            @endif
                            {{-- @if ($job->canBeCancelled()) --}}

                            @hasanyrole('admin')
                                {{-- @role('admin') --}}
                                {{ Form::open([
                                    'route' => ['interpreter-jobs.cancel', $job],
                                    'onsubmit' => 'return confirm("Are you sure you want to cancel this job?")',
                                    'class' => 'd-inline-block',
                                ]) }}
                                {{ Form::submit('Cancel', ['class' => 'btn btn--cancel table__btn']) }}
                                {{ Form::close() }}
                                @elserole('client')
                                @includeWhen($job->canBeCancelled(), 'partials.cancel-button', [
                                    'isWithin24Hours' => json_encode($job->isWithin24Hours()),
                                    'route' => route('interpreter-jobs.cancel', $job),
                                    'class' => 'btn btn--cancel table__btn',
                                ])
                                {{-- @endrole --}}
                            @endhasanyrole
                            {{-- @endif --}}

                            @role('agent')
                                @if ($job->getStatusForAgent() == 'assigned')
                                    {{ Form::open([
                                        'method' => 'DELETE',
                                        'route' => ['interpreter-jobs.matched.unassign', $job->id, $job->matchedAgentId],
                                        'class' => 'd-inline-block',
                                    ]) }}
                                    {{ Form::submit('Unassign', [
                                        'class' => 'btn btn--delete btn--small',
                                        'onclick' => "return confirm('Are you sure you want to be unassigned from this job?')",
                                    ]) }}
                                    {{ Form::close() }}
                                @endif
                            @endrole

                            @role('admin')
                                @if (
                                    !empty($job->timesheet->agent_signature) &&
                                        !empty($job->timesheet->client_signature) &&
                                        $job->statusName == 'completed')
                                    <a href="{{ route('timesheet-pdf', ['id' => $job->timesheet->id]) }}"
                                        class="btn btn--primary table__btn my-1">TimeSheet Print</a>
                                @endif
                            @endrole

                            @role('agent')
                                @if ($job->getStatusForAgent() == 'matched')
                                    @if ($job->requiresQuote() && !auth()->user()->agent->is($job->agent))
                                        <a class="btn btn--primary table__btn"
                                            href="{{ route('interpreter-jobs.quotes.index', $job) }}">
                                            Accept
                                        </a>
                                    @else
                                        {{ Form::open(['route' => ['interpreter-jobs.matched.update', $job], 'method' => 'PUT', 'class' => 'd-inline-block']) }}
                                        <button type="submit" class="btn btn--primary table__btn">Accept</button>
                                        {{ Form::close() }}
                                    @endif

                                    {{ Form::open(['route' => ['interpreter-jobs.matched.reject', $job], 'class' => 'd-inline-block']) }}
                                    <button type="submit" class="btn btn--reject table__btn">Reject</button>
                                    {{ Form::close() }}
                                @else
                                    @if ($job->getStatusForAgent() == 'rejected')
                                        {{ Form::open(['route' => ['interpreter-jobs.matched.update', $job], 'method' => 'PUT', 'class' => 'd-inline-block']) }}
                                        <button type="submit" class="btn btn--primary table__btn">Accept</button>
                                        {{ Form::close() }}
                                    @endif
                                @endif
                            @endrole

                            @role('client')
                                @if (!empty($job->timesheet->job_id) && $job->statusName == 'completed')
                                    <a class="btn btn--secondary table__btn ml-1 my-1"
                                        href="{{ route('timesheet.edit', $job) }}">Sign Timesheet</a>
                                    @if (empty($job->feedback->job_id))
                                        <a class="btn btn--secondary table__btn ml-1 my-1"
                                            href="{{ route('feedback.edit', $job) }}">FeedBack</a>
                                    @endif
                                @endif
                            @endrole

                            @unlessrole('agent')
                                <a class="btn btn--secondary table__btn ml-1"
                                    href="{{ route('interpreter-jobs.edit', $job) }}">View</a>
                            @else
                                <a class="btn btn--secondary table__btn ml-1"
                                    href="{{ route('interpreter-jobs.show', $job) }}">View</a>
                                @if (!empty($job->timesheet->job_id) && $job->statusName == 'completed')
                                    <a class="btn btn--secondary table__btn ml-1 my-1"
                                        href="{{ route('timesheet.edit', $job) }}">Sign Timesheet</a>
                                @endif
                            @endunlessrole

                        </td>
                    </tr>
                @empty
                    <tr class="table__row">
                        <td class="table__data table__data--grey" colspan="10">
                            There are no interpreter jobs
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
