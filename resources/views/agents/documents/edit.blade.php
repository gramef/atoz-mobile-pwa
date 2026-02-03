@extends('layouts.app')

@section('content')
    <main class="section">
        {{ Form::model($agent, [
            'route' => ['agents.documents.update', $agent->id],
            'class' => 'form',
            'method' => 'PUT',
            'files' => true,
        ]) }}
        <div class="section__top d-block d-lg-flex">
            <div class="section__heading">{{ $agent->user->getFullName() }}</div>
            <div class="d-flex">
                <div>
                    {{ Form::submit('Save Documents', ['class' => 'btn btn--cancel']) }}
                </div>
                <div>
                    <button type="submit" class="btn btn-primary form__btn ml-2" name="approve"
                        value ="approve">{{ $agent->user->hasRole('new-agent') ? 'Approve Agent' : 'Update Agent' }}</button>
                </div>
            </div>
        </div>

        @include('agents.partials.tabs')

        @include('agents.documents.form')

        {{ Form::close() }}

    </main>
@endsection
