@extends('layouts.login')

@section('content')
    {!! Form::open(['route' => 'login']) !!}
    <h1 class="heading login__heading">Login</h1>
    <div class="login__inputs">
        <div class="login__field login__field--email">
            <input type="email" class="input login__input" placeholder="Email" name="email" value="{{ old('email') }}"
                required autofocus>
        </div>

        @if ($errors->has('email'))
            <div class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('email') }}</strong>
            </div>
        @endif

        <div class="login__field login__field--password">
            <input type="password" class="input login__input" placeholder="Password" name="password" required>
        </div>

        @if ($errors->has('password'))
            <div class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('password') }}</strong>
            </div>
        @endif

    </div>
    <div class="login__field login__field--check form-check text-left">
        <input class="form-check-input" type="checkbox" name="remember" id="remember"
            {{ old('remember') ? 'checked' : '' }}>

        <label class="form-check-label" for="remember">
            {{ __('Remember Me') }}
        </label>
    </div>
    <a href="{{ route('password.request') }}" class="login__forgot">Forgot your password?</a>
    <button type="submit" class="btn btn--primary login__btn">{{ __('Login') }}</button>
    {!! Form::close() !!}
@endsection
