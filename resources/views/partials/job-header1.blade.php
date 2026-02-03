<header class="job__header">
    <h1 class="job__heading">

    {{$type}}
        @if ($type == 'interpreter-jobs')
            Interpreter job: {{ $job->toLanguage->name }}
        @else
            Translator job: {{ $job->toLanguage->name }} - {{ $job->fromLanguage->name }}
        @endif

        <div class="job__reference">{{ $job->reference }}</div>
    </h1>

   

</header>

<ul class="tabs">
    <li class="tabs__tab">



</ul>


