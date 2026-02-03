@role('agent')
    {{-- @if ($interpreterJob->isWithin48Hours() == true) --}}
    <translation-file-dropzone
        :existing-documents="{{ json_encode($interpreterJob->documents->where('type', $type)->values()) }}"
        :document-type="{{ $type }}" label="{{ $label }}"
        url="{{ route('interpreter-jobs.documents.store', $interpreterJob) }}"></translation-file-dropzone>

    {{-- @else
        <span>Files Upload Time Exceed.</span>
    @endif --}}
@else
    <div class="font-weight-bold mb-2">{{ $label }}</div>

    <ul class="list-unstyled mb-0">

        @forelse ($interpreterJob->documents->where('type', $type) as $document)
            <li>
                <a href="{{ $document->fullUrl }}">
                    {{ $document->name }}
                </a>
            </li>
        @empty
            <li>This file has not been uploaded</li>
        @endforelse

    </ul>

@endrole
