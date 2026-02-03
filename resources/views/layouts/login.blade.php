<!DOCTYPE html>
<html class="html" lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>A to Z</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>

<body class="body body--login">
        @include('layouts.flash')
    <main class="login">
        <div class="container">
            <div class="login__inner">
                <img class="img-fluid login__logo" alt="A to Z" src="/img/logo.svg">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <div class="login__form">
                            @yield('content')
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="login__enquiry">
                            <h2 class="heading heading--white login__enquiry-heading">Have an enquiry?</h2>
                            <div class="login__text">Use the link below to send an enquiry and account registration
                                request</div>
                            
                            <a href="https://atozinterpreting.com/get-a-quote" class="btn login__btn login__btn--enquiry">
                                Send enquiry
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
</body>

</html>
