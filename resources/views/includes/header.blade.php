<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('pagetitle')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="@yield('description')" name="description" />
    <meta content="@yield('keywords')" name="keywords" />
    <meta content="{{ Config::get('metatags.author') }}" name="author" />

    <!-- Bootstrap 5 CSS -->
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/toastr/toastr.min.css') }}">
    <script>
        var baseUrl = "{{ url('/') }}";
        var checkUniqueLabelUrl = "{{ route('custom-fields.check-unique-label') }}";
        var checkUniqueLabelForEditUrl = "{{ route('custom-fields.check-unique-label-edit') }}";
        var checkUniqueEmailUrl = "{{ route('contacts.check-unique-email') }}";
        var checkUniqueEmailForEditUrl = "{{ route('contacts.check-unique-email-edit') }}";
    </script>
    @yield('css-content')
</head>
