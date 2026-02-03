<ul class="tabs">
  <li class="tabs__tab">

      @if (Route::current()->getName() == 'clients.new-requests.client.edit')
        <div class="tabs__text">
          Client details
        </div>
      @else

        @if (!$client->organisation)
          <a class="tabs__text tabs__text--link" href="{{ route('clients.new-requests.client.edit', $client->id) }}">
            Client details
          </a>
        @else
          <a class="tabs__text tabs__text--link" href="{{ route('clients.new-requests.client.edit', ['client' => $client->id, 'organisation' => true]) }}">
            Client details
          </a>
        @endif

      @endif

  </li>
  <li class="tabs__tab">

    @if (Route::current()->getName() == 'clients.new-requests.job.edit')
      <div class="tabs__text">
        Job details
      </div>
    @else
      <a class="tabs__text tabs__text--link" href="{{ route('clients.new-requests.job.edit', $client->id) }}">
        Job Details
      </a>
    @endif

  </li>
</ul>