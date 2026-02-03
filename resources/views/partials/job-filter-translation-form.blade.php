{{ Form::open(['method' => 'GET', 'class' => 'filter', 'id' => 'filterForm']) }}
    <div class="section__left">

        {{ Form::select('language_id', $languages, request('language_id'), [ 
                'class' => 'input input--select mb-3 mb-lg-0 section__input', 
                'placeholder' => 'Filter by language...' 
            ]) 
        }}

        @role('admin')
            {{ Form::select('client', $clients, request('client'), [ 
                    'class' => 'input input--select mb-3 mb-lg-0 section__input', 
                    'placeholder' => 'Filter by client...' 
                ])
            }}

            {{ Form::select('company', $companies, request('company'), [ 
                    'class' => 'input input--select mb-3 mb-lg-0 section__input', 
                    'placeholder' => 'Filter by company...' 
                ])
            }}
             
        @endrole

        {{ Form::select('status', $statuses, request('status'), [ 
                'class' => 'input input--select mb-3 mb-lg-0 section__input section__input--middle', 
                'placeholder' => 'Filter by status...' 
            ])
        }}

    </div>

    {{ Form::text('date', request('date'), [ 
            'class' => 'input input--date-filter',
            'placeholder' => 'Filter By Date...',
            'id' => 'dateFilter'
        ])
    }}

{{ Form::close() }}


@push('scripts')
    <script>
        window.addEventListener('load', function() {
            $('#dateFilter').flatpickr({
                allowInput: true,
                mode: 'range',
                dateFormat: 'Y-m-d',
                altInput: true,
                altFormat: 'd/m/Y',
                onChange: function(selectedDates) {
                    var _this = this,

                    dateArr = selectedDates.map(function(date){
                        return _this.formatDate(date,'d.m.Y')
                    });

                    /* only submit the form when the end date has been selected */
                    // if (dateArr[1]) {
                        $('#filterForm').submit()
                    // }
                }
            })
        })
    </script>
@endpush