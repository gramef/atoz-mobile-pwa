@if ($user->isDirty('title'))
    <tr>
        <td style="font-weight: bold">Title</td>
            <td style="font-weight: 500">
                <del style="color: #4A4A4A">{{ config('enums.titles')[$user->getOriginal('title')] }}</del>
            </td>
        <td style="font-weight: 500">{{ config('enums.titles')[$user->title] }}</td>
    </tr>
@endif

@if ($user->isDirty('first_name'))
    <tr>
        <td style="font-weight: bold">First name:</td>
            <td style="font-weight: 500">
                <del style="color: #4A4A4A">{{ $user->getOriginal('first_name') }}</del>
            </td>
        <td style="font-weight: 500">{{ $user->first_name }}</td>
    </tr>
@endif

@if ($user->isDirty('last_name'))
    <tr>
        <td style="font-weight: bold">Last name:</td>
            <td style="font-weight: 500">
                <del style="color: #4A4A4A">{{ $user->getOriginal('last_name') }}</del>
            </td>
        <td style="font-weight: 500">{{ $user->last_name }}</td>
    </tr>
@endif

@if ($user->isDirty('email'))
    <tr>
        <td style="font-weight: bold">Email:</td>
            <td style="font-weight: 500">
                <del style="color: #4A4A4A">{{ $user->getOriginal('email') }}</del>
            </td>
        <td style="font-weight: 500">{{ $user->email }}</td>
    </tr>
@endif

@role('client')
    @if ($user->client->isDirty('contact_number'))
        <tr>
            <td style="font-weight: bold">Contact number:</td>
                <td style="font-weight: 500">
                    <del style="color: #4A4A4A">{{ $user->client->getOriginal('contact_number') }}</del>
                </td>
            <td style="font-weight: 500">{{ $user->client->contact_number }}</td>
        </tr>
    @endif
@elserole('agent')
    @if ($user->agent->isDirty('contact_number'))
        <tr>
            <td style="font-weight: bold">Contact number:</td>
                <td style="font-weight: 500">
                    <del style="color: #4A4A4A">{{ $user->agent->getOriginal('contact_number') }}</del>
                </td>
            <td style="font-weight: 500">{{ $user->agent->contact_number }}</td>
        </tr>
    @endif
@endrole
