<form class="filter" id="agentFilterForm">
    <div class="d-flex">
        {{ Form::text('name', request('name'), [
            'class' => 'input filter__input',
            'placeholder' => 'Filter by name...',
        ]) }}
        {{ Form::text('email', request('email'), [
            'class' => 'input filter__input ml-2',
            'placeholder' => 'Filter by email...',
        ]) }}
        {{ Form::select('language', $languages, request('language'), [
            'class' => 'input input--select filter__input ml-2',
            'placeholder' => 'Filter by language...',
        ]) }}
        <input type="submit" hidden>
    </div>
    {{ Form::select('agent_type', App\Agent::$agentTypes, request('agent_type'), [
        'class' => 'input input--select filter__input',
        'placeholder' => 'Filter by agent type...',
    ]) }}
</form>

@push('scripts')
    <script>
        window.addEventListener('load', function() {
            $('.filter select').change(function() {
                console.log('select changed')
                $('#agentFilterForm').submit()
            })
        })
    </script>
@endpush
