@php
    $align = $block['align'] ?? 'left';
@endphp
<p style="margin: 0 0 16px; text-align: {{ $align }};">
    <a href="{{ $block['href'] }}" style="color: #1d4ed8; text-decoration: underline; font-size: 16px; line-height: 24px;">{{ $block['label'] }}</a>
</p>
