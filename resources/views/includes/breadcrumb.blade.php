@if(isset($header['breadcrumb']))
<div class="page-header bg-white shadow-sm border-bottom mb-4">
    <div class="container-fluid">
        <div class="row align-items-center py-3">
            <!-- Left Side - Page Title and Breadcrumb -->
            <div class="col-md-6">
                <div class="d-flex flex-column">
                    <h1 class="h3 mb-1 text-dark fw-bold">
                        @if(isset($header['title']))
                            <i class="fas fa-{{ $header['icon'] ?? 'file-alt' }} me-2 text-primary"></i>
                            {{ $header['title'] }}
                        @endif
                    </h1>

                </div>
            </div>

            <!-- Right Side - Navigation and Actions -->
            <div class="col-md-6">
                <div class="d-flex justify-content-end align-items-center gap-2">
                    @if(isset($header['breadcrumb']))
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0 small">

                                @foreach($header['breadcrumb'] as $label => $url)
                                    @if($loop->last)
                                        <li class="breadcrumb-item active" aria-current="page">{{ $label }}</li>
                                    @else
                                        <li class="breadcrumb-item">
                                            <a href="{{ $url }}" class="text-decoration-none">{{ $label }}</a>
                                        </li>
                                    @endif
                                @endforeach
                            </ol>
                        </nav>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endif
