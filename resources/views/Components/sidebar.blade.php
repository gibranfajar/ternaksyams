<nav class="pc-sidebar">
    <div class="navbar-wrapper">
        <div class="m-header">
            <a href="{{ route('dashboard') }}" class="b-brand text-primary">
                <!-- ========   Change your logo from here   ============ -->
                <img src="{{ asset('assets/images/logo-dark.svg') }}" class="img-fluid logo-lg" alt="logo">
            </a>
        </div>
        <div class="navbar-content">
            <ul class="pc-navbar">
                <li class="pc-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <a href="{{ route('dashboard') }}" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-dashboard"></i></span>
                        <span class="pc-mtext">Dashboard</span>
                    </a>
                </li>

                <li class="pc-item pc-hasmenu">
                    <a class="pc-link"><span class="pc-micon"><i class="ti ti-list-check"></i></span><span
                            class="pc-mtext">Products</span><span class="pc-arrow"><i
                                data-feather="chevron-right"></i></span></a>
                    <ul class="pc-submenu">
                        <li
                            class="pc-item pc-hasmenu {{ request()->routeIs('categories.*') || request()->routeIs('flavours.*') || request()->routeIs('sizes.*') ? 'active' : '' }}">
                            <a class="pc-link">Master Data<span class="pc-arrow"><i
                                        data-feather="chevron-right"></i></span></a>
                            <ul class="pc-submenu">
                                <li class="pc-item"><a class="pc-link"
                                        href="{{ route('categories.index') }}">Category</a></li>
                                <li class="pc-item"><a class="pc-link" href="{{ route('flavours.index') }}">Flavour</a>
                                </li>
                                <li class="pc-item"><a class="pc-link" href="{{ route('sizes.index') }}">Size</a></li>
                            </ul>
                        </li>
                        <li class="pc-item {{ request()->routeIs('products.*') ? 'active' : '' }}"><a class="pc-link"
                                href="{{ route('products.index') }}">Product</a></li>
                        <li class="pc-item {{ request()->routeIs('flashsales.*') ? 'active' : '' }}"><a class="pc-link"
                                href="{{ route('flashsales.index') }}">Flash Sale</a></li>
                    </ul>
                </li>

                <li class="pc-item {{ request()->routeIs('orders') ? 'active' : '' }}">
                    <a href="{{ route('orders.index') }}" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-shopping-cart"></i></span>
                        <span class="pc-mtext">Orders</span>
                    </a>
                </li>

                <li class="pc-item {{ request()->routeIs('promotions') ? 'active' : '' }}">
                    <a href="{{ route('promotions.index') }}" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-percentage"></i></span>
                        <span class="pc-mtext">Promotions</span>
                    </a>
                </li>

                <li class="pc-item {{ request()->routeIs('vouchers') ? 'active' : '' }}">
                    <a href="{{ route('vouchers.index') }}" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-gift"></i></span>
                        <span class="pc-mtext">Voucher</span>
                    </a>
                </li>

                <li class="pc-item pc-hasmenu">
                    <a class="pc-link"><span class="pc-micon"><i class="ti ti-list-check"></i></span><span
                            class="pc-mtext">Reseller</span><span class="pc-arrow"><i
                                data-feather="chevron-right"></i></span></a>
                    <ul class="pc-submenu">
                        <li class="pc-item {{ request()->routeIs('resellers.*') ? 'active' : '' }}"><a class="pc-link"
                                href="{{ route('resellers.index') }}">Data Reseller</a></li>
                        <li class="pc-item {{ request()->routeIs('reseller-benefits.*') ? 'active' : '' }}"><a
                                class="pc-link" href="{{ route('reseller-benefits.index') }}">Benefit</a></li>
                    </ul>
                </li>

                <li class="pc-item pc-hasmenu">
                    <a class="pc-link"><span class="pc-micon"><i class="ti ti-list-check"></i></span><span
                            class="pc-mtext">Affiliator</span><span class="pc-arrow"><i
                                data-feather="chevron-right"></i></span></a>
                    <ul class="pc-submenu">
                        <li class="pc-item {{ request()->routeIs('affiliators.*') ? 'active' : '' }}"><a
                                class="pc-link" href="{{ route('affiliators.index') }}">Data Affiliate</a></li>
                        <li class="pc-item {{ request()->routeIs('affiliator-benefits.*') ? 'active' : '' }}"><a
                                class="pc-link" href="{{ route('affiliator-benefits.index') }}">Benefit</a></li>
                    </ul>
                </li>

                <li class="pc-item pc-hasmenu">
                    <a class="pc-link"><span class="pc-micon"><i class="ti ti-list-check"></i></span><span
                            class="pc-mtext">Articles</span><span class="pc-arrow"><i
                                data-feather="chevron-right"></i></span></a>
                    <ul class="pc-submenu">
                        <li class="pc-item {{ request()->routeIs('article-categories.*') ? 'active' : '' }}"><a
                                class="pc-link" href="{{ route('article-categories.index') }}">Category</a></li>
                        <li class="pc-item {{ request()->routeIs('articles.*') ? 'active' : '' }}"><a class="pc-link"
                                href="{{ route('articles.index') }}">Article</a></li>
                    </ul>
                </li>

                <li class="pc-item pc-hasmenu">
                    <a class="pc-link"><span class="pc-micon"><i class="ti ti-list-check"></i></span><span
                            class="pc-mtext">Tutorials</span><span class="pc-arrow"><i
                                data-feather="chevron-right"></i></span></a>
                    <ul class="pc-submenu">
                        <li class="pc-item {{ request()->routeIs('tutorial-categories.*') ? 'active' : '' }}"><a
                                class="pc-link" href="{{ route('tutorial-categories.index') }}">Category</a></li>
                        <li class="pc-item {{ request()->routeIs('tutorials.*') ? 'active' : '' }}"><a
                                class="pc-link" href="{{ route('tutorials.index') }}">Tutorial</a></li>
                    </ul>
                </li>

                <li class="pc-item pc-hasmenu">
                    <a class="pc-link"><span class="pc-micon"><i class="ti ti-list-check"></i></span><span
                            class="pc-mtext">Faqs</span><span class="pc-arrow"><i
                                data-feather="chevron-right"></i></span></a>
                    <ul class="pc-submenu">
                        <li class="pc-item {{ request()->routeIs('faq-categories.*') ? 'active' : '' }}"><a
                                class="pc-link" href="{{ route('faq-categories.index') }}">Category</a></li>
                        <li class="pc-item {{ request()->routeIs('faqs.*') ? 'active' : '' }}"><a class="pc-link"
                                href="{{ route('faqs.index') }}">Faq</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
