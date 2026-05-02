<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * Display all transactions with eager loading.
     * Uses a multi-table JOIN query via Eloquent's with() to load
     * related users, books, categories, and authors efficiently.
     */
    public function index(Request $request)
    {
        // Auto-mark overdue before viewing
        \App\Models\Transaction::where('status', 'borrowed')
            ->where('due_date', '<', now()->toDateString())
            ->update(['status' => 'overdue']);

        $query = \App\Models\Transaction::with(['user', 'book.category', 'book.authors']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($qu) use ($search) {
                    $qu->where('name', 'like', "%{$search}%");
                })->orWhereHas('book', function($qb) use ($search) {
                    $qb->where('title', 'like', "%{$search}%")
                       ->orWhere('isbn', 'like', "%{$search}%");
                });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date')) {
            $query->whereDate('borrowed_at', $request->date);
        }

        $transactions = $query->latest()->get();
        return view('admin.transactions.index', compact('transactions'));
    }

    /**
     * Update a transaction's status.
     *
     * Simplified logic — no more 'available' column to increment/decrement.
     * Only 'stock' is adjusted when a book transitions to/from 'lost' status
     * (since a lost book represents permanently reduced inventory).
     *
     * The CalculateLateFine trigger handles fine generation automatically
     * when returned_at is set.
     */
    public function update(Request $request, \App\Models\Transaction $transaction)
    {
        $validated = $request->validate([
            'status' => 'required|in:borrowed,returned,overdue,lost',
            'due_date' => 'nullable|date',
            'notes' => 'nullable|string|max:500',
        ]);

        $oldStatus = $transaction->status;
        $newStatus = $validated['status'];
        $book = $transaction->book;

        if ($oldStatus !== $newStatus) {
            // Handle stock changes for lost/found books only.
            // 'available' is no longer a stored column — it's computed via JOIN.
            if ($newStatus === 'lost' && $oldStatus !== 'lost') {
                // Book is lost — reduce total inventory
                $book->decrement('stock');
            } elseif ($oldStatus === 'lost' && $newStatus !== 'lost') {
                // Book was found — restore total inventory
                $book->increment('stock');
            }

            // Set return timestamp when marking as returned
            if ($newStatus === 'returned' && $oldStatus !== 'returned') {
                $validated['returned_at'] = now();
            } elseif ($oldStatus === 'returned' && $newStatus !== 'returned') {
                $validated['returned_at'] = null;
            }
        }

        $transaction->update($validated);

        return redirect()->back()->with('success', 'Transaction updated and inventory synchronized.');
    }
}
