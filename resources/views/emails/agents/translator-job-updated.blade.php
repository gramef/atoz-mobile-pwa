@extends('layouts.email')

@section('content')
    <thead>
        <th style="font-size: 28px; font-weight: bold; padding-bottom: 20px;">
            Your assigned Job was updated
        </th>
    </thead>
    <tbody>
        <tr>
            <td>
                <p style="font-size: 16px; font-weight: 500; line-height: 22px; margin-top: 0px; margin-bottom: 27px;">
                    This is an email notification to inform you that your assigned job
                    <span style="font-weight: bold">{{ $job['updated']->reference }}</span> was updated with the following
                    information.
                </p>
                <p style="font-size: 16px; font-weight: 500; line-height: 22px; margin-top: 0px; margin-bottom: 27px;">
                    You can login to the portal to accept or reject these changes.
                </p>
                <table style="width: 100%; background-color: #F9F9F9; margin-bottom: 22px;">
                    <thead>
                        <th colspan="2" style="color: #fff; font-size: 16px; line-height: 22px; font-weight: bold; padding: 15px 17px 13px; background-color: #369ECC;" align="left">
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
                                        <td style="font-weight: 500;text-transform:capitalize">{{ $job['updated']->statusName }}</td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold">Date:</td>

                                        @if ($job['updated']->isDirty('target_date'))
                                          <td style="font-weight: 500">
                                            <del style="color: #4A4A4A">{{ $job['original']->target_date->format('d/m/Y') }}</del>
                                          </td>
                                        @endif

                                        <td style="font-weight: 500">{{ $job['updated']->target_date->format('d/m/Y') }}</td>

                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold">Word Count:</td>

                                        @if ($job['updated']->isDirty('word_count'))
                                          <td style="font-weight: 500">
                                            <del style="color: #4A4A4A">{{ $job['original']->word_count }}</del>
                                          </td>
                                        @endif

                                        <td style="font-weight: 500">{{ $job['updated']->word_count }}</td>

                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold">Languages:</td>

                                        @if ($job['updated']->isDirty('from_language_id') || $job['updated']->isDirty('to_language_id'))
                                            <td style="font-weight: 500">
                                                <del style="color: #4A4A4A">{{ $job['updated']->fromLanguage->name }} - {{ $job['updated']->toLanguage->name }}</del>
                                            </td>
                                        @endif

                                        <td style="font-weight: 500">{{ $job['original']->fromLanguage->name }} - {{ $job['original']->toLanguage->name }}</td>

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
                                        <td style="font-weight: bold">Affirmation:</td>

                                        @if ($job['updated']->isDirty('affirmation'))
                                            <td style="font-weight: 500">
                                                <del style="color: #4A4A4A">{{  $job['original']->affirmation == true ? 'Yes' : 'No' }}</del>
                                            </td>
                                        @endif

                                        <td style="font-weight: 500">{{ $job['updated']->affirmation == true ? 'Yes' : 'No'  }}</td>

                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold">Affidavit:</td>

                                        @if ($job['updated']->isDirty('affidavit'))
                                            <td style="font-weight: 500">
                                                <del style="color: #4A4A4A">{{  $job['original']->affidavit == true ? 'Yes' : 'No' }}</del>
                                            </td>
                                        @endif

                                        <td style="font-weight: 500">{{  $job['updated']->affidavit == true ? 'Yes' : ' No' }}</td>
                                    </tr>
                                    @if ($job['updated']->isDirty('notes'))
                                        <tr>
                                            <td style="font-weight: bold">Notes:</td>
                                            <td style="font-weight: 500">The note content has changed.</td>
                                        </tr>
                                    @endif
                                    @if ($job['updated']->documentUploaded)
                                        <tr>
                                            <td style="font-weight: bold">Documents:</td>
                                            <td style="font-weight: 500">A Document has changed.</td>
                                        </tr>
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
