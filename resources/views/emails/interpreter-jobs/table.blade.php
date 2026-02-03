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

                    @if ($wasSentToAdmin)
                        <tr>
                            <td style="font-weight: bold">Client:</td>
                            <td style="font-weight: 500;text-transform:capitalize">{{ $job->client->user->getFullName() }}</td>
                        </tr>
                        @if(isset($job->client->organisation))
                            <tr>
                                <td style="font-weight: bold"></td>
                                <td style="font-weight: 500;text-transform:capitalize">
                                    @if(isset($job->client->organisation->company))
                                        {{ $job->client->organisation->company->name }}
                                    @else
                                        {{ $job->client->organisation->organisation_company }}
                                    @endif
                                </td>
                            </tr>
                        @endif
                    @endif

                    <tr>
                        <td style="font-weight: bold">Date:</td>
                        <td style="font-weight: 500">{{ $job->appointment_date->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold">Time:</td>
                        <td style="font-weight: 500">{{ $job->start_time }} ({{ $job->formattedDuration }})</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold">Language:</td>
                        <td style="font-weight: 500">{{ $job->toLanguage->name }}</td>
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

@if ($job->hasAddressFields())
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
                            <td style="font-weight: 500">{{ $job->department }}</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold">Address line 1:</td>
                            <td style="font-weight: 500">{{ $job->address_line_1 }}</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold">Address line 2:</td>
                            <td style="font-weight: 500">{{ $job->address_line_2 ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold">County:</td>
                            <td style="font-weight: 500">{{ $job->county }}</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold">Postcode:</td>
                            <td style="font-weight: 500">{{ $job->postcode }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
@endif
