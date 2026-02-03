@extends('layouts.app')

@section('content')

    <nav class="nav">
        <h1 class="nav__heading">Companies</h1>
        <ul class="links">
            <li class="links__item">

                @if (Route::current()->getName() == 'companies.index')
                    <div class="links__text">View all</div>
                @else
                    <a href="{{ route('companies.index') }}" class="links__text links__text--link">View all</a>
                @endif

            </li>

            <li class="links__item links__item--last">

                @if (Route::current()->getName() != 'companies.index')
                    <div class="links__text">Archived</div>
                @else
                    <a href="{{ route('companies.archived.index') }}" class="links__text links__text--link">Archived</a>
                @endif

            </li>
        </ul>
        <a href="{{ route('companies.create') }}" class="btn btn--primary nav__btn">+ Add Company</a>
    </nav>

    @include('partials.company-filter-form')

    </main>
    <section class="content__table">
        <table class="table">
            <thead class="table__header">
                <tr>
                    <th class="table__heading">Name</th>
                    <th class="table__heading"></th>
                </tr>
            </thead>
            <tbody>

                @forelse ($companies as $company)
                    <tr class="table__row">
                        <td class="table__data">{{ $company->name }}</td>
                        <td class="table__data">

                            @if (Route::current()->getName() == 'companies.index')
                                <a class="btn btn--secondary table__btn" href="{{ route('companies.edit', $company->id) }}">Edit</a>

                                {{ Form::open([ 'route' => [ 'companies.archived.update', $company->id ], 'method' => 'PUT', 'class' => 'd-inline-block' ]) }}
                                    <button class="btn btn--grey table__btn" type="submit" onclick="return confirm('Are you sure you want to archive this company?')">
                                        Archive
                                    </button>
                                {{ Form::close() }}
                            @else
                                <a class="btn btn--secondary table__btn" href="{{ route('companies.edit', $company->id) }}">View</a>

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
                            @endif

                        </td>
                    </tr>
                @empty
                    <tr class="table__row">
                        <td class="table__data table__data--grey" colspan="2">

                            @if (Route::current()->getName() == 'companies.index')
                                There are no companies
                            @else
                                There are no archived companies
                            @endif

                        </td>
                    </tr>
                @endforelse

            </tbody>
        </table>
    </section>

        {{ $companies->links() }}

@endsection
