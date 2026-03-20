<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel') - {{ config('app.name', 'POAPP') }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    
    <!-- Custom Styles -->
    <style>
        :root {
            --sidebar-width: 250px;
            --header-height: 60px;
            --primary-color: #4e73df;
            --secondary-color: #858796;
        }
        
        body {
            font-family: 'Figtree', sans-serif;
            background-color: #f8f9fc;
        }
        
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: linear-gradient(180deg, #4e73df 10%, #224abe 100%);
            z-index: 1000;
            overflow-y: auto;
        }
        
        .sidebar-brand {
            height: var(--header-height);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 1.2rem;
            text-decoration: none;
        }
        
        .sidebar-nav {
            padding: 0;
            list-style: none;
        }
        
        .sidebar-nav .nav-item {
            margin-bottom: 0;
        }
        
        .sidebar-nav .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 1rem 1.5rem;
            display: flex;
            align-items: center;
            transition: all 0.3s;
        }
        
        .sidebar-nav .nav-link:hover,
        .sidebar-nav .nav-link.active {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .sidebar-nav .nav-link i {
            width: 20px;
            margin-right: 10px;
        }
        
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
        }
        
        .topbar {
            height: var(--header-height);
            background-color: white;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.5rem;
        }
        
        .content-wrapper {
            padding: 1.5rem;
        }
        
        .card {
            border: none;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
            margin-bottom: 1.5rem;
        }
        
        .card-header {
            background-color: #f8f9fc;
            border-bottom: 1px solid #e3e6f0;
            font-weight: 600;
        }
        
        .stat-card {
            border-left: 4px solid;
        }
        
        .stat-card.primary { border-left-color: #4e73df; }
        .stat-card.success { border-left-color: #1cc88a; }
        .stat-card.info { border-left-color: #36b9cc; }
        .stat-card.warning { border-left-color: #f6c23e; }
        .stat-card.danger { border-left-color: #e74a3b; }
        
        .stat-card .stat-icon {
            font-size: 2rem;
            color: #dddfeb;
        }
        
        /* Collapsible sub-menus */
        .sidebar-nav .nav-link[data-bs-toggle="collapse"] {
            position: relative;
        }
        .sidebar-nav .nav-link[data-bs-toggle="collapse"]::after {
            content: '\f107';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            position: absolute;
            right: 1rem;
            transition: transform 0.3s;
        }
        .sidebar-nav .nav-link[data-bs-toggle="collapse"][aria-expanded="true"]::after {
            transform: rotate(180deg);
        }
        .sidebar-nav .collapse .nav-link,
        .sidebar-nav .collapsing .nav-link {
            padding-left: 3rem;
            font-size: 0.875rem;
        }

        /* BS4-compat utility classes */
        .font-weight-bold { font-weight: 700 !important; }
        .text-gray-800 { color: #5a5c69 !important; }
        .text-gray-300 { color: #dddfeb !important; }
        .border-left-primary { border-left: 4px solid #4e73df !important; }
        .border-left-success { border-left: 4px solid #1cc88a !important; }
        .border-left-warning { border-left: 4px solid #f6c23e !important; }
        .border-left-info { border-left: 4px solid #36b9cc !important; }
        .border-left-danger { border-left: 4px solid #e74a3b !important; }
        .text-xs { font-size: 0.7rem; }
        .text-right { text-align: right !important; }
        .text-left { text-align: left !important; }
        .no-gutters { --bs-gutter-x: 0; --bs-gutter-y: 0; }
        .no-gutters > .col, .no-gutters > [class*="col-"] { padding-right: 0; padding-left: 0; }
        .float-right { float: right !important; }
        .float-left { float: left !important; }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s;
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar">
        <a href="{{ route('admin.dashboard') }}" class="sidebar-brand">
            <i class="fas fa-shopping-cart me-2"></i>
            POAPP
        </a>
        
        <hr class="sidebar-divider my-0 bg-light opacity-25">
        
        <ul class="sidebar-nav">
            <li class="nav-item">
                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <hr class="sidebar-divider my-2 bg-light opacity-25">
            <li class="nav-item"><div class="sidebar-heading text-light opacity-50 px-3 py-2" style="font-size: 0.75rem;">PURCHASE ORDER MANAGEMENT</div></li>

            <li class="nav-item">
                <a href="{{ route('admin.porder.index') }}" class="nav-link {{ request()->routeIs('admin.porder.*') ? 'active' : '' }}">
                    <i class="fas fa-file-invoice"></i>
                    <span>Purchase Orders</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.template.index') }}" class="nav-link {{ request()->routeIs('admin.template.*') ? 'active' : '' }}">
                    <i class="fas fa-copy"></i>
                    <span>PO Templates</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.po-change-orders.index') }}" class="nav-link {{ request()->routeIs('admin.po-change-orders.*') ? 'active' : '' }}">
                    <i class="fas fa-exchange-alt"></i>
                    <span>PO Change Orders</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.receive.index') }}" class="nav-link {{ request()->routeIs('admin.receive.*') ? 'active' : '' }}">
                    <i class="fas fa-box-open"></i>
                    <span>Receive Orders</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.rfq.index') }}" class="nav-link {{ request()->routeIs('admin.rfq.*') ? 'active' : '' }}">
                    <i class="fas fa-file-contract"></i>
                    <span>RFQ Management</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.backorders.index') }}" class="nav-link {{ request()->routeIs('admin.backorders.*') ? 'active' : '' }}">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>Backorders</span>
                </a>
            </li>

            <hr class="sidebar-divider my-2 bg-light opacity-25">
            <li class="nav-item"><div class="sidebar-heading text-light opacity-50 px-3 py-2" style="font-size: 0.75rem;">PROJECT MANAGEMENT</div></li>

            <li class="nav-item">
                <a href="{{ route('admin.projects.index') }}" class="nav-link {{ request()->routeIs('admin.projects.*') ? 'active' : '' }}">
                    <i class="fas fa-project-diagram"></i>
                    <span>Projects</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.budget.index') }}" class="nav-link {{ request()->routeIs('admin.budget.*', 'admin.budgets.*') ? 'active' : '' }}">
                    <i class="fas fa-calculator"></i>
                    <span>Budget Management</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.takeoffs.index') }}" class="nav-link {{ request()->routeIs('admin.takeoffs.*') ? 'active' : '' }}">
                    <i class="fas fa-hard-hat"></i>
                    <span>Takeoffs & Estimates</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.schedule.index') }}" class="nav-link {{ request()->routeIs('admin.schedule.*') ? 'active' : '' }}">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Scheduling</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.costcodes.index') }}" class="nav-link {{ request()->routeIs('admin.costcodes.*') ? 'active' : '' }}">
                    <i class="fas fa-code"></i>
                    <span>Cost Codes</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.approvals.dashboard') }}" class="nav-link {{ request()->routeIs('admin.approvals.*') ? 'active' : '' }}">
                    <i class="fas fa-check-circle"></i>
                    <span>Approvals</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.approval-workflows.index') }}" class="nav-link {{ request()->routeIs('admin.approval-workflows.*') ? 'active' : '' }}">
                    <i class="fas fa-sitemap"></i>
                    <span>Approval Workflows</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.project-roles.index') }}" class="nav-link {{ request()->routeIs('admin.project-roles.*') ? 'active' : '' }}">
                    <i class="fas fa-user-tag"></i>
                    <span>Project Roles</span>
                </a>
            </li>

            <hr class="sidebar-divider my-2 bg-light opacity-25">
            <li class="nav-item"><div class="sidebar-heading text-light opacity-50 px-3 py-2" style="font-size: 0.75rem;">SUBCONTRACTOR MANAGEMENT</div></li>

            <li class="nav-item">
                <a href="{{ route('admin.contracts.index') }}" class="nav-link {{ request()->routeIs('admin.contracts.*') ? 'active' : '' }}">
                    <i class="fas fa-file-signature"></i>
                    <span>Contracts</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.contract-change-orders.index') }}" class="nav-link {{ request()->routeIs('admin.contract-change-orders.*') ? 'active' : '' }}">
                    <i class="fas fa-exchange-alt"></i>
                    <span>Contract Change Orders</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.compliance.dashboard') }}" class="nav-link {{ request()->routeIs('admin.compliance.*', 'admin.supplier-compliance.*') ? 'active' : '' }}">
                    <i class="fas fa-shield-alt"></i>
                    <span>Compliance Tracker</span>
                </a>
            </li>

            <hr class="sidebar-divider my-2 bg-light opacity-25">
            <li class="nav-item"><div class="sidebar-heading text-light opacity-50 px-3 py-2" style="font-size: 0.75rem;">INVENTORY & CATALOG</div></li>

            <li class="nav-item">
                <a href="{{ route('admin.item.index') }}" class="nav-link {{ request()->routeIs('admin.item.*') ? 'active' : '' }}">
                    <i class="fas fa-boxes"></i>
                    <span>Item Management</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.suppliers.index') }}" class="nav-link {{ request()->routeIs('admin.suppliers.*') ? 'active' : '' }}">
                    <i class="fas fa-truck"></i>
                    <span>Suppliers</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.equipment.index') }}" class="nav-link {{ request()->routeIs('admin.equipment.*') ? 'active' : '' }}">
                    <i class="fas fa-tools"></i>
                    <span>Equipment</span>
                </a>
            </li>

            <hr class="sidebar-divider my-2 bg-light opacity-25">
            <li class="nav-item"><div class="sidebar-heading text-light opacity-50 px-3 py-2" style="font-size: 0.75rem;">INTEGRATIONS</div></li>

            <li class="nav-item">
                <a href="{{ route('admin.procore.index') }}" class="nav-link {{ request()->routeIs('admin.procore.*') ? 'active' : '' }}">
                    <i class="fas fa-plug"></i>
                    <span>Procore</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.integrations.index') }}" class="nav-link {{ request()->routeIs('admin.integrations.*') ? 'active' : '' }}">
                    <i class="fas fa-link"></i>
                    <span>Accounting</span>
                </a>
            </li>

            <hr class="sidebar-divider my-2 bg-light opacity-25">
            <li class="nav-item"><div class="sidebar-heading text-light opacity-50 px-3 py-2" style="font-size: 0.75rem;">REPORTS & SETTINGS</div></li>

            <li class="nav-item">
                <a href="{{ route('admin.reports.budget-vs-actual') }}" class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                    <i class="fas fa-chart-line"></i>
                    <span>Reports</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.checklists.index') }}" class="nav-link {{ request()->routeIs('admin.checklists.*') ? 'active' : '' }}">
                    <i class="fas fa-clipboard-check"></i>
                    <span>Checklists</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.uom.*', 'admin.taxgroups.*', 'admin.packages.*', 'admin.permissions.*', 'admin.company.*', 'admin.security.*', 'admin.ai-settings.*') ? 'active' : '' }}" data-bs-toggle="collapse" href="#settingsCollapse" role="button" aria-expanded="{{ request()->routeIs('admin.uom.*', 'admin.taxgroups.*', 'admin.packages.*', 'admin.permissions.*', 'admin.company.*', 'admin.security.*', 'admin.ai-settings.*') ? 'true' : 'false' }}">
                    <i class="fas fa-cog"></i>
                    <span>Settings</span>
                </a>
                <div class="collapse {{ request()->routeIs('admin.uom.*', 'admin.taxgroups.*', 'admin.packages.*', 'admin.permissions.*', 'admin.company.*', 'admin.security.*', 'admin.ai-settings.*') ? 'show' : '' }}" id="settingsCollapse">
                    <li class="nav-item">
                        <a href="{{ route('admin.company.index') }}" class="nav-link {{ request()->routeIs('admin.company.*') ? 'active' : '' }}">
                            <i class="fas fa-building"></i>
                            <span>Company Profile</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.uom.index') }}" class="nav-link {{ request()->routeIs('admin.uom.*') ? 'active' : '' }}">
                            <i class="fas fa-ruler"></i>
                            <span>Units of Measure</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.taxgroups.index') }}" class="nav-link {{ request()->routeIs('admin.taxgroups.*') ? 'active' : '' }}">
                            <i class="fas fa-percent"></i>
                            <span>Tax Groups</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.packages.index') }}" class="nav-link {{ request()->routeIs('admin.packages.*') ? 'active' : '' }}">
                            <i class="fas fa-box"></i>
                            <span>Item Packages</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.permissions.index') }}" class="nav-link {{ request()->routeIs('admin.permissions.*') ? 'active' : '' }}">
                            <i class="fas fa-shield-alt"></i>
                            <span>Permissions</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.security.2fa') }}" class="nav-link {{ request()->routeIs('admin.security.2fa') ? 'active' : '' }}">
                            <i class="fas fa-user-shield"></i>
                            <span>Two-Factor Auth</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.security.audit-logs') }}" class="nav-link {{ request()->routeIs('admin.security.audit-logs') ? 'active' : '' }}">
                            <i class="fas fa-history"></i>
                            <span>Audit Logs</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.ai-settings.index') }}" class="nav-link {{ request()->routeIs('admin.ai-settings.*') ? 'active' : '' }}">
                            <i class="fas fa-robot"></i>
                            <span>AI Settings</span>
                        </a>
                    </li>
                </div>
            </li>

            @if(session('u_type') == 1)
                <li class="nav-item">
                    <a href="{{ route('admin.companies.index') }}" class="nav-link {{ request()->routeIs('admin.companies.*') ? 'active' : '' }}">
                        <i class="fas fa-building"></i>
                        <span>Companies</span>
                    </a>
                </li>
            @endif

            <hr class="sidebar-divider my-2 bg-light opacity-25">

            <li class="nav-item">
                <a href="{{ route('admin.support.index') }}" class="nav-link {{ request()->routeIs('admin.support.*') ? 'active' : '' }}">
                    <i class="fas fa-life-ring"></i>
                    <span>Support</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.logout') }}" class="nav-link">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </nav>
    
    <!-- Main Content -->
    <div class="main-content">
        <!-- Topbar -->
        <header class="topbar">
            <button class="btn btn-link d-md-none" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            
            <div class="d-flex align-items-center">
                <span class="me-3">Welcome, {{ Auth::user()->name ?? 'Admin' }}</span>
                
                @if(session('u_type') == 1)
                    <!-- Company Switcher (Super Admin Only) -->
                    <div class="dropdown me-3">
                        <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-building me-1"></i>
                            {{ $currentCompany ? $currentCompany->name : 'No Company' }}
                        </button>
                        <ul class="dropdown-menu">
                            <li><h6 class="dropdown-header">Switch Company</h6></li>
                            @foreach($switchableCompanies as $company)
                                <li>
                                    @if($company->id == session('company_id'))
                                        <span class="dropdown-item active">
                                            <i class="fas fa-check me-2"></i>{{ $company->name }}
                                        </span>
                                    @else
                                        <form action="{{ route('admin.companies.switch', $company) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="dropdown-item">
                                                <i class="fas fa-exchange-alt me-2"></i>{{ $company->name }}
                                            </button>
                                        </form>
                                    @endif
                                </li>
                            @endforeach
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="{{ route('admin.companies.index') }}">
                                    <i class="fas fa-cog me-2"></i>Manage Companies
                                </a>
                            </li>
                        </ul>
                    </div>
                @endif
                
                <div class="dropdown">
                    <button class="btn btn-link dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle fa-lg"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('admin.profile.index') }}"><i class="fas fa-user me-2"></i>Profile</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.security.2fa') }}"><i class="fas fa-user-shield me-2"></i>Two-Factor Auth</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.security.audit-logs') }}"><i class="fas fa-history me-2"></i>Audit Logs</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.company.index') }}"><i class="fas fa-cog me-2"></i>Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="{{ route('admin.logout') }}"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                    </ul>
                </div>
            </div>
        </header>
        
        <!-- Content -->
        <div class="content-wrapper">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @yield('content')
        </div>
    </div>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        // CSRF Token for AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        // Sidebar Toggle
        $('#sidebarToggle').on('click', function() {
            $('.sidebar').toggleClass('show');
        });
        
        // Initialize DataTables (skip tables already initialized by page scripts)
        $(document).ready(function() {
            $('.datatable').each(function() {
                if (!$.fn.dataTable.isDataTable(this)) {
                    $(this).DataTable({
                        responsive: true,
                        pageLength: 25
                    });
                }
            });
        });
    </script>
    
    @stack('scripts')
</body>
</html>
