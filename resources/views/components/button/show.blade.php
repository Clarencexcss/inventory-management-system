@props([
    'route'
])

<x-button {{ $attributes->class(['btn btn-icon']) }} route="{{ $route }}" style="background-color: #8B0000; border-color: #8B0000;" onmouseover="this.style.backgroundColor='#A52A2A'; this.style.borderColor='#A52A2A';" onmouseout="this.style.backgroundColor='#8B0000'; this.style.borderColor='#8B0000';">
    <x-icon.eye class="text-white"/>

    {{ $slot }}
</x-button>