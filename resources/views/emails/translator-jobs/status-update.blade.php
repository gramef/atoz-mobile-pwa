@extends('layouts.email')

@section('content')
    <thead>
        <th style="font-size: 28px; font-weight: bold; padding-bottom: 20px;">
            {{ ($wasSentToAdmin ? 'Job' : 'Your Job is now') . ' ' . ucfirst($job->statusName) }}
        </th>
    </thead>
    <tbody>
        <tr>
            <td>

                @if ($wasSentToAdmin)
                    <p style="font-size: 16px; font-weight: 500; line-height: 22px; margin-top: 0px; margin-bottom: 27px;">
                        Hi Admin, the job <span style="font-weight: bold">{{ $job->reference }}</span> has now been
                        <span style="font-weight: bold;text-transform:capitalize">{{ $job->statusName }}</span>, please see
                        summary below. Login into the portal to see full details.
                    </p>
                @else
                    <p style="font-size: 16px; font-weight: 500; line-height: 22px; margin-top: 0px; margin-bottom: 27px;">
                        This is an email notification to inform you that your Job
                        <span style="font-weight: bold">{{ $job->reference }}</span> is now
                        <span style="font-weight: bold;text-transform:capitalize">{{ $job->statusName }}</span>.
                    </p>
                @endif

                <table style="width: 100%; background-color: #F9F9F9; margin-bottom: 22px;">
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

                                    @if ($wasSentToAdmin && $job->agent)
                                        <tr>
                                            <td style="font-weight: bold">Agent:</td>
                                            <td style="font-weight: 500;text-transform:capitalize">
                                                {{ $job->agent->user->getFullName() }}</td>
                                        </tr>
                                    @endif

                                    <tr>
                                        <td style="font-weight: bold">Date:</td>
                                        <td style="font-weight: 500">{{ $job->target_date->format('d/m/Y') }}</td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold">Word Count:</td>
                                        <td style="font-weight: 500">{{ $job->word_count }}</td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold">Languages:</td>
                                        <td style="font-weight: 500">{{ $job->fromLanguage->name }} -
                                            {{ $job->toLanguage->name }}</td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold">Service type:</td>
                                        <td style="font-weight: 500">{{ $job->skill->skill }}</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>
                @if (!$wasSentToAdmin && $job->statusName === 'Assigned')
                    <!-- NEW SECTION: Request for File Ref, Name, and PO Number -->
                    <table style="width: 100%; background-color: #FFF3CD; margin-bottom: 17px; border: 1px solid #FFD700;">
                        <thead>
                            <th colspan="2"
                                style="color: #856404; font-size: 16px; line-height: 22px; font-weight: bold; padding: 15px 17px 13px; background-color: #FFEEBA;"
                                align="left">
                                Action Required: Provide Job Details
                            </th>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <table style="padding: 12px 17px 14px; width: 100%">
                                        <tr>
                                            <td style="font-weight: bold; color: #856404;">To complete your booking, please
                                                provide the following details:</td>
                                        </tr>
                                        <tr>
                                            <td style="padding-top: 10px;">
                                                <ul style="color: #856404; font-size: 16px; line-height: 22px;">
                                                    <li><strong>File Reference:</strong> </li>
                                                    <li><strong>Name:</strong> </li>
                                                    <li><strong>PO Number:</strong>
                                                    </li>
                                                </ul>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding-top: 10px;">
                                                <p style="color: #856404; font-size: 14px; font-style: italic;">
                                                    You can submit these details by replying to this email or updating them
                                                    in
                                                    the portal.
                                                </p>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                @endif

                @if (!$wasSentToAdmin && $job->canBeCancelled())
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

                @if ($job->canBeCancelled())
                    <p style="font-size: 12px;line-height: 18px;font-style:italic;font-weight: 500;padding-top: 41px">
                        PLEASE NOTE: <br>
                        If you need to cancel your booking, please do so by contacting a member of the A to Z team or by
                        clicking cancel against the relevant job within the portla. Please note cancellation charges may
                        apply. Please refer to our terms and conditions.
                    </p>
                @endif

            </td>
        </tr>
    </tbody>
@endsection
