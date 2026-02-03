<div class="row">
    <div class="col-lg-3">
        <fieldset class="form__field">
            {{ Form::label('documents[passport]', 'Copy of passport:', ['class' => 'required label form__label']) }}

            @include('agents.documents.upload', [ 
                'agent' => $agent, 
                'document' => $agent->documents->firstWhere('type', config('enums.document_types')['passport']), 
                'input' => 'passport' 
            ])

        </fieldset>
        <fieldset class="form__field">
            {{ Form::label('documents[dbs]', 'Copy of DBS:', ['class' => 'label form__label']) }}

            @include('agents.documents.upload', [ 
                'agent' => $agent, 
                'document' => $agent->documents->firstWhere('type', config('enums.document_types')['dbs']), 
                'input' => 'dbs' 
            ])

        </fieldset>
    </div>
    <div class="col-lg-3">
        <fieldset class="form__field">
            {{ Form::label('documents[interpreter_contract]', 'Copy of contract:', ['class' => 'label form__label']) }}

            @include('agents.documents.upload', [ 
                'agent' => $agent, 
                'document' => $agent->documents->firstWhere('type', config('enums.document_types')['interpreter_contract']), 
                'input' => 'interpreter_contract' 
            ])

        </fieldset>
        <fieldset class="form__field">
            {{ Form::label('documents[proof_of_address]', 'Proof of address:', ['class' => 'required label form__label']) }}

            @include('agents.documents.upload', [ 
                'agent' => $agent, 
                'document' => $agent->documents->firstWhere('type', config('enums.document_types')['proof_of_address']), 
                'input' => 'proof_of_address' 
            ])

        </fieldset>
    </div>
    <div class="col-lg-3">
        <fieldset class="form__field">
            {{ Form::label('documents[references]', 'References:', ['class' => 'label form__label']) }}

            @include('agents.documents.upload', [ 
                'agent' => $agent, 
                'document' => $agent->documents->firstWhere('type', config('enums.document_types')['references']), 
                'input' => 'references' 
            ])

        </fieldset>
        <fieldset class="form__field">
            {{ Form::label('documents[code_of_conduct]', 'Signed code of conduct:', ['class' => 'label form__label']) }}

            @include('agents.documents.upload', [ 
                'agent' => $agent, 
                'document' => $agent->documents->firstWhere('type', config('enums.document_types')['code_of_conduct']), 
                'input' => 'code_of_conduct' 
            ])

        </fieldset>
    </div>
    <div class="col-lg-3">
        <fieldset class="form__field">
            {{ Form::label('documents[induction]', 'Induction:', ['class' => 'required label form__label']) }}

            @include('agents.documents.upload', [ 
                'agent' => $agent, 
                'document' => $agent->documents->firstWhere('type', config('enums.document_types')['induction']), 
                'input' => 'induction' 
            ])

        </fieldset>
    </div>
    <div class="col-sm-2">
        <fieldset class="form__field">
            {{ Form::label('dbs_expiry_date', 'DBS Expiry date:', ['class' => 'label form__label ']) }}
            {{ Form::text('dbs_expiry_date', optional($agent->dbs_expiry_date)->toDateString(), [
                    'class' => 'input form__input',
                    'placeholder' => 'dd/mm/yyyy',
                ])
            }}
        </fieldset>
    </div>
    <div class="col-sm-2">
        <fieldset class="form__field">
            {{ Form::label('dbs_number', 'DBS number:', ['class' => 'label form__label']) }}
            {{ Form::text('dbs_number', $agent->dbs_number, [
                    'class' => 'input form__input',
                    'placeholder' => 'DBS Number',
                ])
            }}
        </fieldset>
    </div>
    <div class="col-sm-2">
        <fieldset class="form__field">
            {{ Form::label('induction_date', 'Induction date:', ['class' => 'label form__label required']) }}
            {{ Form::text('induction_date', optional($agent->induction_date)->toDateString(), [
                    'class' => 'input form__input',
                    'placeholder' => 'dd/mm/yyyy',
                    'required' => 'required',
                ])
            }}
        </fieldset>
    </div>
    <div class="col-sm-2">
        <fieldset class="form__field">
            {{ Form::label('dbs_update_reference_number', 'DBS update reference number:', ['class' => 'label form__label']) }}
            {{ Form::text('dbs_update_reference_number', $agent->dbs_update_reference_number, [
                    'class' => 'input form__input',
                    'placeholder' => 'DBS Ref',
                ])
            }}
        </fieldset>
    </div>
</div>

@push('scripts')
    <script>
        window.addEventListener('load', function() {
         if (document.getElementById('dbs_expiry_date')) {
            $('#dbs_expiry_date, #induction_date').flatpickr({
                allowInput: true,
                altInput: true,
                altFormat: 'd/m/Y',
            })
            }
        })
        

        $('[data-route]').on('click', function() {
            var result = confirm('Are you sure you want to delete this document?')

            if (!result) return;

            axios.delete($(this).data('route'))
                .then(() => {
                    $(this).parent().html('<div class="label form__label">This document has not been uploaded yet</div>') 
                })
        })
    </script>
@endpush