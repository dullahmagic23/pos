<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link rel="shortcut icon" href="{{asset("logo.png")}}" type="image/x-icon">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="{{asset("css/styles.css")}}" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body class="">
    <div class="container-fluid">
        <div id="app">
            <nav class="sb-topnav navbar navbar-expand navbar-dark bg-primary">
                <a class="navbar-brand ps-3" href="{{route('home')}}">{{config('app.name')}}</a>
            @if(auth()->user())
                    <!-- Navbar Brand-->
                <!-- Sidebar Toggle-->
                <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
                <!-- Navbar Search-->
                <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
                    <div class="input-group">
                        <input class="form-control" type="text" placeholder="Search for..." aria-label="Search for..." aria-describedby="btnNavbarSearch" />
                        <button class="btn btn-primary" id="btnNavbarSearch" type="button"><i class="fas fa-search"></i></button>
                    </div>
                </form>
                <!-- Navbar-->
                <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="#">Settings</a></li>
                            <li><a class="dropdown-item" href="#">Activity Log</a></li>
                            <li><hr class="dropdown-divider" /></li>
                            <li>
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                   onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                    {{ __('Logout') }}
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
                @endif
            </nav>
            <div id="layoutSidenav">
                @if(auth()->user())
                <div id="layoutSidenav_nav">
                    <nav class="sb-sidenav accordion sb-sidenav-light" id="sidenavAccordion">
                        <div class="sb-sidenav-menu">
                            <div class="nav">
                                <a class="nav-link" href="{{route('home')}}">
                                    <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                                    Dashboard
                                </a>
                                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseLayouts" aria-expanded="false" aria-controls="collapseLayouts">
                                    <div class="sb-nav-link-icon">
                                        <i class="fa-solid fa-cart-shopping"></i>
                                    </div>
                                    SALES
                                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                                </a>
                                <div class="collapse" id="collapseLayouts" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                                    <nav class="sb-sidenav-menu-nested nav">
                                        <a class="nav-link" href="{{route('pos.index')}}">
                                            <div class="sb-nav-link-icon">
                                                <i class="fa-solid fa-desktop"></i>
                                            </div>
                                            P.O.S
                                        </a>
                                        <a class="nav-link" href="{{route('sales.index')}}">
                                            <div class="sb-nav-link-icon">
                                                <i class="fa-solid fa-cart-shopping"></i>
                                            </div>
                                            SALES</a>
                                        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#pagesCollapseError4" aria-expanded="false" aria-controls="pagesCollapseError">
                                            <div class="sb-nav-link-icon">
                                                <i class="fas fa-check-circle"></i></div>
                                            PAYMENTS
                                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                                        </a>

                                        <div class="collapse" id="pagesCollapseError4" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordionPages">
                                            <nav class="sb-sidenav-menu-nested nav">
                                                <a class="nav-link" href="{{route('payments.create')}}">
                                                    <div class="sb-nav-link-icon">
                                                        <i class="fa-solid fa-cash-register"></i>
                                                    </div>
                                                    New Payment
                                                </a>
                                                <a class="nav-link" href="{{route('payments.index')}}">
                                                    <div class="sb-nav-link-icon">
                                                        <i class="fas fa-check-double"></i></div>
                                                    All Payments
                                                </a>
                                            </nav>
                                        </div>
                                        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#pagesCollapseError5" aria-expanded="false" aria-controls="pagesCollapseError">
                                            <div class="sb-nav-link-icon">
                                                <i class="fas fa-check-circle"></i></div>
                                            DISCOUNTS
                                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                                        </a>

                                        <div class="collapse" id="pagesCollapseError5" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordionPages">
                                            <nav class="sb-sidenav-menu-nested nav">
                                                <a class="nav-link" href="{{route('discounts.create')}}">
                                                    <div class="sb-nav-link-icon">
                                                        <i class="fa fa-check-circle"></i>
                                                    </div>
                                                    Create Discount
                                                </a>
                                                <a class="nav-link" href="{{route('discounts.index')}}">
                                                    <div class="sb-nav-link-icon">
                                                        <i class="fa-solid fa-cash-register"></i>
                                                    </div>
                                                    Manage Discounts
                                                </a>
                                            </nav>
                                        </div>
                                        <a class="nav-link" href="{{route('sales.report')}}">
                                            <div class="sb-nav-link-icon">
                                                <i class="fa-solid fa-cart-shopping"></i>
                                            </div>
                                            SALES REPORT</a>
                                    </nav>
                                </div>
                                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseLayouts1" aria-expanded="false" aria-controls="collapseLayouts">
                                    <div class="sb-nav-link-icon">
                                        <i class="fa-solid fa-circle-down"></i>
                                    </div>
                                    STOCK
                                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                                </a>
                                <div class="collapse" id="collapseLayouts1" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                                    <nav class="sb-sidenav-menu-nested nav">
                                        <a class="nav-link" href="{{route('stocks.create')}}">
                                            <div class="sb-nav-link-icon">
                                                <i class="fa-solid fa-circle-right"></i>
                                            </div>
                                            NEW STOCK</a>
                                        <a class="nav-link" href="{{route('stocks.index')}}">
                                            <div class="sb-nav-link-icon">
                                                <i class="fa-regular fa-folder-open"></i>
                                            </div>
                                            MANAGE STOCK</a>
                                            <a class="nav-link" href="{{route('stocks.convert')}}">
                                                <div class="sb-nav-link-icon">
                                                    <i class="fa fa-window-restore" aria-hidden="true"></i>
                                                </div>
                                                UNITS CONVERSION</a>
                                    </nav>
                                </div>
                                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseLayouts2" aria-expanded="false" aria-controls="collapseLayouts">
                                    <div class="sb-nav-link-icon"><i class="fas fa-home"></i></div>
                                    INVENTORY
                                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                                </a>
                                <div class="collapse" id="collapseLayouts2" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                                    <nav class="sb-sidenav-menu-nested nav">
                                        <a class="nav-link" href="{{route('inventory.create')}}">
                                            <div class="sb-nav-link-icon">
                                                <i class="fas fa-sign-in mr-2"></i>
                                            </div>
                                            NEW ORDER</a>
                                        <a class="nav-link" href="{{route('inventory.index')}}">
                                            <div class="sb-nav-link-icon">
                                                <i class="fa-solid fa-list"></i>
                                            </div>
                                            SUPPLIER ORDERS</a>
                                        <a class="nav-link" href="{{route('inventory.all')}}">
                                            <div class="sb-nav-link-icon">
                                                <i class="fa-sharp-duotone fa-solid fa-list"></i>
                                            </div>
                                            INVENTORY</a>
                                    </nav>
                                </div>
                                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapsePages1" aria-expanded="false" aria-controls="collapsePages">
                                    <div class="sb-nav-link-icon"><i class="fas fa-store-slash"></i></div>
                                    PRODUCTS
                                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                                </a>
                                <div class="collapse" id="collapsePages1" aria-labelledby="headingTwo" data-bs-parent="#sidenavAccordion">
                                    <nav class="sb-sidenav-menu-nested nav accordion" id="sidenavAccordionPages">
                                        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#pagesCollapseAuth" aria-expanded="false" aria-controls="pagesCollapseAuth">
                                            <div class="sb-nav-link-icon">
                                                <i class="fa-solid fa-list"></i></div>
                                            Categories
                                            <div class="sb-sidenav-collapse-arrow">
                                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                                            </div>
                                        </a>
                                        <div class="collapse" id="pagesCollapseAuth" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordionPages">
                                            <nav class="sb-sidenav-menu-nested nav">
                                                <a class="nav-link" href="{{route('categories.index')}}">
                                                    <div class="sb-nav-link-icon">
                                                        <i class="fas fa-user-plus mr-2"></i></div>
                                                    Manage Categories
                                                </a>
                                            </nav>
                                        </div>

                                        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#pagesCollapseAuth1" aria-expanded="false" aria-controls="pagesCollapseAuth">
                                            <div class="sb-nav-link-icon">
                                                <i class="far fa-clone"></i>
                                            </div>
                                            Product Units
                                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                                        </a>
                                        <div class="collapse" id="pagesCollapseAuth1" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordionPages">
                                            <nav class="sb-sidenav-menu-nested nav">
                                                <a class="nav-link" href="{{route('units.index')}}">
                                                    <div class="sb-nav-link-icon">
                                                        <i class="fas fa-user-plus mr-2"></i>
                                                    </div>
                                                    Manage Units
                                                </a>
                                            </nav>
                                        </div>
                                        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#pagesCollapseError" aria-expanded="false" aria-controls="pagesCollapseError">
                                            <div class="sb-nav-link-icon">
                                                <i class="fas fa-user"></i></div>
                                            Product
                                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                                        </a>
                                        <div class="collapse" id="pagesCollapseError" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordionPages">
                                            <nav class="sb-sidenav-menu-nested nav">
                                                <a class="nav-link" href="{{route('products.create')}}">
                                                    <div class="sb-nav-link-icon">
                                                        <i class="fas fa-user-plus mr-2"></i></div>
                                                    New
                                                </a>
                                                <a class="nav-link" href="{{route('products.index')}}">
                                                    <div class="sb-nav-link-icon">
                                                        <i class="fas fa-user-cog mr-2"></i></div>
                                                    Manage
                                                </a>
                                                <a class="nav-link" href="{{route('products.changePrice')}}">
                                                    <div class="sb-nav-link-icon">
                                                        <i class="fa fa-refresh"></i></div>
                                                    Chage Prices
                                                </a>
                                            </nav>
                                        </div>
                                    </nav>
                                </div>
                                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseLayouts3" aria-expanded="false" aria-controls="collapseLayouts">
                                    <div class="sb-nav-link-icon"><i class="fas fa-file-pdf"></i></div>
                                    INVOICES
                                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                                </a>
                                <div class="collapse" id="collapseLayouts3" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                                    <nav class="sb-sidenav-menu-nested nav">
                                        <a class="nav-link" href="{{route('invoices.create')}}">
                                            <div class="sb-nav-link-icon">
                                                <i class="fas fa-sign-in mr-2"></i>
                                            </div>
                                            NEW INVOICE
                                        </a>
                                        <a class="nav-link" href="{{route('invoices.index')}}">
                                            <div class="sb-nav-link-icon">
                                                <i class="fa-sharp-duotone fa-solid fa-list"></i>
                                            </div>
                                            All Invoices
                                        </a>
                                    </nav>
                                </div>
                                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseLayouts4" aria-expanded="false" aria-controls="collapseLayouts">
                                    <div class="sb-nav-link-icon"><i class="fas fa-file-pdf"></i></div>
                                    EXPENSES
                                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                                </a>
                                <div class="collapse" id="collapseLayouts4" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                                    <nav class="sb-sidenav-menu-nested nav">
                                        <a class="nav-link" href="{{route('expenses.create')}}">
                                            <div class="sb-nav-link-icon">
                                                <i class="fas fa-sign-in mr-2"></i>
                                            </div>
                                            NEW EXPENSES
                                        </a>
                                        <a class="nav-link" href="{{route('expenses.index')}}">
                                            <div class="sb-nav-link-icon">
                                                <i class="fa-sharp-duotone fa-solid fa-list"></i>
                                            </div>
                                            All EXPENSES
                                        </a>
                                    </nav>
                                </div>
                                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapsePages" aria-expanded="false" aria-controls="collapsePages">
                                    <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                                    PEOPLE
                                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                                </a>
                                <div class="collapse" id="collapsePages" aria-labelledby="headingTwo" data-bs-parent="#sidenavAccordion">
                                    <nav class="sb-sidenav-menu-nested nav accordion" id="sidenavAccordionPages">
                                        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#pagesCollapseAuth" aria-expanded="false" aria-controls="pagesCollapseAuth">
                                            <div class="sb-nav-link-icon">
                                                <i class="fas fa-user-friends"></i>
                                            </div>
                                            Users
                                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                                        </a>
                                        <div class="collapse" id="pagesCollapseAuth" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordionPages">
                                            <nav class="sb-sidenav-menu-nested nav">
                                                <a class="nav-link" href="{{route('users.create')}}">
                                                    <div class="sb-nav-link-icon">
                                                        <i class="fas fa-user-plus mr-2"></i>
                                                    </div>
                                                     Create Users
                                                </a>
                                                <a class="nav-link" href="{{route('roles.index')}}">
                                                    <div class="sb-nav-link-icon">
                                                        <i class="fa-solid fa-user-shield"></i>
                                                    </div>
                                                     User Roles
                                                </a>
                                                <a class="nav-link" href="{{route('users.index')}}">
                                                    <div class="sb-nav-link-icon">
                                                        <i class="fas fa-user-cog mr-2"></i></div>
                                                    Manage Users
                                                </a>
                                            </nav>
                                        </div>
                                        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#pagesCollapseError" aria-expanded="false" aria-controls="pagesCollapseError">
                                            <div class="sb-nav-link-icon">
                                                <i class="fas fa-user"></i></div>
                                            Customers
                                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                                        </a>
                                        <div class="collapse" id="pagesCollapseError" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordionPages">
                                            <nav class="sb-sidenav-menu-nested nav">
                                                <a class="nav-link" href="{{route('customers.create')}}">
                                                    <div class="sb-nav-link-icon">
                                                        <i class="fas fa-user-plus mr-2"></i></div>
                                                    New Customer
                                                </a>
                                                <a class="nav-link" href="{{route('customers.index')}}">
                                                    <div class="sb-nav-link-icon">
                                                        <i class="fas fa-user-cog mr-2"></i></div>
                                                    Manage Customers
                                                </a>
                                            </nav>
                                        </div>
                                        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#pagesCollapseError1" aria-expanded="false" aria-controls="pagesCollapseError">
                                           <div class="sb-nav-link-icon">
                                               <i class="fas fa-user"></i></div>
                                            Suppliers
                                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                                        </a>
                                        <div class="collapse" id="pagesCollapseError1" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordionPages">
                                            <nav class="sb-sidenav-menu-nested nav">
                                                <a class="nav-link" href="{{route('suppliers.create')}}">
                                                    <div class="sb-nav-link-icon">
                                                        <i class="fas fa-user-plus mr-2"></i></div>
                                                    New Supplier
                                                </a>
                                                <a class="nav-link" href="{{route('suppliers.index')}}">
                                                    <div class="sb-nav-link-icon">
                                                        <i class="fas fa-user-cog mr-2"></i></div>
                                                    Manage Suppliers
                                                </a>
                                            </nav>
                                        </div>
                                    </nav>
                                </div>
                                <a class="nav-link" href="#">
                                    <div class="sb-nav-link-icon"><i class="fas fa-chart-area"></i></div>
                                    Charts
                                </a>
                                <a class="nav-link" href="#">
                                    <div class="sb-nav-link-icon"><i class="fas fa-table"></i></div>
                                    Tables
                                </a>
                            </div>
                        </div>
                        <div class="sb-sidenav-footer">
                            <div class="small">Logged in as:</div>
                            @if(auth()->user())
                                {{auth()->user()->name}}
                            @endif
                        </div>
                    </nav>
                </div>
                @endif
                <div id="layoutSidenav_content">
                    <main class="px-3">
                        @yield("content")
                        <notifications position="bottom right"/>
                    </main>
                    {{-- <footer class="py-4 bg-light mt-auto">
                        <div class="container-fluid px-4">
                            <div class="d-flex align-items-center justify-content-between small">
                                <div class="text-muted">Copyright &copy; Your Website 2023</div>
                                <div>
                                    <a href="#">Privacy Policy</a>
                                    &middot;
                                    <a href="#">Terms &amp; Conditions</a>
                                </div>
                            </div>
                        </div>
                    </footer> --}}
                </div>
            </div>
        </div>
    </div>
    <style>
        .container-fluid {
            padding:0;
            margin:0;
        }
    </style>
    <script src="{{asset('js/scripts.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
    <script src="{{asset('js/datatables-simple-demo.js')}}"></script>
</body>
</html>
