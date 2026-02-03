 @extends('layouts.app')

@section('content')

<main class="job">

    @include('partials.job-header', [ 'job' => $interpreterJob, 'type' => 'interpreter-jobs' ])

    <div class="row">
        <div class="col-xl-6 overflow-auto">
            <table class="table table--detail shadow-none w-100 mb-5 mb-xl-0">
                <tr class="table__row table__row--short">
                    <td class="p-0 h-100">
                        <div class="job__detail pl-4 d-flex align-items-center h-100">
                            Company name:
                        </div>
                    </td>
                    <td class="p-0 h-100">
                        <div class="text-black pl-4 d-flex align-items-center h-100">
                            {{ $interpreterJob->client->organisation->company->name ?? 'n/a' }}
                        </div>
                    </td>
                </tr>
                <tr class="table__row table__row--short">
                    <td class="p-0 h-100">
                        <div class="job__detail pl-4 d-flex align-items-center h-100">
                            Client name:
                        </div>
                    </td>
                    <td class="p-0 h-100">
                        <div class="text-black pl-4 d-flex align-items-center h-100">
                            {{ $interpreterJob->client->user->getFullNameWithTitle() }}
                        </div>
                    </td>
                </tr>
                <tr class="table__row table__row--short">
                    <td class="p-0 h-100">
                        <div class="job__detail pl-4 d-flex align-items-center h-100">
                            Language:
                        </div>
                    </td>
                    <td class="p-0 h-100">
                        <div class="text-black pl-4 d-flex align-items-center h-100">
                            {{ $interpreterJob->toLanguage->name }}
                        </div>
                    </td>
                </tr>
                <tr class="table__row table__row--short">
                    <td class="p-0 h-100">
                        <div class="job__detail pl-4 pt-3 d-flex align-items-start h-100">
                            Interpreter details:
                        </div>
                    </td>
                    <td class="text-black pl-4">
                        <div>{{ $interpreterJob->skill->skill }}</div>
                        <div>     @if ($interpreterJob->required_qualified == 1)
            "Court Qualified Interpreter"
        @elseif ($interpreterJob->required_qualified == 2)
                     "A Community Interpreter"
          
        @elseif ($interpreterJob->required_qualified == 3)
            "Level 2,3,4 Community Interpreter"
        @elseif ($interpreterJob->required_qualified == 4)
            "Qualified Translator"
        @endif</div>
        
        <div>@if ($interpreterJob->security_type_id== 1)
                "Enhanced Dbs"
        @elseif ($interpreterJob->require_security == 2)
            "Standard Dbs"
        @elseif ($interpreterJob->require_security == 3)
            "National Security Vettin"
        @elseif ($interpreterJob->require_security == 4)
            "Dbs Not required"
        @endif</div>
                        <div>{{ $interpreterJob->getGenderName() }}</div>
                    </td>
                </tr>
                <tr class="table__row table__row--short">
                    <td class="p-0 h-100">
                        <div class="job__detail pl-4 d-flex align-items-center h-100">
                            Appointment date and time:
                        </div>
                    </td>
                    <td class="p-0 h-100">
                        <div class="text-black pl-4 d-flex align-items-center h-100">
                            <span class="mr-4">{{ $interpreterJob->appointment_date->format('d/m/Y') }}</span>
                            {{ $interpreterJob->start_time }}
                        </div>
                    </td>
                </tr>
                <tr class="table__row table__row--short">
                    <td class="p-0 h-100">
                        <div class="job__detail pl-4 d-flex align-items-center h-100">
                            Appointment duration:
                        </div>
                    </td>
                    <td class="p-0 h-100">
                        <div class="text-black pl-4 d-flex align-items-center h-100">
                            {{ $interpreterJob->formattedDuration }}
                        </div>
                    </td>
                </tr>
                <tr class="table__row table__row--short">
                    <td class="p-0 h-100">
                        <div class="job__detail pl-4 d-flex align-items-center h-100">
                            Service user details:
                        </div>
                    </td>
                    <td class="p-0 h-100">
                        <div class="text-black pl-4 d-flex align-items-center h-100">
                            Not required
                        </div>
                    </td>
                </tr>
                <tr class="table__row table__row--short">
                    <td class="p-0 h-100">
                        <div class="job__detail pl-4 d-flex align-items-center h-100">
                            Special instructions:
                        </div>
                    </td>
                    <td class="p-0 h-100">
                        <div class="text-black pl-4 d-flex align-items-center h-100">
                            {{ $interpreterJob->special_requirements ?? 'None' }}
                        </div>
                    </td>
                </tr>
                <tr class="table__row table__row--short">
                    <td class="p-0 h-100">
                        <div class="job__detail pl-4 d-flex align-items-center h-100">
                            Client reference:
                        </div>
                    </td>
                    <td class="p-0 h-100">
                        <div class="text-black pl-4 d-flex align-items-center h-100">
                            {{ $interpreterJob->client_reference ?? 'n/a' }}
                        </div>
                    </td>
                </tr>
            </table>
        </div>
        
        @if ($interpreterJob->hasAddressFields())
            <div class="col-xl-6 d-lg-flex flex-lg-column">
                <div class="position-relative">
                    <div id="map" class="map position-relative"></div>
                    <a class="btn btn--secondary btn--get-directions"
                        href="http://maps.google.com/maps?saddr={{ auth()->user()->agent->latitude }},{{ auth()->user()->agent->longitude }}&daddr={{ $interpreterJob->latitude }},{{ $interpreterJob->longitude }}"
                        target="_blank" rel="noopener noreferrer">Get directions</a>
                </div>
                <div class="bg-white p-4 pb-md-5 flex-lg-fill">
                    <div class="row">
                        <div class="col-xl-6">
                            <div class="job__detail mb-2">Appointment address</div>
                            <address class="text-black">
                                <div class="job__address mb-1">{{ $interpreterJob->address_line_1 }}</div>
                                <div class="job__address mb-1">{{ $interpreterJob->address_line_2 }}</div>
                                <div class="job__address mb-1">{{ $interpreterJob->county }}</div>
                                <div class="job__address mb-1">{{ $interpreterJob->postcode }}</div>
                            </address>
                        </div>
                        <div class="col-xl-6">
                            <div class="job__detail mb-2">Contact information of person on arrival</div>
                            <div class="job__address text-black">
                                {{ $interpreterJob->contact_information_is_same_as_account ? 'Same as client account' : $interpreterJob->contact_information  }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="col-12">

            @if ($interpreterJob->getStatusForAgent() == 'matched')
                {{ Form::open(['route' => ['interpreter-jobs.matched.reject', $interpreterJob->id]]) }}
                    {{ Form::submit('Reject Job', ['class' => 'btn btn--reject btn--long mt-4']) }}
                {{ Form::close() }}
            @endif

            <hr class="mt-4">
            <footer class="d-flex justify-content-between">
                <span>Submitted: {{ $interpreterJob->created_at->format('d/m/Y') }}</span>
                <span>Last updated: {{ $interpreterJob->updated_at->format('d/m/Y') }}</span>
            </footer>
        </div>
    </div>
</main>

@endsection

@push('scripts')
    <script>
        function initMap() {
            var jobLocation = { lat: {{ $interpreterJob->latitude }}, lng: {{ $interpreterJob->longitude }} },

            map = new google.maps.Map(document.getElementById('map'), {
                center: jobLocation,
                zoom: 8
            })

            new google.maps.Marker({
                position: jobLocation,
                map: map,
            })
        }
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDKypyV_to1UeVcCmygrW9UIa_VVHGHFXU&callback=initMap" async
        defer></script>
@endpush
