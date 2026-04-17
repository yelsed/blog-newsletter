@php
    $width = $block['width'] ?? null;
    $widthAttr = $width ? ' width="'.(int) $width.'"' : '';
    $href = $block['href'] ?? null;
@endphp
<div style="margin: 0 0 16px;">
@if ($href)
    <a href="{{ $href }}" style="display: inline-block;"><img src="{{ $block['url'] }}" alt="{{ $block['alt'] }}"{!! $widthAttr !!} style="display: block; max-width: 100%; height: auto; border: 0;"></a>
@else
    <img src="{{ $block['url'] }}" alt="{{ $block['alt'] }}"{!! $widthAttr !!} style="display: block; max-width: 100%; height: auto; border: 0;">
@endif
</div>
