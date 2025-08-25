@props([
    'items',
    'field',
    'variant' => 'secondary',
    'emptyMessage' => 'Nenhum item'
])

@if($items && $items->count() > 0)
    @foreach($items as $item)
        <span class="badge bg-{{ $variant }} me-1">
            {{ data_get($item, $field) }}
        </span>
    @endforeach
@else
    <span class="text-muted">{{ $emptyMessage }}</span>
@endif