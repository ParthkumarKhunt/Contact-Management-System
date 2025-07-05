<!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="{{ route('dashboard') }}">
            <i class="fas fa-address-book me-2"></i>
            Contact Management
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                        <i class="fas fa-tachometer-alt me-1"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('contacts.*') ? 'active' : '' }}" href="{{ route('contacts.index') }}">
                        <i class="fas fa-users me-1"></i> Contacts
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('custom-fields.*') ? 'active' : '' }}" href="{{ route('custom-fields.index') }}">
                        <i class="fas fa-cogs me-1"></i> Custom Fields
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
