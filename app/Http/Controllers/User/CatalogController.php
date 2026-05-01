<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index(Request $request)
    {
        $query = \App\Models\Book::query();

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('author', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $books = $query->latest()->paginate(12);
        return view('user.catalog.index', compact('books'));
    }

    public function borrow(Request $request, \App\Models\Book $book)
    {
        $request->validate([
            'due_date' => 'required|date|after:today',
        ]);

        try {
            return \Illuminate\Support\Facades\DB::transaction(function () use ($request, $book) {
                // Lock the record for update to prevent race conditions
                $book = \App\Models\Book::where('id', $book->id)->lockForUpdate()->first();

                if ($book->available <= 0) {
                    return back()->with('error', 'Book is currently unavailable.');
                }

                // Check if user already has an active borrow for this book
                $exists = \App\Models\Transaction::where('user_id', auth()->id())
                    ->where('book_id', $book->id)
                    ->where('status', 'borrowed')
                    ->exists();

                if ($exists) {
                    return back()->with('error', 'You have already borrowed this book.');
                }

                \App\Models\Transaction::create([
                    'user_id' => auth()->id(),
                    'book_id' => $book->id,
                    'borrowed_at' => now(),
                    'due_date' => $request->due_date,
                    'status' => 'borrowed',
                ]);

                $book->decrement('available');

                return redirect()->route('user.dashboard')->with('success', 'Book borrowed successfully until ' . $request->due_date);
            });
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred while processing your request. Please try again.');
        }
    }
}
