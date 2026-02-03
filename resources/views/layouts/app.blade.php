<!DOCTYPE html>
<html class="html" lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>A to Z</title>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />

    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
	<link href="/favicon.ico" rel="shortcut icon" />
    <style>
        .sidebar {
            /* width: 11%; Full viewport width */
            overflow-x: auto;
            /* Enable horizontal scrolling */
            white-space: nowrap;
            /* Prevent items from wrapping to the next line */
        }

        .sidebar__items {
            /* display: flex; Use flexbox for horizontal layout */
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar__item {
            margin-right: 1rem;
            flex-shrink: 0;
            /* Prevent items from shrinking */
        }

        .sidebar__link {
            /* display: flex; */
            align-items: center;
            text-decoration: none;
            color: inherit;
            padding: 0.5rem;
        }

        .sidebar__image {
            margin-right: 0.5rem;
        }

        .sidebar__item--active .sidebar__link {
            background-color: #f0f0f0;
            border-radius: 4px;
        }
    </style>
    <style>
        #signature-pad {
            border: 1px solid #000;
            width: 400px;
            height: 200px;
        }
    </style>
</head>

<body class="body">

    @include('layouts.flash')

    <div id="app">
        <header class="header">
            <div class="header__logo">
                <a href="/">
                    <img class="img-fluid header__image" alt="A to Z" src="/img/logo.svg">
                </a>
            </div>
            <div class="header__inner">

                {{ Form::open(['method' => 'GET', 'class' => 'header__search']) }}

                {{ Form::text('search', request('search'), [
                    'placeholder' => 'Search by ref',
                    'class' => 'input header__input',
                ]) }}

                <button type="submit" class="btn btn--secondary header__btn">Submit</button>

                {{ Form::close() }}

                <div class="header__right">
                    <div class="header__logout" id="headerLogout">
                        <h2 class="header__name">{{ auth()->user()->getFullName() }}</h2>

                        @if (session()->has('impersonate'))
                            <a class="d-none d-xl-flex btn btn--primary btn--logout bg-primary mr-3"
                                href="{{ route('unimpersonate', auth()->user()->id) }}">Stop</a>
                        @endif

                        <a class="d-none d-xl-flex btn btn--primary btn--logout" href="{{ route('logout') }}"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            {{ __('Logout') }}
                        </a>
                        {{ Form::open(['route' => 'logout', 'class' => 'd-none', 'id' => 'logout-form']) }}{{ Form::close() }}

                    </div>
                    <button class="header__hamburger hamburger hamburger--spin" type="button" id="hamburger">
                        <span class="hamburger-box">
                            <span class="hamburger-inner"></span>
                        </span>
                    </button>
                </div>
            </div>
            <div class="header__collapse" id="headerCollapse">
                <ul class="list header__items">

                    @unlessrole('new-agent')
                        <li
                            class="header__item {{ \Request::is('*jobs*') || \Request::is('/') ? 'sidebar__item--active' : '' }}">
                            <a href="{{ route('interpreter-jobs.index') }}" class="header__link">
                                <img src="/img/post-it.svg" alt="Job Board" class="img-fluid header__image">
                                <div class="header__text">Job Board</div>
                            </a>
                        </li>
                    @endunlessrole

                    @unlessrole('admin')
                        <li class="header__item sidebar__item--active">
                            <a href="@hasanyrole('new-agent|agent') {{ route('agents.profile.edit') }} @else {{ route('clients.profile.edit') }} @endhasanyrole"
                                class="header__link">
                                <img src="/img/clients.svg" alt="My Account" class="img-fluid header__image">
                                <div class="header__text">My Account</div>
                            </a>
                        </li>
                        <li class="sidebar__item {{ \Request::is('*timesheet*') ? 'sidebar__item--active' : '' }}">
                            <a href="{{ route('timesheet.index') }}" class="sidebar__link">
                                <img src="/img/inbox.svg" alt="Timesheet" class="img-fluid sidebar__image">
                                <div class="sidebar__text">Timesheet</div>
                            </a>
                        </li>
                        <li class="sidebar__item {{ \Request::is('*feedback*') ? 'sidebar__item--active' : '' }}">
                            <a href="{{ route('feedback.index') }}" class="sidebar__link">
                                <img src="/img/agents.svg" alt="feedback" class="img-fluid sidebar__image">
                                <div class="sidebar__text">Feedback</div>
                            </a>
                        </li>
                    @endunlessrole

                    @role('admin')

                        <li class="header__item {{ \Request::is('*clients*') ? 'header__item--active' : '' }}">
                            <a href="{{ route('clients.index') }}" class="header__link">
                                <img src="/img/clients.svg" alt="Clients" class="img-fluid header__image">
                                <div class="header__text">Clients</div>
                            </a>
                        </li>

                        <li class="header__item {{ \Request::is('*agents*') ? 'header__item--active' : '' }}">
                            <a href="{{ route('agents.index') }}" class="header__link">
                                <img src="/img/agents.svg" alt="Agents" class="img-fluid header__image">
                                <div class="header__text">Agents</div>
                            </a>
                        </li>

                        <li class="header__item {{ \Request::is('*companies*') ? 'header__item--active' : '' }}">
                            <a href="{{ route('companies.index') }}" class="header__link">
                                <img src="/img/inbox.svg" alt="Companies" class="img-fluid header__image">
                                <div class="header__text">Companies</div>
                            </a>
                        </li>

                    @endrole



                    @role('super-admin')
                        <li class="header__item {{ \Request::is('*admins*') ? 'header__item--active' : '' }}">
                            <a href="{{ route('admins.index') }}" class="header__link">
                                <img src="/img/key.svg" alt="Admins" class="img-fluid header__image">
                                <div class="header__text">Admins</div>
                            </a>
                        </li>

                    @endhasrole

                    <li class="header__item header__item--logout d-xl-none">
                        <a class="btn btn--primary btn--logout" href="{{ route('logout') }}"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            {{ __('Logout') }}
                        </a>
                    </li>
                </ul>
            </div>
        </header>

        <aside class="sidebar">
            <ul class="list sidebar__items">

                @unlessrole('new-agent')
                    <li
                        class="sidebar__item {{ \Request::is('*jobs*') || \Request::is('/') ? 'sidebar__item--active' : '' }}">
                        <a href="{{ route('interpreter-jobs.index') }}" class="sidebar__link">
                            <img src="/img/post-it.svg" alt="Job Board" class="img-fluid sidebar__image">
                            <div class="sidebar__text">Job Board</div>
                        </a>
                    </li>
                @endunlessrole

                @unlessrole('admin')
                    <li class="sidebar__item {{ \Request::is('*profile*') ? 'sidebar__item--active' : '' }}">
                        <a href="@hasanyrole('new-agent|agent'){{ route('agents.profile.edit') }}@else{{ route('clients.profile.edit', ['organisation' => optional(auth()->user()->client)->organisation ?? 0]) }}@endhasanyrole"
                            class="sidebar__link">
                            <img src="/img/clients.svg" alt="My Account" class="img-fluid sidebar__image">
                            <div class="sidebar__text">My Account</div>
                        </a>
                    </li>
                    <li class="sidebar__item {{ \Request::is('*timesheet*') ? 'sidebar__item--active' : '' }}">
                        <a href="{{ route('timesheet.index') }}" class="sidebar__link">
                            <img src="/img/inbox.svg" alt="Timesheet" class="img-fluid sidebar__image">
                            <div class="sidebar__text">Timesheet</div>
                        </a>
                    </li>

                    <li class="sidebar__item {{ \Request::is('*feedback*') ? 'sidebar__item--active' : '' }}">
                        <a href="{{ route('feedback.index') }}" class="sidebar__link">
                            <img src="/img/agents.svg" alt="feedback" class="img-fluid sidebar__image">
                            <div class="sidebar__text">Feedback</div>
                        </a>
                    </li>
                @endunlessrole

                @role('admin')
                    <li class="sidebar__item {{ \Request::is('*agents*') ? 'sidebar__item--active' : '' }}">
                        <a href="{{ route('agents.index') }}" class="sidebar__link">
                            <img src="/img/agents.svg" alt="Agents" class="img-fluid sidebar__image">
                            <div class="sidebar__text">Agents</div>
                        </a>
                    </li>
                    <li class="sidebar__item {{ \Request::is('*timesheet*') ? 'sidebar__item--active' : '' }}">
                        <a href="{{ route('timesheet.index') }}" class="sidebar__link">
                            <img src="/img/inbox.svg" alt="Timesheet" class="img-fluid sidebar__image">
                            <div class="sidebar__text">Timesheet</div>
                        </a>
                    </li>
                    <li class="sidebar__item {{ \Request::is('*clients*') ? 'sidebar__item--active' : '' }}">
                        <a href="{{ route('clients.index') }}" class="sidebar__link">
                            <img src="/img/clients.svg" alt="Clients" class="img-fluid sidebar__image">
                            <div class="sidebar__text">Clients</div>
                        </a>
                    </li>
                    <li class="sidebar__item {{ \Request::is('*companies*') ? 'sidebar__item--active' : '' }}">
                        <a href="{{ route('companies.index') }}" class="sidebar__link">
                            <img src="/img/inbox.svg" alt="Companies" class="img-fluid sidebar__image">
                            <div class="sidebar__text">Companies</div>
                        </a>
                    </li>
                    <li class="sidebar__item {{ \Request::is('*report*') ? 'sidebar__item--active' : '' }}">
                        <a href="{{ route('report.index', request()->query()) }}" class="sidebar__link">
                            <img src="/img/inbox.svg" alt="Reports" class="img-fluid sidebar__image">
                            <div class="sidebar__text">Report</div>
                        </a>

                    </li>
                @endrole
                @role('super-admin')
                    <li class="sidebar__item {{ \Request::is('*admins*') ? 'sidebar__item--active' : '' }}">
                        <a href="{{ route('admins.index') }}" class="sidebar__link">
                            <img src="/img/key.svg" alt="Admins" class="img-fluid sidebar__image">
                            <div class="sidebar__text">Admins</div>
                        </a>
                    </li>
                @endhasrole
                @role('super-admin|admin')
                    <li class="sidebar__item {{ \Request::is('*languages*') ? 'sidebar__item--active' : '' }}">
                        <a href="{{ route('languages.index') }}" class="sidebar__link">
                            <img src="/img/agents.svg" alt="Languages" class="img-fluid sidebar__image">
                            <div class="sidebar__text">Languages</div>
                        </a>
                    </li>
                    <li class="sidebar__item {{ \Request::is('*feedback*') ? 'sidebar__item--active' : '' }}">
                        <a href="{{ route('feedback.index') }}" class="sidebar__link">
                            <img src="/img/agents.svg" alt="feedback" class="img-fluid sidebar__image">
                            <div class="sidebar__text">Feedback</div>
                        </a>
                    </li>
                @endhasrole

            </ul>
        </aside>

        @if (auth()->user()->hasNotSeenTerms())
            <div class="terms" id="terms">
                <div class="terms-title">
                    <h1>A to Z T&C’s</h1>
                    <h2>Please read our user policy</h2>
                </div>
                <div class="terms-inner">
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer id odio ac elit tempor vehicula.
                        Suspendisse lobortis nisi ut tincidunt dignissim. Sed auctor lacinia ipsum, et aliquet lectus.
                        Duis eget lacus ut quam facilisis volutpat. Sed tempus massa non eros tincidunt mattis.
                        Suspendisse potenti. Praesent eget neque dignissim, maximus tellus vel, imperdiet velit. Aenean
                        quis lorem finibus, elementum risus in, facilisis enim. Donec eget neque feugiat, pellentesque
                        dolor porta, aliquet nulla. Nunc nec erat eros.</p>
                    <p>
                        Etiam a magna finibus, lacinia risus eu, convallis nisi. Praesent vitae nisl ut elit interdum
                        tincidunt a ut justo. Mauris varius at quam bibendum varius. Donec eu sem dictum, sagittis nulla
                        ut, laoreet ligula. Proin blandit rutrum rutrum. Etiam ullamcorper eget augue et pretium. Donec
                        vehicula sed dui ut hendrerit. Etiam pretium suscipit mauris quis placerat. Maecenas feugiat
                        sapien vitae sapien consectetur efficitur. Vestibulum lacinia nibh malesuada augue porttitor
                        imperdiet. Morbi eget est sem. Aliquam sed cursus enim. Proin malesuada quis enim et aliquet.
                        Donec id scelerisque elit.
                    </p>
                    <p>
                        Donec bibendum dui vel justo varius, quis aliquet tortor congue. Pellentesque habitant morbi
                        tristique senectus et netus et malesuada fames ac turpis egestas. Fusce malesuada porttitor arcu
                        at accumsan. Maecenas id consectetur lorem. Etiam nec ornare nunc. Nunc at nisl id quam
                        malesuada iaculis ut vel lorem. Integer feugiat lorem non luctus lacinia.
                    </p>
                    <p>
                        Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas.
                        Curabitur egestas quis tellus eget rutrum. Nunc auctor urna sodales condimentum molestie. Donec
                        at risus orci. Quisque laoreet rutrum enim, in scelerisque odio vulputate eu. Suspendisse porta,
                        quam sed faucibus dapibus, sem justo faucibus dui, in mattis mi augue id purus. Donec lobortis
                        sagittis leo non blandit. Interdum et malesuada fames ac ante
                    </p>
                </div>
                <button class="btn btn--primary terms__btn" id="termsButton">Accept</button>
            </div>

            <script>
                window.addEventListener('load', function() {
                    var termsButton = document.getElementById('termsButton');

                    termsButton.onclick = function() {
                        document.getElementById('terms').remove();
                        axios.put('{{ route('client.seen-terms', auth()->user()->client->id) }}');
                    }
                });
            </script>
        @endif

        <div class="container content">
            @yield('content')
        </div>
    </div>

    <script src="{{ asset('js/app.js') }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/signature_pad@2.3.2/dist/signature_pad.min.js"></script>
    <script>
        var canvas = document.getElementById('signature-pad');
        if(canvas) {
            var signaturePad = new SignaturePad(canvas);

            var clearButton = document.getElementById('clear');
            if(clearButton) {
                clearButton.addEventListener('click', function() {
                    signaturePad.clear();
                });
            }

            var saveButton = document.getElementById('save');
            if(saveButton) {
                saveButton.addEventListener('click', function() {
                    if (signaturePad.isEmpty()) {
                        alert("Please provide a signature first.");
                    } else {
                        // Do something with the signature image data
                        // For example: document.getElementById('signature-input').value = signaturePad.toDataURL();
                    }
                });
            }
        }
    </script>
    @if(env('APP_ENV') == 'local')
    <script>
        // Additional signature pad functionality for local environment
        var canvas = document.getElementById('signature-pad');
        if(canvas) {
            var signaturePad = new SignaturePad(canvas);
            var saveButton = document.getElementById('save');
            if(saveButton) {
                saveButton.addEventListener('click', function() {
                    if (signaturePad.isEmpty()) {
                        alert('Please provide a signature first.');
                    } else {
                        var dataURL = signaturePad.toDataURL('image/png');
                        var signatureInput = document.getElementById('signature');
                        var signatureForm = document.getElementById('signature-form');
                        if(signatureInput) signatureInput.value = dataURL;
                        if(signatureForm) signatureForm.submit();
                    }
                });
            }
        }
    </script>
    @endif


    @stack('scripts')
</body>

</html>
