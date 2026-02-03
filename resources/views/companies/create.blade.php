@extends('layouts.app')

@section('content')

    <main class="section">
        <div class="section__top">
            <div class="section__heading">Add a new company</div>
        </div>

        {{ Form::open([ 'route' => 'companies.store', 'class' => 'form' ]) }}
            @include('companies.form')
        {{ Form::close() }}

    </main>

@endsection
