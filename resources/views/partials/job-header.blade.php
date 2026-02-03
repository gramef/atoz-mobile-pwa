<header class="job__header">
    <h1 class="job__heading">

        @if ($type == 'interpreter-jobs')
            Interpreter job: {{ $job->toLanguage->name }}
        @else
            Translator job: {{ $job->toLanguage->name }} - {{ $job->fromLanguage->name }}
        @endif

        <div class="job__reference">{{ $job->reference }}</div>
    </h1>

    @unlessrole('agent')
        <section class="job__actions {{ $job->cannotBeCancelled() ? 'job__actions--short' : '' }}">
            <div class="job__wrapper">
                <label class="job__label job__label--big">Status:</label>
                <span class="job__status status status--{{ $job->statusName }}">
                    {{ $job->statusName }}

                    @if ($job->cancellation)
                        <a class="table__agent-name align-bottom" data-toggle="modal" data-target="#messageModal"
                            data-msg="{{ $job->cancellation->message }}">
                            <img src="/img/cancellations.svg" alt="Agent Cancellation">
                        </a>
                    @endif
                </span>
            </div>

            @if ($job->canBeCompleted())
                {{ Form::open([
                    'method' => 'PUT',
                    'route' => ["$type.complete", $job],
                    'onsubmit' => 'return confirm("Are you sure you want to complete this job?")',
                    'class' => 'd-inline-block',
                ]) }}
                {{ Form::submit('Complete', ['class' => 'btn btn--primary px-4']) }}
                {{ Form::close() }}
                {{-- @elseif ($job->canBeCancelled()) --}}
                @role('client')
                    @includeWhen($job->canBeCancelled(), 'partials.cancel-button', [
                        'isWithin24Hours' => json_encode($job->isWithin24Hours()),
                        'route' => route("$type.cancel", $job->id),
                        'class' => 'btn btn--cancel w-50',
                    ])
                @else
                    {{ Form::open(['route' => ["$type.cancel", $job->id], 'class' => 'w-50']) }}
                    {{ Form::submit('Cancel', [
                        'class' => 'btn btn--cancel w-100',
                        'onclick' => "return confirm('Are you sure you want to cancel this job?')",
                    ]) }}
                    {{ Form::close() }}
                @endrole
            @endif

        </section>
    @else
        <section class="job__actions {{ $job->cannotBeCancelled() ? 'job__actions--short' : '' }}">
            <div class="job__wrapper">
                <label class="job__label job__label--big">Status:</label>
                <span class="job__status status status--{{ $job->getStatusForAgent() ?? $job->statusName }}">
                    {{ $job->getStatusForAgent() ?? $job->statusName }}

                    @if ($job->cancellation)
                        <a class="table__agent-name align-bottom" data-toggle="modal" data-target="#messageModal"
                            data-msg="{{ $job->cancellation->message }}">
                            <img src="/img/cancellations.svg" alt="Agent Cancellation">
                        </a>
                    @endif

                </span>
            </div>

            @if ($job->matchedLoggedInAgent()->status == 'matched')
                @if ($type == 'translator-jobs' || ($type == 'interpreter-jobs' && $job->requiresQuote()))
                    <a class="btn btn--primary w-50" href="{{ route("$type.quotes.index", $job->id) }}">
                        Quote Job
                    </a>
                @else
                    {{ Form::open(['route' => ['interpreter-jobs.matched.update', $job->id], 'method' => 'PUT', 'class' => 'd-inline-block w-50']) }}
                    <button type="submit" class="btn btn--primary">Accept</button>
                    {{ Form::close() }}
                @endif
            @endif

        </section>
    @endunlessrole

</header>

<ul class="tabs">
    <li class="tabs__tab">

        @if (in_array(Route::current()->getName(), ["$type.show", "$type.edit"]))
            <div class="tabs__text">Job details</div>
        @else
            @hasanyrole('admin|client')
                <a href="{{ route("$type.edit", $job->id) }}" class="tabs__text tabs__text--link">Job details</a>
            @else
                <a href="{{ route("$type.show", $job->id) }}" class="tabs__text tabs__text--link">Job details</a>
            @endhasanyrole
        @endif

    </li>

    @hasanyrole('admin|client')
        @if ($job->agent && $type == 'interpreter-jobs' && $job->client->show_agents)
            <li class="tabs__tab">
                @if (Route::current()->getName() == "$type.agent.index")
                    <div class="tabs__text">Agent ID</div>
                @else
                    <a href="{{ route("$type.agent.index", $job->id) }}" class="tabs__text tabs__text--link">Agent ID</a>
                @endif
            </li>
        @endif
    @endhasanyrole

    @role('admin')
        <li class="tabs__tab">
            @if (Route::current()->getName() == "$type.matched.index")
                <div class="tabs__text">Assign</div>
            @else
                <a href="{{ route("$type.matched.index", $job->id) }}" class="tabs__text tabs__text--link">Assign</a>
            @endif
        </li>
    @endrole
    @role('admin')
        <li class="tabs__tab">
            @if (Route::current()->getName() == "$type.allupdates.index")
                <div class="tabs__text">Job Updates</div>
            @else
                <a href="{{ route("$type.allupdates.index", $job->id) }}" class="tabs__text tabs__text--link">Job
                    Updates</a>
            @endif
        </li>

        <li class="tabs__tab">
            @if (Route::current()->getName() == "$type.documents.index")
                <div class="tabs__text">Travel Expences</div>
            @else
                <a href="{{ route("$type.documents.index", $job->id) }}" class="tabs__text tabs__text--link">Travel
                    Expences</a>
            @endif
        </li>



    @endrole
    @role('agent')
        <li class="tabs__tab">
            @if (Route::current()->getName() == "$type.documents.index")
                <div class="tabs__text">Travel Expences Reciept</div>
            @else
                <a href="{{ route("$type.documents.index", $job->id) }}" class="tabs__text tabs__text--link">Travel
                    Expences Reciept</a>
            @endif
        </li>
    @endrole
    @can('viewQuotes', $job)
        <li class="tabs__tab">
            @if (Route::current()->getName() == "$type.quotes.index")
                <div class="tabs__text">Quotes</div>
            @else
                <a href="{{ route("$type.quotes.index", $job->id) }}" class="tabs__text tabs__text--link">Quotes</a>
            @endif
        </li>
    @endcan

    @can('viewDocuments', $job)
        <li class="tabs__tab">
            @if (Route::current()->getName() == "$type.documents.index")
                <div class="tabs__text">Translation Files</div>
            @else
                <a href="{{ route("$type.documents.index", $job->id) }}" class="tabs__text tabs__text--link">Translation
                    Files</a>
            @endif
        </li>
    @endcan

</ul>

@include('partials.cancel-modal')
@include('partials.message-modal')
