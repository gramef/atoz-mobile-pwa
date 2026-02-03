@extends('layouts.app')

@section('content')

<main class="job">

    @include('partials.job-header', [ 'job' => $translatorJob, 'type' => 'translator-jobs' ])

    <div class="row">
        <div class="col-xl-6 overflow-auto">
            <table class="table table--detail shadow-none w-100 mb-0">
                <tr class="table__row table__row--short">
                    <td class="p-0 h-100">
                        <div class="job__detail pl-4 d-flex align-items-center h-100">
                            Company name:
                        </div>
                    </td>
                    <td class="p-0 h-100">
                        <div class="text-black pl-4 d-flex align-items-center h-100">
                            {{ $translatorJob->client->organisation->company->name ?? 'n/a' }}
                        </div>
                    </td>
                </tr>
                <tr class="table__row table__row--short">
                    <td class="p-0 h-100">
                        <div class="job__detail pl-4 d-flex align-items-center h-100">
                            Client name:
                        </div>
                    </td>
                    <td class="p-0 h-100">
                        <div class="text-black pl-4 d-flex align-items-center h-100">
                            {{ $translatorJob->client->user->getFullNameWithTitle() }}
                        </div>
                    </td>
                </tr>
                <tr class="table__row table__row--short">
                    <td class="p-0 h-100">
                        <div class="job__detail pl-4 d-flex align-items-center h-100">
                            Languages:
                        </div>
                    </td>
                    <td class="p-0 h-100">
                        <div class="text-black pl-4 d-flex align-items-center h-100">
                            {{ $translatorJob->fromLanguage->name }} - {{ $translatorJob->toLanguage->name }}
                        </div>
                    </td>
                </tr>
                <tr class="table__row table__row--short">
                    <td class="p-0 h-100">
                        <div class="job__detail pl-4 d-flex align-items-center h-100">
                            Service type:
                        </div>
                    </td>
                    <td class="p-0 h-100">
                        <div class="text-black pl-4 d-flex align-items-center h-100">
                            {{ $translatorJob->skill->skill }}
                        </div>
                    </td>
                </tr>
                <tr class="table__row table__row--short">
                    <td class="p-0 h-100">
                        <div class="job__detail pl-4 d-flex align-items-center h-100">
                            Required by date:
                        </div>
                    </td>
                    <td class="p-0 h-100">
                        <div class="text-black pl-4 d-flex align-items-center h-100">
                            {{ $translatorJob->target_date->format('d/m/Y') }}
                        </div>
                    </td>
                </tr>
                <tr class="table__row table__row--short">
                    <td class="p-0 h-100">
                        <div class="job__detail pl-4 d-flex align-items-center h-100">
                            Word Count:
                        </div>
                    </td>
                    <td class="p-0 h-100">
                        <div class="text-black pl-4 d-flex align-items-center h-100">
                            {{ $translatorJob->word_count }}
                        </div>
                    </td>
                </tr>
                <tr class="table__row table__row--short">
                    <td class="p-0 h-100">
                        <div class="job__detail pl-4 d-flex align-items-center h-100">
                            Affirmation required:
                        </div>
                    </td>
                    <td class="p-0 h-100">
                        <div class="text-black pl-4 d-flex align-items-center h-100">
                            {{ $translatorJob->affirmation ? 'Yes' : 'No' }}
                        </div>
                    </td>
                </tr>
                <tr class="table__row table__row--short">
                    <td class="p-0 h-100">
                        <div class="job__detail pl-4 d-flex align-items-center h-100">
                            Sword affidavit required:
                        </div>
                    </td>
                    <td class="p-0 h-100">
                        <div class="text-black pl-4 d-flex align-items-center h-100">
                            {{ $translatorJob->affidavit ? 'Yes' : 'No' }}
                        </div>
                    </td>
                </tr>
                <tr class="table__row table__row--short">
                    <td class="p-0 h-100">
                        <div class="job__detail pl-4 d-flex align-items-center h-100">
                            Special instructions:
                        </div>
                    </td>
                    <td class="p-0 h-100">
                        <div class="text-black pl-4 d-flex align-items-center h-100">
                            {{ $translatorJob->notes ?? 'n/a' }}
                        </div>
                    </td>
                </tr>
                <tr class="table__row table__row--short">
                    <td class="p-0 h-100">
                        <div class="job__detail pl-4 d-flex align-items-center h-100">
                            Client reference:
                        </div>
                    </td>
                    <td class="p-0 h-100">
                        <div class="text-black pl-4 d-flex align-items-center h-100">
                            {{ $translatorJob->client_reference }}
                        </div>
                    </td>
                </tr>
            </table>
        </div>
        <div class="col-xl-6 mt-4 mt-xl-0">
            <div class="px-3 py-4 p-xl-5 bg-light-gray">
                <div>Uploaded file(s) for translation</div>
                <ul class="list-unstyled">

                    @foreach ($translatorJob->documents as $document)
                        <li class="mt-2">
                            <a href="{{ $document->fullUrl }}">
                                {{ $document->name }}
                            </a>
                        </li>
                    @endforeach

                </ul>
            </div>
        </div>
        <div class="col-12 mt-4">

            @if ($translatorJob->getStatusForAgent() == 'matched')
                {{ Form::open(['route' => ['translator-jobs.matched.reject', $translatorJob->id]]) }}
                    {{ Form::submit('Reject Job', ['class' => 'btn btn--reject btn--long']) }}
                {{ Form::close() }}
            @endif

            <hr class="mt-4">
            <footer class="d-flex justify-content-between">
                <span>Submitted: {{ $translatorJob->created_at->format('d/m/Y') }}</span>
                <span>Last updated: {{ $translatorJob->updated_at->format('d/m/Y') }}</span>
            </footer>
        </div>
    </div>
</main>

@endsection
