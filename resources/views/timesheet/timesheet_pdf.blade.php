<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Timesheet</title>
    <style>
        * {
            padding: 0;
            margin: 0;
            box-sizing: border-box;
        }

        .container {
            width: 100%;
            margin: 0 auto;
            border-radius: 6px;
            font-size: 14px;
        }

        .header,
        .footer {
            text-align: center;
            padding: 5px;
        }

        .form {
            margin: 15px;
        }

        .border-self {
            border: 1px solid black;
            border-radius: 3px;
            padding: 5px;
        }

        .contact-form h5 {
            border-bottom: 2px solid black;
            display: inline-block;
            padding-bottom: 2px;
            font-size: 17px;
            margin-bottom: 5px;
            margin-left: 5px;
        }

        .contact-form label {
            font-size: 14px;
            color: black;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .bottom-label p {
            font-size: 14px;
            font-weight: 500;
        }

        .contact-form {
            margin-top: 10px;
        }

        .para {
            font-size: 9px;
            font-weight: 400 !important;
        }

        .interpreter {
            border-top: 1px solid black;
        }

        .text-bold {
            font-size: 15px;
            font-weight: 700;
        }

        .ranking li {
            margin-right: 150px;
            font-size: 14px;
            font-weight: 500;
            list-style: none;
        }

        ul {
            list-style-type: none;
        }

        .bottom-border {
            border-bottom: 2px solid black;
            display: inline-block;
            padding-bottom: 2px;
            font-size: 17px;
            margin-bottom: 5px;
            margin-left: 5px;
        }

        .content {
            font-size: 13px;
            padding-left: 120px;
            margin-top: 15px;
            font-weight: 500;
        }

        .footer-para h6 {
            font-size: 15px;
            color: black;
        }

        .bottom-label {
            border-bottom: 1px solid black;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 8px;
            text-align: left;
            font-size: 14px;
        }

        .no-border {
            border: none;
        }

        .no-padding {
            padding: 0;
        }

        .text-center {
            text-align: center;
        }

        .mt-4 {
            margin-top: 10px;
        }

        .mt-3 {
            margin-top: 10px;
        }

        .mb-0 {
            margin-bottom: 0;
        }

        .remove-m {
            margin: 0px !important;
        }

        .sigimg {
            margin-bottom: -10px;
            width: 100px;
            border: 1px solid;
            text-align: center;
        }

        .extra-margin {
            padding-left: 100px;
        }

        .d-flex {
            display: flex;
        }

        .span-box {
            padding-left: 5px;
        }

        .logo-img {
            display: flex;


        }

        .logo-img img {
            max-height: 100px;
            /* Adjust as needed */
        }

        .logo-img .logo-center {
            margin-left: auto;
        }

        .logo-img .agent-img {
            max-width: 50px;
            margin-right: auto;


        }
    </style>
</head>

<body>
    <div class="container">
        <header class="header">
            <div class="logo-img">
                <img src="img/logo-full-atoz.jpg" alt="Logo" class="logo-center" style="max-width: 250px;">
                <img src="{{ $timesheet->agent->getProfilePicture() }}" alt="Agent"
                    style="max-height: 100px; max-width: 100px;" class="float-right">
            </div>
        </header>
        <div class="form">
            <div class="form-content border-self">
                <h4><b>APPOINTMENT RECORD</b></h4>
            </div>
            <div class="contact-form border-self">
                <form action="">
                    <h5>Contact Details:</h5>
                    <table>
                        <tr>
                            <td><b> Authorization / Ref No:</b> {{ $timesheet->job_id }}/atoz</td>
                            <td><b>No. of Hours (booked): </b> {{ $timesheet->interpreter->duration_hours }}</td>
                        </tr>
                        <tr>
                            <td> <b>Client’s name: </b>
                                {{ $timesheet->interpreter->client->userSheet->first_name . $timesheet->interpreter->client->userSheet->last_name }}
                            </td>
                            <td><b>Session: </b> 2024-2025</td>
                        </tr>
                        <tr>
                            <td><b>Date:</b> {{ $timesheet->interpreter->appointment_date }}</td>
                            <td><b>Time: </b> {{ $timesheet->interpreter->start_time }}</td>
                        </tr>
                        <tr>
                            <td><b>Contact Name: </b>
                                {{ $timesheet->agentOne->user->first_name . $timesheet->agentOne->user->last_name }}
                            </td>
                            <td><b>Tel no. </b> {{ $timesheet->agentOne->contact_number }}</td>
                        </tr>
                    </table>
                </form>
            </div>
            <section class="border-self mt-3">
                <div class="bottom-label p-3 ">
                    <table>
                        <tr>
                            <td colspan="6"><b>Language Required:</b> {{ $timesheet->interpreter->toLanguage->name }}
                            </td>
                            <td class="extra-margin">I declare that the information provided above is correct. I
                                understand that if I give information that is incorrect or incomplete, action may be
                                taken against me.</td>
                        </tr>
                        <tr>
                            <td colspan="6"><b> Arrival Time: </b>{{ $timesheet->interpreter->start_time }}</td>
                            <td class="extra-margin"> <b>Signed:</b> <img class="sigimg" height="40px" width="80px"
                                    src="{{ $timesheet->agent_signature }}" /></td>
                        </tr>
                        <tr>
                            <td colspan="6"><b> Interpreter’s
                                    Name:</b>{{ $timesheet->interpreter->agent->user->getFullName() }}</td>
                            <td class="extra-margin"><b> Date:</b> {{ $timesheet->interpreter->appointment_date }}</td>
                        </tr>
                        <tr>
                            <td colspan="6"><b> Start time:</b> {{ $timesheet->agent_start_time }}</td>
                            <td class="extra-margin"><b>Agent Interpretation
                                    time:</b>{{ $timesheet->agent_duration_hours . ' H ' . $timesheet->agent_duration_minutes }}
                                M</td>
                        </tr>
                        <tr>
                            <td colspan="6"><b> End time:</b> {{ $timesheet->agent_end_time }}</td>
                            <td class="extra-margin"><b>Total interpreting time :
                                </b>{{ $timesheet->interpreter->duration_hours . 'H ' . $timesheet->interpreter->duration_minutes }}
                                M</td>
                        </tr>
                    </table>
                </div>
                <div class="signatory mt-4">
                    <h4 class="text-bold bottom-border">Signatory (Organisation):</h4>
                    <table>
                        <tr>
                            <td><b> Name: </b> {{ $timesheet->client_name }} </td>
                            <td><b>Signature: </b><img class="sigimg" height="40px" width="80px"
                                    src="{{ $timesheet->client_signature }}" /></td>
                            <td><b> Designation: </b>{{ $timesheet->client_designation }} </td>
                        </tr>
                        <tr>
                            <td><b> Tel. No:</b> {{ $timesheet->client_phone }}</td>
                            <td><b> Appointment Time:</b> {{ $timesheet->interpreter->start_time }}</td>
                            <td><b> Date:</b> {{ $timesheet->interpreter->appointment_date }}</td>
                        </tr>
                        <tr>
                            <td><b> Time In:</b> {{ $timesheet->interpreter->start_time }}</td>
                            <td><b> Time Out:</b> {{ $timesheet->interpreter->end_time }}</td>
                            <td><b>Client Reference:</b>-------------</td>
                        </tr>
                    </table>
                </div>
            </section>
        </div>
        <footer class="footer mt-3">
            <p class="footer-para">
                Issue 1 QMF31 28th February 2013
            </p>
            <div class="footer-para">
                <h6> Please return completed timesheet to: timesheets@atozinterpreting.com</h6>
            </div>
            <img src="img/logo.jpg" alt="Footer Logo" style="width: 100%; height:150px;">
        </footer>

    </div>
</body>

</html>
