@extends('layouts.app')

@section('content')

    <main class="section">
        <div class="section__top">
            <div class="section__heading">Add a new admin</div>
        </div>

        {{ Form::open([ 'route' => 'admins.store', 'class' => 'form' ]) }}

            @include('admins.form')

        {{ Form::close() }}

    </main>

@endsection
