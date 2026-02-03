@extends('layouts.app')

@section('content')
    
    <main class="section">
        <div class="section__top">
            <div class="section__heading">Edit company</div>

            @if ($company->trashed())
                <div>
                    {{ Form::open([ 'route' => [ 'companies.archived.destroy', $company->id ], 'method' => 'DELETE', 'class' => 'd-inline-block' ]) }}
                        <button class="btn btn--primary table__btn" type="submit">
                            Restore
                        </button>
                    {{ Form::close() }}
    
                    {{ Form::open([ 'route' => [ 'companies.destroy', $company->id ], 'method' => 'DELETE', 'class' => 'd-inline-block' ]) }}
                        <button class="btn btn--delete table__btn" type="submit" onclick="return confirm('Are you sure you want to delete this company?')">
                            Delete
                        </button>
                    {{ Form::close() }}
                </div>
            @endif

        </div>

        {{ Form::model($company, [ 'route' => ['companies.update', $company->id], 'method' => 'PUT' ]) }}
            @include('companies.form')
        {{ Form::close() }}

    </main>

@endsection
