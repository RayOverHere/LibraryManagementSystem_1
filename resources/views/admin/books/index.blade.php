<x-app-layout>
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-bold text-navy">Library Catalog</h1>
            <p class="text-slate text-sm">Manage your collection of literature</p>
        </div>
        <a href="{{ route('admin.books.create') }}" class="w-full sm:w-auto text-center bg-navy text-white px-6 py-3 rounded-lg font-bold hover:bg-slate transition shadow-md">
            + Add New Book
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($books as $book)
            <div class="bg-white rounded-xl shadow-lg border border-silver/30 overflow-hidden hover:shadow-2xl transition duration-300">
                <div class="h-32 bg-navy flex items-center justify-center relative overflow-hidden">
                    @if($book->image)
                        <img src="{{ asset('storage/' . $book->image) }}" alt="{{ $book->title }}" class="absolute inset-0 w-full h-full object-cover opacity-60">
                    @endif
                    <div class="text-center relative z-10 p-4">
                        <span class="text-silver text-xs uppercase tracking-widest">{{ $book->category->name ?? 'None' }}</span>
                        <h3 class="text-white font-bold text-lg leading-tight">{{ $book->title }}</h3>
                    </div>
                </div>
                <div class="p-6">
                    <p class="text-slate text-sm mb-2">By <span class="text-navy font-medium">{{ $book->authors->pluck('name')->implode(', ') }}</span></p>
                    <p class="text-slate text-xs mb-4">ISBN: {{ $book->isbn }}</p>
                    
                    <div class="flex justify-between items-center mb-6">
                        <div class="text-center">
                            <span class="block text-xl font-bold text-navy">{{ $book->stock }}</span>
                            <span class="text-[10px] text-slate uppercase">Total</span>
                        </div>
                        <div class="text-center">
                            <span class="block text-xl font-bold {{ $book->available > 0 ? 'text-green-600' : 'text-red-600' }}">{{ $book->available }}</span>
                            <span class="text-[10px] text-slate uppercase">Available</span>
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <a href="{{ route('admin.books.edit', $book) }}" class="flex-1 text-center py-2 border border-silver rounded-lg text-sm font-medium hover:bg-silver/10 transition">
                            Edit
                        </a>
                        <button type="button" onclick="openDeleteModal('{{ route('admin.books.destroy', $book) }}')" 
                            class="flex-1 text-center py-2 border border-red-200 rounded-lg text-sm font-medium text-red-600 hover:bg-red-50 transition">
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-20 bg-white rounded-xl border border-dashed border-silver">
                <p class="text-slate italic">No books found in the collection.</p>
            </div>
        @endforelse
    </div>
    <!-- Custom Delete Modal -->
    <div id="deleteModal" class="fixed inset-0 z-[100] hidden">
        <div class="absolute inset-0 bg-navy/60 backdrop-blur-sm"></div>
        <div class="absolute inset-0 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-2xl max-w-sm w-full p-8 transform transition-all border border-silver/20">
                <div class="w-16 h-16 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-navy text-center mb-2">Remove from Catalog?</h3>
                <p class="text-slate text-center text-sm mb-8">This action cannot be undone. The book will be permanently removed from the library system.</p>
                <div class="flex gap-4">
                    <button onclick="closeDeleteModal()" class="flex-1 px-4 py-2 border border-silver rounded-lg font-bold text-navy hover:bg-noble-bg transition">
                        Cancel
                    </button>
                    <form id="deleteForm" method="POST" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg font-bold hover:bg-red-700 transition shadow-lg shadow-red-200">
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openDeleteModal(action) {
            document.getElementById('deleteForm').action = action;
            document.getElementById('deleteModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
    </script>
</x-app-layout>
