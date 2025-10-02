<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb bg-light p-2 rounded">
        @foreach ($items as $item)
            @php
                $url = $item['url'] ?? ($item['route'] ?? null ? route($item['route']) : null);
            @endphp

            @if ($loop->last)
                <li class="breadcrumb-item active fw-bold text-dark" aria-current="page">
                    {{ $item['label'] }}
                </li>
            @else
                <li class="breadcrumb-item">
                    <a href="{{ $url }}" class="text-decoration-none text-primary">
                        {{ $item['label'] }}
                    </a>
                </li>
            @endif
        @endforeach
    </ol>
</nav>
