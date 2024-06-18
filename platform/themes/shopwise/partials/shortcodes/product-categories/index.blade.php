@php
    $style = $shortcode->style;
    if (! in_array($style, ['style-1', 'style-2', 'shop'])) {
        $style = 'style-1';
    }
@endphp

@if (count($categories) > 0)
    @include(Theme::getThemeNamespace('partials.shortcodes.product-categories.' . $style))
@endif