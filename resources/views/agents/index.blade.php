@extends('layouts.app')

@section('content')

    @include('agents.partials.nav')
    @include('agents.partials.filter')

    <section class="content__table">
        <table class="table">
            <thead class="table__header">
                <tr>
                    <th class="table__heading">Agent Name</th>
                    <th class="table__heading">Agent type</th>
                    <th class="table__heading">Email</th>
                    <th class="table__heading">Contact Number</th>
                    <th class="table__heading">Languages</th>
                    <th class="table__heading">No. of DNAs</th>
                    <th class="table__heading">No. of Late Arrivals</th>
                    <th class="table__heading"></th>
                </tr>
            </thead>
            <tbody>

                @forelse ($agents as $agent)
                    <tr class="table__row">
                        <td class="table__data">{{ $agent->user->getFullName() }}</td>
                        <td class="table__data">{{ $agent->getAgentType() }}</td>
                        <td class="table__data">
                            <a  class="table__link" href="mailto:{{ $agent->user->email }}">
                                {{ $agent->user->email }}
                            </a>
                        </td>
                        <td class="table__data">{{ $agent->contact_number }}</td>
                        <td class="table__data">{{ implode(', ', $agent->languages->pluck('name')->all()) }}</td>
                        <td class="table__data">0</td>
                        <td class="table__data">0</td>
                        <td class="table__data table__data--actions">

                            @if ($agent->user->hasRole('agent'))
                                {{ Form::open([ 'route' => [ 'agents.archived.update', $agent->id ], 'method' => 'PUT', 'class' => 'd-inline-block' ]) }}
                                    <button class="btn btn--grey table__btn" type="submit" onclick="return confirm('Are you sure you want to archive this agent?')">
                                        Archive
                                    </button>
                                {{ Form::close() }}
                            @endif

                            <a class="btn btn--secondary table__btn" href="{{ route('agents.edit', $agent->id) }}">View</a>
                            <a class="btn btn--secondary table__btn" href="{{ route('impersonate', $agent->user->id) }}">Login As</a>
                        </td>
                    </tr>
                @empty
                    <tr class="table__row">
                        <td class="table__data table__data--grey" colspan="8">
                            There are no active agents
                        </td>
                    </tr>
                @endforelse

            </tbody>
        </table>
    </section>

    {{ $agents->appends(request()->all())->links() }}

@endsection