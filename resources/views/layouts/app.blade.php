<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Livraria Spassu de Saber')</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #4f46e5;
            --secondary-color: #6b7280;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --info-color: #3b82f6;
            --light-color: #f9fafb;
            --dark-color: #111827;
            --accent-color: #4f46e5;
            --text-primary: #111827;
            --text-secondary: #6b7280;
            --text-muted: #9ca3af;
            --bg-primary: #ffffff;
            --bg-secondary: #f9fafb;
            --light-bg: #f3f4f6;
            --lighter-bg: #f9fafb;
            --border-color: #e5e7eb;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
        
        [data-theme="dark"] {
            --primary-color: #6366f1;
            --secondary-color: #9ca3af;
            --success-color: #34d399;
            --warning-color: #fbbf24;
            --danger-color: #f87171;
            --info-color: #60a5fa;
            --light-color: #1f2937;
            --dark-color: #f9fafb;
            --accent-color: #6366f1;
            --text-primary: #f9fafb;
            --text-secondary: #d1d5db;
            --text-muted: #9ca3af;
            --bg-primary: #111827;
            --bg-secondary: #1f2937;
            --light-bg: #374151;
            --lighter-bg: #4b5563;
            --border-color: #4b5563;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.3);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.4);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.4);
        }
        
        * {
            transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: var(--lighter-bg) !important;
            color: var(--text-primary) !important;
            line-height: 1.6;
        }
        
        .navbar {
            background-color: var(--light-bg) !important;
            border-bottom: 1px solid var(--border-color) !important;
            box-shadow: none !important;
        }
        
        .navbar-brand {
            font-weight: 600;
            font-size: 1.25rem;
            color: var(--primary-color) !important;
            letter-spacing: -0.025em;
        }
        
        .sidebar {
            min-height: calc(100vh - 56px);
            background-color: var(--light-bg) !important;
            border-right: 1px solid var(--border-color) !important;
            padding: 1.5rem 0;
        }
        
        .sidebar .nav-link {
            color: var(--text-secondary) !important;
            font-weight: 500;
            padding: 0.75rem 1.5rem;
            border-radius: 0;
            transition: all 0.2s ease;
            border-left: 3px solid transparent;
        }
        
        .sidebar .nav-link:hover {
            color: var(--primary-color);
            background-color: var(--lighter-bg);
            border-left-color: var(--accent-color);
        }
        
        .sidebar .nav-link.active {
            color: var(--primary-color);
            background-color: var(--lighter-bg);
            border-left-color: var(--accent-color);
            font-weight: 600;
        }
        
        .main-content {
            min-height: calc(100vh - 56px);
            background-color: var(--lighter-bg) !important;
            padding: 2rem;
        }
        
        .card {
            background-color: var(--light-bg) !important;
            border: 1px solid var(--border-color) !important;
            border-radius: 8px;
            box-shadow: none;
            transition: border-color 0.2s ease;
        }
        
        .card:hover {
            border-color: #d1d5db;
        }
        
        .card-header {
            background-color: var(--light-bg) !important;
            border-bottom: 1px solid var(--border-color) !important;
            padding: 1.25rem 1.5rem;
            font-weight: 600;
            color: var(--text-primary) !important;
        }
        
        .card-body {
            padding: 1.5rem;
        }
        
        .btn {
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.15s ease;
            border-width: 1px;
            font-size: 0.875rem;
            padding: 0.5rem 1rem;
            text-decoration: none;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #3730a3;
            border-color: #3730a3;
            color: white;
        }
        
        .btn-success {
            background-color: var(--success-color);
            border-color: var(--success-color);
            color: white;
        }
        
        .btn-success:hover {
            background-color: #059669;
            border-color: #059669;
            color: white;
        }
        
        .btn-warning {
            background-color: var(--warning-color);
            border-color: var(--warning-color);
            color: white;
        }
        
        .btn-warning:hover {
            background-color: #d97706;
            border-color: #d97706;
            color: white;
        }
        
        .btn-danger {
            background-color: var(--danger-color);
            border-color: var(--danger-color);
            color: white;
        }
        
        .btn-danger:hover {
            background-color: #dc2626;
            border-color: #dc2626;
            color: white;
        }
        
        .btn-outline-primary {
            color: var(--primary-color);
            border-color: #d1d5db;
            background-color: transparent;
        }
        
        .btn-outline-primary:hover {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
        }
        
        .btn-outline-warning {
            color: var(--warning-color);
            border-color: #d1d5db;
            background-color: transparent;
        }
        
        .btn-outline-warning:hover {
            background-color: var(--warning-color);
            border-color: var(--warning-color);
            color: white;
        }
        
        .btn-outline-danger {
            color: var(--danger-color);
            border-color: #d1d5db;
            background-color: transparent;
        }
        
        .btn-outline-danger:hover {
            background-color: var(--danger-color);
            border-color: var(--danger-color);
            color: white;
        }
        
        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            border-radius: 4px;
        }
        
        .btn-group .btn {
            margin-right: 0;
        }
        
        .btn-group .btn:first-child {
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
        }
        
        .btn-group .btn:last-child {
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
        }
        
        .btn-group .btn:not(:first-child):not(:last-child) {
            border-radius: 0;
        }
        
        .table {
            background-color: var(--bg-primary);
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid var(--border-color);
        }
        
        .table th {
            background-color: var(--bg-secondary);
            border-bottom: 1px solid var(--border-color);
            border-top: none;
            font-weight: 600;
            color: var(--text-primary);
            font-size: 0.875rem;
            padding: 1rem;
        }
        
        .table td {
            vertical-align: middle;
            border-color: var(--border-color);
            padding: 1rem;
            color: var(--text-primary);
            background-color: var(--bg-primary);
        }
        
        .table tbody tr:hover {
            background-color: var(--bg-secondary);
        }
        
        .table tbody tr:hover td {
            background-color: var(--bg-secondary);
        }
        
        .loading {
            display: none;
        }
        
        /* Form Controls */
        .form-control {
            border: 1px solid var(--border-color);
            border-radius: 6px;
            padding: 0.75rem;
            font-size: 0.875rem;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }
        
        .form-control:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }
        
        .form-select {
            border: 1px solid var(--border-color);
            border-radius: 6px;
            padding: 0.75rem;
            font-size: 0.875rem;
        }
        
        .form-select:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }
        
        .form-label {
            font-weight: 500;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }
        
        /* Alert Components */
        .alert {
            border: none;
            border-radius: 8px;
            padding: 1rem 1.25rem;
            font-weight: 500;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .alert-warning {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .alert-info {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        
        /* Badge Components */
        .badge {
            font-weight: 500;
            border-radius: 4px;
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }
        
        .badge.bg-secondary {
            background-color: var(--secondary-color) !important;
        }
        
        .badge.bg-info {
            background-color: var(--info-color) !important;
        }
        
        .badge.bg-success {
            background-color: var(--success-color) !important;
        }
        
        .badge.bg-warning {
            background-color: var(--warning-color) !important;
        }
        
        .badge.bg-danger {
            background-color: var(--danger-color) !important;
        }
        
        /* Pagination */
        .pagination {
            gap: 0.25rem;
        }
        
        .page-link {
            border: 1px solid var(--border-color);
            color: var(--text-secondary);
            padding: 0.5rem 0.75rem;
            border-radius: 6px;
            transition: all 0.2s ease;
        }
        
        .page-link:hover {
            background-color: var(--lighter-bg);
            border-color: var(--accent-color);
            color: var(--accent-color);
        }
        
        .page-item.active .page-link {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
            color: white;
        }
        
        /* DataTables Customization */
        .dataTables_wrapper .dataTables_length select,
        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid var(--border-color);
            border-radius: 6px;
            padding: 0.375rem 0.75rem;
            background-color: var(--bg-primary);
            color: var(--text-primary);
        }
        
        .dataTables_wrapper .dataTables_info {
            color: var(--text-primary);
        }
        
        .dataTables_wrapper .dataTables_length label,
        .dataTables_wrapper .dataTables_filter label {
            color: var(--text-primary);
        }
        
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            color: var(--text-primary) !important;
        }
        
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: var(--bg-secondary) !important;
            border-color: var(--border-color) !important;
            color: var(--text-primary) !important;
        }
        
        /* Documentation and Code Styling */
        .nav-pills .nav-link {
            color: var(--text-secondary);
            background-color: transparent;
            border: 1px solid var(--border-color);
            margin-bottom: 0.25rem;
        }
        
        .nav-pills .nav-link:hover {
            color: var(--text-primary);
            background-color: var(--bg-secondary);
        }
        
        .nav-pills .nav-link.active {
            color: white;
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        code {
            color: var(--primary-color);
            background-color: var(--bg-secondary);
            padding: 0.2rem 0.4rem;
            border-radius: 4px;
            font-size: 0.875em;
        }
        
        pre {
            background-color: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: 6px;
            padding: 1rem;
            color: var(--text-primary);
        }
        
        pre code {
            background-color: transparent;
            color: var(--text-primary);
            padding: 0;
        }
        
        .accordion-item {
            background-color: var(--bg-primary);
            border: 1px solid var(--border-color);
        }
        
        .accordion-button {
            background-color: var(--bg-secondary);
            color: var(--text-primary);
            border: none;
        }
        
        .accordion-button:not(.collapsed) {
            background-color: var(--primary-color);
            color: white;
        }
        
        .accordion-button:focus {
            box-shadow: 0 0 0 0.25rem rgba(99, 102, 241, 0.25);
        }
        
        .accordion-body {
            background-color: var(--bg-primary);
            color: var(--text-primary);
        }
        
        .badge {
            color: white;
        }
        
        h5, h6 {
            color: var(--text-primary);
        }
        
        p {
            color: var(--text-secondary);
        }
        
        /* Reports Statistics Cards */
        .border {
            border-color: var(--border-color) !important;
        }
        
        .text-muted {
            color: var(--text-secondary) !important;
        }
        
        .fs-4, .fs-6 {
            color: var(--text-primary) !important;
        }
        
        .fw-semibold {
            color: var(--text-primary) !important;
        }
        
        /* Colored Statistics Cards */
        .stats-card-books {
            background: linear-gradient(135deg, var(--primary-color), #4f46e5);
            border: none !important;
            color: white;
        }
        
        .stats-card-books .text-muted {
            color: rgba(255, 255, 255, 0.8) !important;
        }
        
        .stats-card-books .fs-4 {
            color: white !important;
        }
        
        .stats-card-authors {
            background: linear-gradient(135deg, var(--success-color), #059669);
            border: none !important;
            color: white;
        }
        
        .stats-card-authors .text-muted {
            color: rgba(255, 255, 255, 0.8) !important;
        }
        
        .stats-card-authors .fs-4 {
            color: white !important;
        }
        
        .stats-card-subjects {
            background: linear-gradient(135deg, var(--warning-color), #d97706);
            border: none !important;
            color: white;
        }
        
        .stats-card-subjects .text-muted {
            color: rgba(255, 255, 255, 0.8) !important;
        }
        
        .stats-card-subjects .fs-4 {
            color: white !important;
        }
        
        .stats-card-updated {
            background: linear-gradient(135deg, var(--info-color), #0284c7);
            border: none !important;
            color: white;
        }
        
        .stats-card-updated .text-muted {
            color: rgba(255, 255, 255, 0.8) !important;
        }
        
        .stats-card-updated .fs-6 {
            color: white !important;
        }
        
        /* Button Groups */
        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
            background-color: transparent;
        }
        
        .btn-outline-primary:hover {
            color: white;
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-outline-danger {
            color: var(--danger-color);
            border-color: var(--danger-color);
            background-color: transparent;
        }
        
        .btn-outline-danger:hover {
            color: white;
            background-color: var(--danger-color);
            border-color: var(--danger-color);
        }
        
        /* Logo SVG - modo escuro */
        .navbar-brand img {
            transition: filter 0.3s ease;
        }
        
        [data-theme="dark"] .navbar-brand img {
            filter: brightness(0) saturate(100%) invert(100%) sepia(0%) saturate(0%) hue-rotate(0deg) brightness(100%) contrast(100%);
        }
        
        /* DataTables Buttons Styling */
        .dataTables_wrapper .dt-buttons {
            margin-bottom: 0.5rem;
        }
        
        .dataTables_wrapper .dt-button {
            border: 1px solid var(--border-color) !important;
            background: var(--light-bg) !important;
            color: var(--text-color) !important;
            border-radius: 6px !important;
            padding: 0.375rem 0.75rem !important;
            font-size: 0.875rem !important;
            font-weight: 500 !important;
            transition: all 0.2s ease !important;
            margin-right: 0.25rem !important;
            margin-bottom: 0.25rem !important;
        }
        
        .dataTables_wrapper .dt-button:hover {
            background: var(--lighter-bg) !important;
            border-color: var(--accent-color) !important;
            color: var(--accent-color) !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1) !important;
        }
        
        /* DataTables Buttons - Specific Colors with Higher Specificity */
        .dataTables_wrapper .dt-buttons .dt-button.buttons-excel,
        .dt-button.buttons-excel,
        .dataTables_wrapper .dt-button.buttons-excel {
            border-color: #198754 !important;
            background-color: transparent !important;
            color: #198754 !important;
        }
        
        .dataTables_wrapper .dt-buttons .dt-button.buttons-excel:hover,
        .dt-button.buttons-excel:hover,
        .dataTables_wrapper .dt-button.buttons-excel:hover {
            background-color: #198754 !important;
            border-color: #198754 !important;
            color: white !important;
        }
        
        .dataTables_wrapper .dt-buttons .dt-button.buttons-pdf,
        .dt-button.buttons-pdf,
        .dataTables_wrapper .dt-button.buttons-pdf {
            border-color: #dc3545 !important;
            background-color: transparent !important;
            color: #dc3545 !important;
        }
        
        .dataTables_wrapper .dt-buttons .dt-button.buttons-pdf:hover,
        .dt-button.buttons-pdf:hover,
        .dataTables_wrapper .dt-button.buttons-pdf:hover {
            background-color: #dc3545 !important;
            border-color: #dc3545 !important;
            color: white !important;
        }
        
        .dataTables_wrapper .dt-buttons .dt-button.buttons-print,
        .dt-button.buttons-print,
        .dataTables_wrapper .dt-button.buttons-print {
            border-color: #0dcaf0 !important;
            background-color: transparent !important;
            color: #0dcaf0 !important;
        }
        
        .dataTables_wrapper .dt-buttons .dt-button.buttons-print:hover,
        .dt-button.buttons-print:hover,
        .dataTables_wrapper .dt-button.buttons-print:hover {
            background-color: #0dcaf0 !important;
            border-color: #0dcaf0 !important;
            color: white !important;
        }
        
        .dataTables_wrapper .dt-buttons .dt-button.buttons-copy,
        .dt-button.buttons-copy,
        .dataTables_wrapper .dt-button.buttons-copy {
            border-color: #6c757d !important;
            background-color: transparent !important;
            color: #6c757d !important;
        }
        
        .dataTables_wrapper .dt-buttons .dt-button.buttons-copy:hover,
        .dt-button.buttons-copy:hover,
        .dataTables_wrapper .dt-button.buttons-copy:hover {
            background-color: #6c757d !important;
            border-color: #6c757d !important;
            color: white !important;
        }
        
        /* Responsive improvements */
        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                top: 56px;
                left: -100%;
                width: 280px;
                height: calc(100vh - 56px);
                z-index: 1040;
                transition: left 0.3s ease;
            }
            
            .sidebar.show {
                left: 0;
            }
            
            .main-content {
                margin-left: 0;
                padding: 1rem;
            }
            
            .table-responsive {
                border-radius: 8px;
            }
        }
        
        @media (max-width: 576px) {
            .card {
                margin-bottom: 1rem;
            }
            
            .btn-toolbar {
                flex-direction: column;
                gap: 0.5rem;
            }
            
            .btn-toolbar .btn {
                width: 100%;
            }
            
            .main-content {
                padding: 0.75rem;
            }
        }
        
        /* Toast Container */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1055;
        }
        
        .toast {
            border: none;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            min-width: 300px;
        }
        
        .toast-header {
            background-color: var(--light-bg);
            border-bottom: 1px solid var(--border-color);
            font-weight: 600;
        }
        
        /* Loading States */
        .btn-loading {
            position: relative;
            pointer-events: none;
            opacity: 0.7;
        }
        
        .btn-loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 1rem;
            height: 1rem;
            border: 2px solid transparent;
            border-top: 2px solid currentColor;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: translate(-50%, -50%) rotate(0deg); }
            100% { transform: translate(-50%, -50%) rotate(360deg); }
        }
        
        .page-loading {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(2px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }
        
        .form-loading {
            position: relative;
            pointer-events: none;
            opacity: 0.6;
        }
        
        .toast-error .toast-header {
            background-color: #f8d7da;
            border-bottom: 1px solid #f5c6cb;
        }
        
        .toast-warning .toast-header {
            background-color: #fff3cd;
            border-bottom: 1px solid #ffeaa7;
        }
        
        .toast-info .toast-header {
            background-color: #d1ecf1;
            border-bottom: 1px solid #bee5eb;
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Toast Container -->
    <div class="toast-container"></div>
    
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <!-- Mobile sidebar toggle -->
            <button class="btn btn-outline-light d-md-none me-2" type="button" id="sidebarToggle">
                <i class="bi bi-list"></i>
            </button>
            
            <a class="navbar-brand d-flex align-items-center" href="{{ route('books.index') }}">
                <img src="{{ asset('images/sps.svg') }}" alt="Livraria Spassu de Saber" height="60" class="me-2">
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <button class="btn btn-outline-light btn-sm me-2" id="themeToggle" title="Alternar tema">
                            <i class="bi bi-moon-fill" id="themeIcon"></i>
                        </button>
                    </li>

                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is(patterns: 'books*') ? 'active' : '' }}" href="{{ route('books.index') }}">
                                <i class="bi bi-book"></i> Livros
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('authors*') ? 'active' : '' }}" href="{{ route('authors.index') }}">
                                <i class="bi bi-people"></i> Autores
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('subjects*') ? 'active' : '' }}" href="{{ route('subjects.index') }}">
                                <i class="bi bi-tags"></i> Assuntos
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}" href="{{ route('reports.index') }}">
                                <i class="bi bi-graph-up"></i> Relatórios
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('documentation.*') ? 'active' : '' }}" href="{{ route('documentation.index') }}">
                                <i class="bi bi-file-text"></i> Documentação API
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
                <div class="pt-3">
                    {{-- Mensagens flash serão exibidas como toasts --}}

                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <!-- Toast Container -->
    <div class="toast-container"></div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <!-- DataTables Buttons -->
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <!-- Axios -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    
    <!-- API client, Form utils, Masks -->
    <script src="{{ asset('js/api-client.js') }}"></script>
    <script src="{{ asset('js/form-utils.js') }}"></script>
    @vite(['resources/js/masks.js'])
    <script src="{{ asset('js/search-filters.js') }}"></script>
    <script src="{{ asset('js/confirmation-modals.js') }}"></script>
    <script src="{{ asset('js/feedback.js') }}"></script>
    <script src="{{ asset('js/flash-messages.js') }}"></script>
    <script src="{{ asset('js/datatables-config.js') }}"></script>
    
    <script>
        // Configure Axios defaults
        axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Initialize feedback system
        // Aguarda o Bootstrap estar disponível
        function initializeFeedbackSystem() {
            if (typeof bootstrap !== 'undefined' && typeof bootstrap.Toast !== 'undefined') {
                const toastManager = new ToastManager();
                toastManager.init(); // Garante que o container seja criado
                const loadingManager = new LoadingManager();
                const notificationManager = new NotificationManager(toastManager);
                
                // Torna os managers globalmente disponíveis
                window.toastManager = toastManager;
                window.loadingManager = loadingManager;
                window.notificationManager = notificationManager;
                
                console.log('Sistema de feedback inicializado com sucesso');
            } else {
                console.warn('Bootstrap não está disponível ainda, tentando novamente...');
                setTimeout(initializeFeedbackSystem, 100);
            }
        }
        
        initializeFeedbackSystem();
        
        // Flash messages will be handled by individual pages or via a separate script
        
        // Global helper functions for backward compatibility
        function showToast(message, type = 'success') {
            if (window.toastManager) {
                window.toastManager.show(message, type);
            } else {
                console.warn('ToastManager não está disponível ainda');
            }
        }
        
        function showLoading(element) {
            if (window.loadingManager) {
                window.loadingManager.showButtonLoading(element);
            } else {
                console.warn('LoadingManager não está disponível ainda');
            }
        }
        
        function hideLoading(element, originalText) {
            if (window.loadingManager) {
                window.loadingManager.hideButtonLoading(element);
            } else {
                console.warn('LoadingManager não está disponível ainda');
            }
        }
        
        // Theme Manager
        class ThemeManager {
            constructor() {
                this.themeToggle = document.getElementById('themeToggle');
                this.themeIcon = document.getElementById('themeIcon');
                this.currentTheme = localStorage.getItem('theme') || 'light';
                this.init();
            }
            
            init() {
                this.applyTheme(this.currentTheme);
                
                if (this.themeToggle) {
                    this.themeToggle.addEventListener('click', () => {
                        this.toggleTheme();
                    });
                }
            }
            
            applyTheme(theme) {
                document.documentElement.setAttribute('data-theme', theme);
                this.updateIcon(theme);
                localStorage.setItem('theme', theme);
                this.currentTheme = theme;
            }
            
            updateIcon(theme) {
                if (this.themeIcon) {
                    if (theme === 'dark') {
                        this.themeIcon.className = 'bi bi-sun-fill';
                    } else {
                        this.themeIcon.className = 'bi bi-moon-fill';
                    }
                }
            }
            
            toggleTheme() {
                const newTheme = this.currentTheme === 'light' ? 'dark' : 'light';
                this.applyTheme(newTheme);
            }
        }
        
        // Initialize notification system for Laravel flash messages
        document.addEventListener('DOMContentLoaded', function() {
            
            // Initialize theme manager
            const themeManager = new ThemeManager();
            
            // Mobile sidebar toggle
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.querySelector('.sidebar');
            const overlay = document.createElement('div');
            overlay.className = 'sidebar-overlay d-md-none';
            overlay.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
                z-index: 1039;
                display: none;
            `;
            document.body.appendChild(overlay);
            
            if (sidebarToggle && sidebar) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('show');
                    overlay.style.display = sidebar.classList.contains('show') ? 'block' : 'none';
                });
                
                overlay.addEventListener('click', function() {
                    sidebar.classList.remove('show');
                    overlay.style.display = 'none';
                });
            }
        });
        
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
    
    <!-- Flash Messages Data -->
    @if(session()->has(['success', 'error', 'warning', 'info']))
        <script type="application/json" id="flash-messages-data">
            {
                @if(session('success'))
                    "success": {!! json_encode(session('success')) !!}@if(session()->has(['error', 'warning', 'info'])),@endif
                @endif
                @if(session('error'))
                    "error": {!! json_encode(session('error')) !!}@if(session()->has(['warning', 'info'])),@endif
                @endif
                @if(session('warning'))
                    "warning": {!! json_encode(session('warning')) !!}@if(session()->has(['info'])),@endif
                @endif
                @if(session('info'))
                    "info": {!! json_encode(session('info')) !!}
                @endif
            }
        </script>
    @endif
    
    <!-- Confirmation Modal -->
    <div class="modal fade" id="confirmDeletionModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
          <div class="modal-header">
            <h5 class="modal-title">Confirmar exclusão</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
          </div>
          <div class="modal-body">
            <p class="mb-0" id="confirmDeletionMessage">Tem certeza que deseja excluir este registro?</p>
          </div>
          <div class="modal-footer">
            <button type="button" id="confirmDeletionCancel" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="button" id="confirmDeletionOk" class="btn btn-danger">
              <span class="spinner-border spinner-border-sm me-2 d-none" role="status" aria-hidden="true"></span>
              Excluir
            </button>
          </div>
        </div>
      </div>
    </div>

    <script type="module">
      function confirmDeletion({ title, message }) {
        return new Promise(resolve => {
          const modalEl = document.getElementById('confirmDeletionModal');
          modalEl.querySelector('.modal-title').textContent = title || 'Confirmar exclusão';
          modalEl.querySelector('#confirmDeletionMessage').textContent = message || 'Tem certeza que deseja excluir este registro?';
          const okBtn = modalEl.querySelector('#confirmDeletionOk');
          const spinner = okBtn.querySelector('.spinner-border');
          const cancelBtn = modalEl.querySelector('#confirmDeletionCancel');
          const modal = bootstrap.Modal.getOrCreateInstance(modalEl);

          function cleanup(result) {
            okBtn.classList.remove('disabled');
            spinner.classList.add('d-none');
            okBtn.onclick = null;
            cancelBtn.onclick = null;
            modalEl.removeEventListener('hidden.bs.modal', onHidden);
            resolve(result);
          }
          function onHidden() { cleanup(false); }
          modalEl.addEventListener('hidden.bs.modal', onHidden, { once: true });

          okBtn.onclick = () => {
            okBtn.classList.add('disabled');
            spinner.classList.remove('d-none');
            modal.hide();
            cleanup(true);
          };
          cancelBtn.onclick = () => { modal.hide(); };

          modal.show();
        });
      }
      window.UIFx = window.UIFx || {};
      window.UIFx.confirmDeletion = confirmDeletion;
    </script>

    @stack('scripts')
</body>
</html>
