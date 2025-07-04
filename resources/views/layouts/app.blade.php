<!DOCTYPE html>
<html lang="en">
@include('includes.header')
<body class="d-flex flex-column min-vh-100">
    @include('includes.body-header')

    <!-- Main Content -->
    <main class="container my-4 flex-grow-1">
        <div class="row">
            <div class="col-12">
                @include('includes.breadcrumb')
            </div>
        </div>
        @yield('content')
    </main>

    @include('includes.body-footer')
    @include('includes.footer')
    @yield('js-content')
</body>
</html>
