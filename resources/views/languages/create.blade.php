@extends('layouts.app')

@section('content')

    <main class="section">
        <div class="section__top">
            <div class="section__heading">Add a new language</div>
        </div>

        {{ Form::open([ 'route' => 'languages.store', 'class' => 'form' ]) }}
            @include('languages.form')
        {{ Form::close() }}

    </main>

@endsection
