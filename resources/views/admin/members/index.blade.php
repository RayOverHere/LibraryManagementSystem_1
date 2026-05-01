<x-app-layout>
    <div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-navy">Member Management</h1>
            <p class="text-slate text-sm">View and manage library patrons</p>
        </div>

        <form action="{{ route('admin.members.index') }}" method="GET" class="w-full md:w-auto flex flex-col sm:flex-row gap-3">
            <div class="relative flex-grow sm:min-w-[250px]">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name, email, or phone..."
                    class="w-full pl-4 pr-10 py-2 border border-silver rounded-xl focus:ring-2 focus:ring-navy outline-none transition text-sm">
                <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate hover:text-navy">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </button>
            </div>
            
            <select name="role" onchange="this.form.submit()" class="px-4 py-2 border border-silver rounded-xl focus:ring-2 focus:ring-navy outline-none transition text-sm bg-white">
                <option value="member" {{ request('role') == 'member' ? 'selected' : '' }}>Members Only</option>
                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admins Only</option>
            </select>

            @if(request()->anyFilled(['search', 'role']))
                <a href="{{ route('admin.members.index') }}" class="p-2 text-red-600 hover:bg-red-50 rounded-xl transition flex items-center justify-center" title="Clear Filters">
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
                    <th class="px-4 md:px-6 py-4 text-[10px] md:text-sm font-bold uppercase tracking-widest">Name</th>
                    <th class="px-4 md:px-6 py-4 text-[10px] md:text-sm font-bold uppercase tracking-widest">Email</th>
                    <th class="px-4 md:px-6 py-4 text-[10px] md:text-sm font-bold uppercase tracking-widest">Phone</th>
                    <th class="px-4 md:px-6 py-4 text-[10px] md:text-sm font-bold uppercase tracking-widest text-center whitespace-nowrap">Active Tokens</th>
                    <th class="px-4 md:px-6 py-4 text-[10px] md:text-sm font-bold uppercase tracking-widest text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-silver/20">
                @foreach($members as $member)
                    <tr class="hover:bg-noble-bg transition">
                        <td class="px-6 py-4 font-medium text-navy">{{ $member->name }}</td>
                        <td class="px-6 py-4 text-slate">{{ $member->email }}</td>
                        <td class="px-6 py-4 text-slate text-sm font-mono">{{ $member->phone ?: 'N/A' }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-3 py-1 bg-navy/5 text-navy border border-navy/10 rounded-full text-[10px] font-bold uppercase">
                                {{ $member->devices_count }} Tokens
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right flex justify-end gap-3">
                            <a href="{{ route('admin.members.edit', $member) }}" class="text-navy font-bold hover:text-slate transition text-sm">Edit</a>
                            <button type="button" onclick="openDeleteModal('{{ route('admin.members.destroy', $member) }}')" 
                                class="text-red-600 font-bold hover:text-red-800 transition text-sm">
                                Delete
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
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
                <h3 class="text-xl font-bold text-navy text-center mb-2">Revoke Membership?</h3>
                <p class="text-slate text-center text-sm mb-8">This will permanently remove the user and their access. Ensure they have no outstanding borrowings.</p>
                <div class="flex gap-4">
                    <button onclick="closeDeleteModal()" class="flex-1 px-4 py-2 border border-silver rounded-lg font-bold text-navy hover:bg-noble-bg transition text-sm">
                        Cancel
                    </button>
                    <form id="deleteForm" method="POST" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg font-bold hover:bg-red-700 transition shadow-lg shadow-red-200 text-sm">
                            Remove
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
