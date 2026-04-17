@php
    $align = $block['align'] ?? 'left';
@endphp
<p style="margin: 0 0 16px; font-size: 16px; line-height: 24px; color: #334155; text-align: {{ $align }};">{{ $block['body'] }}</p>
