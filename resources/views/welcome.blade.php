<x-app-layout>
    <div class="py-12 md:py-20 text-center">
        <h1 class="text-4xl md:text-6xl font-bold text-navy mb-6 tracking-tighter">
            <span class="text-silver uppercase">Noble</span> LIBRARY SYSTEM
        </h1>
        <p class="text-lg md:text-xl text-slate mb-10 max-w-2xl mx-auto italic px-4">
            "A sanctuary for the mind, an archive for the ages. Experience the elegance of modern literature management."
        </p>

        <div class="flex flex-col md:flex-row justify-center gap-4 md:gap-6 px-10">
            @auth
                <a href="{{ auth()->user()->role === 'admin' ? route('admin.books.index') : route('user.dashboard') }}" 
                   class="bg-navy text-white px-10 py-4 rounded-full font-bold text-lg hover:bg-slate transition shadow-2xl text-center">
                    Enter Dashboard
                </a>
            @else
                <a href="{{ route('register') }}" 
                   class="bg-navy text-white px-10 py-4 rounded-full font-bold text-lg hover:bg-slate transition shadow-2xl text-center">
                    Join the Library
                </a>
                <a href="{{ route('login') }}" 
                   class="border-2 border-navy text-navy px-10 py-4 rounded-full font-bold text-lg hover:bg-navy hover:text-white transition text-center">
                    Sign In
                </a>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-10 mt-20 pb-20">
        <div class="bg-white p-8 rounded-2xl border border-silver/20 shadow-sm">
            <div class="w-12 h-12 bg-navy/10 rounded-xl flex items-center justify-center text-navy mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
            </div>
            <h3 class="text-xl font-bold text-navy mb-2">Curated Collection</h3>
            <p class="text-slate text-sm">Access thousands of titles ranging from timeless classics to modern scientific journals.</p>
        </div>

        <div class="bg-white p-8 rounded-2xl border border-silver/20 shadow-sm">
            <div class="w-12 h-12 bg-navy/10 rounded-xl flex items-center justify-center text-navy mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h3 class="text-xl font-bold text-navy mb-2">Seamless Borrowing</h3>
            <p class="text-slate text-sm">One-click borrowing with automated due-date tracking and reminders.</p>
        </div>

        <div class="bg-white p-8 rounded-2xl border border-silver/20 shadow-sm">
            <div class="w-12 h-12 bg-navy/10 rounded-xl flex items-center justify-center text-navy mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
            </div>
            <h3 class="text-xl font-bold text-navy mb-2">Secure & Private</h3>
            <p class="text-slate text-sm">Your reading history and personal details are protected by state-of-the-art security.</p>
        </div>
    </div>
</x-app-layout>
