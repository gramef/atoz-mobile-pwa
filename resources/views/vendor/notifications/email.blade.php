@extends('layouts.email')

@section('content')
    
<thead>
    <th style="color:#000;font-size: 28px; font-weight: bold; padding-bottom: 20px;">Verify Email Address</th>
</thead>
<tbody>
    <tr>
        <td>
            <p style="color:#000;font-size: 16px; font-weight: 500; line-height: 22px; margin-top: 0px; margin-bottom: 27px;">
                Hello! Please click the button below to verify your email address. 
            </p>
            <table width="100%" border="0" cellspacing="0" cellpadding="0" style="text-align: center;margin-bottom:20px">
                <tr>
                    <td>
                    <div>
                        <!--[if mso]>
                        <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="{{ $actionUrl }}" style="height:36px;v-text-anchor:middle;width:220px;" arcsize="5%" strokecolor="#EFA847" fillcolor="#EFA847">
                            <w:anchorlock/>
                            <center style="color:#ffffff;font-weight: 500;font-family:Helvetica, Arial,sans-serif;font-size:16px;">{{ $actionText }}</center>
                        </v:roundrect>
                        <![endif]-->
                        <a href="{{ $actionUrl }}" style="background-color:#EFA847;font-weight: 500;border:1px solid #EFA847;border-radius:27.3649px;color:#ffffff;display:inline-block;font-family:sans-serif;font-size:16px;line-height:43px;text-align:center;text-decoration:none;width:220px;-webkit-text-size-adjust:none;mso-hide:all;">{{ $actionText }}</a>
                    </div>
                    </td>
                </tr>
            </table>
            <p style="color:#000;font-size: 16px; font-weight: 500; line-height: 22px; margin-top: 0px; margin-bottom: 27px;">
                If you did not create an account, no further action is required.
            </p>
            <p style="color:#000;font-size: 16px; font-weight: 500; line-height: 22px; margin-top: 0px; margin-bottom: 27px;">
                Regards, <br>
                A to Z
            </p>
        </td>
    </tr>
</tbody>

@endsection