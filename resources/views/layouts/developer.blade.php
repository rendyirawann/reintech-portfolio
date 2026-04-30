<!DOCTYPE html>
<html lang="en" data-dev-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Developer CMS</title>
    @vite(['resources/css/developer.css', 'resources/js/developer.js'])
    <style>
        .dev-layout { display: flex; min-height: 100vh; }
        .dev-sidebar { width: 240px; background-color: var(--dev-surface); border-right: 1px solid var(--dev-border); display: flex; flex-direction: column; }
        .dev-sidebar-header { padding: 1.5rem; border-bottom: 1px solid var(--dev-border); font-weight: bold; font-size: 1.2rem; }
        .dev-nav { padding: 1rem 0; flex-grow: 1; }
        .dev-nav-link { display: block; padding: 0.75rem 1.5rem; color: var(--dev-text-muted); text-decoration: none; transition: all 0.2s; }
        .dev-nav-link:hover, .dev-nav-link.active { color: var(--dev-accent); background-color: rgba(59, 130, 246, 0.1); border-right: 3px solid var(--dev-accent); }
        .dev-content { flex-grow: 1; display: flex; flex-direction: column; }
        .dev-topbar { padding: 1rem 2rem; border-bottom: 1px solid var(--dev-border); background-color: var(--dev-surface); display: flex; justify-content: space-between; align-items: center; }
        .dev-main { padding: 2rem; overflow-y: auto; flex-grow: 1; }
        .alert { padding: 1rem; border-radius: 0.375rem; margin-bottom: 1.5rem; }
        .alert-success { background-color: rgba(16, 185, 129, 0.1); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.2); }
        .alert-danger { background-color: rgba(239, 68, 68, 0.1); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.2); }
    </style>
</head>
<body>
    <div class="dev-layout">
        <div class="dev-sidebar">
            <div class="dev-sidebar-header">
                REINTECH DEV
            </div>
            <nav class="dev-nav">
                <a href="{{ route('developer.dashboard') }}" class="dev-nav-link {{ request()->routeIs('developer.dashboard') ? 'active' : '' }}">Dashboard</a>
                <a href="{{ route('developer.identity') }}" class="dev-nav-link {{ request()->routeIs('developer.identity') ? 'active' : '' }}">Identity</a>
                <a href="{{ route('developer.identity.socials') }}" class="dev-nav-link {{ request()->routeIs('developer.identity.socials') ? 'active' : '' }}">Social Links</a>
                <a href="{{ route('developer.sections') }}" class="dev-nav-link {{ request()->routeIs('developer.sections') ? 'active' : '' }}">Sections</a>
                <a href="{{ route('developer.projects') }}" class="dev-nav-link {{ request()->routeIs('developer.projects*') ? 'active' : '' }}">Projects</a>
                <a href="{{ route('developer.settings') }}" class="dev-nav-link {{ request()->routeIs('developer.settings') ? 'active' : '' }}">Settings</a>
            </nav>
            <div style="padding: 1rem; border-top: 1px solid var(--dev-border);">
                <form action="{{ route('developer.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="dev-nav-link" style="width: 100%; text-align: left; background: transparent; border: none; cursor: pointer; color: #ef4444;">
                        Logout
                    </button>
                </form>
            </div>
        </div>
        
        <div class="dev-content">
            <div class="dev-topbar">
                <h1 style="font-size: 1.25rem; font-weight: 600;">@yield('title', 'Dashboard')</h1>
                <div style="display: flex; gap: 1rem; align-items: center;">
                    <span style="color: var(--dev-text-muted);">{{ session('developer_name') }}</span>
                    <button id="theme-toggle" class="dev-btn" style="background: transparent; color: var(--dev-text); border: 1px solid var(--dev-border);">Theme</button>
                </div>
            </div>
            
            <main class="dev-main">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul style="margin: 0; padding-left: 1.5rem;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
