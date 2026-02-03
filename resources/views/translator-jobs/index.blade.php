@extends('layouts.app')

@section('content')

    <nav class="nav">
        <h1 class="nav__heading">Job Board</h1>
        <ul class="links">
            <li class="links__item">
                <a href="{{ route('interpreter-jobs.index') }}" class="links__text links__text--link">Interpreter Jobs</a>
            </li>
            <li class="links__item links__item--last">
                <div class="links__text">Translation Jobs</div>
            </li>
        </ul>
        <section class="job__actions ">
            @role('admin')
                <a href="{{ route('translator-jobs.export', request()->query()) }}" class="btn btn--secondary nav__btn">Export
                    CSV</a>
            @endrole
            @unlessrole('agent')
                <a href="{{ route('translator-jobs.create') }}" class="btn btn--primary nav__btn">+ Post Translation Job</a>
            @else
                <div></div>
            @endunlessrole
        </section>
    </nav>


    @include('partials.job-filter-translation-form')

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

                    @role('admin')
                        <th class="table__heading">Company Name</th>
                        <th class="table__heading">Posted By</th>
                    @endrole

                    <th class="table__heading">Status</th>
                    <th class="table__heading"></th>
                </tr>
            </thead>
            <tbody>

                @forelse ($jobs as $job)
                    <tr class="table__row">
                        <td class="table__data">{{ $job->reference }}</td>
                        <td class="table__data">{{ $job->created_at->format('d/m/Y') }}</td>
                        <td class="table__data">{{ $job->target_date->format('d/m/Y') }}</td>
                        <td class="table__data">{{ $job->word_count }}</td>
                        <td class="table__data">
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
                        </td>
                        <td class="table__data">{{ $job->affirmation ? 'Yes' : 'No' }}</td>
                        <td class="table__data">{{ $job->affidavit ? 'Yes' : 'No' }}</td>

                        @role('admin')
                            <td class="table__data">{{ $job->client->organisation->company->name ?? 'N/A' }}</td>
                            <td class="table__data">{{ $job->client->user->getFullName() }}</td>
                        @endrole

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
                                    'route' => ['translator-jobs.complete', $job],
                                    'onsubmit' => 'return confirm("Are you sure you want to complete this job?")',
                                    'class' => 'd-inline-block',
                                ]) }}
                                {{ Form::submit('Complete', ['class' => 'btn btn--primary table__btn']) }}
                                {{ Form::close() }}
                            @elseif ($job->canBeCancelled())
                                @role('admin')
                                    {{ Form::open([
                                        'route' => ['translator-jobs.cancel', $job],
                                        'onsubmit' => 'return confirm("Are you sure you want to cancel this job?")',
                                        'class' => 'd-inline-block',
                                    ]) }}
                                    {{ Form::submit('Cancel', ['class' => 'btn btn--cancel table__btn']) }}
                                    {{ Form::close() }}
                                    @elserole('client')
                                    @includeWhen($job->canBeCancelled(), 'partials.cancel-button', [
                                        'isWithin24Hours' => json_encode($job->isWithin24Hours()),
                                        'route' => route('translator-jobs.cancel', $job),
                                        'class' => 'btn btn--cancel table__btn',
                                    ])
                                @endrole
                            @endif

                            @role('agent')
                                @if ($job->getStatusForAgent() == 'matched')
                                    @if (!auth()->user()->agent->is($job->agent))
                                        <a class="btn btn--primary table__btn"
                                            href="{{ route('translator-jobs.quotes.index', $job) }}">
                                            Accept
                                        </a>
                                    @else
                                        {{ Form::open(['route' => ['translator-jobs.matched.update', $job], 'method' => 'PUT', 'class' => 'd-inline-block']) }}
                                        <button type="submit" class="btn btn--primary table__btn">Accept</button>
                                        {{ Form::close() }}
                                    @endif

                                    {{ Form::open([
                                        'route' => ['translator-jobs.matched.reject', $job],
                                        'class' => 'd-inline-block',
                                    ]) }}
                                    <button type="submit" class="btn btn--reject table__btn">Reject</button>
                                    {{ Form::close() }}
                                @else
                                    @includeWhen($job->canBeCancelled(), 'partials.cancel-button', [
                                        'isWithin24Hours' => json_encode($job->isWithin24Hours()),
                                        'route' => route('matched.cancel', $job->matchedLoggedInAgent()),
                                        'class' => 'btn btn--cancel table__btn',
                                    ])
                                @endif
                            @endrole

                            @unlessrole('agent')
                                <a class="btn btn--secondary table__btn"
                                    href="{{ route('translator-jobs.edit', $job) }}">View</a>
                            @else
                                <a class="btn btn--secondary table__btn"
                                    href="{{ route('translator-jobs.show', $job) }}">View</a>
                            @endunlessrole

                        </td>
                    </tr>
                @empty
                    <tr class="table__row">
                        <td class="table__data table__data--grey" colspan="10">
                            There are no translation jobs
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
