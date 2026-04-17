@php
    $align = $block['align'] ?? 'left';
@endphp
<div style="margin: 0 0 16px; text-align: {{ $align }};">
    <a href="{{ $block['href'] }}" style="display: inline-block; text-decoration: none; padding: 16px 24px; font-size: 16px; line-height: 1; border-radius: 4px; background-color: #0f172a; color: #fffffe;">{{ $block['label'] }}</a>
</div>
