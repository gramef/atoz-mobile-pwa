@extends('layouts.email')

@section('content')
    <thead>
        <th style="font-size: 28px; font-weight: bold; padding-bottom: 20px;">
            {{ $wasSentToAdmin ? 'Job was updated' : 'Your Job was updated' }}
        </th>
    </thead>
    <tbody>
        <tr>
            <td>
                @if ($wasSentToAdmin)
                    <p style="font-size: 16px; font-weight: 500; line-height: 22px; margin-top: 0px; margin-bottom: 27px;">
                        Hi Admin, the job <span style="font-weight: bold">{{ $job['updated']->reference }}</span> was
                        updated,
                        please
                        see summary below. Login into the portal to see full details.
                    </p>
                @else
                    <p style="font-size: 16px; font-weight: 500; line-height: 22px; margin-top: 0px; margin-bottom: 27px;">
                        This is an email notification to inform you that your job
                        <span style="font-weight: bold">{{ $job['updated']->reference }}</span> was updated with the
                        following
                        information.
                    </p>
                @endif

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
                                        <td style="font-weight: 500">{{ $job['updated']->reference }}</td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold">Status:</td>
                                        <td style="font-weight: 500;text-transform:capitalize">
                                            {{ $job['updated']->statusName }}</td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold">Date:</td>

                                        @if ($job['updated']->isDirty('appointment_date'))
                                            <td style="font-weight: 500">
                                                <del
                                                    style="color: #4A4A4A">{{ Carbon\Carbon::parse($job['original']->appointment_date)->format('d/m/Y') }}</del>
                                            </td>
                                        @endif

                                        <td style="font-weight: 500">
                                            {{ $job['updated']->appointment_date->format('d/m/Y') }}</td>

                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold">Time:</td>

                                        @if ($job['updated']->isDirty(['start_time', 'duration_hours', 'duration_minutes']))
                                            <td style="font-weight: 500">
                                                <del style="color: #4A4A4A">{{ $job['original']->start_time }}
                                                    ({{ $job['original']->formattedDuration }})</del>
                                            </td>
                                        @endif

                                        <td style="font-weight: 500">{{ $job['updated']->start_time }}
                                            ({{ $job['updated']->formattedDuration }})
                                        </td>

                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold">Language:</td>

                                        @if ($job['updated']->isDirty('to_language_id'))
                                            <td style="font-weight: 500">
                                                <del style="color: #4A4A4A">{{ $job['original']->toLanguage->name }}</del>
                                            </td>
                                        @endif

                                        <td style="font-weight: 500">{{ $job['updated']->toLanguage->name }}</td>

                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold">Service type:</td>

                                        @if ($job['updated']->isDirty('skill_id'))
                                            <td style="font-weight: 500">
                                                <del style="color: #4A4A4A">{{ $job['original']->skill->skill }}</del>
                                            </td>
                                        @endif

                                        <td style="font-weight: 500">{{ $job['updated']->skill->skill }}</td>

                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold">Gender:</td>

                                        @if ($job['updated']->isDirty('gender'))
                                            <td style="font-weight: 500">
                                                <del style="color: #4A4A4A">{{ $job['original']->getGenderName() }}</del>
                                            </td>
                                        @endif

                                        <td style="font-weight: 500">{{ $job['updated']->getGenderName() }}</td>

                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>


                @if ($job['updated']->hasAddressFields())
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

                                            @if ($job['updated']->isDirty('department'))
                                                <td style="font-weight: 500">
                                                    <del style="color: #4A4A4A">{{ $job['original']->department }}</del>
                                                </td>
                                            @endif

                                            <td style="font-weight: 500">{{ $job['updated']->department }}</td>

                                        </tr>
                                        <tr>
                                            <td style="font-weight: bold">Address line 1:</td>

                                            @if ($job['updated']->isDirty('address_line_1'))
                                                <td style="font-weight: 500">
                                                    <del
                                                        style="color: #4A4A4A">{{ $job['original']->address_line_1 }}</del>
                                                </td>
                                            @endif

                                            <td style="font-weight: 500">{{ $job['updated']->address_line_1 }}</td>

                                        </tr>
                                        <tr>
                                            <td style="font-weight: bold">Address line 2:</td>

                                            @if ($job['updated']->isDirty('address_line_2'))
                                                <td style="font-weight: 500">
                                                    <del
                                                        style="color: #4A4A4A">{{ $job['original']->address_line_2 }}</del>
                                                </td>
                                            @endif

                                            <td style="font-weight: 500">{{ $job['updated']->address_line_2 }}</td>

                                        </tr>
                                        <tr>
                                            <td style="font-weight: bold">County:</td>

                                            @if ($job['updated']->isDirty('county'))
                                                <td style="font-weight: 500">
                                                    <del style="color: #4A4A4A">{{ $job['original']->county }}</del>
                                                </td>
                                            @endif

                                            <td style="font-weight: 500">{{ $job['updated']->county }}</td>

                                        </tr>
                                        <tr>
                                            <td style="font-weight: bold">Postcode:</td>

                                            @if ($job['updated']->isDirty('postcode'))
                                                <td style="font-weight: 500">
                                                    <del style="color: #4A4A4A">{{ $job['original']->postcode }}</del>
                                                </td>
                                            @endif

                                            <td style="font-weight: 500">{{ $job['updated']->postcode }}</td>

                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                @endif

                @if (!$wasSentToAdmin && $job['updated']->canBeCancelled())
                    <p style="font-size: 16px; line-height: 22px; margin-top: 0px; margin-bottom: 27px; font-weight: 500">
                        You can view the status of your Job Request at any time by logging into the portal.
                    </p>
                @endif

                <table width="100%" border="0" cellspacing="0" cellpadding="0" style="text-align: center">
                    <tr>
                        <td>
                            <div>
                                <!--[if mso]>
                                                                <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="{{ $jobLink }}" style="height:36px;v-text-anchor:middle;width:150px;" arcsize="5%" strokecolor="#EFA847" fillcolor="#EFA847">
                                                                    <w:anchorlock/>
                                                                    <center style="color:#ffffff;font-weight: 500;font-family:Helvetica, Arial,sans-serif;font-size:16px;">Portal Login</center>
                                                                </v:roundrect>
                                                                <![endif]-->
                                <a href="{{ $jobLink }}"
                                    style="background-color:#EFA847;font-weight: 500;border:1px solid #EFA847;border-radius:27.3649px;color:#ffffff;display:inline-block;font-family:sans-serif;font-size:16px;line-height:43px;text-align:center;text-decoration:none;width:150px;-webkit-text-size-adjust:none;mso-hide:all;">Portal
                                    Login</a>
                            </div>
                        </td>
                    </tr>
                </table>

                @if (!$wasSentToAdmin && $job['updated']->canBeCancelled())
                    <p style="font-size: 12px;line-height: 18px;font-style:italic;font-weight: 500;padding-top: 41px">
                        PLEASE NOTE: <br>
                        If you need to cancel your booking, please do so by contacting a member of the A to Z team or by
                        clicking cancel
                        against the relevant job within the portal. Please note cancellation charges may apply. Please
                        refer
                        to our terms
                        and
                        conditions.
                    </p>
                @endif

            </td>
        </tr>
    </tbody>
@endsection
