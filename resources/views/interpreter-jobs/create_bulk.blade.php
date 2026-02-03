@extends('layouts.app')

@section('content')
    <main class="section">
        <div class="section__top">
            <div class="section__heading">Post an interpreter job</div>
        </div>

        {{ Form::open(['route' => 'interpreter-jobs.storebulk', 'class' => 'form']) }}

        @include('interpreter-jobs.bulk')

        {{ Form::close() }}



    </main>
@endsection
