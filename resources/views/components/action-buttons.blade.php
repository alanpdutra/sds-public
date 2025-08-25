@props([
    'item',
    'routePrefix',
    'itemName' => '',
    'itemInfo' => '',
    'showView' => true,
    'showEdit' => true,
    'showDelete' => true
])

<div class="btn-group" role="group">
    @if($showView)
        <a href="{{ route($routePrefix . '.show', $item) }}" 
           class="btn btn-sm btn-outline-primary" title="Visualizar">
            <i class="bi bi-eye"></i>
        </a>
    @endif
    
    @if($showEdit)
        <a href="{{ route($routePrefix . '.edit', $item) }}" 
           class="btn btn-sm btn-outline-warning" title="Editar">
            <i class="bi bi-pencil"></i> Editar
        </a>
    @endif
    
    @if($showDelete)
        <form action="{{ route($routePrefix . '.destroy', $item) }}" method="POST" class="d-inline confirm-delete"
              data-confirm-title="Excluir {{ ucfirst(str_replace(['.', '_'], ' ', $routePrefix)) }}"
              data-confirm-message="Tem certeza que deseja excluir este item?"
              data-item-name="{{ $itemName }}"
              data-item-info="{{ $itemInfo }}">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm btn-outline-danger" title="Excluir">
                <i class="bi bi-trash"></i>
            </button>
        </form>
    @endif
</div>