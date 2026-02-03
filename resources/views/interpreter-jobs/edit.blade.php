@extends('layouts.app')

@section('content')
    <main class="section">

        @include('partials.job-header', ['job' => $interpreterJob, 'type' => 'interpreter-jobs'])

        <section class="job__section job__section--no-border">

            {{ Form::model($interpreterJob, ['route' => ['interpreter-jobs.update', $interpreterJob->id], 'class' => 'form', 'method' => 'PUT']) }}
				@include('interpreter-jobs.form')
            {{ Form::close() }}

            <hr class="mt-4">
            <footer class="d-flex justify-content-between">
                <span>Submitted: {{ $interpreterJob->created_at->format('d/m/Y') }}</span>

                @if ($interpreterJob->statusName == 'cancelled')
                    <span>Cancelled: {{ $interpreterJob->cancelled_at->format('d/m/Y') }}</span>
                @else
                    <span>Last updated: {{ $interpreterJob->updated_at->format('d/m/Y') }}</span>
                @endif

            </footer>
        </section>
    </main>
@endsection
