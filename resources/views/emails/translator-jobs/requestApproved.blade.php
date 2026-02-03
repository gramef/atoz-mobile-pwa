@extends('layouts.email')

@section('content')
    <thead>
        <th style="font-size: 28px; font-weight: bold; padding-bottom: 20px;">Your request has been approved</th>
    </thead>
    <tbody>
        <tr>
            <td>
                <p style="font-size: 16px; font-weight: 500; line-height: 22px; margin-top: 0px; margin-bottom: 27px;">
                    Your job request has now been approved. You can now access the <a style="color: #EFA847;" href="portal.atozinterpreting.com">A-Z Portal</a> to track your job request, Please login using the email and password provided when you sent your request.
                </p>

                <p style="font-size: 16px; line-height: 22px; margin-top: 0px; margin-bottom: 27px; font-weight: 500">
                    You can view the status of your Job Request at any time by logging into the portal.
                </p>

                <table width="100%" border="0" cellspacing="0" cellpadding="0" style="text-align: center">
                    <tr>
                        <td>
                        <div>
                            <!--[if mso]>
                            <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="{{ url('/') }}" style="height:36px;v-text-anchor:middle;width:150px;" arcsize="5%" strokecolor="#EFA847" fillcolor="#EFA847">
                                <w:anchorlock/>
                                <center style="color:#ffffff;font-weight: 500;font-family:Helvetica, Arial,sans-serif;font-size:16px;">Portal Login</center>
                            </v:roundrect>
                            <![endif]-->
                            <a href="{{ url('/') }}" style="background-color:#EFA847;font-weight: 500;border:1px solid #EFA847;border-radius:27.3649px;color:#ffffff;display:inline-block;font-family:sans-serif;font-size:16px;line-height:43px;text-align:center;text-decoration:none;width:150px;-webkit-text-size-adjust:none;mso-hide:all;">Portal Login</a>
                        </div>
                        </td>
                    </tr>
                </table>

                <p style="font-size: 12px;line-height: 18px;font-style:italic;font-weight: 500;padding-top: 41px">
                    PLEASE NOTE: <br>
                    If you need to cancel your booking, please do so by contacting a member of the A to Z team or by clicking cancel against the relevant job within the portal. Please note cancellation charges may apply. Please refer to our terms and conditions.
                </p>

            </td>
        </tr>
    </tbody>
@endsection