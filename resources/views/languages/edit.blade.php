@extends('layouts.app')

@section('content')

    <main class="section">
        <div class="section__top">
            <div class="section__heading">Edit Language</div>
            @if(!$language->agents()->count() && !$language->activeJobCount)
                <div>
                    {{ Form::open([ 'route' => [ 'languages.destroy', $language->id ], 'method' => 'DELETE', 'class' => 'd-inline-block' ]) }}
                        <button class="btn btn--delete table__btn" type="submit" onclick="return confirm('Are you sure you want to delete this language?')">
                            Delete
                        </button>
                    {{ Form::close() }}
                </div>
            @endif
        </div>

        {{ Form::model($language, [ 'route' => ['languages.update', $language->id], 'method' => 'PUT' ]) }}
            @include('languages.form')
        {{ Form::close() }}

    </main>

@endsection
