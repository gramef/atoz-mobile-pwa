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

                    @isset($quote) 
                    <tr>
                        <td style="font-weight: bold">Quote:</td>
                        <td style="font-weight: 500">{{ '£' . $quote->cost }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold">Description:</td>
                        <td style="font-weight: 500; text-transform:capitalize">{{ $quote->cost_description }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold">Quote status:</td>
                        <td style="font-weight: 500; text-transform:capitalize">{{ $quote->status }}</td>
                    </tr>
                    @endisset
                    
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
                        <td style="font-weight: 500">{{ $job->fromLanguage->name }} - {{ $job->toLanguage->name }}</td>
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
