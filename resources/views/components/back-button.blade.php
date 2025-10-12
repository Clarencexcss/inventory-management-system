@props(['url' => null, 'text' => 'Back'])

@php
    $backUrl = $url ?? url()->previous();
@endphp

<a href="{{ $backUrl }}" class="btn btn-outline-secondary">
    <i class="fas fa-arrow-left me-2"></i>
    {{ $text }}
</a>
