<aside class="messages">

    @if($errors->any())

        @foreach ($errors->all() as $error)

            <div class="message message--error">
                <span class="message__text">{{ $error }}</span>
                <span class="message__close"></span>
            </div>

        @endforeach

    @endif

    @if(session()->has('failure'))

        <div class="message message--error">
            <span class="message__text">{{ session()->get('failure') }}</span>
            <span class="message__close"></span>
        </div>

    @endif


    @if(session()->has('success'))

        <div class="message message--success">
            <span class="message__text">{{ session()->get('success') }}</span>
            <span class="message__close"></span>
        </div>

    @endif

    @if(session()->has('status'))

        <div class="message message--success">
            <span class="message__text">{{ session()->get('status') }}</span>
            <span class="message__close"></span>
        </div>

    @endif

</aside>
