<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-laugh-wink"></i>
        </div>
        <div class="sidebar-brand-text mx-3">UPLOAD POT<sup>VB</sup></div>
    </a>
    @php
        $sub_url = Request::segment(1);
    @endphp

    <!-- Divider -->
    <hr class="sidebar-divider my-0">
    <li class="nav-item @if($sub_url == 'home') active @endif">
        <a class="nav-link" href="{{ url('/home') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Konversi PLU</span></a>
    </li>

    <li class="nav-item @if($sub_url == 'upload-pot') active @endif">
        <a class="nav-link" href="{{ url('/upload-pot') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Upload PB POT</span></a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <!-- <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div> -->
</ul>
