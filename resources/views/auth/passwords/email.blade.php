@extends('layouts.login')

@section('content')
    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        
        <div class="login__inputs">
            <div class="login__field login__field--email">
                <input id="email" type="email" class="input login__input" placeholder="Email" name="email" value="{{ old('email') }}" required autofocus>
            </div>

            @if ($errors->has('email'))
                <div class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('email') }}</strong>
                </div>
            @endif
            
        </div>

        <button type="submit" class="btn btn--primary m-auto" style="max-width:220px">Send Password Reset</button>
    </form>
@endsection
