@props(['inputSize' => 'medium', 'name' => null, 'label' => null])

@php
    $sizeClass = match($inputSize){
        'small' => 'h-7', 'large' => 'h-12', default => 'h-8'};
    $hasError = $name && $errors->has($name);
    $classes = implode(' ', ['w-full rounded-md border border-gray-300 px-2 outline-none focus:border-primary', $sizeClass]);
@endphp

<div class = 'my-3'>
    @if (@label)
        <label @if ($name) for="{{ $name }}" @endif class = "mb-1 block text-sm font-medium">
            {{ $label }}
        </label>
    @endif
    <input {{ $attributes->merge(['type' => 'text', 'class' => $classes]) }}
        @if ($name) name="{{ $name }}" id="{{ $name }}" @endif
        @if ($name) value="{{ old($name, $attributes->get('value')) }}" @endif
        @if ($hasError) aria-invalid="true" @endif>
    
    @if ($hasError)
        <div class="w-full text-sm text-red-500">
            <p>{{ $errors->first($name) }}</p>
        </div>
    @endif
</div>
