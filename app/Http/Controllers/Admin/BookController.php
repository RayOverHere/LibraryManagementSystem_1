<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    public function lookup($isbn)
    {
        $isbnKey = "ISBN:{$isbn}";
        $cacheKey = "book_lookup_{$isbn}";

        return \Illuminate\Support\Facades\Cache::remember($cacheKey, now()->addDay(), function () use ($isbnKey, $isbn) {
            $response = Http::get("https://openlibrary.org/api/books?bibkeys={$isbnKey}&format=json&jscmd=data");

            if ($response->successful() && isset($response[$isbnKey])) {
                $book = $response[$isbnKey];
                
                // Map subjects to our predefined categories
                $category = 'None';
                if (isset($book['subjects'])) {
                    foreach ($book['subjects'] as $subject) {
                        $name = strtolower($subject['name']);
                        if (preg_match('/science|physics|chemistry|biology|math|nature/i', $name)) { $category = 'Science'; break; }
                        if (preg_match('/history|archaeology|civilization/i', $name)) { $category = 'History'; break; }
                        if (preg_match('/biography|autobiography|memoir|portrait/i', $name)) { $category = 'Biography'; break; }
                        if (preg_match('/fiction|novel|literature|story|fantasy|thriller/i', $name)) { $category = 'Fiction'; break; }
                        if (preg_match('/non-fiction|essay|philosophy|politics|religion/i', $name)) { $category = 'Non-Fiction'; break; }
                    }
                }

                return [
                    'success' => true,
                    'title' => $book['title'] ?? '',
                    'author' => isset($book['authors']) ? collect($book['authors'])->pluck('name')->implode(', ') : '',
                    'category' => $category,
                ];
            }

            // If not found, return failure but don't cache long-term (or cache briefly)
            return ['success' => false, 'message' => 'Book not found.'];
        });
    }

    /**
     * Display a listing of books.
     * Uses eager loading + JOIN-based availability computation.
     */
    public function index()
    {
        $books = \App\Models\Book::with(['category', 'authors'])
                    ->withAvailability()
                    ->latest()
                    ->get();

        return view('admin.books.index', compact('books'));
    }

    public function create()
    {
        return view('admin.books.create');
    }

    /**
     * Store a newly created book.
     * Handles Category and Author relational data via firstOrCreate (normalized).
     * No 'available' column — computed dynamically via JOIN.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'isbn' => ['required', 'string', 'unique:books', 'regex:/^(?:\d{9}[\dXx]|\d{13})$/'],
            'category' => 'required|string',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'isbn.regex' => 'Please enter a valid ISBN-10 or ISBN-13 (numbers only, or ending in X for ISBN-10).',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('books', 'public');
            $validated['image'] = $path;
        }

        // Handle Category (Find or Create in normalized table)
        $category = \App\Models\Category::firstOrCreate(['name' => $validated['category']]);
        $validated['category_id'] = $category->id;
        
        // Remove non-column fields before mass assignment
        $authorString = $validated['author'];
        unset($validated['category'], $validated['author']);

        $book = \App\Models\Book::create($validated);

        // Handle Authors (Find or Create, then sync pivot table)
        $authors = array_filter(array_map('trim', explode(',', $authorString)));
        $authorIds = [];
        foreach ($authors as $authorName) {
            $author = \App\Models\Author::firstOrCreate(['name' => $authorName]);
            $authorIds[] = $author->id;
        }
        
        $book->authors()->sync($authorIds);

        return redirect()->route('admin.books.index')->with('success', 'Book added successfully.');
    }

    public function edit(\App\Models\Book $book)
    {
        $book->load(['category', 'authors']);
        return view('admin.books.edit', compact('book'));
    }

    /**
     * Update a book.
     * No 'available' adjustment needed — computed dynamically via JOIN.
     */
    public function update(Request $request, \App\Models\Book $book)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'isbn' => ['required', 'string', 'unique:books,isbn,' . $book->id, 'regex:/^(?:\d{9}[\dXx]|\d{13})$/'],
            'category' => 'required|string',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'isbn.regex' => 'Please enter a valid ISBN-10 or ISBN-13 (numbers only, or ending in X for ISBN-10).',
        ]);

        if ($request->hasFile('image')) {
            if ($book->image) {
                Storage::disk('public')->delete($book->image);
            }
            $path = $request->file('image')->store('books', 'public');
            $validated['image'] = $path;
        }

        // Handle Category
        $category = \App\Models\Category::firstOrCreate(['name' => $validated['category']]);
        $validated['category_id'] = $category->id;
        
        // Handle Authors string
        $authorString = $validated['author'];
        unset($validated['category'], $validated['author']);

        $book->update($validated);

        // Sync Authors
        $authors = array_filter(array_map('trim', explode(',', $authorString)));
        $authorIds = [];
        foreach ($authors as $authorName) {
            $author = \App\Models\Author::firstOrCreate(['name' => $authorName]);
            $authorIds[] = $author->id;
        }
        
        $book->authors()->sync($authorIds);

        return redirect()->route('admin.books.index')->with('success', 'Book updated successfully.');
    }

    public function destroy(\App\Models\Book $book)
    {
        if ($book->image) {
            Storage::disk('public')->delete($book->image);
        }
        $book->delete();
        return redirect()->route('admin.books.index')->with('success', 'Book deleted successfully.');
    }
}
