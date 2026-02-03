@extends('layouts.app')

@section('content')
    <main class="section">
        <div class="section__top">
            <div class="section__heading">Post an interpreter job</div>
        </div>

        {{ Form::open(['route' => 'interpreter-jobs.store', 'class' => 'form']) }}

        @include('interpreter-jobs.form')

        {{ Form::close() }}


    </main>
@endsection
