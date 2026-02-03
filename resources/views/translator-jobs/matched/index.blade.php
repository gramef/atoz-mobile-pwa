@extends('layouts.app')

@section('content')

<main class="section">

    @include('partials.job-header', [ 'job' => $translatorJob, 'type' => 'translator-jobs' ])

    <section class="job__section job__section--no-border">
        <div class="row">
            <div class="col-xl-3 mb-4 mb-xl-0">

                @if ($unmatchedAgents->isNotEmpty())
                    {{ Form::label('unmatchedAgentsSelect', 'Search agent by name:', [ 'class' => 'big-label d-block mb-3' ]) }}

                    {{ Form::open([ 'route' => ['translator-jobs.matched.store', $translatorJob] ]) }}
                        {{ Form::select('agent_id', $unmatchedAgents, null, [
                                'class' => 'input input--select form__input',
                                'id' => 'unmatchedAgentsSelect',
                                'placeholder' => 'Select an agent...',
                            ])
                        }}
                    {{ Form::close() }}
                @else
                    <p class="big-label d-block mb-3">There are no other matched agents for this job</p>
                @endif

            </div>
            <div class="col-xl-9">
                <p class="big-label">List of notified agents</p>
                <section class="content__table">
                    <table class="table">
                        <thead class="table__header">
                            <tr>
                                <th class="table__heading">Name</th>
                                <th class="table__heading">Amount</th>
                                <th class="table__heading">Description</th>
                                <th class="table__heading">Status</th>
                                <th class="table__heading"></th>
                            </tr>
                        </thead>
                        <tbody>

                            @forelse ($matchedAgents as $matchedAgent)
                                <tr class="table__row">
                                    <td class="table__data">{{ $matchedAgent->agent->user->getFullName() }}</td>
                                    <td class="table__data table__data--grey">
                                        {{ optional($matchedAgent->latestQuote())->cost ? '£' . $matchedAgent->latestQuote()->cost : '...' }}
                                    </td>
                                    <td class="table__data table__data--grey">
                                        {{ optional($matchedAgent->latestQuote())->cost_description ? $matchedAgent->latestQuote()->cost_description : '...' }}
                                    </td>
                                    <td class="table__data status status--{{ $matchedAgent->status }}">
                                        {{ $matchedAgent->status }}

                                        @if ($matchedAgent->cancellation)
                                            <a class="table__agent-name align-bottom"
                                                data-toggle="modal"
                                                data-target="#messageModal"
                                                data-msg="{{ $matchedAgent->cancellation->message }}">
                                                <img src="/img/cancellations.svg" alt="Agent Cancellation">
                                            </a>
                                        @endif


                                    </td>

                                    @can('assign', $translatorJob)
                                        <td class="table__data table__data--actions">

                                            @if ($matchedAgent->canBeAssigned())
                                                {{ Form::open([
                                                        'method' => 'PUT',
                                                        'route' => [
                                                            'translator-jobs.matched.update',
                                                            $translatorJob->id,
                                                            $matchedAgent->id
                                                        ],
                                                        'class' => 'd-inline-block'
                                                    ])
                                                }}
                                                    {{ Form::submit('Assign', [ 'class' => 'btn btn--secondary btn--small' ]) }}
                                                {{ Form::close() }}
                                            @endif

                                            @if ($matchedAgent->canBeCancelled())
                                                {{ Form::open([
                                                        'method' => 'DELETE',
                                                        'route' => [
                                                            'translator-jobs.matched.destroy',
                                                            $translatorJob->id,
                                                            $matchedAgent->id
                                                        ],
                                                        'class' => 'd-inline-block'
                                                    ])
                                                }}
                                                    {{ Form::submit('Cancel', [
                                                            'class' => 'btn btn--delete btn--small',
                                                            'onclick' => "return confirm('Are you sure you want to cancel this job?')"
                                                        ])
                                                    }}
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

@endsection
