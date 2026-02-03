@extends('layouts.app')

@section('content')

    <main class="section">
        <div class="section__top">
            <nav class="flex-container section__job-types">
                <div class="section__heading">Admins</div>
            </nav>
            <a href="{{ route('admins.create') }}" class="btn btn--primary section__btn">+ Add New Admin</a>
        </div>
    </main>

    @if ($admins->isNotEmpty())
        <section class="content__table">
            <table class="table">
                <thead class="table__header">
                    <tr>
                        <th class="table__heading">Name</th>
                        <th class="table__heading">Email</th>
                        <th class="table__heading">User Level</th>
                        <th class="table__heading"></th>
                    </tr>
                </thead>
                <tbody>

                    @foreach ($admins as $admin)
                        <tr class="table__row">
                            <td class="table__data">{{ $admin->getFullName() }}</td>
                            <td class="table__data">{{ $admin->email }}</td>
                            <td class="table__data">{{ $admin->getUserLevel() }}</td>
                            <td class="table__data table__data--actions">
                                <a class="btn btn--secondary table__btn" href="{{ route('admins.edit', $admin->id) }}">Edit</a>

                                {{ Form::open([ 
                                        'route' => [ 'admins.destroy', $admin->id ], 
                                        'method' => 'DELETE', 
                                        'class' => 'd-inline-block',
                                        'onsubmit' => 'return confirm("Are you sure you want to delete this admin?")'
                                    ]) 
                                }}
                                    <button class="btn btn--grey table__btn" type="submit">
                                        Delete
                                    </button>
                                {{ Form::close() }}
                            </td>
                        </tr>
                    @endforeach

                </tbody>
            </table>
        </section>

        {{ $admins->links() }}

    @else
        <div class="content__heading">No admins were found, <a href="{{ route('admins.create') }}">create one?</a></div>
    @endif

@endsection
