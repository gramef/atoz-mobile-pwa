<nav class="nav">
  <h1 class="nav__heading">Clients</h1>
  <ul class="links">
    <li class="links__item">

      @if (Route::current()->getName() == 'clients.index')
        <div class="links__text">Active</div>
      @else
        <a href="{{ route('clients.index') }}" class="links__text links__text--link">Active</a>
      @endif

    </li>
    <li class="links__item">

      @if (Route::current()->getName() == 'clients.new-requests.index')
        <div class="links__text">New Requests</div>
      @else
        <a href="{{ route('clients.new-requests.index') }}" class="links__text links__text--link">New Requests</a>
      @endif

    </li>
    <li class="links__item">

      @if (Route::current()->getName() == 'clients.archived.index')
        <div class="links__text">Archived</div>
      @else
        <a href="{{ route('clients.archived.index') }}" class="links__text links__text--link">Archived</a>
      @endif  

    </li>
    <li class="links__item links__item--last">

      @if (Route::current()->getName() == 'clients.rejected.index')
        <div class="links__text">Rejected</div>
      @else
        <a href="{{ route('clients.rejected.index') }}" class="links__text links__text--link">Rejected</a>
      @endif

    </li>
  </ul>
  <a href="{{ route('clients.create') }}" class="btn btn--primary section__btn">+ Add Client</a>
</nav>