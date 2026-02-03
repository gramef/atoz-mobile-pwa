@extends('layouts.app')

@section('content')

    <main class="section">

        {{ Form::model($client, [ 
                'route' => [ $client->rejected ? 'clients.destroy' : 'clients.new-requests.client.update', $client->id ], 
                'method' => $client->rejected ? 'DELETE' : 'PUT', 
                'id' => 'requestForm',
                'onsubmit' => $client->rejected ? 'return confirm("Are you sure you want to delete this request?")' : ''
            ]) 
        }}

            <div class="section__top mb-xl-5">
                <div class="section__heading">{{ $client->user->getFullName() }} (New Request, {{ $client->getRequestType() }})</div>
                <div class="d-flex section__actions">

                    @unless ($client->rejected)
                        {{ Form::submit('Approve', ['class' => 'btn btn--primary form__btn form__btn--approve d-none d-xl-flex']) }}
                        {{ Form::button('Reject', [
                                'class' => 'btn btn--delete form__btn form__btn--reject d-none d-xl-flex', 
                                'data-button' => 'reject'
                            ]) 
                        }}
                    @else
                        {{ Form::submit('Delete', ['class' => 'btn btn--delete form__btn form__btn--approve d-none d-xl-flex']) }}
                    @endunless

                </div>
            </div>

            @include('clients.new-requests.partials.tabs')

            @include('clients.form')

            @unless ($client->rejected)
                {{ Form::submit('Approve', ['class' => 'btn btn--primary form__btn form__btn--approve d-xl-none mb-3']) }}
                {{ Form::button('Reject', [
                        'class' => 'btn btn--delete form__btn form__btn--reject d-xl-none', 
                        'data-button' => 'reject'
                    ]) 
                }}
            @else
                {{ Form::submit('Delete', ['class' => 'btn btn--delete form__btn form__btn--approve d-xl-none']) }}
            @endunless

        {{ Form::close() }}

        @unless ($client->rejected)
            {{ Form::open([ 'route' => ['clients.rejected.update', $client->id], 'method' => 'PUT', 'id' => 'rejectForm' ]) }}
            {{ Form::close() }}
        @endunless

    </main>
    
    @if ($client->rejected)
        @push('scripts')
            <script>
                window.addEventListener('load', function() {
                    $('#requestForm input,#requestForm select,#requestForm textarea')
                        .not("[name='_token'], [name='_method'], .btn")
                        .attr('disabled', true);
                })
            </script>
        @endpush
    @endif

@endsection
