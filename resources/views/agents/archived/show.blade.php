@extends('layouts.app')

@section('content')

    <main class="section">
        <div class="section__top d-block d-lg-flex">
            <div class="section__heading">{{ $agent->user->getFullName() }}</div>

            {{ Form::open([ 'route' => [ 'agents.archived.destroy', $agent->id ], 'method' => 'DELETE' ]) }}
                {{ Form::submit('Restore Agent', [ 'class' => 'btn btn--primary form__btn' ]) }}
            {{ Form::close() }}

        </div>
        <nav class="section__tabs">
            <div class="section__heading">Agent Details</div>
            <a href="{{ route('agents.documents.edit', $agent) }}" class="section__heading section__heading--blue">Agent Documents</a>
        </nav>

        @include('agents.form')

    </main>

@endsection