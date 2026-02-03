@extends('layouts.email')

@section('content')
<thead>
    <th style="font-size: 28px; font-weight: bold; padding-bottom: 20px;">{{ $subject }}</th>
</thead>
<tbody>
    <tr>
        <td>
            <p style="font-size: 16px; font-weight: 500; line-height: 22px; margin-top: 0px; margin-bottom: 27px;">
                Thank you for your booking request. A member of the A to Z team will be in touch shortly.
            </p>
            <p style="font-size: 16px; font-weight: 500; line-height: 22px; margin-top: 0px; margin-bottom: 27px;">
                Once your request has been approved you will recieve an email with a link to login to the portal.
            </p>

            @includeWhen(get_class($job) == 'App\InterpreterJob', 'emails.interpreter-jobs.table')

            @includeWhen(get_class($job) == 'App\TranslatorJob', 'emails.translator-jobs.table')

            @if ($job->canBeCancelled())
                <p style="font-size: 12px;line-height: 18px;font-style:italic;font-weight: 500;padding-top: 41px">
                    PLEASE NOTE: <br>
                    If you need to cancel your booking, please do so by contacting a member of the A to Z team. Please note cancellation charges may apply. Please refer to our terms and conditions.
                </p>
            @endif

        </td>
    </tr>
</tbody>
@endsection