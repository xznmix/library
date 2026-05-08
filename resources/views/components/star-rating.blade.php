@props(['rating' => 0, 'size' => 'md', 'showCount' => false, 'count' => 0])

@php
    $sizes = [
        'sm' => 'text-sm',
        'md' => 'text-base',
        'lg' => 'text-xl',
        'xl' => 'text-2xl'
    ];
    
    $starSize = $sizes[$size] ?? $sizes['md'];
    $fullStars = floor($rating);
    $halfStar = ($rating - $fullStars) >= 0.5;
    $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);
@endphp

<div class="flex items-center gap-0.5 {{ $starSize }}">
    @for($i = 1; $i <= $fullStars; $i++)
        <i class="fas fa-star text-yellow-400"></i>
    @endfor
    
    @if($halfStar)
        <i class="fas fa-star-half-alt text-yellow-400"></i>
    @endif
    
    @for($i = 1; $i <= $emptyStars; $i++)
        <i class="far fa-star text-yellow-400"></i>
    @endfor
    
    @if($showCount && $count > 0)
        <span class="ml-1 text-gray-500 text-xs">({{ $count }})</span>
    @endif
</div>