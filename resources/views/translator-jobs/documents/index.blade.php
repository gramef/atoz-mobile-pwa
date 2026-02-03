@extends('layouts.app')

@section('content')
    <main class="section">

        @include('partials.job-header', ['job' => $translatorJob, 'type' => 'translator-jobs'])

        <div class="row">

            <div class="col-xl-4">

                @include('translator-jobs.partials.file-upload', [
                    'translatorJob' => $translatorJob,
                    'type' => config('enums.document_types')['translated_file'],
                    'label' => 'Completed translation file',
                ])

                @if ($translatorJob->affirmation)
                    <div class="mt-xl-4">
                        @include('translator-jobs.partials.file-upload', [
                            'translatorJob' => $translatorJob,
                            'type' => config('enums.document_types')['affirmation'],
                            'label' => 'Affirmation',
                        ])
                    </div>
                @endif

                @if ($translatorJob->affidavit)
                    <div class="mt-xl-4">
                        @include('translator-jobs.partials.file-upload', [
                            'translatorJob' => $translatorJob,
                            'type' => config('enums.document_types')['affidavit'],
                            'label' => 'Sworn affadavit',
                        ])
                    </div>
                @endif

            </div>

            <div class="col-xl-4">
                {{ Form::open(['route' => ['translator-jobs.comments.store', $translatorJob]]) }}
                {{ Form::label('body', 'Add comments', ['class' => 'font-weight-bold']) }}
                {{ Form::textarea('body', null, ['class' => 'input input--textarea mt-2']) }}
                {{ Form::submit('Send comment', ['class' => 'btn btn-secondary btn--long mt-3']) }}
                {{ Form::close() }}
            </div>

            <div class="col-xl-4">
                <label class="font-weight-bold mb-0" for="">Message thread</label>

                @foreach ($translatorJob->comments->sortByDesc('created_at') as $comment)
                    <div class="font-italic mt-4">
                        <div>{{ $comment->body }}</div>
                        <strong class="font-weight-500 font-italic">
                            Posted by {{ $comment->user->getUserLevel() }}, {{ $comment->created_at->format('d/m/Y H:ia') }}
                        </strong>
                    </div>
                @endforeach

            </div>

        </div>
    </main>
@endsection
