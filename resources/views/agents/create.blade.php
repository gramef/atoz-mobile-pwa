@extends('layouts.app')

@section('content')

    <main class="section">

        {{ Form::open([ 'route' => 'agents.store', 'class' => 'form' ]) }}
        
            <div class="section__top">
                <div class="section__heading mb-4 mb-md-0">New Agent</div>
                {{ Form::submit('Add Agent', ['class' => 'btn btn--primary form__btn']) }}
            </div>

            @include('agents.form')

        {{ Form::close() }}

    </main>

@endsection