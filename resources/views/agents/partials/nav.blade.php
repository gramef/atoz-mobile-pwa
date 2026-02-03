<nav class="nav">
  <h1 class="nav__heading">Agents</h1>
  <ul class="links">
      <li class="links__item">

        @if (Route::current()->getName() == 'agents.index')
          <div class="links__text">View all</div>
        @else
          <a href="{{ route('agents.index') }}" class="links__text links__text--link">View all</a>
        @endif

      </li>
      <li class="links__item">

        @if (Route::current()->getName() == 'agents.new.index')
          <div class="links__text">New agents</div>
        @else
          <a href="{{ route('agents.new.index') }}" class="links__text links__text--link">New agents</a>
        @endif

      </li>
      <li class="links__item links__item--last">

        @if (Route::current()->getName() == 'agents.archived.index')
          <div class="links__text">Archived Agents</div>
        @else
          <a href="{{ route('agents.archived.index') }}" class="links__text links__text--link">Archived Agents</a>
        @endif
          
      </li>
  </ul>
  <a href="{{ route('agents.create') }}" class="btn btn--primary nav__btn">+ Add Agent</a>
</nav>