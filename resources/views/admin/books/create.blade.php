<x-app-layout>
    <div class="max-w-2xl mx-auto">
        <div class="bg-white p-6 sm:p-8 rounded-2xl shadow-xl border border-silver/20">
            <h2 class="text-2xl sm:text-3xl font-bold text-navy mb-6">Catalog Entry</h2>
            
            @if ($errors->any())
                <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-sm">
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <form method="POST" action="{{ route('admin.books.store') }}" enctype="multipart/form-data">
                @csrf
 
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-slate mb-1">Book Title</label>
                        <input type="text" name="title" id="title" value="{{ old('title') }}" required
                            class="w-full px-4 py-3 border border-silver rounded-xl focus:ring-2 focus:ring-navy focus:border-transparent outline-none transition shadow-sm">
                    </div>
 
                    <div class="col-span-1">
                        <label class="block text-sm font-medium text-slate mb-1">Author</label>
                        <input type="text" name="author" id="author" value="{{ old('author') }}" required
                            class="w-full px-4 py-3 border border-silver rounded-xl focus:ring-2 focus:ring-navy focus:border-transparent outline-none transition shadow-sm">
                    </div>
 
                    <div class="col-span-1">
                        <label class="block text-sm font-medium text-slate mb-1">ISBN Identifier</label>
                        <div class="relative flex items-center">
                            <input type="text" name="isbn" id="isbn" value="{{ old('isbn') }}" required placeholder="e.g. 978..."
                                class="w-full pl-4 pr-14 py-3 border border-silver rounded-xl focus:ring-2 focus:ring-navy focus:border-transparent outline-none transition bg-white shadow-sm">
                            <button type="button" onclick="fetchBookDetails()" id="fetchBtn"
                                class="absolute right-2 p-2 bg-navy text-white rounded-lg hover:bg-slate transition-all shadow-md flex items-center justify-center group">
                                <svg xmlns="http://www.w3.org/2000/svg" id="searchIcon" class="h-5 w-5 group-hover:scale-110 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                <svg xmlns="http://www.w3.org/2000/svg" id="loadingIcon" class="h-5 w-5 animate-spin hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                            </button>
                        </div>
                        <p id="lookupStatus" class="text-[10px] mt-1 hidden font-bold"></p>
                    </div>
 
                    <div class="col-span-1">
                        <label class="block text-sm font-medium text-slate mb-1">Category</label>
                        <select name="category" id="category" required
                            class="w-full px-4 py-3 border border-silver rounded-xl focus:ring-2 focus:ring-navy focus:border-transparent outline-none transition shadow-sm appearance-none bg-white">
                            <option value="None" selected>None</option>
                            <option value="Fiction">Fiction</option>
                            <option value="Non-Fiction">Non-Fiction</option>
                            <option value="Science">Science</option>
                            <option value="History">History</option>
                            <option value="Biography">Biography</option>
                        </select>
                    </div>
 
                    <div class="col-span-1">
                        <label class="block text-sm font-medium text-slate mb-1">Total Stock</label>
                        <input type="number" name="stock" value="{{ old('stock', 1) }}" required min="1"
                            class="w-full px-4 py-3 border border-silver rounded-xl focus:ring-2 focus:ring-navy focus:border-transparent outline-none transition shadow-sm">
                    </div>
 
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-slate mb-1">Book Cover Image</label>
                        <div class="mt-1 flex items-center justify-center px-6 pt-5 pb-6 border-2 border-silver border-dashed rounded-xl hover:border-navy transition">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-silver" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-slate">
                                    <label for="image" class="relative cursor-pointer bg-white rounded-md font-medium text-navy hover:text-slate focus-within:outline-none">
                                        <span>Upload a file</span>
                                        <input id="image" name="image" type="file" class="sr-only" accept="image/*">
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-slate">PNG, JPG, GIF up to 2MB</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-10 flex flex-col sm:flex-row gap-4">
                    <button type="submit" class="w-full sm:w-auto bg-navy text-white font-bold px-10 py-4 rounded-xl hover:bg-slate transition shadow-lg text-lg">
                        Add to Catalog
                    </button>
                    <a href="{{ route('admin.books.index') }}" class="w-full sm:w-auto text-center bg-silver/20 text-navy font-bold px-10 py-4 rounded-xl hover:bg-white transition text-lg border border-silver/10">
                        Cancel
                    </a>
                </div>
            </form>

            <script>
                async function fetchBookDetails() {
                    const isbn = document.getElementById('isbn').value.trim().replace(/-/g, '');
                    const btn = document.getElementById('fetchBtn');
                    const searchIcon = document.getElementById('searchIcon');
                    const loadingIcon = document.getElementById('loadingIcon');
                    const status = document.getElementById('lookupStatus');
                    
                    // Client-side ISBN validation (10 or 13 digits)
                    const isbn10 = /^\d{9}[\dXx]$/;
                    const isbn13 = /^\d{13}$/;

                    if (!isbn10.test(isbn) && !isbn13.test(isbn)) {
                        status.innerText = '✗ Invalid format. Use 10 or 13 digits.';
                        status.classList.remove('hidden');
                        status.classList.add('text-red-500');
                        return;
                    }

                    btn.disabled = true;
                    searchIcon.classList.add('hidden');
                    loadingIcon.classList.remove('hidden');
                    
                    status.innerText = 'Searching Open Library...';
                    status.classList.remove('hidden', 'text-red-500', 'text-green-600');
                    status.classList.add('text-navy');

                    try {
                        const response = await fetch(`/admin/books/lookup/${isbn}`);
                        const data = await response.json();

                        if (data.success) {
                            document.getElementById('title').value = data.title;
                            document.getElementById('author').value = data.author;
                            document.getElementById('category').value = data.category;
                            status.innerText = '✓ Catalog data synchronized!';
                            status.classList.replace('text-navy', 'text-green-600');
                        } else {
                            status.innerText = '✗ Not found in Open Library. Manual entry required.';
                            status.classList.replace('text-navy', 'text-red-500');
                        }
                    } catch (error) {
                        status.innerText = '✗ Connection to Open Library failed.';
                        status.classList.replace('text-navy', 'text-red-500');
                    } finally {
                        btn.disabled = false;
                        searchIcon.classList.remove('hidden');
                        loadingIcon.classList.add('hidden');
                    }
                }
            </script>
        </div>
    </div>
</x-app-layout>
