<ul class="tabs">

    @role('admin')
        <li class="tabs__tab">
            @if (Route::current()->getName() == 'agents.edit')
                <div class="tabs__text">
                    Agent Details
                </div>
            @else
                <a class="tabs__text tabs__text--link" href="{{ route('agents.edit', $agent->id) }}">
                    Agent Details
                </a>
            @endif
        </li>
    @else
        <li class="tabs__tab">
            @if (Route::current()->getName() == 'agents.profile.edit')
                <div class="tabs__text">
                    Agent Details
                </div>
            @else
                <a class="tabs__text tabs__text--link" href="{{ route('agents.profile.edit') }}">
                    Agent Details
                </a>
            @endif
        </li>
    @endrole

    @role('admin')
        <li class="tabs__tab">

            @if (Route::current()->getName() == 'agents.documents.edit')
                <div class="tabs__text">
                    Agent Documents
                </div>
            @else
                <a class="tabs__text tabs__text--link" href="{{ route('agents.documents.edit', $agent->id) }}">
                    Agent Documents
                </a>
            @endif

        </li>
    @else
        <li class="tabs__tab">

            @if (Route::current()->getName() == 'agents.profile.documents.edit')
                <div class="tabs__text">
                    Agent Documents
                </div>
            @else
                <a class="tabs__text tabs__text--link" href="{{ route('agents.profile.documents.edit') }}">
                    Agent Documents
                </a>
            @endif

        </li>
    @endrole

</ul>
