<x-app-layout>
    <div class="max-w-md mx-auto">
        <div class="bg-white p-8 rounded-xl shadow-2xl border border-silver/20 glass">
            <h2 class="text-3xl font-bold text-navy mb-6 text-center">Create Account</h2>
            
            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate mb-1">Full Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" required autofocus
                        class="w-full px-4 py-2 border border-silver rounded-lg focus:ring-2 focus:ring-navy focus:border-transparent outline-none transition">
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate mb-1">Email Address (Gmail Only)</label>
                    <input type="email" name="email" value="{{ old('email') }}" required placeholder="yourname@gmail.com"
                        class="w-full px-4 py-2 border border-silver rounded-lg focus:ring-2 focus:ring-navy focus:border-transparent outline-none transition">
                    <p class="text-[10px] text-slate mt-1 italic">Note: Only standard Gmail addresses are accepted (e.g. name@gmail.com).</p>
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate mb-1">Phone Number</label>
                    <div class="flex gap-2">
                        <select name="country_code" class="w-24 px-2 py-2 border border-silver rounded-lg focus:ring-2 focus:ring-navy outline-none transition text-sm">
                            <option value="+62">+62 (ID)</option>
                            <option value="+1">+1 (US)</option>
                            <option value="+44">+44 (UK)</option>
                            <option value="+60">+60 (MY)</option>
                            <option value="+65">+65 (SG)</option>
                            <option value="+61">+61 (AU)</option>
                            <option value="+81">+81 (JP)</option>
                        </select>
                        <input type="tel" name="phone_number" value="{{ old('phone_number') }}" required placeholder="8123456789"
                            class="flex-grow px-4 py-2 border border-silver rounded-lg focus:ring-2 focus:ring-navy focus:border-transparent outline-none transition">
                    </div>
                    <p class="text-[10px] text-slate mt-1 italic">Select country code and enter number without leading 0.</p>
                    @error('phone')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate mb-1">Password</label>
                    <input type="password" name="password" required
                        class="w-full px-4 py-2 border border-silver rounded-lg focus:ring-2 focus:ring-navy focus:border-transparent outline-none transition">
                    @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-slate mb-1">Confirm Password</label>
                    <input type="password" name="password_confirmation" required
                        class="w-full px-4 py-2 border border-silver rounded-lg focus:ring-2 focus:ring-navy focus:border-transparent outline-none transition">
                </div>

                <button type="submit" class="w-full bg-navy text-white font-bold py-3 rounded-lg hover:bg-slate transition shadow-lg">
                    Register
                </button>
            </form>

            <p class="text-center text-sm text-slate mt-6">
                Already have an account? <a href="{{ route('login') }}" class="text-navy font-bold hover:underline">Login</a>
            </p>
        </div>
    </div>
</x-app-layout>
