@props([
    'title',
    'icon',
    'createRoute' => null,
    'createLabel' => 'Novo',
    'items' => null,
    'itemLabel' => 'item'
])

<div class="card-header d-flex justify-content-between align-items-center">
    <div>
        <h3 class="card-title mb-0">
            <i class="bi bi-{{ $icon }}"></i> {{ $title }}
        </h3>
        @if($items !== null)
            <div class="results-count mt-1">
                @if(method_exists($items, 'total'))
                    {{ $items->total() }} {{ $itemLabel }}(s) encontrado(s)
                @elseif(method_exists($items, 'count'))
                    {{ $items->count() }} {{ $itemLabel }}(s) encontrado(s)
                @else
                    Nenhum {{ $itemLabel }} encontrado
                @endif
            </div>
        @endif
    </div>
    @if($createRoute)
        <a href="{{ $createRoute }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> {{ $createLabel }}
        </a>
    @endif
</div>