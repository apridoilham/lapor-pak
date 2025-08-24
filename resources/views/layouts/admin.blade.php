<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>@yield('title', 'Admin Dashboard')</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" type="text/css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/startbootstrap-sb-admin-2/4.1.4/css/sb-admin-2.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-blue: #0d6efd;
            --sidebar-bg: #1a202c;
            --sidebar-text: #a0aec0;
            --sidebar-active-text: #ffffff;
            --content-bg: #f7f9fc;
            --card-bg: #ffffff;
            --card-border: #e2e8f0;
            --text-heading: #1a202c;
            --text-body: #4a5568;
            --shadow-sm: 0 .125rem .25rem rgba(0,0,0,.075);
            --shadow-md: 0 .5rem 1rem rgba(0,0,0,.15);
        }
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--content-bg);
            color: var(--text-body);
        }
        .sidebar {
            background-image: none !important;
            background-color: var(--sidebar-bg);
        }
        .sidebar .nav-item .nav-link {
            color: var(--sidebar-text);
            font-weight: 500;
        }
        .sidebar .nav-item.active .nav-link {
            color: var(--sidebar-active-text);
            background-color: rgba(255,255,255,0.05);
        }
        .sidebar .nav-item.active .nav-link i {
            color: var(--sidebar-active-text);
        }
        .sidebar .sidebar-brand .sidebar-brand-text {
            font-weight: 700;
        }
        .sidebar-dark .sidebar-heading {
            color: rgba(255,255,255,0.3);
            font-weight: 600;
        }
        .card {
            border: 1px solid var(--card-border);
            box-shadow: var(--shadow-sm) !important;
        }
        .topbar {
           box-shadow: var(--shadow-sm) !important;
           border-bottom: 1px solid var(--card-border);
        }
    </style>
    @stack('styles')
</head>
<body id="page-top">
    <div id="wrapper">
        @include('includes.sidebar')
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                @include('includes.topbar')
                <div class="container-fluid">
                    @include('sweetalert::alert')
                    @yield('content')
                </div>
            </div>
            @include('includes.footer')
        </div>
    </div>
    <a class="scroll-to-top rounded" href="#page-top"><i class="fas fa-angle-up"></i></a>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/startbootstrap-sb-admin-2/4.1.4/js/sb-admin-2.min.js"></script>
    @stack('scripts')
</body>
</html>