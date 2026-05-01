<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        // Auto-mark overdue before viewing
        \App\Models\Transaction::where('status', 'borrowed')
            ->where('due_date', '<', now()->toDateString())
            ->update(['status' => 'overdue']);

        $query = \App\Models\Transaction::with(['user', 'book']);

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
            // Handle Stock and Available transitions
            if (($oldStatus === 'borrowed' || $oldStatus === 'overdue') && $newStatus === 'returned') {
                $book->increment('available');
            } elseif (($oldStatus === 'borrowed' || $oldStatus === 'overdue') && $newStatus === 'lost') {
                $book->decrement('stock');
            } elseif ($oldStatus === 'returned' && ($newStatus === 'borrowed' || $newStatus === 'overdue')) {
                $book->decrement('available');
            } elseif ($oldStatus === 'returned' && $newStatus === 'lost') {
                $book->decrement('stock');
                $book->decrement('available');
            } elseif ($oldStatus === 'lost' && ($newStatus === 'borrowed' || $newStatus === 'overdue')) {
                $book->increment('stock');
            } elseif ($oldStatus === 'lost' && $newStatus === 'returned') {
                $book->increment('stock');
                $book->increment('available');
            }

            // Set return date if returning
            if ($newStatus === 'returned' && $oldStatus !== 'returned') {
                $transaction->returned_at = now();
            } elseif ($oldStatus === 'returned' && $newStatus !== 'returned') {
                $transaction->returned_at = null;
            }
        }

        $transaction->update($validated);

        return redirect()->back()->with('success', 'Transaction updated and inventory synchronized.');
    }
}
