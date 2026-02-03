@extends('layouts.app')

@section('content')

<main class="section">

    @include('partials.job-header', [ 'job' => $translatorJob, 'type' => 'translator-jobs' ])


    <section class="content__table">
        <table class="table">
            <thead class="table__header">
                <tr>
                    <th class="table__heading">S.NO</th>
                    <th class="table__heading">User Name</th>
                    <th class="table__heading">User type</th>
                    <th class="table__heading">Job Id</th>
                    <th class="table__heading">Job New status</th>
                    <th class="table__heading">Comment</th>
                    <th class="table__heading">Updated DateTime</th>
                
                </tr>
     

            </thead>
            <tbody>
                       @forelse($job_update as $key => $updates)
            <tr class="table__row">
               
                                    <td class="table__data">{{ $key+ 1 }}</td>
                                    <td class="table__data">{{ $updates->user->first_name }} {{$updates->user->last_name }}</td>
                                    <td class="table__data">{{ $updates->user_type }}</td>
                                    <td class="table__data">{{ $updates->job_id}}</td>
                                    <td class="table__data">{{ $updates->code }}</td>
                                    <td class="table__data">{{ $updates->comment }}</td>
                                    <td class="table__data">{{ $updates->update_date }}</td>
                              
                                                                
                                    @empty
                                <tr class="table__row">
                                    <td class="table__data table__data--grey" colspan="5">
                                        No updates for this job 
                                    </td>
                                </tr>
                            @endforelse
            </tbody>
</section>
</main>

@endsection