@extends('layouts.email')

@section('content')
    <thead>
        <th style="font-size: 28px; font-weight: bold; padding-bottom: 20px;">
          Your DBS is expiring
        </th>
    </thead>
    <tbody>
        <tr>
            <td>

                <p style="font-size: 16px; font-weight: 500; line-height: 22px; margin-top: 0px; margin-bottom: 27px;">
                    Hi {{ $agent->user->first_name }} {{ $agent->user->last_name }},
                </p>

                <p style="font-size: 16px; font-weight: 500; line-height: 22px; margin-top: 0px; margin-bottom: 27px;">
                    This is an email notification to inform you that your DBS is expiring on {{ $agent->dbs_expiry_date->format('d/m/Y') }}.
                </p>
                <p style="font-size: 16px; font-weight: 500; line-height: 22px; margin-top: 0px; margin-bottom: 27px;">
                    You can login to the portal by clicking the link below to update it.
                </p>
                <table width="100%" border="0" cellspacing="0" cellpadding="0" style="text-align: center">
                    <tr>
                        <td>
                        <div>
                            <!--[if mso]>
                            <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="{{ route('agents.profile.edit', $agent) }}" style="height:36px;v-text-anchor:middle;width:150px;" arcsize="5%" strokecolor="#EFA847" fillcolor="#EFA847">
                                <w:anchorlock/>
                                <center style="color:#ffffff;font-weight: 500;font-family:Helvetica, Arial,sans-serif;font-size:16px;">Portal Login</center>
                            </v:roundrect>
                            <![endif]-->
                            <a href="{{ route('agents.profile.documents.edit') }}" style="background-color:#EFA847;font-weight: 500;border:1px solid #EFA847;border-radius:27.3649px;color:#ffffff;display:inline-block;font-family:sans-serif;font-size:16px;line-height:43px;text-align:center;text-decoration:none;width:150px;-webkit-text-size-adjust:none;mso-hide:all;">Portal Login</a>
                        </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </tbody>
@endsection
