@extends('layouts.app')

@section('content')

    <main class="section">
        <div class="section__top">
            <div class="section__heading">Post an translation job</div>
        </div>

        {{ Form::open([ 'route' => 'translator-jobs.store', 'class' => 'form' ]) }}

            @include('translator-jobs.form')

        {{ Form::close() }}

    </main>

@endsection
