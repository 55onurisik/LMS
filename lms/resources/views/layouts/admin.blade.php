<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard')</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body>
<div id="wrapper" class="d-flex">
    <!-- Sidebar -->
    <nav id="sidebar" class="bg-dark text-white">
        <div class="sidebar-header py-4 px-3">
            <div class="d-flex align-items-center">
                <i class="fa fa-user-shield d-md-none"></i>
                <h3 class="mb-0 d-none d-md-block">LMS</h3>
            </div>
        </div>
        <ul class="list-unstyled components">
            <li>
                <a href="{{ route('admin.dashboard') }}" class="text-white">
                    <i class="fa fa-tachometer-alt"></i>
                    <span class="d-none d-md-inline">Ana Sayfa</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.exams.create') }}" class="text-white">
                    <i class="fa fa-plus-circle"></i>
                    <span class="d-none d-md-inline">Sınav Oluştur</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.students.index') }}" class="text-white">
                    <i class="fa fa-users"></i>
                    <span class="d-none d-md-inline">Öğrencileri Yönet</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.exams.index') }}" class="text-white">
                    <i class="fa fa-file-alt"></i>
                    <span class="d-none d-md-inline">Sınavları Yönet</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.ai-analysis') }}" class="text-white">
                    <i class="fa fa-robot"></i>
                    <span class="d-none d-md-inline">Yapay Zeka Analizi</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.chat.index') ? 'active' : '' }}"
                   href="{{ route('admin.chat.index') }}">
                    <i class="fas fa-comments"></i> Sohbet
                </a>

            </li>

        </ul>
    </nav>

    <!-- Page Content -->
    <div id="content" class="w-100">
        <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
            <button class="btn btn-primary me-3" id="menu-toggle">
                <i class="fa fa-bars"></i>
            </button>
            <span class="navbar-brand mb-0 h1">@yield('page_title')</span>
        </nav>

        <main class="container-fluid px-4 py-3">
            @yield('content')
        </main>
    </div>
</div>

<!-- Settings Icon -->
<div id="settings-icon" class="position-fixed bottom-0 start-0 mb-3 ms-3">
    <a href="{{ route('admin.students.manage') }}" class="btn btn-outline-secondary">
        <i class="fa fa-cogs"></i>
    </a>
</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Custom JS for Menu Toggle -->
<script>
    var menuToggle = document.getElementById("menu-toggle");
    var sidebar = document.getElementById("sidebar");
    var content = document.getElementById("content");

    menuToggle.onclick = function() {
        sidebar.classList.toggle("collapsed");
        content.classList.toggle("expanded");
    }
</script>

@yield('scripts')
</body>
</html>
