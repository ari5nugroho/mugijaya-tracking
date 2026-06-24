<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'CV Mugijaya Logistics ERP')</title>
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    
    <!-- Inline Theme Script to prevent flash of light theme -->
    <script>
        (function () {
            const savedTheme = localStorage.getItem('theme') || 'dark';
            document.documentElement.setAttribute('data-bs-theme', savedTheme);
        })();
    </script>
    
    @yield('styles')
</head>
<body>
    <div class="app-wrapper">
        <!-- Sidebar -->
        @include('partials.sidebar')
        
        <!-- Main Content Area -->
        <div class="main-content">
            <!-- Navbar -->
            @include('partials.navbar')
            
            <!-- Content Body -->
            <div class="content-body">
                @yield('content')
            </div>
            
            <!-- Footer -->
            @include('partials.footer')
        </div>
    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Mock Database System -->
    <script src="{{ asset('js/mock-data.js') }}"></script>
    
    <!-- Global Script for Layout and Theme toggling -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // 1. Session check & User data mapping
            const activeUser = JSON.parse(sessionStorage.getItem('erp_user'));
            if (!activeUser) {
                // If not logged in, redirect to login
                window.location.href = "{{ route('login') }}";
                return;
            }

            // Bind user details to Sidebar
            const avatarEl = document.getElementById('sidebar-user-avatar');
            const nameEl = document.getElementById('sidebar-user-name');
            const roleEl = document.getElementById('sidebar-user-role');
            
            if (avatarEl && activeUser.avatar) avatarEl.src = activeUser.avatar;
            if (nameEl) nameEl.innerText = activeUser.name;
            if (roleEl) roleEl.innerText = activeUser.role;

            // 2. Sidebar Mobile Toggle
            const toggleBtn = document.getElementById('sidebar-toggle-btn');
            const sidebar = document.querySelector('.sidebar');
            if (toggleBtn && sidebar) {
                toggleBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    sidebar.classList.toggle('show');
                });

                document.addEventListener('click', (e) => {
                    if (sidebar.classList.contains('show') && !sidebar.contains(e.target) && e.target !== toggleBtn) {
                        sidebar.classList.remove('show');
                    }
                });
            }

            // 3. Theme Toggle (Light / Dark Mode)
            const themeToggle = document.getElementById('theme-toggle');
            const sunIcon = document.getElementById('theme-sun-icon');
            const moonIcon = document.getElementById('theme-moon-icon');

            function applyTheme(theme) {
                document.documentElement.setAttribute('data-bs-theme', theme);
                localStorage.setItem('theme', theme);
                if (theme === 'dark') {
                    if (sunIcon) sunIcon.classList.remove('d-none');
                    if (moonIcon) moonIcon.classList.add('d-none');
                } else {
                    if (sunIcon) sunIcon.classList.add('d-none');
                    if (moonIcon) moonIcon.classList.remove('d-none');
                }
            }

            // Apply initially
            const currentTheme = localStorage.getItem('theme') || 'dark';
            applyTheme(currentTheme);

            if (themeToggle) {
                themeToggle.addEventListener('click', () => {
                    const activeTheme = document.documentElement.getAttribute('data-bs-theme');
                    const nextTheme = activeTheme === 'dark' ? 'light' : 'dark';
                    applyTheme(nextTheme);
                    
                    // Trigger custom chart theme changes if exists
                    if (typeof refreshChartsTheme === 'function') {
                        refreshChartsTheme(nextTheme);
                    }
                });
            }

            // 4. Logout handling
            const logoutBtn = document.getElementById('btn-logout');
            if (logoutBtn) {
                logoutBtn.addEventListener('click', () => {
                    if (window.db) {
                        db.logAction(activeUser.name, "User Access", "Logout", `Admin ${activeUser.email} logout dari sistem`, "Success");
                    }
                    sessionStorage.removeItem('erp_user');
                    window.location.href = "{{ route('login') }}";
                });
            }

            // 5. Dynamic Notifications Simulation
            const notifContainer = document.getElementById('notif-items-container');
            if (notifContainer && window.db) {
                const rawLogs = localStorage.getItem('erp_auditLogs');
                const notifications = rawLogs ? JSON.parse(rawLogs).slice(0, 4) : [];
                
                let notifHtml = '';
                if (notifications.length > 0) {
                    notifications.forEach(n => {
                        let icon = 'bi-info-circle';
                        if (n.type.includes('Stock') || n.type.includes('Mutasi')) icon = 'bi-box';
                        if (n.type.includes('QC') || n.type.includes('Valid')) icon = 'bi-patch-check';
                        if (n.type.includes('Assign') || n.type.includes('Delivery')) icon = 'bi-truck';
                        
                        notifHtml += `
                            <li>
                                <a class="dropdown-item py-2 border-bottom border-light-dark" href="{{ route('audit.index') }}">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="bg-light-dark p-2 rounded text-indigo">
                                            <i class="bi ${icon}"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm fw-semibold text-wrap" style="color: var(--text-primary);">${n.details}</div>
                                            <div class="text-xs text-muted">${new Date(n.timestamp).toLocaleTimeString('id-ID')}</div>
                                        </div>
                                    </div>
                                </a>
                            </li>
                        `;
                    });
                } else {
                    notifHtml = `<li><div class="dropdown-item text-center py-3 text-muted">Tidak ada notifikasi baru</div></li>`;
                }
                notifContainer.innerHTML = notifHtml;
            }
        });
    </script>
    
    @yield('scripts')
</body>
</html>
