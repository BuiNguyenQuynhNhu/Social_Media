@props(['size' => 'medium', 'bg' => 'bg-primary', 'color' => 'text-white', 'fontWeight' => 'font-semibold',
    'fontSize' => 'text-sm', 'isDisabled' => false, 'isLoading' => false, 'block' => false,])

@php
    $sizeClass = match($size){
    'small' => 'h-7', 'large' => 'h-11', default => 'h-8',};

    $class = implode(' ', array_filter([
    'items-center justify-center rounded-md px-4 shadow-md',
    $block ? 'w-full' : 'inline-block', $sizeClass, $bg, $color, $fontWeight, $fontSize]));
@endphp
    <button {{ $attibutes->merge(['class' => $class , 'type' => 'button',]) }} @disabled($isDisable)>
    @if ($isLoading) ...
    @else {{ $slot }}
    @endif
    </button>