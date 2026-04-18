@props([
    'label',
    'display',
    'status' => 'gray',
    'subtitle' => '',
    'formula' => '',
])

@php
    $stripe = match ($status) {
        'green' => 'border-l-green-500',
        'yellow' => 'border-l-yellow-500',
        'red' => 'border-l-red-500',
        default => 'border-l-gray-300',
    };
    $dot = match ($status) {
        'green' => 'bg-green-500',
        'yellow' => 'bg-yellow-500',
        'red' => 'bg-red-500',
        default => 'bg-gray-300',
    };
    $statusLabel = match ($status) {
        'green' => 'Healthy',
        'yellow' => 'Watch',
        'red' => 'Attention',
        default => 'N/A',
    };
@endphp

<div class="flex items-start gap-3 p-3 bg-gray-50 rounded-md border-l-4 {{ $stripe }}"
     title="{{ $formula }}">
    <span class="mt-1 h-2.5 w-2.5 shrink-0 rounded-full {{ $dot }}"></span>
    <div class="flex-1 min-w-0">
        <div class="flex items-baseline justify-between gap-2">
            <span class="text-sm font-medium text-gray-700">{{ $label }}</span>
            <span class="text-lg font-bold text-gray-900 tabular-nums">{{ $display }}</span>
        </div>
        <div class="mt-0.5 flex items-center justify-between gap-2">
            <span class="text-xs text-gray-500 truncate">{{ $subtitle }}</span>
            <span class="text-[10px] uppercase tracking-wide font-semibold text-gray-400 shrink-0">{{ $statusLabel }}</span>
        </div>
    </div>
</div>
