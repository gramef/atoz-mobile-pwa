@extends('layouts.app')

@section('content')

    <main class="section">
        
        {{ Form::model(auth()->user(), [ 'route' => [ 'agents.profile.store' ], 'class' => 'form' ]) }}
        
            <div class="section__top d-block d-lg-flex">
                <div class="section__heading">My Account</div>
                <div class="d-md-flex">
                    <p class="profile__notice m-md-0">Please note an admin will be notified of any changes and new email addresses will require validation.</p>
                    {{ Form::submit('Create Account', ['class' => 'btn btn--primary form__btn']) }}
                </div>
            </div>

            <ul class="tabs">
                <li class="tabs__tab">
                    <div class="tabs__text">
                        Agent Details
                    </div>      
                </li>
            </ul>

            @include('agents.form')

        {{ Form::close() }}

    </main>
    
@endsection