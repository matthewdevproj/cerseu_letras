@props(['id', 'title', 'active' => false])

<div class="border-b border-gray-200 last:border-b-0" x-data="{ open: {{ $active ? 'true' : 'false' }} }">
    <button @click="open = !open" type="button"
        :aria-expanded="open.toString()"
        aria-controls="{{ $id }}-panel"
        id="{{ $id }}-trigger"
        class="w-full px-6 py-5 bg-gradient-to-r from-white to-gray-50 hover:from-gray-50 hover:to-gray-100 flex items-center justify-between transition-all duration-200 hover:shadow-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-[-2px] focus-visible:outline-unmsm-azul"
        :class="{ 'bg-gradient-to-r from-unmsm-azul/5 to-unmsm-azul/2 border-l-4 border-unmsm-azul': open }">
        <h2 class="text-lg font-semibold transition-colors duration-200" :class="{ 'text-unmsm-azul': open, 'text-gray-900': !open }">
            {{ $title }}
        </h2>
        <div class="flex items-center gap-3">
            <svg class="w-6 h-6 transition-all duration-300 flex-shrink-0" aria-hidden="true"
                :class="{ 'text-unmsm-azul rotate-180': open, 'text-gray-400': !open }"
                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
            </svg>
        </div>
    </button>
    {{-- x-collapse (plugin @alpinejs/collapse) anima la altura real del
         contenido de forma fluida y honra prefers-reduced-motion. Sin JS, el
         panel queda visible (degradación correcta). --}}
    <div x-show="open" x-collapse
        id="{{ $id }}-panel"
        role="region"
        aria-labelledby="{{ $id }}-trigger"
        class="px-6 py-5 bg-gray-50/50 border-l-4 border-unmsm-dorado/30">
        <div class="text-gray-700 leading-relaxed">
            {{ $slot }}
        </div>
    </div>
</div>