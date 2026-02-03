@extends('layouts.email')

@section('content')
    <thead>
        <th style="font-size: 28px; font-weight: bold; padding-bottom: 20px;">
            {{ $user->getFullName() }} has updated their profile
        </th>
    </thead>
    <tbody>
        <tr>
            <td>
                <p style="font-size: 16px; font-weight: 500; line-height: 22px; margin-top: 0px; margin-bottom: 27px;">
                    This is an email notification to inform you that the agent {{ $user->getFullName() }} has updated their profile.
                </p>

                <table style="width: 100%; background-color: #F9F9F9; margin-bottom: 17px;">
                    <thead>
                        <th colspan="2"
                            style="color: #fff; font-size: 16px; line-height: 22px; font-weight: bold; padding: 15px 17px 13px; background-color: #369ECC;"
                            align="left">
                            User Details:
                        </th>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <table style="padding: 12px 17px 14px; width: 100%">

                                    @include('emails.partials.user-info', ['user' => $user])

                                    @if ($user->agent->isDirty('address_line_1'))
                                        <tr>
                                            <td style="font-weight: bold">Address line 1:</td>
                                                <td style="font-weight: 500">
                                                    <del style="color: #4A4A4A">{{ $user->agent->getOriginal('address_line_1') }}</del>
                                                </td>
                                            <td style="font-weight: 500">{{ $user->agent->address_line_1 }}</td>
                                        </tr>
                                    @endif

                                    @if ($user->agent->isDirty('address_line_2'))
                                        <tr>
                                            <td style="font-weight: bold">Address line 2:</td>
                                                <td style="font-weight: 500">
                                                    <del style="color: #4A4A4A">{{ $user->agent->getOriginal('address_line_2') }}</del>
                                                </td>
                                            <td style="font-weight: 500">{{ $user->agent->address_line_2 }}</td>
                                        </tr>
                                    @endif

                                    @if ($user->agent->isDirty('county'))
                                        <tr>
                                            <td style="font-weight: bold">County:</td>
                                                <td style="font-weight: 500">
                                                    <del style="color: #4A4A4A">{{ $user->agent->getOriginal('county') }}</del>
                                                </td>
                                            <td style="font-weight: 500">{{ $user->agent->county }}</td>
                                        </tr>
                                    @endif

                                    @if ($user->agent->isDirty('postcode'))
                                        <tr>
                                            <td style="font-weight: bold">Postcode:</td>
                                                <td style="font-weight: 500">
                                                    <del style="color: #4A4A4A">{{ $user->agent->getOriginal('postcode') }}</del>
                                                </td>
                                            <td style="font-weight: 500">{{ $user->agent->postcode }}</td>
                                        </tr>
                                    @endif

                                    @if ($user->agent->isDirty('dbs_expiry_date'))
                                        <tr>
                                            <td style="font-weight: bold">DBS expiry date:</td>
                                                <td style="font-weight: 500">
                                                    <del style="color: #4A4A4A">{{ Carbon\Carbon::parse($user->agent->getOriginal('dbs_expiry_date'))->format('d/m/Y') }}</del>
                                                </td>
                                            <td style="font-weight: 500">{{ $user->agent->dbs_expiry_date->format('d/m/Y') }}</td>
                                        </tr>
                                    @endif

                                    @if ($user->agent->isDirty('dbs_number'))
                                        <tr>
                                            <td style="font-weight: bold">DBS Number:</td>
                                                <td style="font-weight: 500">
                                                    <del style="color: #4A4A4A">{{ $user->agent->getOriginal('dbs_number') }}</del>
                                                </td>
                                            <td style="font-weight: 500">{{ $user->agent->dbs_number }}</td>
                                        </tr>
                                    @endif

                                    @if ($user->agent->isDirty('induction_date'))
                                        <tr>
                                            <td style="font-weight: bold">Induction date:</td>
                                                <td style="font-weight: 500">
                                                    <del style="color: #4A4A4A">{{ Carbon\Carbon::parse($user->agent->getOriginal('induction_date'))->format('d/m/Y') }}</del>
                                                </td>
                                            <td style="font-weight: 500">{{ $user->agent->induction_date->format('d/m/Y') }}</td>
                                        </tr>
                                    @endif

                                    @if (isset($documents) && $documents->isNotEmpty())
                                        @foreach ($documents as $document)
                                            <tr>
                                                <td style="font-weight: 500">{{ $document }}</td>
                                            </tr>
                                        @endforeach
                                    @endif

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
            </td>
        </tr>
    </tbody>
@endsection
