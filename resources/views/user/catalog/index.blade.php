<x-app-layout>
    <div class="mb-10">
        <h1 class="text-3xl font-bold text-navy mb-2">Explore the Collection</h1>
        <p class="text-slate italic">Find your next great read</p>
    </div>

    <!-- Search & Filter -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-silver/30 mb-8">
        <form method="GET" action="{{ route('user.catalog') }}" class="flex flex-col md:flex-row gap-4">
            <div class="flex-grow">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by title or author..."
                    class="w-full px-4 py-2 border border-silver rounded-lg focus:ring-2 focus:ring-navy outline-none transition">
            </div>
            <div class="w-full md:w-48">
                <select name="category" onchange="this.form.submit()"
                    class="w-full px-4 py-2 border border-silver rounded-lg focus:ring-2 focus:ring-navy outline-none transition">
                    <option value="">All Categories</option>
                    <option value="None" {{ request('category') == 'None' ? 'selected' : '' }}>None</option>
                    <option value="Fiction" {{ request('category') == 'Fiction' ? 'selected' : '' }}>Fiction</option>
                    <option value="Non-Fiction" {{ request('category') == 'Non-Fiction' ? 'selected' : '' }}>Non-Fiction</option>
                    <option value="Science" {{ request('category') == 'Science' ? 'selected' : '' }}>Science</option>
                    <option value="History" {{ request('category') == 'History' ? 'selected' : '' }}>History</option>
                    <option value="Biography" {{ request('category') == 'Biography' ? 'selected' : '' }}>Biography</option>
                </select>
            </div>
            <button type="submit" class="w-full md:w-auto bg-navy text-white px-8 py-2 rounded-lg font-bold hover:bg-slate transition">
                Search
            </button>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @forelse($books as $book)
            <div class="bg-white rounded-xl shadow-md border border-silver/20 overflow-hidden flex flex-col hover:translate-y-[-4px] transition duration-300">
                <div class="h-64 bg-slate/10 flex items-center justify-center border-b border-silver/10 overflow-hidden">
                    @if($book->image)
                        <img src="{{ asset('storage/' . $book->image) }}" alt="{{ $book->title }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-2/3 h-4/5 border-2 border-navy/20 rounded flex items-center justify-center bg-white shadow-inner">
                            <span class="text-navy/30 font-bold uppercase text-[10px] tracking-widest">{{ $book->category }}</span>
                        </div>
                    @endif
                </div>
                <div class="p-6 flex-grow flex flex-col">
                    <span class="text-[10px] bg-navy/5 text-navy px-2 py-1 rounded font-bold uppercase w-fit mb-3">{{ $book->category }}</span>
                    <h3 class="text-navy font-bold text-lg mb-1 leading-tight">{{ $book->title }}</h3>
                    <p class="text-slate text-sm mb-4">{{ $book->author }}</p>
                    
                    <div class="mt-auto">
                        <div class="flex justify-between items-center mb-4">
                            <span class="text-xs font-bold {{ $book->available > 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $book->available > 0 ? $book->available . ' available' : 'Out of stock' }}
                            </span>
                        </div>
                        
                        @if($book->available > 0)
                            <form action="{{ route('user.borrow', $book) }}" method="POST" class="space-y-3">
                                @csrf
                                <div>
                                    <label class="block text-[10px] font-bold text-slate uppercase mb-1">Return Date</label>
                                    <input type="date" name="due_date" value="{{ now()->addDays(14)->format('Y-m-d') }}" required
                                        class="w-full px-3 py-1.5 text-xs border border-silver rounded focus:ring-1 focus:ring-navy outline-none">
                                </div>
                                <button type="submit" class="w-full bg-navy text-white text-sm font-bold py-2 rounded-lg hover:bg-slate transition">
                                    Borrow Now
                                </button>
                            </form>
                        @else
                            <button disabled class="w-full bg-silver text-white text-sm font-bold py-2 rounded-lg cursor-not-allowed">
                                Unavailable
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-20 bg-white rounded-xl border border-silver">
                <p class="text-slate italic">No books match your search.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $books->links() }}
    </div>
</x-app-layout>
