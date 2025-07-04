@extends('layouts.app')

{{-- Meta tag section --}}
@section('description', Config::get('metatags.dashboard.description'))
@section('keywords', Config::get('metatags.dashboard.keywords'))
@section('pagetitle', Config::get('metatags.dashboard.pagetitle'))
{{-- End Meta tag section --}}

{{-- CSS section --}}
@section('css-content')
<style>
    .dashboard-card {
        transition: transform 0.2s ease-in-out;
    }
    .dashboard-card:hover {
        transform: translateY(-5px);
    }
    .card-icon {
        font-size: 2.5rem;
        opacity: 0.8;
    }
    .card-value {
        font-size: 2rem;
        font-weight: bold;
    }
</style>
@endsection

@section('content')
<div class="row g-4 mb-4">

    <!-- Additional Dashboard Content -->
    <div class="row mt-5 mb-5">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-tachometer-alt me-2 text-primary"></i>
                        Dashboard Overview
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-0">
                        Welcome to your contact management dashboard. Here you can view key metrics and manage your contacts efficiently.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Total Contacts Card -->
        <div class="col-xl-6 col-md-6">
            <a href="javascript:;" class="text-decoration-none">
            <div class="card dashboard-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-muted mb-2">Total Contacts</h6>
                            <div class="card-value text-primary">452</div>
                        </div>
                        <div class="card-icon text-primary">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
            </div>
            </a>
        </div>

        <!-- Custom Fields Card -->
        <div class="col-xl-6 col-md-6">
            <a href="{{ route('custom-fields.index') }}" class="text-decoration-none">
                <div class="card dashboard-card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title text-muted mb-2">Custom Fields</h6>
                                <div class="card-value text-warning">{{ $customFieldsCount }}</div>
                            </div>
                            <div class="card-icon text-warning">
                                <i class="fas fa-tags"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

</div>


@endsection

{{-- JS section --}}
@section('js-content')
@endsection
