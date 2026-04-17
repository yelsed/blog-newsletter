@php
    $width = $block['width'] ?? null;
    $widthAttr = $width ? ' width="'.(int) $width.'"' : '';
@endphp
<div style="margin: 0 0 16px;">
    <img src="{{ $block['url'] }}" alt="{{ $block['alt'] }}"{!! $widthAttr !!} style="display: block; max-width: 100%; height: auto; border: 0;">
</div>
