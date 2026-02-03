{{ Form::open([ 'method' => 'GET', 'class' => 'filter', 'id' => 'clientFilterForm' ]) }}
    {{ Form::text('search', request('email'), [
            'class' => 'input filter__input ml-2',
            'placeholder' => 'Filter by name or email...'
        ])
    }}
    {{ Form::select('company', $companies, request('company'), [
            'class' => 'input input--select filter__input ml-auto',
            'placeholder' => 'Filter by company...',
        ])
    }}
{{ Form::close() }}

@push('scripts')
    <script>
        window.addEventListener('load', function() {
            $('#clientFilterForm .filter__input').change(function() {
                $(this).parents('form').submit()
            })
        })
    </script>
@endpush
