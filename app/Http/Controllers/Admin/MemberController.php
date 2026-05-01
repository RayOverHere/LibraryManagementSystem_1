<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $query = \App\Models\User::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        } else {
            // Default to showing only members unless filtered
            $query->where('role', 'member');
        }

        $members = $query->withCount('devices')->latest()->get();
        return view('admin.members.index', compact('members'));
    }

    public function edit(\App\Models\User $member)
    {
        $history = $member->transactions()->with('book')->latest()->take(10)->get();
        return view('admin.members.edit', compact('member', 'history'));
    }

    public function update(Request $request, \App\Models\User $member)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|in:admin,member',
        ]);

        $member->update($validated);

        return redirect()->route('admin.members.index')->with('success', 'Patron updated successfully. Identity fields (Email/Phone) remained unchanged.');
    }

    public function destroy(\App\Models\User $member)
    {
        // Check if user has active borrowings
        $hasActiveBorrowings = $member->transactions()->where('status', 'borrowed')->exists();

        if ($hasActiveBorrowings) {
            return redirect()->route('admin.members.index')->with('error', 'Cannot delete member: User still has borrowed books that must be returned.');
        }

        $member->delete();

        return redirect()->route('admin.members.index')->with('success', 'Member deleted successfully.');
    }
}
