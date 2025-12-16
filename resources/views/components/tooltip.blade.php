@props(['text'])

<div class="relative group" x-data="{ show: false }" @mouseenter="show = true" @mouseleave="show = false">
    {{ $slot }}
    <div x-show="show"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute z-50 px-2 py-1 text-xs font-medium text-white bg-gray-900 rounded shadow-lg whitespace-nowrap pointer-events-none"
         style="display: none; bottom: 100%; left: 50%; transform: translateX(-50%); margin-bottom: 4px;">
        {{ $text }}
        <div class="absolute top-full left-1/2 transform -translate-x-1/2 -mt-1">
            <div class="w-2 h-2 bg-gray-900 rotate-45"></div>
        </div>
    </div>
</div>

