<x-app-layout>
    <div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-navy">Borrowing Logs</h1>
            <p class="text-slate text-sm">Monitor books currently in circulation</p>
        </div>
        
        <form action="{{ route('admin.transactions.index') }}" method="GET" class="w-full md:w-auto flex flex-col sm:flex-row gap-3">
            <div class="relative flex-grow sm:min-w-[250px]">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name, title, or ISBN..."
                    class="w-full pl-4 pr-10 py-2 border border-silver rounded-xl focus:ring-2 focus:ring-navy outline-none transition text-sm">
                <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate hover:text-navy">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </button>
            </div>
            
            <select name="status" onchange="this.form.submit()" class="px-4 py-2 border border-silver rounded-xl focus:ring-2 focus:ring-navy outline-none transition text-sm bg-white">
                <option value="">All Status</option>
                <option value="borrowed" {{ request('status') == 'borrowed' ? 'selected' : '' }}>Borrowed</option>
                <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>Returned</option>
                <option value="lost" {{ request('status') == 'lost' ? 'selected' : '' }}>Lost</option>
            </select>

            <input type="date" name="date" value="{{ request('date') }}" onchange="this.form.submit()"
                class="px-4 py-2 border border-silver rounded-xl focus:ring-2 focus:ring-navy outline-none transition text-sm">
            
            @if(request()->anyFilled(['search', 'status', 'date']))
                <a href="{{ route('admin.transactions.index') }}" class="p-2 text-red-600 hover:bg-red-50 rounded-xl transition flex items-center justify-center" title="Clear Filters">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </a>
            @endif
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-lg border border-silver/30 overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-navy text-white">
                    <th class="px-4 md:px-6 py-4 text-[10px] md:text-sm font-bold uppercase tracking-widest">Book</th>
                    <th class="px-4 md:px-6 py-4 text-[10px] md:text-sm font-bold uppercase tracking-widest">Member</th>
                    <th class="px-4 md:px-6 py-4 text-[10px] md:text-sm font-bold uppercase tracking-widest whitespace-nowrap">Borrowed At</th>
                    <th class="px-4 md:px-6 py-4 text-[10px] md:text-sm font-bold uppercase tracking-widest text-center">Status</th>
                    <th class="px-4 md:px-6 py-4 text-[10px] md:text-sm font-bold uppercase tracking-widest text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-silver/20">
                @foreach($transactions as $transaction)
                    <tr class="hover:bg-noble-bg transition">
                        <td class="px-6 py-4">
                            <div class="font-bold text-navy">{{ $transaction->book->title }}</div>
                            <div class="text-[10px] text-slate uppercase">{{ $transaction->book->isbn }}</div>
                        </td>
                        <td class="px-6 py-4 text-slate">{{ $transaction->user->name }}</td>
                        <td class="px-6 py-4 text-slate text-sm">{{ $transaction->borrowed_at->format('d M Y, H:i') }}</td>
                        <td class="px-6 py-4 text-center">
                            @php
                                $statusColors = [
                                    'borrowed' => 'bg-blue-100 text-blue-700',
                                    'returned' => 'bg-green-100 text-green-700',
                                    'overdue' => 'bg-red-100 text-red-700',
                                    'lost' => 'bg-gray-100 text-gray-700 border border-gray-300',
                                ];
                            @endphp
                            <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase {{ $statusColors[$transaction->status] }}">
                                {{ $transaction->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <!-- Static View -->
                            <div id="static-{{ $transaction->id }}" class="flex items-center justify-end gap-4">
                                <div class="text-right">
                                    @if($transaction->notes)
                                        <p class="text-[10px] text-slate italic mb-1">"{{ $transaction->notes }}"</p>
                                    @endif
                                    <p class="text-[10px] text-navy font-bold uppercase">Due: {{ $transaction->due_date->format('d M Y') }}</p>
                                </div>
                                <button onclick="toggleEdit('{{ $transaction->id }}')" class="p-2 text-navy hover:text-slate transition" title="Edit Log">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                            </div>

                            <!-- Edit Form (Hidden by default) -->
                            <form id="edit-{{ $transaction->id }}" action="{{ route('admin.transactions.update', $transaction) }}" method="POST" class="hidden flex flex-col gap-2">
                                @csrf
                                @method('PUT')
                                
                                <div class="flex flex-col md:flex-row items-end gap-2">
                                    <div class="w-full md:w-32">
                                        <label class="block text-[8px] uppercase text-slate font-bold mb-1">Status</label>
                                        <select name="status" class="w-full text-[10px] border border-silver rounded px-2 py-1 outline-none bg-white">
                                            <option value="borrowed" {{ $transaction->status == 'borrowed' ? 'selected' : '' }}>Borrowed</option>
                                            <option value="overdue" {{ $transaction->status == 'overdue' ? 'selected' : '' }}>Overdue</option>
                                            <option value="returned" {{ $transaction->status == 'returned' ? 'selected' : '' }}>Returned</option>
                                            <option value="lost" {{ $transaction->status == 'lost' ? 'selected' : '' }}>Lost</option>
                                        </select>
                                    </div>

                                    <div class="w-full md:w-32">
                                        <label class="block text-[8px] uppercase text-slate font-bold mb-1">Due Date</label>
                                        <input type="date" name="due_date" value="{{ $transaction->due_date->format('Y-m-d') }}" 
                                            class="w-full text-[10px] border border-silver rounded px-2 py-1 outline-none">
                                    </div>

                                    <div class="flex-grow w-full md:min-w-[120px]">
                                        <label class="block text-[8px] uppercase text-slate font-bold mb-1">Notes</label>
                                        <input type="text" name="notes" value="{{ $transaction->notes }}" placeholder="Add note..."
                                            class="w-full text-[10px] border border-silver rounded px-2 py-1 outline-none italic">
                                    </div>

                                    <div class="flex gap-1">
                                        <button type="submit" class="bg-navy text-white p-2 rounded hover:bg-slate transition shadow-sm" title="Save Changes">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </button>
                                        <button type="button" onclick="toggleEdit('{{ $transaction->id }}')" class="bg-silver/20 text-navy p-2 rounded hover:bg-white transition" title="Cancel">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <script>
        function toggleEdit(id) {
            const staticView = document.getElementById(`static-${id}`);
            const editForm = document.getElementById(`edit-${id}`);
            
            if (editForm.classList.contains('hidden')) {
                editForm.classList.remove('hidden');
                staticView.classList.add('hidden');
            } else {
                editForm.classList.add('hidden');
                staticView.classList.remove('hidden');
            }
        }
    </script>
</x-app-layout>
