@extends('layouts.app')

@section('content')
    <main class="section">

        @include('partials.job-header', ['job' => $translatorJob, 'type' => 'translator-jobs'])

        <section class="job__section job__section--no-border mb-0">

            {{ Form::model($translatorJob, ['route' => ['translator-jobs.update', $translatorJob->id], 'class' => 'form', 'method' => 'PUT']) }}

            @include('translator-jobs.form')

            {{ Form::close() }}

        </section>
        <hr>
        <footer class="d-flex justify-content-between">
            <span>Submitted: {{ $translatorJob->created_at->format('d/m/Y') }}</span>

            @if ($translatorJob->statusName == 'cancelled')
                <span>Cancelled: {{ $translatorJob->cancelled_at->format('d/m/Y') }}</span>
            @else
                <span>Last updated: {{ $translatorJob->updated_at->format('d/m/Y') }}</span>
            @endif

        </footer>
    </main>
@endsection
