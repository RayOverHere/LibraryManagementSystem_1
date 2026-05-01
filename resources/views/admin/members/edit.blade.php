<x-app-layout>
    <div class="max-w-2xl mx-auto">
        <div class="bg-white p-8 rounded-xl shadow-2xl border border-silver/20">
            <h2 class="text-3xl font-bold text-navy mb-6">Manage Patron</h2>
            
            <form method="POST" action="{{ route('admin.members.update', $member) }}">
                @csrf
                @method('PUT')

                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-slate mb-1">Patron Name</label>
                        <input type="text" name="name" value="{{ old('name', $member->name) }}" required
                            class="w-full px-4 py-2 border border-silver rounded-lg focus:ring-2 focus:ring-navy focus:border-transparent outline-none transition">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate mb-1">Email Address (View Only)</label>
                        <input type="email" value="{{ $member->email }}" readonly
                            class="w-full px-4 py-2 border border-silver rounded-lg bg-gray-50 text-slate cursor-not-allowed outline-none transition">
                        <p class="text-[10px] text-slate mt-1 italic">Email addresses cannot be changed by administrators.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate mb-1">Phone Number (View Only)</label>
                        <input type="text" value="{{ $member->phone }}" readonly
                            class="w-full px-4 py-2 border border-silver rounded-lg bg-gray-50 text-slate cursor-not-allowed outline-none transition">
                        <p class="text-[10px] text-slate mt-1 italic">Phone numbers cannot be changed by administrators.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate mb-1">Access Role</label>
                        <select name="role" required
                            class="w-full px-4 py-2 border border-silver rounded-lg focus:ring-2 focus:ring-navy focus:border-transparent outline-none transition">
                            <option value="member" {{ $member->role == 'member' ? 'selected' : '' }}>Member</option>
                            <option value="admin" {{ $member->role == 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                    </div>
                </div>

                <div class="mt-8 flex gap-4">
                    <button type="submit" class="bg-navy text-white font-bold px-8 py-3 rounded-lg hover:bg-slate transition shadow-lg">
                        Update Patron
                    </button>
                    <a href="{{ route('admin.members.index') }}" class="bg-silver text-navy font-bold px-8 py-3 rounded-lg hover:bg-white transition">
                        Cancel
                    </a>
                </div>
            </form>

            <div class="mt-12 pt-8 border-t border-silver/20">
                <h3 class="text-xl font-bold text-navy mb-4">Recent Borrowing History</h3>
                <div class="space-y-3">
                    @forelse($history as $transaction)
                        <div class="flex justify-between items-center p-4 bg-noble-bg rounded-lg border border-silver/10">
                            <div>
                                <p class="text-sm font-bold text-navy">{{ $transaction->book->title }}</p>
                                <p class="text-[10px] text-slate uppercase">{{ $transaction->borrowed_at->format('d M Y, H:i') }}</p>
                            </div>
                            <span class="text-[10px] font-bold uppercase px-2 py-1 rounded {{ $transaction->status == 'returned' ? 'text-green-600 bg-green-50' : ($transaction->status == 'overdue' ? 'text-red-600 bg-red-50' : 'text-navy bg-navy/5') }}">
                                {{ $transaction->status }}
                            </span>
                        </div>
                    @empty
                        <p class="text-slate text-sm italic">No history found for this patron.</p>
                    @endforelse
                </div>
                @if($history->isNotEmpty())
                    <div class="mt-4 text-center">
                        <a href="{{ route('admin.transactions.index') }}" class="text-xs text-navy font-bold hover:underline">View All Library Logs →</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
