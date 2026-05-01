<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Noble Library') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS (via CDN for simplicity since Breeze failed, but ideally via Vite) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        navy: '#001f3f',
                        slate: '#708090',
                        silver: '#c0c0c0',
                        noble: {
                            bg: '#f8f9fa',
                            card: '#ffffff',
                            text: '#1a1a1a'
                        }
                    },
                    fontFamily: {
                        sans: ['Outfit', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        body { background-color: #f8f9fa; color: #1a1a1a; }
        .glass { background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.2); }
    </style>
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen flex flex-col">
        <!-- Navigation -->
        <nav class="bg-navy text-white shadow-lg sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <a href="/" class="text-2xl font-bold tracking-tight flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-silver" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                            <span class="text-silver uppercase">Noble</span><span>Library</span>
                        </a>
                        <div class="hidden sm:ml-10 sm:flex sm:space-x-8">
                            @auth
                                @if(auth()->user()->role === 'admin')
                                    <a href="{{ route('admin.books.index') }}" class="inline-flex items-center px-1 pt-1 text-sm font-medium leading-5 hover:text-silver transition duration-150 ease-in-out">Books</a>
                                    <a href="{{ route('admin.members.index') }}" class="inline-flex items-center px-1 pt-1 text-sm font-medium leading-5 hover:text-silver transition duration-150 ease-in-out">Members</a>
                                    <a href="{{ route('admin.transactions.index') }}" class="inline-flex items-center px-1 pt-1 text-sm font-medium leading-5 hover:text-silver transition duration-150 ease-in-out">Logs</a>
                                @else
                                    <a href="{{ route('user.dashboard') }}" class="inline-flex items-center px-1 pt-1 text-sm font-medium leading-5 hover:text-silver transition duration-150 ease-in-out">My Books</a>
                                    <a href="{{ route('user.catalog') }}" class="inline-flex items-center px-1 pt-1 text-sm font-medium leading-5 hover:text-silver transition duration-150 ease-in-out">Catalog</a>
                                    <a href="{{ route('user.devices.index') }}" class="inline-flex items-center px-1 pt-1 text-sm font-medium leading-5 hover:text-silver transition duration-150 ease-in-out">Devices</a>
                                @endif
                            @endauth
                        </div>
                    </div>
                    
                    <!-- Desktop Auth Links -->
                    <div class="hidden sm:flex sm:items-center">
                        @auth
                            <div class="flex items-center space-x-4">
                                <span class="text-sm text-silver">{{ auth()->user()->name }}</span>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="text-sm font-medium hover:text-silver transition">Logout</button>
                                </form>
                            </div>
                        @else
                            <a href="{{ route('login') }}" class="text-sm font-medium hover:text-silver transition mr-4">Login</a>
                            <a href="{{ route('register') }}" class="bg-silver text-navy px-4 py-2 rounded-md text-sm font-bold hover:bg-white transition">Join Now</a>
                        @endauth
                    </div>

                    <div class="flex items-center -mr-2 sm:hidden">
                        <button type="button" onclick="toggleMobileMenu()" class="inline-flex items-center justify-center p-2 rounded-md text-silver hover:text-white hover:bg-navy focus:outline-none transition">
                            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path id="menuIcon" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                <path id="closeIcon" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile menu, show/hide based on menu state. -->
            <div id="mobileMenu" class="hidden sm:hidden border-t border-silver/10 bg-navy">
                <div class="pt-2 pb-3 space-y-1 px-4">
                    @auth
                        @if(auth()->user()->role === 'admin')
                            <a href="{{ route('admin.books.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-silver hover:text-white hover:bg-silver/10">Books</a>
                            <a href="{{ route('admin.members.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-silver hover:text-white hover:bg-silver/10">Members</a>
                            <a href="{{ route('admin.transactions.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-silver hover:text-white hover:bg-silver/10">Logs</a>
                        @else
                            <a href="{{ route('user.dashboard') }}" class="block px-3 py-2 rounded-md text-base font-medium text-silver hover:text-white hover:bg-silver/10">My Books</a>
                            <a href="{{ route('user.catalog') }}" class="block px-3 py-2 rounded-md text-base font-medium text-silver hover:text-white hover:bg-silver/10">Catalog</a>
                            <a href="{{ route('user.devices.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-silver hover:text-white hover:bg-silver/10">Devices</a>
                        @endif
                        <div class="pt-4 pb-1 border-t border-silver/10">
                            <div class="flex items-center px-3">
                                <div class="text-base font-medium text-white">{{ auth()->user()->name }}</div>
                            </div>
                            <div class="mt-3 space-y-1">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-3 py-2 rounded-md text-base font-medium text-silver hover:text-white hover:bg-silver/10">Logout</button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="block px-3 py-2 rounded-md text-base font-medium text-silver hover:text-white hover:bg-silver/10">Login</a>
                        <a href="{{ route('register') }}" class="block px-3 py-2 rounded-md text-base font-medium text-white bg-silver/20">Join Now</a>
                    @endif
                </div>
            </div>
        </nav>

        <script>
            function toggleMobileMenu() {
                const menu = document.getElementById('mobileMenu');
                const menuIcon = document.getElementById('menuIcon');
                const closeIcon = document.getElementById('closeIcon');
                
                if (menu.classList.contains('hidden')) {
                    menu.classList.remove('hidden');
                    menuIcon.classList.add('hidden');
                    closeIcon.classList.remove('hidden');
                    document.body.style.overflow = 'hidden'; // Prevent background scroll
                } else {
                    menu.classList.add('hidden');
                    menuIcon.classList.remove('hidden');
                    closeIcon.classList.add('hidden');
                    document.body.style.overflow = 'auto';
                }
            }
        </script>

        <!-- Page Content -->
        <main class="flex-grow py-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                @if(session('success'))
                    <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm">
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-sm">
                        {{ session('error') }}
                    </div>
                @endif

                {{ $slot }}
            </div>
        </main>

        <footer class="bg-navy text-silver py-8 mt-auto border-t border-navy">
            <div class="max-w-7xl mx-auto px-4 text-center">
                <p class="text-sm">&copy; {{ date('Y') }} Noble Library Management System. Elegant & Minimalist.</p>
            </div>
        </footer>
    </div>
</body>
</html>
