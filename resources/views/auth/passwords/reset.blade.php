@extends('layouts.login')

@section('content')

    <form method="POST" action="{{ route('password.update') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $token }}">

        <div class="login__inputs">
            <div class="login__field login__field--email">
                <input type="email" class="input login__input" placeholder="Email" name="email" value="{{ $email ?? old('email') }}" required auto>
            </div>

            @if ($errors->has('email'))
                <div class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('email') }}</strong>
                </div>
            @endif

            <div class="login__field login__field--email login__field--password">
                <input type="password" class="input login__input" placeholder="Password" name="password" required {{ isset($email) ? 'autofocus' : ''}}>
            </div>

            @if ($errors->has('password'))
                <div class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('password') }}</strong>
                </div>
            @endif

            <div class="login__field login__field--password">
                <input type="password" class="input login__input" placeholder="Confirm Password" name="password_confirmation" required>
            </div>
            
        </div>

        <button type="submit" class="btn btn--primary m-auto" style="max-width:220px">
            {{ $buttonText ?? 'Reset Password' }}
        </button>
    </form>
    
@endsection
