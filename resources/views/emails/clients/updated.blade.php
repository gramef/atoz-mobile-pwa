@extends('layouts.email')

@section('content')
    <thead>
        <th style="font-size: 28px; font-weight: bold; padding-bottom: 20px;">
          A client has updated their profile
        </th>
    </thead>
    <tbody>
        <tr>
            <td>
                <p style="font-size: 16px; font-weight: 500; line-height: 22px; margin-top: 0px; margin-bottom: 27px;">
                    This is an email notification to inform you that the client {{ $user->getFullName() }} has updated their profile.
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

                                    @if ($user->client->isDirty('client_address_line_1'))
                                        <tr>
                                            <td style="font-weight: bold">Address line 1:</td>
                                                <td style="font-weight: 500">
                                                    <del style="color: #4A4A4A">{{ $user->client->getOriginal('client_address_line_1') }}</del>
                                                </td>
                                            <td style="font-weight: 500">{{ $user->client->client_address_line_1 }}</td>
                                        </tr>
                                    @endif

                                    @if ($user->client->isDirty('client_address_line_2'))
                                        <tr>
                                            <td style="font-weight: bold">Address line 2:</td>
                                                <td style="font-weight: 500">
                                                    <del style="color: #4A4A4A">{{ $user->client->getOriginal('client_address_line_2') }}</del>
                                                </td>
                                            <td style="font-weight: 500">{{ $user->client->client_address_line_2 }}</td>
                                        </tr>
                                    @endif

                                    @if ($user->client->isDirty('client_county'))
                                        <tr>
                                            <td style="font-weight: bold">County:</td>
                                                <td style="font-weight: 500">
                                                    <del style="color: #4A4A4A">{{ $user->client->getOriginal('client_county') }}</del>
                                                </td>
                                            <td style="font-weight: 500">{{ $user->client->client_county }}</td>
                                        </tr>
                                    @endif

                                    @if ($user->client->isDirty('client_postcode'))
                                        <tr>
                                            <td style="font-weight: bold">Postcode:</td>
                                                <td style="font-weight: 500">
                                                    <del style="color: #4A4A4A">{{ $user->client->getOriginal('client_postcode') }}</del>
                                                </td>
                                            <td style="font-weight: 500">{{ $user->client->client_postcode }}</td>
                                        </tr>
                                    @endif

                                    @if ($newContactMethods != $user->client->contactMethods->pluck('id')->all())
                                        <tr>
                                            <td style="font-weight: bold">Contact preferences :</td>
                                            <td style="font-weight: 500">
                                                <del style="color: #4A4A4A">
                                                    {{ $user->client->contactMethods->isNotEmpty() ? join(', ', $user->client->contactMethods->pluck('contact_method')->all()) : 'no contact preferences' }}
                                                </del>
                                            </td>
                                            <td style="font-weight: 500">
                                                {{ $newContactMethods->isNotEmpty() ? join(', ', $newContactMethods->pluck('contact_method')->all()) : 'no contact preferences' }}
                                            </td>
                                        </tr>
                                    @endif

                                    @if ($user->client->isDirty('always_requires_a_quote'))
                                        <tr>
                                            <td style="font-weight: bold">Always require a quote:</td>
                                                <td style="font-weight: 500">
                                                    <del style="color: #4A4A4A">{{ $user->client->getOriginal('always_requires_a_quote') ? 'yes' : 'no' }}</del>
                                                </td>
                                            <td style="font-weight: 500">{{ $user->client->always_requires_a_quote ? 'yes' : 'no' }}</td>
                                        </tr>
                                    @endif

                                    @if ($user->client->organisation)

                                        @if ($user->client->organisation->isDirty('organisation_company'))
                                            <tr>
                                                <td style="font-weight: bold">Company name:</td>
                                                    <td style="font-weight: 500">
                                                        <del style="color: #4A4A4A">{{ $user->client->organisation->getOriginal('organisation_company') }}</del>
                                                    </td>
                                                <td style="font-weight: 500">{{ $user->client->organisation->organisation_company }}</td>
                                            </tr>
                                        @endif

                                        @if ($user->client->organisation->isDirty('vat_number'))
                                            <tr>
                                                <td style="font-weight: bold">VAT number:</td>
                                                    <td style="font-weight: 500">
                                                        <del style="color: #4A4A4A">{{ $user->client->organisation->getOriginal('vat_number') }}</del>
                                                    </td>
                                                <td style="font-weight: 500">{{ $user->client->organisation->vat_number }}</td>
                                            </tr>
                                        @endif

                                        @if ($user->client->organisation->isDirty('company_number'))
                                            <tr>
                                                <td style="font-weight: bold">Company number:</td>
                                                    <td style="font-weight: 500">
                                                        <del style="color: #4A4A4A">{{ $user->client->organisation->getOriginal('company_number') }}</del>
                                                    </td>
                                                <td style="font-weight: 500">{{ $user->client->organisation->company_number }}</td>
                                            </tr>
                                        @endif

                                        @if ($user->client->isDirty('invoice_details_same_as_account'))
                                            <tr>
                                                <td style="font-weight: bold">Invoice details same as account:</td>
                                                    <td style="font-weight: 500">
                                                        <del style="color: #4A4A4A">{{ $user->client->getOriginal('invoice_details_same_as_account') ? 'yes' : 'no' }}</del>
                                                    </td>
                                                <td style="font-weight: 500">{{ $user->client->invoice_details_same_as_account  ? 'yes' : 'no' }}</td>
                                            </tr>
                                        @endif

                                        @if ($user->client->organisation->isDirty('organisation_address_line_1'))
                                            <tr>
                                                <td style="font-weight: bold">Invoice address line 1:</td>
                                                    <td style="font-weight: 500">
                                                        <del style="color: #4A4A4A">{{ $user->client->organisation->getOriginal('organisation_address_line_1') }}</del>
                                                    </td>
                                                <td style="font-weight: 500">{{ $user->client->organisation->organisation_address_line_1 }}</td>
                                            </tr>
                                        @endif

                                        @if ($user->client->organisation->isDirty('organisation_address_line_2'))
                                            <tr>
                                                <td style="font-weight: bold">Invoice address line 2:</td>
                                                    <td style="font-weight: 500">
                                                        <del style="color: #4A4A4A">{{ $user->client->organisation->getOriginal('organisation_address_line_2') }}</del>
                                                    </td>
                                                <td style="font-weight: 500">{{ $user->client->organisation->organisation_address_line_2 }}</td>
                                            </tr>
                                        @endif

                                        @if ($user->client->organisation->isDirty('organisation_county'))
                                            <tr>
                                                <td style="font-weight: bold">Invoice county:</td>
                                                    <td style="font-weight: 500">
                                                        <del style="color: #4A4A4A">{{ $user->client->organisation->getOriginal('organisation_county') }}</del>
                                                    </td>
                                                <td style="font-weight: 500">{{ $user->client->organisation->organisation_county }}</td>
                                            </tr>
                                        @endif

                                        @if ($user->client->organisation->isDirty('organisation_postcode'))
                                            <tr>
                                                <td style="font-weight: bold">Invoice postcode:</td>
                                                    <td style="font-weight: 500">
                                                        <del style="color: #4A4A4A">{{ $user->client->organisation->getOriginal('organisation_postcode') }}</del>
                                                    </td>
                                                <td style="font-weight: 500">{{ $user->client->organisation->organisation_postcode }}</td>
                                            </tr>
                                        @endif

                                        @if ($user->client->isDirty('invoice_email_same_as_account'))
                                            <tr>
                                                <td style="font-weight: bold">Invoice details same as account:</td>
                                                    <td style="font-weight: 500">
                                                        <del style="color: #4A4A4A">{{ $user->client->getOriginal('invoice_email_same_as_account') ? 'yes' : 'no' }}</del>
                                                    </td>
                                                <td style="font-weight: 500">{{ $user->client->invoice_email_same_as_account  ? 'yes' : 'no' }}</td>
                                            </tr>
                                        @endif

                                        @if ($user->client->organisation->isDirty('organisation_email'))
                                            <tr>
                                                <td style="font-weight: bold">Invoice postcode:</td>
                                                    <td style="font-weight: 500">
                                                        <del style="color: #4A4A4A">{{ $user->client->organisation->getOriginal('organisation_email') }}</del>
                                                    </td>
                                                <td style="font-weight: 500">{{ $user->client->organisation->organisation_email }}</td>
                                            </tr>
                                        @endif

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
