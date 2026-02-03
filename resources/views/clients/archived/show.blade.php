@extends('layouts.app')

@section('content')

    <main class="job">
        <header class="job__header">
            <h1 class="job__heading">
                Archived client
            </h1>
            <section class="job__actions job__actions--update">

                {{ Form::open([ 'route' => [ 'clients.archived.update', $client->id ], 'method' => 'PUT', 'class' => 'job__btn' ]) }}
                    <button class="btn btn--primary" type="submit" onclick="return confirm('Are you sure you want to restore this client?')">
                        Restore
                    </button>
                {{ Form::close() }}

            </section>
        </header>
        <section class="job__section job__section--no-border">
            <div class="row">
                <div class="col-lg-5">
                    <fieldset class="job__field">
                        <label class="job__label">
                            Account details:
                            <div class="job__text">Account type: {{ !is_null($client->organisation) ? 'Organisation' : 'Individual' }}</div>
                            <br>
                            <div class="job__text">Title: {{ config('enums.titles')[$client->user->title] }}</div>
                            <div class="job__text">First name: {{ $client->user->first_name }}</div>
                            <div class="job__text">Last name: {{ $client->user->last_name }}</div>
                            <div class="job__text">Email: {{ $client->user->email }}</div>
                            <div class="job__text">Contact number: {{ $client->contact_number }}</div>
                            <br>

                            @if (!is_null($client->organisation))
                                <div class="job__text">Company: {{ $client->organisation->organisation_company }}</div>
                                <div class="job__text">VAT number: {{ $client->organisation->vat_number ?? 'N/A' }}</div>
                                <div class="job__text">Company number: {{ $client->organisation->company_number }}</div>
                                <br>
                            @endif

                            <div class="job__text job__text--bold job__label--inline">Communication preferences:

                                @foreach ($client->contactMethods as $contactMethod)
                                    <span class="job__text job__text--inline">{{ $contactMethod->contact_method }}</span>
                                @endforeach

                            </div>
                        </label>
                    </fieldset>
                </div>
                <div class="col-lg-4 offset-lg-1">
                    <fieldset class="job__field">
                        <label class="job__label">
                            Address details:
                            <div class="job__text">Address line 1: {{ $client->client_address_line_1 }}</div>
                            <div class="job__text">Address line 2: {{ $client->client_address_line_2 ?? 'N/A' }}</div>
                            <div class="job__text">County: {{ $client->client_county }}</div>
                            <div class="job__text">Postcode: {{ $client->client_postcode }}</div>

                            @if (!is_null($client->organisation))
                                <br>
                                Invoice details:
                                <div class="job__text">Address line 1: {{ $client->organisation->organisation_address_line_1 }}</div>
                                <div class="job__text">Address line 2: {{ $client->organisation->organisation_address_line_2 ?? 'N/A' }}</div>
                                <div class="job__text">County: {{ $client->organisation->organisation_county }}</div>
                                <div class="job__text">Postcode: {{ $client->organisation->organisation_postcode }}</div>
                                <div class="job__text">Invoice email: {{ $client->organisation->organisation_email }}</div>
                            @endif

                        </label>
                    </fieldset>
                </div>
            </div>
        </section>
    </main>

@endsection
