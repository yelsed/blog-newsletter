@php
    $tag = ($block['ordered'] ?? false) ? 'ol' : 'ul';
@endphp
<{{ $tag }} style="margin: 0 0 16px; padding-left: 24px; font-size: 16px; line-height: 24px; color: #334155;">
@foreach ($block['items'] as $item)
    <li style="margin-bottom: 4px;">{{ $item }}</li>
@endforeach
</{{ $tag }}>
