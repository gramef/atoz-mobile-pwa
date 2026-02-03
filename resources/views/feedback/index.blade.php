@extends('layouts.app')

@section('content')

    <main class="section">
        <div class="section__top">
            <nav class="flex-container section__job-types">
                <div class="section__heading">FeedBack</div>
            </nav>

        </div>
    </main>

    <section class="content__table">
        <table class="table">
            <thead class="table__header">
                <tr>

                    <th class="table__heading">Ref#</th>
                    <th class="table__heading">Clent Name</th>
                    <th class="table__heading">Agent Name</th>
                    <th class="table__heading">Appearance</th>
                    <th class="table__heading">Punctuality</th>
                    <th class="table__heading">Quality of Interpreting</th>
                    <th class="table__heading">Empathy</th>
                    <th class="table__heading">Comments</th>
                    <th class="table__heading">Actions</th>
                    <th class="table__heading"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($feedbacks as $feedback)
                    @role('admin')
                        <tr class="table__row">
                            <td class="table__data">{{ $feedback->interpreter->id }}/atoz</td>
                            <td class="table__data">
                                {{ $feedback->interpreter->client->userSheet->first_name . ' ' . $feedback->interpreter->client->userSheet->last_name }}
                            </td>
                            <td class="table__data">
                                {{ $feedback->agentOne->user->first_name . ' ' . $feedback->agentOne->user->last_name }}</td>

                            <td class="table__data">{{ $feedback->appearance_rating }} </td>
                            <td class="table__data">{{ $feedback->punctuality }} </td>
                            <td class="table__data">{{ $feedback->quality_of_interpreting }} </td>
                            <td class="table__data">{{ $feedback->empathy }} </td>
                            <td class="table__data">{{ $feedback->comment }} </td>

                            <td class="table__data d-flex colspan-2">
                                @if ($feedback->agent_status == 'N')
                                    <a class="btn btn-secondary table__btn" style="font-size: 15px; text-decoration: underline;"
                                        href="{{ route('feedback.status', ['id' => $feedback->id]) }}">Allow Agent</a>
                                @else
                                    <span class="btn btn-primary table__btn">Agent Allowed</span>
                                @endif

                            </td>
                        </tr>
                    @endrole
                    @role('agent')
                        @if ($feedback->agent_status == 'Y')
                            <tr class="table__row">
                                <td class="table__data">{{ $feedback->interpreter->id }}/atoz</td>
                                <td class="table__data">
                                    {{ $feedback->interpreter->client->userSheet->first_name . ' ' . $feedback->interpreter->client->userSheet->last_name }}
                                </td>
                                <td class="table__data">
                                    {{ $feedback->agentOne->user->first_name . ' ' . $feedback->agentOne->user->last_name }}
                                </td>

                                <td class="table__data">{{ $feedback->appearance_rating }} </td>
                                <td class="table__data">{{ $feedback->punctuality }} </td>
                                <td class="table__data">{{ $feedback->quality_of_interpreting }} </td>
                                <td class="table__data">{{ $feedback->empathy }} </td>
                                <td class="table__data">{{ $feedback->comment }} </td>
                            </tr>
                        @endif
                    @endrole

                    @role('client')
                        <tr class="table__row">
                            <td class="table__data">{{ $feedback->interpreter->id }}/atoz</td>
                            <td class="table__data">
                                {{ $feedback->interpreter->client->userSheet->first_name . ' ' . $feedback->interpreter->client->userSheet->last_name }}
                            </td>
                            <td class="table__data">
                                {{ $feedback->agentOne->user->first_name . ' ' . $feedback->agentOne->user->last_name }}</td>

                            <td class="table__data">{{ $feedback->appearance_rating }} </td>
                            <td class="table__data">{{ $feedback->punctuality }} </td>
                            <td class="table__data">{{ $feedback->quality_of_interpreting }} </td>
                            <td class="table__data">{{ $feedback->empathy }} </td>
                            <td class="table__data">{{ $feedback->comment }} </td>
                        </tr>
                    @endrole

                @empty
                    <tr class="table__row">
                        <td class="table__data table__data--grey" colspan="2">

                            @if (Route::current()->getName() == 'feedback.index')
                                There are no Feedbacks
                            @else
                                There are no archived Feedbacks
                            @endif

                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="pagination">
            {{ $feedbacks->links() }}
        </div>
    </section>


@endsection
