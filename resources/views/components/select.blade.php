@props([
    'name',
    'id' => null,
    'wrapperClass' => '',
])

<div class="{{ $wrapperClass }}">
    <select
        name="{{ $name }}"
        id="{{ $id ?? $name }}"
        {{ $attributes->merge([
            'class' => 'w-full'
        ]) }}
    >
        {{ $slot }}
    </select>
</div>
