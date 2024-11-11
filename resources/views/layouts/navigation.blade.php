<!--begin::Sidebar-->
<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark"> <!--begin::Sidebar Brand-->
    <div class="sidebar-brand"> <!--begin::Brand Link-->
        <a href="{{ route("dashboard") }}" class="brand-link"> <!--begin::Brand Text-->
            <span class="brand-text fw-light">{{ env("APP_NAME") }}</span> <!--end::Brand Text-->
        </a> <!--end::Brand Link-->
    </div> <!--end::Sidebar Brand-->

    <!--begin::Sidebar Wrapper-->
    <div class="sidebar-wrapper">
        <nav class="mt-2"> <!--begin::Sidebar Menu-->
            <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="{{ route("dashboard") }}" class="nav-link active"> <i class="nav-icon bi bi-speedometer"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route("profile.edit") }}" class="nav-link"> <i class="nav-icon bi bi-person-badge-fill"></i>
                        <p>Update Profile</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route("center.index") }}" class="nav-link"> <i class="nav-icon bi bi-recycle"></i>
                        <p>Recycle Centers</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route("dashboard") }}" class="nav-link"> <i class="nav-icon bi bi-people-fill"></i>
                        <p>Registered Owners</p>
                    </a>
                </li>
            </ul> <!--end::Sidebar Menu-->
        </nav>
    </div> <!--end::Sidebar Wrapper-->
</aside> <!--end::Sidebar-->