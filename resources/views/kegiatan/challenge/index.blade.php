@extends('app')

@section('content')
    <div class="container mt-4">
        <h3 class="mb-4">Bebras Challenge</h3>
        <div class="col-md-12">
            <ul class="nav nav-pills flex-column flex-md-row mb-3">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('challenge.index') ? 'active' : '' }}"
                        href="{{ route('challenge.index') }}">
                        <i class="bx bx-table me-1"></i> Table
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('form-challenge.index') ? 'active' : '' }}"
                        href="{{ route('form-challenge.index') }}">
                        <i class="bx bx-edit me-1"></i> Form
                    </a>
                </li>
            </ul>
        </div>

      
    </div>
    @push('js')
        <script>
          
        </script>
    @endpush
@endsection
