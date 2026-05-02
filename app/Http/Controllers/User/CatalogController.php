<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    /**
     * Display the book catalog with search and category filtering.
     * Uses eager loading and JOIN-based availability computation.
     */
    public function index(Request $request)
    {
        $query = \App\Models\Book::with(['category', 'authors'])
                    ->withAvailability();

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhereHas('authors', function($q2) use ($request) {
                      $q2->where('name', 'like', '%' . $request->search . '%');
                  });
            });
        }

        if ($request->filled('category')) {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('name', $request->category);
            });
        }

        $books = $query->latest()->paginate(12);
        return view('user.catalog.index', compact('books'));
    }

    /**
     * Borrow a book using the ProcessLoan stored procedure.
     * The procedure handles availability checking and transaction creation atomically.
     */
    public function borrow(Request $request, \App\Models\Book $book)
    {
        $request->validate([
            'due_date' => 'required|date|after:today',
        ]);

        try {
            \Illuminate\Support\Facades\DB::select('CALL ProcessLoan(?, ?, ?)', [
                auth()->id(),
                $book->id,
                $request->due_date
            ]);

            return redirect()->route('user.dashboard')->with('success', 'Book borrowed successfully until ' . $request->due_date);
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == '45000') {
                return back()->with('error', 'Book is currently unavailable.');
            }
            return back()->with('error', 'An error occurred while processing your request. Please try again.');
        }
    }
}
