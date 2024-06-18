@php
    $style = $shortcode->style;
    if (! in_array($style, ['style-1','style-2','style-3','style-4','style-5'])) {
        $style = 'style-1';
    }
@endphp
@include(Theme::getThemeNamespace('partials.shortcodes.about-us.' . $style))
