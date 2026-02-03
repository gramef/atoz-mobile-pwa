@extends('layouts.email')

@section('content')
<thead>
    <th style="font-size: 28px; font-weight: bold; padding-bottom: 20px;">You have a new matched job</th>
</thead>
<tbody>
    <tr>
        <td>
            <table width="100%" border="0" cellspacing="0" cellpadding="0" style="text-align: center">
                <tr>
                    <td>
                        <table style="width: 100%; background-color: #F9F9F9; margin-bottom: 22px;">
                            <thead>
                                <th colspan="2" style="color: #fff; font-size: 16px; line-height: 22px; font-weight: bold; padding: 15px 17px 13px; background-color: #369ECC;" align="left">
                                    Job Details:
                                </th>
                            </thead>
                            <tbody align="left">
                                <tr>
                                    <td>
                                        <table style="padding: 12px 17px 14px; width: 100%">
                                            <tr>
                                                <td style="font-weight: bold">AtoZ ID:</td>
                                                <td style="font-weight: 500">{{ $matchedAgent->job->reference }}</td>
                                            </tr>
                                            <tr>
                                                <td style="font-weight: bold">Language:</td>
                                                <td style="font-weight: 500;text-transform:capitalize">{{ $matchedAgent->job->fromLanguage->name }} - {{ $matchedAgent->job->toLanguage->name }}</td>
                                            </tr>
                                            <tr>
                                                <td style="font-weight: bold">Time</td>
                                                <td style="font-weight: 500;text-transform:capitalize">{{ $matchedAgent->job->start_time }} ({{ $matchedAgent->job->duration_hours != null ? $matchedAgent->job->duration_hours : 0 }} hours {{ $matchedAgent->job->duration_minutes != null ?  $matchedAgent->job->duration_minutes : 0}} minutes)</td>
                                            </tr>
                                            <tr>

                                                @if (get_class($matchedAgent->job) == 'App\InterpreterJob')
                                                    <td style="font-weight: bold">Appointment Date:</td>
                                                    <td style="font-weight: 500;text-transform:capitalize">{{ $matchedAgent->job->appointment_date->format('d/m/Y') }}</td>
                                                @else
                                                    <td style="font-weight: bold">Target Date:</td>
                                                    <td style="font-weight: 500;text-transform:capitalize">{{ $matchedAgent->job->target_date->format('d/m/Y') }}</td>
                                                @endif

                                            </tr>
                                            <tr>
                                                <td style="font-weight: bold">Type of job:</td>
                                                <td style="font-weight: 500;text-transform:capitalize">{{  $matchedAgent->job->skill->skill }}</td>
                                            </tr>
                                            @if( $matchedAgent->job->skill_id == 1 )
                                            <tr>
                                                <td style="font-weight: bold">Post code:</td>
                                                <td style="font-weight: 500;text-transform:capitalize">{{  $matchedAgent->job->postcode }}</td>
                                            </tr>
                                            @endif
                                        </table>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div>
                            <!--[if mso]>
                                <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="{{ route("$jobType.show", $matchedAgent->job) }}" style="height:36px;v-text-anchor:middle;width:150px;" arcsize="5%" strokecolor="#EFA847" fillcolor="#EFA847">
                                    <w:anchorlock/>
                                    <center style="color:#ffffff;font-weight: 500;font-family:Helvetica, Arial,sans-serif;font-size:16px;">Portal Login</center>
                                </v:roundrect>
                            <![endif]-->
                            <a href="{{ route("$jobType.show", $matchedAgent->job) }}"
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
