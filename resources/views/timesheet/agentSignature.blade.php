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


    <script src="https://cdn.jsdelivr.net/npm/signature_pad@2.3.2/dist/signature_pad.min.js"></script>

    <main class="section">
        <div class="section__top">
            <nav class="flex-container section__job-types">
                <div class="section__heading">TimeSheet Details</div>
            </nav>
        </div>
    </main>

    <section class="content__table">
        <table class="table">
            <thead class="table__header">
                <tr>
                    <th class="table__heading">Ref#</th>
                    <th class="table__heading">Agent Name</th>
                    <th class="table__heading">Client Name</th>
                    <th class="table__heading">TimeSheet Assign Date</th>
                    <th class="table__heading">From Language</th>
                    <th class="table__heading">To Language</th>
                    <th class="table__heading">Total Hour</th>
                    <th class="table__heading">Total Minutes</th>
                    <th class="table__heading"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($timesheets as $timesheet)
                    <tr class="table__row">
                        <td class="table__data">{{ $timesheet->interpreter->id }}/atoz</td>
                        <td class="table__data">
                            {{ $timesheet->agentOne->user->first_name . $timesheet->agentOne->user->last_name }}</td>
                        <td class="table__data">
                            {{ $timesheet->interpreter->client->userSheet->first_name . $timesheet->interpreter->client->userSheet->last_name }}
                        </td>
                        <td class="table__data">{{ $timesheet->created_at }} </td>
                        <td class="table__data">{{ $timesheet->interpreter->from_language->name }} </td>
                        <td class="table__data">{{ $timesheet->interpreter->to_language->name }} </td>
                        <td class="table__data">{{ $timesheet->interpreter->duration_hours }} </td>
                        <td class="table__data">{{ $timesheet->interpreter->duration_minutes }} </td>
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
            </tbody>
        </table>
        <div class="row">
            <div class="col-md-6">



                {{ Form::open(['route' => ['timesheet.update', $timesheets[0]->id], 'class' => 'form', 'id' => 'signature-form', 'method' => 'PUT']) }}
                <input type="hidden" name="signature" id="signature">
                <input type="hidden" name="job_id" id="job_id" value="{{ $timesheets[0]->id }}">
                <div class="d-flex">
                    <div class="col-md-5">
                        <div class="form__input relative">
                            {{ Form::number('duration_hours', $timesheet->interpreter->duration_hours, [
                                'min' => 0,
                                'placeholder' => '0',
                                'class' => 'input',
                            ]) }}
                            <span class="form__after-input">Hours</span>
                        </div>
                    </div>

                    <div class="col-md-5 ">
                        <div class="form__input relative">
                            {{ Form::number(
                                'duration_minutes',
                                isset($timesheet->interpreter->duration_minutes) ? $timesheet->interpreter->duration_minutes : 0,
                                [
                                    'min' => 0,
                                    'max' => 59,
                                    'placeholder' => '00',
                                    'class' => 'input',
                                ],
                            ) }}
                            <span class="form__after-input">Minutes</span>
                        </div>
                    </div>
                </div>
                @role('agent')
                    <div class="d-flex">
                        <div class="col-md-6 ">
                            <div class="form__input relative">
                                {{ Form::text('agent_start_time', isset($timesheet->agent_start_time) ? $timesheet->agent_start_time : 0, [
                                    'min' => 0,
                                    'max' => 59,
                                    'placeholder' => '00',
                                    'class' => 'input',
                                    'id' => 'start_time',
                                ]) }}
                                <span class="form__after-input">Job Start Time</span>
                            </div>
                        </div>

                        <div class="col-md-6 ">
                            <div class="form__input relative">
                                {{ Form::text('agent_end_time', isset($timesheet->agent_end_time) ? $timesheet->agent_end_time : 0, [
                                    'min' => 0,
                                    'max' => 59,
                                    'placeholder' => '00',
                                    'class' => 'input',
                                    'id' => 'end_time',
                                ]) }}
                                <span class="form__after-input">Job End Time</span>
                            </div>
                        </div>
                    </div>
                @endrole

                @role('client')
                    <div class="d-flex">
                        <div class="col-md-6">


                            <div class="form__input relative">
                                {{ Form::number('duration_hours_agent', $timesheet->agent_duration_hours, [
                                    'min' => 0,
                                    'placeholder' => '0',
                                    'class' => 'input ',
                                    'disabled' => 'disabled',
                                ]) }}
                                <span class="form__after-input">Agent Claimed Hours</span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form__input relative">
                                {{ Form::number(
                                    'duration_minutes_agent',
                                    isset($timesheet->agent_duration_minutes) ? $timesheet->agent_duration_minutes : 0,
                                    [
                                        'min' => 0,
                                        'max' => 59,
                                        'placeholder' => '00',
                                        'class' => 'input',
                                        'disabled' => 'disabled',
                                    ],
                                ) }}
                                <span class="form__after-input">Agent Claimed Minutes</span>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex">
                        <div class="col-md-6">
                            <div class="form__input relative">
                                {{ Form::text('client_name', isset($timesheet->client_name) ? $timesheet->client_name : '', [
                                    'placeholder' => 'Client Name',
                                    'class' => 'input form__input',
                                ]) }}

                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form__input relative">
                                {{ Form::number('client_phone', isset($timesheet->client_phone) ? $timesheet->client_phone : '', [
                                    'placeholder' => 'Client Phone',
                                    'class' => 'input form__input',
                                    'pattern' => '[0-9]*',
                                ]) }}

                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form__input relative">
                            {{ Form::text(
                                'client_designation',
                                isset($timesheet->client_designation) ? $timesheet->client_designation : '',
                                [
                                    'placeholder' => 'Client Designation',
                                    'class' => 'input form__input',
                                ],
                            ) }}

                        </div>
                    </div>
                @endrole



                <h5 class="form__input relative">Digital Signature</h5>
                <span style="color: red;">Please ensure your signature is clear and legible.</span>
                <br>
                <canvas class="form__input relative" id="signature-pad"></canvas>
                <div class="d-flex">
                    <button id="clear" class="btn btn-danger">Clear</button> &nbsp;&nbsp;

                    <button class="btn btn-secondary" id="save">Save</button>

                </div>

            </div>
            {{ Form::close() }}
            @role('client')
                <div class="col-md-4 formBorder">
                    <h5>Agent Digital Signature</h5>

                    <div class="form-group mt-3" style="border:1px solid black; height: 155px;">

                        <img height="80px" width="200px" src="{{ $timesheets[0]->agent_signature }}" alt="NA" />
                    </div>
                    <span>DateTime : {{ $timesheets[0]->created_at }}</span>
                    @if (!empty($timesheets[0]->client_signature))
                        <h5>Client Digital Signature</h5>
                        <div class="col-md-12 formBorder" style="border:1px solid black; height: 124px;">
                            <div class="form-group">

                                <img height="80px" width="200px" src="{{ $timesheets[0]->client_signature }}"
                                    alt="NA" />
                            </div><br>
                            <span style="margin-left: -16px;">DateTime : {{ $timesheets[0]->created_at }}</span>
                    @endif
                @endrole

                @role('agent')
                    <div class="col-md-4 formBorder" style="border:1px solid black; height: 155px;">
                        <h5>Agent Digital Signature</h5>

                        <div class="form-group">

                            <img height="80px" width="200px" src="{{ $timesheets[0]->agent_signature }}" alt="NA" />
                        </div><br>
                        <span style="margin-left: -16px;">DateTime : </span>{{ $timesheets[0]->created_at }}
                    @endrole

                </div>


            </div>



        </div>

    </section>

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

    <script>
        var canvas = document.getElementById('signature-pad');
        var signaturePad = new SignaturePad(canvas);

        document.getElementById('clear').addEventListener('click', function() {
            signaturePad.clear();
        });

        document.getElementById('save').addEventListener('click', function() {
            if (signaturePad.isEmpty()) {
                alert('Please provide a signature first.');
            } else {
                var dataURL = signaturePad.toDataURL('image/png');
                document.getElementById('signature').value = dataURL;
                document.getElementById('signature-form').submit();
            }
        });
    </script>

@endsection
