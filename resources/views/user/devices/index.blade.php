<x-app-layout>
    <div class="mb-10">
        <h1 class="text-3xl font-bold text-navy mb-2">My Devices</h1>
        <p class="text-slate italic">Manage your active sessions and security tokens</p>
    </div>

    <div class="bg-white rounded-xl shadow-lg border border-silver/30 overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-navy text-white">
                    <th class="px-6 py-4 text-sm font-bold uppercase tracking-widest">Device / Browser</th>
                    <th class="px-6 py-4 text-sm font-bold uppercase tracking-widest">Last Active</th>
                    <th class="px-6 py-4 text-sm font-bold uppercase tracking-widest text-center">Status</th>
                    <th class="px-6 py-4 text-sm font-bold uppercase tracking-widest text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-silver/20">
                @foreach($devices as $device)
                    @php 
                        $isCurrent = session('current_device_token') === $device->token;
                    @endphp
                    <tr class="hover:bg-noble-bg transition {{ $isCurrent ? 'bg-navy/5' : '' }}">
                        <td class="px-6 py-4">
                            <div class="font-medium text-navy">{{ $device->short_name }}</div>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="text-[9px] text-slate font-mono uppercase tracking-tighter opacity-60">ID: {{ substr($device->token, 0, 6) }}</span>
                                <span class="text-[10px] text-silver font-mono">•</span>
                                <span class="text-[10px] text-navy font-bold uppercase tracking-tighter">IP: {{ $device->ip_address ?: 'Unknown' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-slate text-sm">
                            {{ $device->last_active_at ? $device->last_active_at->diffForHumans() : 'Unknown' }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($isCurrent)
                                <span class="inline-flex items-center px-3 py-1 bg-navy text-white rounded-full text-[10px] font-bold uppercase tracking-widest shadow-sm">
                                    <span class="w-1.5 h-1.5 bg-silver rounded-full mr-1.5 animate-pulse"></span>
                                    Current
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 bg-noble-bg text-slate border border-silver/30 rounded-full text-[10px] font-bold uppercase tracking-widest">
                                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5"></span>
                                    Active
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <form action="{{ route('user.devices.destroy', $device) }}" method="POST" onsubmit="return confirm('Revoke this device session?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 font-bold hover:text-red-800 transition text-sm">
                                    {{ $isCurrent ? 'Logout' : 'Revoke' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-12 p-8 bg-navy text-white rounded-2xl shadow-2xl relative overflow-hidden">
        <div class="relative z-10">
            <h2 class="text-2xl font-bold mb-4">Security Tip</h2>
            <p class="text-silver mb-6">Every time you log in from a new device, a unique secure token is generated. If you suspect any unauthorized access, you can revoke tokens for other devices here.</p>
            <div class="inline-block px-6 py-2 border border-silver/50 rounded-lg text-sm text-silver italic">
                Total Active Tokens: {{ $devices->count() }}
            </div>
        </div>
        <!-- Decorative SVG -->
        <svg class="absolute right-0 bottom-0 w-64 h-64 text-white/5 transform translate-x-10 translate-y-10" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z" />
        </svg>
    </div>
</x-app-layout>
