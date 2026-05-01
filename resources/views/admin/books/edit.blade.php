<x-app-layout>
    <div class="max-w-2xl mx-auto">
        <div class="bg-white p-8 rounded-xl shadow-2xl border border-silver/20">
            <h2 class="text-3xl font-bold text-navy mb-6">Edit Catalog Entry</h2>
            
            @if ($errors->any())
                <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-sm">
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <form method="POST" action="{{ route('admin.books.update', $book) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-slate mb-1">Book Title</label>
                        <input type="text" name="title" value="{{ old('title', $book->title) }}" required
                            class="w-full px-4 py-2 border border-silver rounded-lg focus:ring-2 focus:ring-navy focus:border-transparent outline-none transition">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate mb-1">Author</label>
                        <input type="text" name="author" value="{{ old('author', $book->author) }}" required
                            class="w-full px-4 py-2 border border-silver rounded-lg focus:ring-2 focus:ring-navy focus:border-transparent outline-none transition">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate mb-1">ISBN</label>
                        <input type="text" name="isbn" value="{{ old('isbn', $book->isbn) }}" required
                            class="w-full px-4 py-2 border border-silver rounded-lg focus:ring-2 focus:ring-navy focus:border-transparent outline-none transition">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate mb-1">Category</label>
                        <select name="category" required
                            class="w-full px-4 py-2 border border-silver rounded-lg focus:ring-2 focus:ring-navy focus:border-transparent outline-none transition">
                            <option value="None" {{ $book->category == 'None' ? 'selected' : '' }}>None</option>
                            <option value="Fiction" {{ $book->category == 'Fiction' ? 'selected' : '' }}>Fiction</option>
                            <option value="Non-Fiction" {{ $book->category == 'Non-Fiction' ? 'selected' : '' }}>Non-Fiction</option>
                            <option value="Science" {{ $book->category == 'Science' ? 'selected' : '' }}>Science</option>
                            <option value="History" {{ $book->category == 'History' ? 'selected' : '' }}>History</option>
                            <option value="Biography" {{ $book->category == 'Biography' ? 'selected' : '' }}>Biography</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate mb-1">Total Stock</label>
                        <input type="number" name="stock" value="{{ old('stock', $book->stock) }}" required min="1"
                            class="w-full px-4 py-2 border border-silver rounded-lg focus:ring-2 focus:ring-navy focus:border-transparent outline-none transition">
                    </div>

                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-slate mb-1">Book Cover Image</label>
                        @if($book->image)
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $book->image) }}" alt="{{ $book->title }}" class="h-32 rounded-lg border border-silver">
                            </div>
                        @endif
                        <input type="file" name="image" accept="image/*"
                            class="w-full px-4 py-2 border border-silver rounded-lg focus:ring-2 focus:ring-navy focus:border-transparent outline-none transition">
                    </div>
                </div>

                <div class="mt-8 flex gap-4">
                    <button type="submit" class="bg-navy text-white font-bold px-8 py-3 rounded-lg hover:bg-slate transition shadow-lg">
                        Update Entry
                    </button>
                    <a href="{{ route('admin.books.index') }}" class="bg-silver text-navy font-bold px-8 py-3 rounded-lg hover:bg-white transition">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
