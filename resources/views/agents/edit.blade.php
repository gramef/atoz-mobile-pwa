@extends('layouts.app')

@section('content')
    <main class="section">
        <div class="section__top d-block d-lg-flex">
            <div class="section__heading">{{ $agent->user->getFullName() }}</div>
            <div class="d-flex">

                @role('admin')
                    {{ Form::open([
                        'route' => ['users.send-password-reset-link', $agent->user],
                        'onsubmit' => 'return confirm("Are you sure you want to reset this user\'s password")',
                    ]) }}
                    <button class="btn btn--cancel" type="submit">Reset password</button>
                    {{ Form::close() }}
                @endrole
                {{-- <label class="btn btn--primary form__btn ml-2" for="submit-form">
                    @if ($agent->user->hasRole('new-agent'))
                        Approve New Agent
                    @elseif($agent->restrict_job_notifications == 1)
                        Approve Agent Update / Unrestrict Agent
                    @else
                        Update Agent
                    @endif
                </label> --}}

                <label class="btn btn--primary form__btn ml-2"
                    for="submit-form">{{ $agent->user->hasRole('new-agent') ? 'Approve Agent' : 'Update Agent' }}</label>
            </div>
        </div>

        @include('agents.partials.tabs')

        {{ Form::model($agent, [
            'route' => [$agent->user->hasRole('new-agent') ? 'agents.new.update' : 'agents.update', $agent->id],
            'class' => 'form',
            'method' => 'PUT',
        ]) }}
        @include('agents.form')
        <input type="submit" id="submit-form" hidden>
        {{ Form::close() }}

    </main>
@endsection
