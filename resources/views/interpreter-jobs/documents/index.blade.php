@extends('layouts.app')


<style>
    #signature-pad {
        border: 1px solid #000;
        width: 400px;
        height: 200px;
    }

    form {
        max-width: 600px;
        margin: auto;
        border: 1px solid #ccc;
        padding: 20px;
        border-radius: 10px;
        background-color: #f9f9f9;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-group label {
        display: block;
        margin-bottom: 5px;
    }

    .form-group input[type="radio"] {
        margin-right: 10px;
    }

    textarea {
        width: 100%;
        height: 100px;
        padding: 10px;
        margin-top: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        resize: vertical;
    }

    .form-group button {
        display: inline-block;
        padding: 10px 20px;
        font-size: 16px;
        color: white;
        background-color: #007bff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    .form-group button:hover {
        background-color: #0056b3;
    }

    /* .formBorder {
            border: 1px solid;
            box-shadow: 0px 5px 11px 3px;
            padding: 10px;
        } */
</style>


@section('content')
    <main class="section">

        @include('partials.job-header', ['job' => $interpreterJob, 'type' => 'interpreter-jobs'])

        <div class="row">

            <table class="table">
                <thead class="table__header">
                    <tr>
                        <th class="table__heading">S.NO</th>
                        <th class="table__heading">Start Time</th>
                        <th class="table__heading">End Time</th>
                        <th class="table__heading">Travel Amount</th>

                        <th class="table__heading">Travel Date</th>
                        <th class="table__heading"></th>

                    </tr>


                </thead>
                <tbody>
                    @if (!empty($travel))
                        <tr>
                            <td class="table__data">{{ $interpreterJob->id }}/atoz</td>
                            <td class="table__data">{{ $travel->travel_start_time }}</td>
                            <td class="table__data">{{ $travel->travel_end_time }}</td>
                            <td class="table__data">{{ $travel->travel_amount }}</td>
                            <td class="table__data">{{ $travel->travel_date }}</td>
                        </tr>
                    @endif

                </tbody>
            </table>


            <div class="col-xl-4">

                @include('interpreter-jobs.partials.file-upload', [
                    'interpreterJob' => $interpreterJob,
                    'type' => config('enums.document_types')['translated_file'],
                    'label' => 'Travel Expences',
                ])

                @if ($interpreterJob->affirmation)
                    <div class="mt-xl-4">
                        @include('interpreter-jobs.partials.file-upload', [
                            'interpreterJob' => $interpreterJob,
                            'type' => config('enums.document_types')['affirmation'],
                            'label' => 'Affirmation',
                        ])
                    </div>
                @endif

                @if ($interpreterJob->affidavit)
                    <div class="mt-xl-4">
                        @include('interpreter-jobs.partials.file-upload', [
                            'interpreterJob' => $interpreterJob,
                            'type' => config('enums.document_types')['affidavit'],
                            'label' => 'Sworn affadavit',
                        ])
                    </div>
                @endif

            </div>

            <div class="col-xl-2">

            </div>

            <div class="col-xl-6">


                @role('agent')
                    {{-- @if ($interpreterJob->isWithin48Hours() == true) --}}
                    {{ Form::open(['route' => ['travelDetails.travel_details'], 'id' => 'travel-form', 'method' => 'POST']) }}
                    @csrf
                    <input type="hidden" name="job_id" value="{{ $interpreterJob->id }}">
                    <input type="hidden" name="client_id" value="{{ $interpreterJob->client->id }}">
                    <input type="hidden" name="agent_id" value="{{ $interpreterJob->agent_id }}">
                    <input type="hidden" name="file"
                        value="{{ !empty($interpreterJob->documents[0]->fullurl) ? $interpreterJob->documents[0]->fullurl : 'fildd' }}">
                    <div class="col-md-12 formBorder">
                        <h5>Please Update Travel Details</h5>

                        <div class="form-group">
                            <label for="start_time">Travel Start Time:</label>
                            <input type="text" name="start_time" class="input form__input form__input--time" id="start_time"
                                placeholder="00:00">
                        </div>

                        <div class="form-group">
                            <label for="end_time">Travel End Time:</label>
                            <input type="text" name="end_time" class="input form__input form__input--time" id="end_time"
                                placeholder="00:00">
                        </div>

                        <div class="form-group">
                            <label for="amount">Travel Amount:</label>
                            <input type="number" name="amount" class="input" id="" placeholder="0" step="0.01">
                        </div>

                        <div class="form-group">
                            <label for="date">Travel Date:</label>
                            <input type="text" name="date" class="input form__input" id="appointment_date"
                                placeholder="dd/mm/yyyy">
                        </div>



                        <div class="form-group">

                            <button type="submit">Submit</button>


                        </div>
                        {{-- @else
                            <span>Details Submission 48 Hours Time Exceeded.</span> --}}
                        {{-- @endif --}}

                    </div>


                </div>
            </div>


            {{ Form::close() }}
            </div>
        @endrole




        </div>

        </div>
    </main>

    @push('scripts')
        <script>
            window.addEventListener('load', function() {

                $('#start_time').flatpickr({
                    enableTime: true,
                    noCalendar: true,
                    dateFormat: "H:i:ss",
                    altFormat: 'H:i',
                    altInput: true,
                })

                $('#end_time').flatpickr({
                    enableTime: true,
                    noCalendar: true,
                    dateFormat: "H:i:ss",
                    altFormat: 'H:i',
                    altInput: true,
                })

                $('#appointment_date').flatpickr({
                    allowInput: true,
                    altInput: true,
                    altFormat: 'd/m/Y',
                    disable: [
                        function(selectedDate) {
                            var today = new Date();
                            today.setHours(0, 0, 0, 0);
                            // return (selectedDate < today);
                            return false;
                        }
                    ]
                })
            })
        </script>
    @endpush
@endsection
