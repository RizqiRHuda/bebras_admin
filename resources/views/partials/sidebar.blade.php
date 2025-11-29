<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo d-flex align-items-center">
        <a href="{{ route('admin.dashboard') }}" class="app-brand-link d-flex align-items-center text-decoration-none">
            <span class="app-brand-logo demo">
                <img src="{{ asset('assets/img/logo/logo.jpg') }}" alt="" style="height:40px;">
            </span>
            <span class="app-brand-text demo menu-text fw-bolder ms-2">
                Admin
                <p>
                    Bebras
                </p>

            </span>
        </a>
        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        @if (auth()->user()->role === 'admin')
            <!-- Dashboard -->
            <li class="menu-item {{ Route::is('admin.dashboard') ? 'active' : '' }}">
                <a href="{{ route('admin.dashboard') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-home-circle"></i>
                    <div data-i18n="Analytics">Dashboard</div>
                </a>
            </li>
            <li class="menu-item {{ Route::is('tentang_bebras.index') ? 'active' : '' }}">
                <a href="{{ route('tentang_bebras.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-crown"></i>
                    <div data-i18n="Analytics">Tentang Bebras</div>
                </a>
            </li>
            <li class="menu-item {{ Route::is('soal_bebras.index') ? 'active' : '' }}">
                <a href="{{ route('soal_bebras.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-book"></i>
                    <div data-i18n="Analytics">Soal Bebras</div>
                </a>
            </li>
            <li class="menu-item {{ Route::is('kontak') ? 'active' : '' }}">
                <a href="{{ route('kontak.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-box"></i>
                    <div data-i18n="Analytics">Kontak</div>
                </a>
            </li>
            </li>
            <li class="menu-item {{ Route::is('latihan') ? 'active' : '' }}">
                <a href="{{ route('latihan.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-copy"></i>
                    <div data-i18n="Analytics">Latihan</div>
                </a>
            </li>

            <!-- Layouts -->
            <li class="menu-item {{ Request::is('layouts*') ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-layout"></i>
                    <div data-i18n="Layouts">Kegiatan</div>
                </a>
                <ul class="menu-sub">
                    <li
                        class="menu-item {{ Request::is('kegiatan/workshop*') || Request::is('kegiatan/workshop-page') ? 'active' : '' }}">
                        <a href="{{ route('page_workhop.index') }}" class="menu-link">
                            <div data-i18n="Without menu">Workshop</div>
                        </a>
                    </li>

                    <li class="menu-item {{ Request::is('kegiatan/challenge-index') ? 'active' : '' }}">
                        <a href="{{ route('challenge.index') }}" class="menu-link">
                            <div data-i18n="Without menu">Bebras Challenge</div>
                        </a>
                    </li>

                    <li class="menu-item {{ Request::is('pengumuman*') ? 'active' : '' }}">
                        <a href="{{ route('pengumuman.index') }}" class="menu-link">
                            <div data-i18n="Without menu">Pengumuman Hasil</div>
                        </a>
                    </li>


                </ul>
            </li>

            <!-- Pages -->
            <li class="menu-header small text-uppercase">
                <span class="menu-header-text">Pages</span>
            </li>

            <li class="menu-item {{ Request::is('account*') ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-dock-top"></i>
                    <div data-i18n="Account Settings">Account Settings</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ Route::is('register') ? 'active' : '' }}">
                        <a href="{{ route('register') }}" class="menu-link">
                            <div data-i18n="Account">Pengaturan Akun</div>
                        </a>
                    </li>
                </ul>

            </li>

    </ul>
@else
    <ul class="menu-inner py-1">
        <li class="menu-item {{ Route::is('user.dashboard') ? 'active' : '' }}">
            <a href="{{ route('user.dashboard') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Analytics">Dashboard</div>
            </a>
        </li>
        <li class="menu-item {{ Route::is('berita.index') ? 'active' : '' }}">
            <a href="{{ route('berita.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-copy"></i>
                <div data-i18n="Analytics">Berita</div>
            </a>
        </li>
    </ul>
@endif
</aside>
