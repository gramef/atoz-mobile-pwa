@extends('layouts.app')

@section('content')

    <div class="pb-5">
        @include('clients.partials.nav')
    </div>

    <section class="content__table">
        <table class="table">
            <thead class="table__header">
                <tr>
                    <th class="table__heading">Client Name</th>
                    <th class="table__heading">Company Name</th>
                    <th class="table__heading">Job Type</th>
                    <th class="table__heading">Email</th>
                    <th class="table__heading">Contact Number</th>
                    <th class="table__heading"></th>
                </tr>
            </thead>
            <tbody>

                @forelse ($clients as $client)
                    <tr class="table__row">
                        <td class="table__data">{{ $client->user->getFullName() }}</td>
                        <td class="table__data">{{ $client->organisation->company->name ?? 'N/A' }}</td>
                        <td class="table__data">{{ $client->interpreterJobs->isNotEmpty() ? 'Interpreter' : 'Translation' }}</td>
                        <td class="table__data">
                            <a class="table__link" href="mailto:{{ $client->user->email }}">
                                {{ $client->user->email }}
                            </a>
                        </td>
                        <td class="table__data">{{ $client->contact_number }}</td>
                        <td class="table__data table__data--actions table__data--wide">

                            {{ Form::open([ 
                                    'route' => [ 'clients.new-requests.update', $client->id ], 
                                    'method' => 'PUT', 'class' => 'd-inline-block' 
                                ]) 
                            }}
                                <button class="btn btn--primary table__btn" type="submit">Approve</button>
                            {{ Form::close() }}

                            {{ Form::open([ 
                                    'route' => [ 'clients.rejected.update', $client->id ], 
                                    'method' => 'PUT', 
                                    'class' => 'd-inline-block' 
                                ]) 
                            }}
                                <button class="btn btn--cancel table__btn" type="submit" onclick="return confirm('Are you sure you want to reject this request?')">Reject</button>
                            {{ Form::close() }}

                            @if (!$client->organisation)
                                <a class="btn btn--secondary table__btn" href="{{ route('clients.new-requests.client.edit', $client->id) }}">View</a>
                            @else
                                <a class="btn btn--secondary table__btn" href="{{ route('clients.new-requests.client.edit', ['client' => $client->id, 'organisation' => true]) }}">View</a>
                            @endif

                        </td>
                    </tr>
                @empty
                    <tr class="table__row">
                        <td class="table__data table__data--grey" colspan="7">
                            There are no new requests
                        </td>
                    </tr>
                @endforelse

            </tbody>
        </table>
    </section>

        {{ $clients->links() }}

        @includeWhen(!auth()->user()->hasRole('agent'), 'partials.cancel-modal')

@endsection
