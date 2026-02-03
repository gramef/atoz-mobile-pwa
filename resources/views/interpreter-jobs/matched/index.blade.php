@extends('layouts.app')

@section('content')

    <main class="section">

        @include('partials.job-header', ['job' => $interpreterJob, 'type' => 'interpreter-jobs'])

        <section class="job__section job__section--no-border">
            <div class="row">
                <div class="col-xl-6 mb-4 mb-xl-0">
                    @if ($matchedAgentsOptions->isNotEmpty())
                        {{ Form::label('unmatchedAgentsSelect', 'Search agent by name:', ['class' => 'big-label d-block mb-3']) }}

                        {{ Form::open(['route' => ['interpreter-jobs.matched.store', $interpreterJob]]) }}

                        {{ Form::select('agent_id', $matchedAgentsOptions, null, [
                            'class' => 'input input--select form__input',
                            'id' => 'unmatchedAgentsSelect',
                            'placeholder' => 'Select an agent...',
                        ]) }}

                        {{ Form::close() }}
                    @else
                        <p class="big-label d-block mb-3">There are no other matched agents for this job</p>
                    @endif
                </div>
                <div class="col-xl-12" style="margin-top: 20px;">
                    <p class="big-label">List of notified agents</p>
                    <section class="content__table">
                        <table class="table">
                            <thead class="table__header">
                                <tr>
                                    <th class="table__heading">Name</th>
                                    <th class="table__heading">Location / Distance</th>
                                    <th class="table__heading">Agent Postcode</th>
                                    <th class="table__heading">Job Postcode</th>

                                    @if ($interpreterJob->requiresQuote())
                                        <th class="table__heading">Total Amount</th>
                                    @endif

                                    <th class="table__heading">Status</th>
                                    <th class="table__heading">Actions</th>
                                    <th class="table__heading"></th>
                                </tr>
                            </thead>
                            <tbody>

                                @forelse ($matchedAgents as $matchedAgent)
                                    <tr class="table__row">
                                        <td class="table__data">{{ $matchedAgent->agent->user->getFullName() }}</td>
                                        <td class="table__data">{{ $matchedAgent->getFormattedDistance() }}</td>
                                        <td class="table__data">{{ $matchedAgent->agent->postcode }}</td>
                                        <td class="table__data">{{ $matchedAgent->job->postcode }}</td>

                                        @if ($interpreterJob->requiresQuote())
                                            <td class="table__data">
                                                {{ optional($matchedAgent->latestQuote())->totalAmount ?? '...' }}

                                                @if ($matchedAgent->canBeCancelled())
                                                    <button class="btn btn--grey btn--small table__btn" data-toggle="modal"
                                                        data-target="#quoteModal"
                                                        data-quote="{{ $matchedAgent->latestQuote()->toJson() }}">
                                                        View Quote
                                                    </button>
                                                @endif
                                            </td>
                                        @endif

                                        <td class="table__data status status--{{ $matchedAgent->status }}">
                                            {{ $matchedAgent->status }}

                                            @if ($matchedAgent->cancellation)
                                                <a class="table__agent-name align-bottom" data-toggle="modal"
                                                    data-target="#messageModal"
                                                    data-msg="{{ $matchedAgent->cancellation->message }}">
                                                    <img src="/img/cancellations.svg" alt="Agent Cancellation">
                                                </a>
                                            @endif
                                            @if ($matchedAgent->agent_id == $interpreterJob->requested_agent_id)
                                                <br /><small class="requested">Requested Agent</small>
                                            @endif

                                        </td>
                                        @can('assign', $interpreterJob)
                                            <td class="table__data table__data--actions">

                                                @if ($matchedAgent->canBeAssigned())
                                                    {{ Form::open([
                                                        'method' => 'PUT',
                                                        'route' => ['interpreter-jobs.matched.update', $interpreterJob->id, $matchedAgent->id],
                                                        'class' => 'd-inline-block',
                                                    ]) }}
                                                    {{ Form::submit('Assign', ['class' => 'btn btn--secondary btn--small']) }}
                                                    {{ Form::close() }}
                                                @endif

                                                @if ($interpreterJob->isAssignedToAgent($matchedAgent->id) && $matchedAgent->canBeCancelled())
                                                    {{ Form::open([
                                                        'method' => 'DELETE',
                                                        'route' => ['interpreter-jobs.matched.destroy', $interpreterJob->id, $matchedAgent->id],
                                                        'class' => 'd-inline-block',
                                                    ]) }}
                                                    {{ Form::submit('Cancel', [
                                                        'class' => 'btn btn--delete btn--small',
                                                        'onclick' => "return confirm('Are you sure you want to unassign this agent?')",
                                                    ]) }}
                                                    {{ Form::close() }}
                                                @endif

                                            </td>
                                        @endcan

                                    </tr>
                                @empty
                                    <tr class="table__row">
                                        <td class="table__data table__data--grey" colspan="5">
                                            No agents have been notified for this job.
                                        </td>
                                    </tr>
                                @endforelse

                            </tbody>
                        </table>
                    </section>
                </div>
            </div>
        </section>
    </main>

    @include('partials.message-modal')

    @includeWhen($interpreterJob->requiresQuote(), 'partials.quote-modal')

@endsection
