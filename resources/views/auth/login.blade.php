<x-app-layout>
    <div class="max-w-md mx-auto">
        <div class="bg-white p-8 rounded-xl shadow-2xl border border-silver/20 glass">
            <h2 class="text-3xl font-bold text-navy mb-6 text-center">Welcome Back</h2>
            
            @if($errors->any())
                <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded shadow-sm">
                    @foreach($errors->all() as $error)
                        <p class="text-red-700 text-xs font-bold">{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded shadow-sm">
                    <p class="text-red-700 text-xs font-bold">{{ session('error') }}</p>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate mb-1">Email or Phone Number</label>
                    <input type="text" name="login" value="{{ old('login') }}" required autofocus placeholder="name@gmail.com or 081..."
                        class="w-full px-4 py-2 border border-silver rounded-lg focus:ring-2 focus:ring-navy focus:border-transparent outline-none transition">
                    @error('login')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-slate mb-1">Password</label>
                    <input type="password" name="password" required
                        class="w-full px-4 py-2 border border-silver rounded-lg focus:ring-2 focus:ring-navy focus:border-transparent outline-none transition">
                    @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between mb-6">
                    <label class="flex items-center">
                        <input type="checkbox" name="remember" class="rounded border-silver text-navy focus:ring-navy">
                        <span class="ml-2 text-sm text-slate">Remember me</span>
                    </label>
                    <a href="#" class="text-sm text-navy hover:underline">Forgot password?</a>
                </div>

                <button type="submit" class="w-full bg-navy text-white font-bold py-3 rounded-lg hover:bg-slate transition shadow-lg">
                    Sign In
                </button>
            </form>

            <p class="text-center text-sm text-slate mt-6">
                Don't have an account? <a href="{{ route('register') }}" class="text-navy font-bold hover:underline">Register</a>
            </p>
        </div>
    </div>
</x-app-layout>
