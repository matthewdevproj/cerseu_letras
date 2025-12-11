@props(['id', 'title', 'active' => false])

<div class="border-b border-gray-200 last:border-b-0" x-data="{ open: {{ $active ? 'true' : 'false' }} }">
    <button @click="open = !open"
        class="w-full px-6 py-5 bg-gradient-to-r from-white to-gray-50 hover:from-gray-50 hover:to-gray-100 flex items-center justify-between transition-all duration-200 hover:shadow-sm"
        :class="{ 'bg-gradient-to-r from-unmsm-guinda/5 to-unmsm-guinda/2 border-l-4 border-unmsm-guinda': open }">
        <h3 class="text-lg font-semibold transition-colors duration-200" :class="{ 'text-unmsm-guinda': open, 'text-gray-900': !open }">
            {{ $title }}
        </h3>
        <div class="flex items-center gap-3">
            <svg class="w-6 h-6 transition-all duration-300 flex-shrink-0" 
                :class="{ 'text-unmsm-guinda rotate-180': open, 'text-gray-400': !open }" 
                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
            </svg>
        </div>
    </button>
    <div x-show="open" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 max-h-0"
        x-transition:enter-end="opacity-100 max-h-[2000px]"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 max-h-[2000px]"
        x-transition:leave-end="opacity-0 max-h-0" 
        style="overflow: hidden;"
        class="px-6 py-5 bg-gray-50/50 border-l-4 border-unmsm-dorado/30">
        <div class="text-gray-700 leading-relaxed">
            {{ $slot }}
        </div>
    </div>
</div>