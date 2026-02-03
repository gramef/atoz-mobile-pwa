   <!-- Search Form -->
   <section class="content__search">
       {!! Form::open([
           'route' => 'timesheet.index',
           'method' => 'GET',
           'class' => 'd-flex flex-row justify-content-between',
       ]) !!}
       <div class="form-group mb-0">
           {{ Form::text('client_name', request('client_name'), [
               'class' => 'input filter__input',
               'placeholder' => 'Search by Client Name',
           ]) }}
       </div>

       <div class="form-group mb-0">
           {{ Form::text('agent_name', request('agent_name'), [
               'class' => 'input filter__input',
               'placeholder' => 'Search by Agent Name',
           ]) }}
       </div>

       <div class="form-group mb-0">
           {{ Form::text('ref', request('ref'), [
               'class' => 'input filter__input',
               'placeholder' => 'Search by Job ID (Ref)',
           ]) }}
       </div>
       {{-- <div class="form-group">
           {{ Form::select('bulk_id', $bulkIds, request('bulk_id'), [
               'class' => 'form-control',
               'placeholder' => 'Select Bulk ID...',
           ]) }}
       </div> --}}

       <div class="form-group mb-0">
           {{ Form::submit('Search', ['class' => 'btn btn--primary']) }}
       </div>
       {!! Form::close() !!}

   </section>
