@props(['icon' => ''])
{{--
    Renderiza un icono Font Awesome guardado en BD como cadena de clases
    (p. ej. "fas fa-folder" o "fa-solid fa-newspaper") usando el SVG inline del
    paquete owenvoke/blade-fontawesome. Cualquier clase extra pasada al
    componente se fusiona sobre el SVG.
--}}
@php
    $__tokens = preg_split('/\s+/', trim((string) $icon), -1, PREG_SPLIT_NO_EMPTY) ?: [];
    $__style = 'fas';
    $__name = null;
    $__extra = [];
    foreach ($__tokens as $__t) {
        if (in_array($__t, ['fas', 'fa-solid'], true)) { $__style = 'fas'; continue; }
        if (in_array($__t, ['far', 'fa-regular'], true)) { $__style = 'far'; continue; }
        if (in_array($__t, ['fab', 'fa-brands'], true)) { $__style = 'fab'; continue; }
        if ($__t === 'fa') { $__style = 'fas'; continue; }
        if (str_starts_with($__t, 'fa-')) {
            $__rest = substr($__t, 3);
            if (in_array($__rest, ['fw', 'lg', 'sm', 'xs', '2x', '3x'], true)) { continue; }
            if (in_array($__rest, ['spin', 'pulse'], true)) { $__extra[] = 'animate-' . $__rest; continue; }
            if ($__name === null && $__rest !== '') { $__name = $__rest; continue; }
        }
        if ($__t !== '') { $__extra[] = $__t; }
    }
@endphp
@if ($__name)
    <x-dynamic-component
        :component="$__style . '-' . $__name"
        {{ $attributes->merge(['class' => implode(' ', $__extra)]) }} />
@endif
