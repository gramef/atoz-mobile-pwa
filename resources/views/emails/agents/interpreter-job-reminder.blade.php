@extends('layouts.email')

@section('content')
<thead>
    <th style="font-size: 28px; font-weight: bold; padding-bottom: 20px;">
        Job Reminder
    </th>
</thead>
<tbody>
    <tr>
        <td>
            <p style="font-size: 16px; font-weight: 500; line-height: 22px; margin-top: 0px; margin-bottom: 27px;">
                This is an email notification to inform you that your assigned job 
                <span style="font-weight: bold">{{ $job->reference }}</span> is scheduled to begin in 24 hours.
            </p>
            <p style="font-size: 16px; font-weight: 500; line-height: 22px; margin-top: 0px; margin-bottom: 27px;">
             You can log in to the portal to review the details.
            </p>
            <table style="width: 100%; background-color: #F9F9F9; margin-bottom: 17px;">
            <thead>
        <th colspan="2"
            style="color: #fff; font-size: 16px; line-height: 22px; font-weight: bold; padding: 15px 17px 13px; background-color: #369ECC;"
            align="left">
            Job Details:
        </th>
        </thead>
<tbody>
    <tr>
        <td>
            <table style="padding: 12px 17px 14px; width: 100%">
                <tr>
                    <td style="font-weight: bold">AtoZ ID:</td>
                    <td style="font-weight: 500">{{ $job->reference }}</td>
                </tr>
                <tr>
                    <td style="font-weight: bold">Status:</td>
                    <td style="font-weight: 500;text-transform:capitalize">{{ $job->statusName }}</td>
                </tr>
                <tr>
                    <td style="font-weight: bold">Date:</td>

                    @if ($job->isDirty('appointment_date'))
                    <td style="font-weight: 500">
                        <del
                            style="color: #4A4A4A">{{ Carbon\Carbon::parse($job->appointment_date)->format('d/m/Y') }}</del>
                    </td>
                    @endif

                    <td style="font-weight: 500">{{ $job->appointment_date->format('d/m/Y') }}</td>

                </tr>
                <tr>
                    <td style="font-weight: bold">Time:</td>

                    @if ($job->isDirty(['start_time', 'duration_hours', 'duration_minutes']))
                    <td style="font-weight: 500">
                        <del style="color: #4A4A4A">{{ $job['original']->start_time }}
                            ({{ $job['original']->formattedDuration }})</del>
                    </td>
                    @endif

                    <td style="font-weight: 500">{{ $job->start_time }} ({{ $job->formattedDuration }})
                    </td>

                </tr>
                <tr>
                    <td style="font-weight: bold">Language:</td>

                    @if ($job->isDirty('to_language_id'))
                    <td style="font-weight: 500">
                        <del style="color: #4A4A4A">{{ $job['original']->toLanguage->name }}</del>
                    </td>
                    @endif

                    <td style="font-weight: 500">{{ $job->toLanguage->name }}</td>

                </tr>
                <tr>
                    <td style="font-weight: bold">Service type:</td>

                    @if ($job->isDirty('skill_id'))
                    <td style="font-weight: 500">
                        <del style="color: #4A4A4A">{{ $job['original']->skill->skill }}</del>
                    </td>
                    @endif

                    <td style="font-weight: 500">{{ $job->skill->skill }}</td>

                </tr>
            </table>
        </td>
    </tr>
</tbody>
</table>
<table style="width: 100%; background-color: #F9F9F9; margin-bottom: 22px;">
    <thead>
        <th colspan="2"
            style="color: #fff; font-size: 16px; line-height: 22px; font-weight: bold; padding: 15px 17px 13px; background-color: #369ECC;"
            align="left">
            Location information:
        </th>
    </thead>
    <tbody>
        <tr>
            <td>
                <table style="padding: 12px 17px 14px; width: 100%">
                    <tr>
                        <td style="font-weight: bold">Department:</td>

                        @if ($job->isDirty('department'))
                        <td style="font-weight: 500">
                            <del style="color: #4A4A4A">{{ $job['original']->department }}</del>
                        </td>
                        @endif

                        <td style="font-weight: 500">{{ $job->department }}</td>

                    </tr>
                    <tr>
                        <td style="font-weight: bold">Address line 1:</td>

                        @if ($job->isDirty('address_line_1'))
                        <td style="font-weight: 500">
                            <del style="color: #4A4A4A">{{ $job['original']->address_line_1 }}</del>
                        </td>
                        @endif

                        <td style="font-weight: 500">{{ $job->address_line_1 }}</td>

                    </tr>
                    <tr>
                        <td style="font-weight: bold">Address line 2:</td>

                        @if ($job->isDirty('address_line_2'))
                        <td style="font-weight: 500">
                            <del style="color: #4A4A4A">{{ $job['original']->address_line_2 }}</del>
                        </td>
                        @endif

                        <td style="font-weight: 500">{{ $job->address_line_2 }}</td>

                    </tr>
                    <tr>
                        <td style="font-weight: bold">County:</td>

                        @if ($job->isDirty('county'))
                        <td style="font-weight: 500">
                            <del style="color: #4A4A4A">{{ $job['original']->county }}</del>
                        </td>
                        @endif

                        <td style="font-weight: 500">{{ $job->county }}</td>

                    </tr>
                    <tr>
                        <td style="font-weight: bold">Postcode:</td>

                        @if ($job->isDirty('postcode'))
                        <td style="font-weight: 500">
                            <del style="color: #4A4A4A">{{ $job['original']->postcode }}</del>
                        </td>
                        @endif

                        <td style="font-weight: 500">{{ $job->postcode }}</td>

                    </tr>
                </table>
            </td>
        </tr>
    </tbody>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0" style="text-align: center">
    <tr>
        <td>
            <div>
                <!--[if mso]>
                            <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="{{ route('interpreter-jobs.show', $job->id) }}" style="height:36px;v-text-anchor:middle;width:150px;" arcsize="5%" strokecolor="#EFA847" fillcolor="#EFA847">
                                <w:anchorlock/>
                                <center style="color:#ffffff;font-weight: 500;font-family:Helvetica, Arial,sans-serif;font-size:16px;">Portal Login</center>
                            </v:roundrect>
                            <![endif]-->
                <a href="{{ route('interpreter-jobs.show', $job->id) }}"
                    style="background-color:#EFA847;font-weight: 500;border:1px solid #EFA847;border-radius:27.3649px;color:#ffffff;display:inline-block;font-family:sans-serif;font-size:16px;line-height:43px;text-align:center;text-decoration:none;width:150px;-webkit-text-size-adjust:none;mso-hide:all;">Portal
                    Login</a>
            </div>
        </td>
    </tr>
</table>
</td>
</tr>
</tbody>
@endsection
