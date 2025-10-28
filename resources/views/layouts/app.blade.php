<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'CT Waste Management')</title>

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    @stack('styles')
    <style>
        @media (min-width: 1200px) {
            .container-xxl {
                max-width: 1400px;
            }
        }

        @media (min-width: 1400px) {
            .container-xxl {
                max-width: 1600px;
            }
        }
    </style>
</head>

<body>

    <!-- Development Notice Banner -->
    <div class="alert alert-warning alert-dismissible fade show m-0 rounded-0" role="alert">
        <div class="container-fluid">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <span>
                    <strong>Notice:</strong>
This dashboard is part of an ongoing development effort and currently displays preliminary data intended for demonstration purposes. The information presented is subject to refinement and validation and may not reflect final or complete datasets. It is provided for demonstration use only, and the hosting organization is not responsible for any decisions or actions taken based on this preliminary data.
                </span>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <nav class="navbar navbar-expand-lg mb-4 position-relative">
        <div class="container d-flex justify-content-start align-items-center text-start mt-3">
            <a class="navbar-brand display-1 fw-bolder brand-title" href="/">CT Waste Management Dashboard</a>
            <style>
                .brand-title {
                    font-size: 2.5rem;
                    font-weight: 600;
                    text-decoration: underline;
                    text-decoration-color: #3490dc;
                    text-decoration-style: underline;

                }
            </style>
        </div>
    </nav>


    <main class="container-xxl px-4">
        {{-- <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                @yield('breadcrumbs', view('partials.breadcrumbs'))
            </ol>
        </nav> --}}

        <!-- Error and Success Messages -->
        @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <strong>Error:</strong>
            @if ($errors->count() == 1)
            {{ $errors->first() }}
            @else
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
            @endif
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            <strong>Success:</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if (session('warning'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <strong>Warning:</strong> {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if (session('info'))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <i class="bi bi-info-circle-fill me-2"></i>
            <strong>Info:</strong> {{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
        <section id="response-content">
            @yield('content')
        </section>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @stack('scripts')
</body>

</html>