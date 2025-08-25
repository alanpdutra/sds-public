@props([
    'title',
    'icon' => 'bi-plus-circle',
    'backRoute',
    'backLabel' => 'Voltar'
])

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="{{ $icon }}"></i> {{ $title }}
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route($backRoute) }}" class="btn btn-outline-secondary{{ isset($slot) && !empty(trim($slot)) ? ' me-2' : '' }}">
            <i class="bi bi-arrow-left"></i> {{ $backLabel }}
        </a>
        {{ $slot ?? '' }}
    </div>
</div>