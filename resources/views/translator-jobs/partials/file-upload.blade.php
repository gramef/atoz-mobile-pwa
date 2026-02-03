@role('agent')

    <translation-file-dropzone
        :existing-documents="{{ json_encode($translatorJob->documents->where('type', $type)->values()) }}"
        :document-type="{{ $type }}"
        label="{{ $label }}"
        url="{{ route('translator-jobs.documents.store', $translatorJob) }}"
    ></translation-file-dropzone>

@else

    <div class="font-weight-bold mb-2">{{ $label }}</div>

    <ul class="list-unstyled mb-0">

        @forelse ($translatorJob->documents->where('type', $type) as $document)
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
