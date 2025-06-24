<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'CT Waste Management')</title>

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    @stack('styles')
</head>
<body>

    <nav class="navbar navbar-expand-lg mb-4 position-relative">
        <div class="container-fluid mt-3" style="width: 82.5vw;">
            <a class="navbar-brand display-1 brand-title" href="/" >CT Waste Management Database</a>
            <style>
                .brand-title {
                    font-size: 2.5rem;
                    font-weight: 600;
                    text-decoration: underline;
                    text-decoration-color: #0d6efd;
                    text-decoration-style: dotted;
                }
            </style>
        </div>
    </nav>
    

    <main class="container">
        <nav aria-label="breadcrumbb">
            <ol class="breadcrumb">
                @yield('breadcrumbs', view('partials.breadcrumbs'))
            </ol>
        </nav>

        @yield('content')
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')

</body>
</html>
