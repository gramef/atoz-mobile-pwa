@extends('layouts.app')

@section('content')

    <main class="section">

        {{ Form::model($agent, [
                'route' => [ 'agents.documents.update', $agent->id ],
                'class' => 'form',
                'method' => 'PUT',
                'files' => true,
                'onsubmit' => auth()->user()->hasRole('agent') ? 'return confirm("Updating your documents will require you be approved again, are you sure")' : '',
            ])
        }}

            <div class="section__top d-block d-lg-flex mb-lg-5">
                <div class="section__heading">My Account</div>

                <div class="d-md-flex">
                    <p class="profile__notice m-md-0">Please note an admin will be notified of any changes and new email addresses will require validation.</p>
                    {{ Form::submit('Update Documents', ['class' => 'btn btn--primary form__btn']) }}
                </div>

            </div>

            @include('agents.partials.tabs')

            @include('agents.documents.form')

        {{ Form::close() }}

    </main>

@endsection
