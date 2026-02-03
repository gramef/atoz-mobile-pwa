@extends('layouts.app')

@section('content')

    <main class="section">
        <div class="section__top">
            <nav class="flex-container section__job-types">
                <div class="section__heading">Languages</div>
            </nav>
            <a href="{{ route('languages.create') }}" class="btn btn--primary section__btn">+ Add New Language</a>
        </div>
    </main>

    <section class="content__table">
        <table class="table">
            <thead class="table__header">
                <tr>
                    <th class="table__heading">Name</th>
                    <th class="table__heading">Agents</th>
                    <th class="table__heading">Active Jobs</th>
                    <th class="table__heading"></th>
                </tr>
            </thead>
            <tbody>

                @forelse ($languages as $language)
                    <tr class="table__row">
                        <td class="table__data">{{ $language->name }}</td>
                        <td class="table__data">{{ $language->agents()->count() }}</td>
                        <td class="table__data">{{ $language->activeJobCount }}</td>
                        <td class="table__data">
                            <a class="btn btn--secondary table__btn" href="{{ route('languages.edit', $language->id) }}">View</a>

                            @if(!$language->agents()->count() && !$language->activeJobCount)
                                {{ Form::open([ 'route' => [ 'languages.destroy', $language->id ], 'method' => 'DELETE', 'class' => 'd-inline-block' ]) }}
                                    <button class="btn btn--delete table__btn" type="submit" onclick="return confirm('Are you sure you want to delete this language?')">
                                        Delete
                                    </button>
                                {{ Form::close() }}
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr class="table__row">
                        <td class="table__data table__data--grey" colspan="2">

                            @if (Route::current()->getName() == 'languages.index')
                                There are no languages
                            @else
                                There are no archived languages
                            @endif

                        </td>
                    </tr>
                @endforelse

            </tbody>
        </table>
    </section>

        {{ $languages->links() }}

@endsection
