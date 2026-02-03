@extends('layouts.app')

@section('content')
    <main class="section">
        <div class="section__top">
            <nav class="flex-container section__job-types">
                <div class="section__heading">TimeSheet</div>
            </nav>

        </div>
        @include('timesheet.partials.filter')
    </main>

    <section class="content__table">
        <table class="table">
            <thead class="table__header">
                <tr>
                    <th class="table__heading">Ref#</th>
                    <th class="table__heading">Agent Name</th>
                    <th class="table__heading">Agent Signature</th>
                    <th class="table__heading">Client Name</th>
                    <th class="table__heading">Client Signature</th>
                    {{-- <th class="table__heading">Submittedt Date</th> --}}
                    <th class="table__heading">Booked Date</th>
                    <th class="table__heading">TimeSheet Assign Date</th>
                    {{-- <th class="table__heading">Bulk ID</th> --}}
                    <th class="table__heading">From Language</th>
                    <th class="table__heading">To Language</th>
                    <th class="table__heading">Action</th>
                    <th class="table__heading"></th>
                </tr>
            </thead>
            <tbody>
                @unlessrole('new-agent')
                    @forelse ($timesheets as $timesheet)
                        <tr class="table__row">
                            <td class="table__data">{{ $timesheet->interpreter->id }}/atoz</td>
                            <td class="table__data">
                                {{ $timesheet->agentOne->user->first_name . ' ' . $timesheet->agentOne->user->last_name }}</td>
                            <td class="table__data"><img height="40px" width="140px" src="{{ $timesheet->agent_signature }}"
                                    alt="NA" /></td>
                            <td class="table__data">
                                {{ $timesheet->interpreter->client->userSheet->first_name . ' ' . $timesheet->interpreter->client->userSheet->last_name }}
                            </td>
                            <td class="table__data"><img height="40px" width="140px" src="{{ $timesheet->client_signature }}"
                                    alt="NA" /></td>
                            {{-- <td class="table__data">{{ $timesheet->interpreter->created_at }} </td> --}}
                            <td class="table__data">{{ $timesheet->interpreter->appointment_date }} </td>
                            <td class="table__data">{{ $timesheet->created_at }} </td>
                            {{-- <td class="table__data">{{ $timesheet->interpreter->bulk_id }} </td> --}}
                            <td class="table__data">{{ $timesheet->interpreter->from_language->name }} </td>
                            <td class="table__data">{{ $timesheet->interpreter->to_language->name }} </td>

                            <td class="table__data d-flex colspan-2">
                                @if ($timesheet->interpreter->statusName == 'completed')
                                    <a href="{{ route('timesheet-pdf', ['id' => $timesheet->id]) }}"
                                        class="btn btn--primary table__btn my-1">TimeSheet Print</a>
                                @endif

                            </td>
                        </tr>


                    @empty
                        <tr class="table__row">
                            <td class="table__data table__data--grey" colspan="2">

                                @if (Route::current()->getName() == 'timesheets.index')
                                    There are no timesheets
                                @else
                                    There are no archived timesheets
                                @endif

                            </td>
                        </tr>
                    @endforelse
                @endunlessrole
            </tbody>
        </table>
        @unlessrole('new-agent')
            <div class="pagination">
                {{ $timesheets->appends(request()->query())->links() }}
            </div>
        @endunlessrole
    </section>
@endsection
