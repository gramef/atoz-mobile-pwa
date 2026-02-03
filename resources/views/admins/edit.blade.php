@extends('layouts.app')

@section('content')

    <main class="section">
        <div class="section__top">
            <div class="section__heading">Edit an admin</div>
        </div>

        {{ Form::model($admin, [ 'class' => 'form', 'method' => 'PUT', 'route' => [ 'admins.update', $admin->id ] ]) }}
            
            @include('admins.form')

        {{ Form::close() }}

    </main>

@endsection
