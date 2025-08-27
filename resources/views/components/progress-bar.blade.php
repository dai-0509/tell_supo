@props([
    'value' => 0,
    'max' => 100,
    'size' => 'md',
    'color' => 'blue',
    'showLabel' => false,
    'animated' => false,
    'className' => ''
])

@php
    $percentage = $max > 0 ? min(($value / $max) * 100, 100) : 0;
    
    $sizeClasses = [
        'sm' => 'h-2',
        'md' => 'h-4',
        'lg' => 'h-6',
        'xl' => 'h-8'
    ];
    
    $colorClasses = [
        'blue' => 'bg-gradient-to-r from-blue-500 to-blue-600',
        'green' => 'bg-gradient-to-r from-green-500 to-green-600',
        'red' => 'bg-gradient-to-r from-red-500 to-red-600',
        'yellow' => 'bg-gradient-to-r from-yellow-500 to-yellow-600',
        'purple' => 'bg-gradient-to-r from-purple-500 to-purple-600',
        'indigo' => 'bg-gradient-to-r from-indigo-500 to-indigo-600',
    ];
    
    $heightClass = $sizeClasses[$size] ?? $sizeClasses['md'];
    $colorClass = $colorClasses[$color] ?? $colorClasses['blue'];
    $animationClass = $animated ? 'transition-all duration-700 ease-in-out' : '';
@endphp

<div class="w-full {{ $className }}">
    @if($showLabel)
        <div class="flex justify-between items-center mb-2">
            <span class="text-sm font-medium text-gray-700">進捗</span>
            <span class="text-sm font-medium text-gray-700">{{ round($percentage, 1) }}%</span>
        </div>
    @endif
    
    <div class="w-full bg-gray-200 rounded-full {{ $heightClass }} overflow-hidden">
        <div 
            class="{{ $colorClass }} {{ $heightClass }} rounded-full {{ $animationClass }} shadow-sm"
            style="width: {{ $percentage }}%"
            role="progressbar"
            aria-valuenow="{{ $value }}"
            aria-valuemin="0"
            aria-valuemax="{{ $max }}"
        >
            @if($size === 'lg' || $size === 'xl')
                <div class="flex items-center justify-center h-full text-xs font-medium text-white">
                    {{ round($percentage) }}%
                </div>
            @endif
        </div>
    </div>
</div>