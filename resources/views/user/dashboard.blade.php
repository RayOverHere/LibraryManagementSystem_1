<x-app-layout>
    <div class="mb-10">
        <h1 class="text-3xl font-bold text-navy">Welcome, {{ auth()->user()->name }}</h1>
        <p class="text-slate italic">Your personal reading dashboard</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Currently Borrowed -->
        <div class="lg:col-span-2">
            <h2 class="text-xl font-bold text-navy mb-6 flex items-center gap-2">
                <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                Currently Borrowed
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @forelse($activeBorrows as $transaction)
                    <div class="bg-white p-6 rounded-xl border border-silver/30 shadow-sm flex flex-col">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-navy font-bold">{{ $transaction->book->title }}</h3>
                                @if($transaction->status === 'overdue')
                                    <span class="inline-flex items-center text-[10px] text-red-600 font-bold uppercase mt-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Overdue
                                    </span>
                                @endif
                            </div>
                            <span class="text-[10px] bg-navy/5 text-navy px-2 py-1 rounded font-bold uppercase">{{ $transaction->book->category->name ?? 'None' }}</span>
                        </div>
                        <p class="text-slate text-sm mb-4">By {{ $transaction->book->authors->pluck('name')->implode(', ') }}</p>
                        
                        <div class="mt-auto pt-4 border-t border-silver/10 flex justify-between items-center text-xs">
                            <span class="text-slate">Due Date:</span>
                            <span class="font-bold {{ $transaction->status === 'overdue' ? 'text-red-600' : 'text-navy' }}">
                                {{ $transaction->due_date->format('d M Y') }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-10 bg-white rounded-xl border border-dashed border-silver text-center">
                        <p class="text-slate text-sm">You haven't borrowed any books yet.</p>
                        <a href="{{ route('user.catalog') }}" class="text-navy font-bold text-sm mt-2 inline-block hover:underline">Browse Catalog</a>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- History -->
        <div>
            <h2 class="text-xl font-bold text-navy mb-6">Recent History</h2>
            <div class="space-y-4">
                @forelse($history as $item)
                    <div class="bg-white/50 p-4 rounded-lg border border-silver/20 flex items-center gap-4">
                        <div class="w-10 h-10 rounded-full bg-navy/5 flex items-center justify-center text-navy font-bold text-xs">
                            {{ substr($item->book->title, 0, 1) }}
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-navy">{{ $item->book->title }}</h4>
                            <p class="text-[10px] text-slate">
                                @if($item->status === 'returned')
                                    Returned on {{ $item->returned_at->format('d M Y, H:i') }}
                                @else
                                    <span class="text-red-600 font-bold uppercase">Marked Lost</span>
                                @endif
                            </p>
                        </div>
                    </div>
                @empty
                    <p class="text-slate text-sm italic">No past transactions found.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
