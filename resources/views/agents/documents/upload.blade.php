{{ Form::file("documents[$input]", [ 'accept' => '.doc,.docx,.jpeg,.jpg,.pdf' ]) }}
<!--{{$document}}-->
@if ($document)
    <div class="d-flex pt-2">
        <a class="d-block" download href="{{ $document->fullUrl }}">
            {{ $document->name }}
        </a>
        <button class="ml-3 border-0 p-0 bg-transparent" type="button" data-route="{{ route('documents.destroy', $document) }}">
            <img class="img-fluid" src="{{ asset('/img/delete.svg') }}">
        </button>
    </div>
@else
    <div class="label form__label">This document has not been uploaded yet</div>
@endif