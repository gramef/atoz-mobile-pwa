@extends('layouts.app')

@section('content')

    <main class="section">
        <div class="section__top mb-xl-5">
            <div class="section__heading">Edit client</div>
            <div class="d-flex">

                @role('admin')
                    {{ Form::open([
                            'route' => ['users.send-password-reset-link', $client->user],
                            'onsubmit' => 'return confirm("Are you sure you want to reset this user\'s password")'
                        ])
                    }}
                        <button class="btn btn--cancel" type="submit">Reset password</button>
                    {{ Form::close() }}
                @endrole

                <label class="btn btn--primary form__btn d-none d-xl-flex ml-2" for="submit-form">Update Client</label>
            </div>
        </div>

        {{ Form::model($client, [ 'route' => ['clients.update', $client->id], 'method' => 'PUT', 'id' => 'clients_form' ]) }}
            @include('clients.form')
            <input id="submit-form"  type="submit" hidden>
        {{ Form::close() }}

        <label id="submit-form" class="btn btn--primary form__btn d-xl-none" for="submit-form">Update Client</label>

    </main>
@endsection
@push('scripts')
    <script>
        @if($client->organisation)
            if ($('#isNotOrganisation').is(':checked')){
                $('#submit-form').on('click', function (event){
                    event.preventDefault()
                    if (confirm('Are you sure you want to change the client to an individual, this will delete the organisations record')) {
                        $('#clients_form').submit()
                    }
                });
            }
        @endif
    </script>
@endpush

