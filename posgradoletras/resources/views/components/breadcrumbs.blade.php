@props(['items'])

<nav class="flex mb-4" aria-label="Breadcrumb">
    <ol class="inline-flex flex-wrap items-center gap-y-1 text-sm">
        <li>
            <a href="{{ route('home') }}" class="text-gray-500 hover:text-unmsm-guinda transition-colors">
                <x-fas-house class="text-xs" aria-hidden="true" />
                <span class="sr-only">Inicio</span>
            </a>
        </li>
        @foreach($items as $item)
            <li class="flex items-center">
                <x-fas-chevron-right class="text-gray-300 mx-2 text-[10px]" aria-hidden="true" />
                @if(!$loop->last && !empty($item['url']))
                    <a href="{{ $item['url'] }}" class="text-gray-500 hover:text-unmsm-guinda transition-colors">{{ $item['label'] }}</a>
                @else
                    <span class="text-gray-700 font-medium" aria-current="page">{{ $item['label'] }}</span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
