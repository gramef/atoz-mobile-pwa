@extends('layouts.app')

@section('content')
    <main class="section">

        @include( 'partials.job-header', ['job' => $interpreterJob, 'type' => 'interpreter-jobs'])

        <section class="job__section job__section--no-border">
            <h2 class="label">Agent information</h2>
            <div class="row">
                <div class="col-lg-5">
                    <article class="bg-white px-3 py-4 p-lg-5">
                        <div class="row d-lg-flex align-items-center">
                            <div class="col-lg-5 offset-lg-1">
                                <img src="{{ $agent->getProfilePicture() }}" alt="Agent profile picture"
                                    class="rounded-circle d-block mx-auto mb-4 mb-lg-0 agent__image">
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-4">
                                    <p class="label mb-2">Agent information:</p>
                                    <p class="label agent__detail">
                                        {{ $agent->user ? $agent->user->getFullNameWithTitle() : 'DELETED USER' }}</p>
                                </div>
                                <div class="mb-4">
                                    <p class="label mb-2">Interpreter Type:</p>
                                    <p class="label agent__detail">{{ $agent->getAgentType() }}</p>
                                </div>
                                <div class="mb-4">
                                    <p class="label mb-2">Gender:</p>
                                    <p class="label agent__detail mb-0">{{ $agent->getGenderName() }}</p>
                                </div>
                                @if (($interpreterJob->skill_id == 3 || $interpreterJob->skill_id == 4) && $agent && isset($agent->contact_number))
                                    <div class="mb-4">
                                        <p class="label mb-2">Telephone:</p>
                                        <p class="label agent__detail mb-0">{{ $agent->contact_number }}</p>
                                    </div>
                                @endif
                                @if ($interpreterJob->skill_id == 4 && $agent && isset($agent->user->email))
                                    <div class="mb-4">
                                        <p class="label mb-2">Email:</p>
                                        <p class="label agent__detail mb-0">{{ $agent->user->email }}</p>
                                    </div>
                                @endif
                                <div class="mb-4">
                                    <p class="label mb-2">DBS Expiry Date:</p>
                                    <p class="label agent__detail mb-0">
                                        {{ optional($agent->dbs_expiry_date)->format('d/m/Y') ?? 'n/a' }}</p>
                                </div>
                                <div class="mb-4">
                                    <p class="label mb-2">DBS Number:</p>
                                    <p class="label agent__detail mb-0">{{ $agent->dbs_number }}</p>
                                </div>
                                <div class="mb-4">
                                    <p class="label mb-2">Induction Date:</p>
                                    <p class="label agent__detail mb-0">
                                        {{ optional($agent->induction_date)->format('d/m/Y') ?? 'n/a' }}</p>
                                </div>
                                <div class="mb-4">
                                    <p class="label mb-2">DBS Update Reference Number:</p>
                                    <p class="label agent__detail mb-0">{{ $agent->dbs_update_reference_number ?? 'n/a' }}
                                    </p>
                                </div>

                            </div>
                        </div>
                    </article>
                </div>
            </div>
        </section>
    </main>
@endsection
