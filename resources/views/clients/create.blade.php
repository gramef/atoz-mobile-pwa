@extends('layouts.app')

@section('content')

    <main class="section">

        {{ Form::open([ 'route' => 'clients.store', 'class' => 'form' ]) }}

            <div class="section__top">
                <div class="section__heading">Add Client</div>
                {{ Form::submit('+ Add Client', ['class' => 'btn btn--primary form__btn d-none d-xl-flex']) }}
            </div>
        
            @include('clients.form')

            {{ Form::submit('+ Add Client', ['class' => 'btn btn--primary form__btn d-xl-none']) }}
            
        {{ Form::close() }}

    </main>

@endsection
