<x-layout>
    <div class="py-10 px-4 sm:px-6 lg:px-8">
        <div class="max-w-6xl mx-auto">
            <div class="mb-6 flex items-center justify-between">
                <h1 class="text-2xl font-bold text-slate-800">Inventory Items</h1>
            </div>

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if($items->isEmpty())
                <div class="p-6 bg-white rounded-xl shadow-sm border border-gray-200 text-gray-600">
                    No inventory items found. Add some items first.
                </div>
            @else
                <div class="mb-4 flex flex-col gap-4">
                    <div class="flex flex-col sm:flex-row sm:items-center gap-4 sm:justify-between">
                        <p class="text-sm text-slate-600">Showing <span id="itemsStart">{{ $items->firstItem() ?? 0 }}</span> to <span id="itemsEnd">{{ $items->lastItem() ?? 0 }}</span> of <span id="itemsTotal">{{ $items->total() }}</span> items</p>
                        <div class="flex items-center gap-2">
                            <input type="text" id="searchInput" placeholder="Search items..." value="{{ $searchQuery ?? '' }}" class="px-3 py-2 rounded border border-gray-300 text-sm" />
                            @if($searchQuery)
                                <button onclick="clearSearch()" class="px-4 py-2 bg-gray-300 text-gray-700 text-sm font-medium rounded hover:bg-gray-400">Clear</button>
                            @endif
                        </div>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                        <form method="GET" action="{{ url()->current() }}" class="flex items-center gap-2">
                            <label for="per_page" class="text-sm text-slate-700">Items per page:</label>
                            <select id="per_page" name="per_page" onchange="this.form.submit()" class="px-3 py-1 rounded border border-gray-300">
                                <option value="5" {{ ($perPage ?? 5) == 5 ? 'selected' : '' }}>5</option>
                                <option value="10" {{ ($perPage ?? 5) == 10 ? 'selected' : '' }}>10</option>
                                <option value="100" {{ ($perPage ?? 5) == 100 ? 'selected' : '' }}>100</option>
                            </select>
                        </form>
                    </div>
                </div>
                <div class="overflow-x-auto rounded-xl shadow-sm border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200 bg-white text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 text-left font-semibold text-gray-600 whitespace-nowrap">Sr#</th>
                                <th class="px-3 py-2 text-left font-semibold text-gray-600 whitespace-nowrap">Item Description</th>
                                <th class="px-3 py-2 text-left font-semibold text-gray-600 whitespace-nowrap">Category</th>
                                <th class="px-3 py-2 text-left font-semibold text-gray-600 whitespace-nowrap">Supplier</th>
                                <th class="px-3 py-2 text-left font-semibold text-gray-600 whitespace-nowrap">Venue</th>
                                <th class="px-3 py-2 text-left font-semibold text-gray-600 whitespace-nowrap">Barcode</th>
                                <th class="px-3 py-2 text-right font-semibold text-gray-600 whitespace-nowrap">In</th>
                                <th class="px-3 py-2 text-right font-semibold text-gray-600 whitespace-nowrap">Out</th>
                                <th class="px-3 py-2 text-right font-semibold text-gray-600 whitespace-nowrap">Return</th>
                                <th class="px-3 py-2 text-right font-semibold text-gray-600 whitespace-nowrap">Current</th>
                                <th class="px-3 py-2 text-right font-semibold text-gray-600 whitespace-nowrap">Physical</th>
                                <th class="px-3 py-2 text-left font-semibold text-gray-600 whitespace-nowrap">Recon</th>
                                <th class="px-3 py-2 text-right font-semibold text-gray-600 whitespace-nowrap">Diff</th>
                                <th class="px-3 py-2 text-left font-semibold text-gray-600 whitespace-nowrap">Remarks</th>
                                <th class="px-3 py-2 text-center font-semibold text-gray-600 whitespace-nowrap">Status</th>
                                <th class="px-3 py-2 text-left font-semibold text-gray-600 whitespace-nowrap">Created</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100" id="itemsTableBody">
                            @foreach($items as $item)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-3 py-2 text-gray-800">{{ $item->sr_number }}</td>
                                    <td class="px-3 py-2 text-gray-800 line-clamp-2">{{ $item->item_description }}</td>
                                    <td class="px-3 py-2 text-gray-700">{{ $item->category_name ?? '–' }}</td>
                                    <td class="px-3 py-2 text-gray-800">{{ $item->supplier ?? '–' }}</td>
                                    <td class="px-3 py-2 text-gray-800">{{ $item->venue ?? '–' }}</td>
                                    <td class="px-3 py-2 text-gray-800">{{ $item->barcode ?? '–' }}</td>
                                    <td class="px-3 py-2 text-right text-gray-800">{{ $item->total_in ?? '–' }}</td>
                                    <td class="px-3 py-2 text-right text-gray-800">{{ $item->total_out ?? '–' }}</td>
                                    <td class="px-3 py-2 text-right text-gray-800">{{ $item->total_return ?? '–' }}</td>
                                    <td class="px-3 py-2 text-right text-gray-800 font-semibold">{{ $item->quantity_in_hand_current }}</td>
                                    <td class="px-3 py-2 text-right text-gray-800">{{ $item->physical_stock ?? '–' }}</td>
                                    <td class="px-3 py-2 text-gray-700">{{ $item->reconciliation ?? '–' }}</td>
                                    <td class="px-3 py-2 text-right text-gray-800">{{ $item->difference ?? '–' }}</td>
                                    <td class="px-3 py-2 text-gray-700 line-clamp-2">{{ $item->remarks ?? '–' }}</td>
                                    <td class="px-3 py-2 text-center">
                                        @php
                                            $isAvailable = ($item->physical_stock ?? 0) > 0 && $item->availability === 'available';
                                        @endphp
                                        <span class="inline-block px-2 py-1 text-xs font-medium rounded-full 
                                            {{ $isAvailable ? 'bg-green-100 text-green-800' : 
                                               ($item->availability == 'unavailable' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                            {{ $isAvailable ? 'Ava' : substr(ucfirst(str_replace('_', ' ', $item->availability)), 0, 3) }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-2 text-gray-600 whitespace-nowrap">{{ $item->created_at->format('m-d H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $items->appends(request()->except('page'))->links() }}
                </div>
            @endif
        </div>

        <div class="mt-10">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-2xl font-bold text-slate-800">Pending Borrow Requests</h2>
                <form method="GET" action="{{ url()->current() }}" class="flex items-center gap-2">
                    <label for="requests_per_page" class="text-sm text-slate-700">Per page:</label>
                    <select id="requests_per_page" name="requests_per_page" onchange="this.form.submit()" class="px-3 py-1 rounded border border-gray-300">
                        <option value="5" {{ ($requestsPerPage ?? 5) == 5 ? 'selected' : '' }}>5</option>
                        <option value="10" {{ ($requestsPerPage ?? 5) == 10 ? 'selected' : '' }}>10</option>
                        <option value="100" {{ ($requestsPerPage ?? 5) == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </form>
            </div>                              
            @if($borrowRequests->isEmpty())
                <div class="p-6 bg-white rounded-xl shadow-sm border border-gray-200 text-gray-600">
                    No pending borrow requests.
                </div>
            @else
                <div class="overflow-x-auto rounded-xl shadow-sm border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200 bg-white">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">User</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Item</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Description</th>
                                <th class="px-4 py-2 text-center text-xs font-semibold uppercase tracking-wide text-gray-500">Quantity</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Requested At</th>
                                <th class="px-4 py-2 text-center text-xs font-semibold uppercase tracking-wide text-gray-500">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($borrowRequests as $request)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-800">{{ $request->user->name }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-800">{{ $request->item_name }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ $request->item_description ?? '–' }}</td>
                                    <td class="px-4 py-3 text-center text-sm text-gray-800">{{ $request->quantity }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">{{ $request->created_at->format('Y-m-d H:i') }}</td>
                                    <td class="px-4 py-3 text-center space-x-2">
                                        <form action="{{ route('manager.borrow-request.approve', $request) }}" method="POST" class="inline-block">
                                            @csrf
                                            <button type="submit" class="px-2 py-1 text-xs font-medium rounded bg-green-500 text-white hover:bg-green-600">Approve</button>
                                        </form>
                                        <button type="button" onclick="showRejectModal({{ $request->id }})" class="px-2 py-1 text-xs font-medium rounded bg-red-500 text-white hover:bg-red-600">Reject</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $borrowRequests->appends(request()->except('requests_page'))->links() }}
                </div>
            @endif
        </div>

        <div class="mt-10">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-2xl font-bold text-slate-800">Currently Borrowed Items</h2>
                <form method="GET" action="{{ url()->current() }}" class="flex items-center gap-2">
                    <label for="borrowed_per_page" class="text-sm text-slate-700">Per page:</label>
                    <select id="borrowed_per_page" name="borrowed_per_page" onchange="this.form.submit()" class="px-3 py-1 rounded border border-gray-300">
                        <option value="5" {{ ($borrowedPerPage ?? 5) == 5 ? 'selected' : '' }}>5</option>
                        <option value="10" {{ ($borrowedPerPage ?? 5) == 10 ? 'selected' : '' }}>10</option>
                        <option value="100" {{ ($borrowedPerPage ?? 5) == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </form>
            </div>

            @if($borrowedItems->isEmpty())
                <div class="p-6 bg-white rounded-xl shadow-sm border border-gray-200 text-gray-600">
                    No items are currently borrowed.
                </div>
            @else
                <div class="overflow-x-auto rounded-xl shadow-sm border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200 bg-white">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">User</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Item</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Description</th>
                                <th class="px-4 py-2 text-center text-xs font-semibold uppercase tracking-wide text-gray-500">Quantity</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Borrowed At</th>
                                <th class="px-4 py-2 text-center text-xs font-semibold uppercase tracking-wide text-gray-500">Days Borrowed</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($borrowedItems as $borrowed)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-800">{{ $borrowed->user->name }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-800">{{ $borrowed->item_name }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ $borrowed->item_description ?? '–' }}</td>
                                    <td class="px-4 py-3 text-center text-sm text-gray-800">{{ $borrowed->count }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">{{ $borrowed->borrowed_at->format('Y-m-d H:i') }}</td>
                                    <td class="px-4 py-3 text-center text-sm">
                                        <span class="px-2 py-1 text-xs font-medium rounded-full 
                                            {{ now()->diffInDays($borrowed->borrowed_at) <= 7 ? 'bg-green-100 text-green-800' : 
                                               (now()->diffInDays($borrowed->borrowed_at) <= 30 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                            {{ now()->diffInDays($borrowed->borrowed_at) < 1 ? '<1 day' : now()->diffInDays($borrowed->borrowed_at) . ' days' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $borrowedItems->appends(request()->except('borrowed_page'))->links() }}
                </div>
            @endif
        </div>

    </div>

    <!-- Reject Modal -->
    <div id="rejectModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
        <div class="bg-white rounded-lg shadow-lg p-6 max-w-md w-full mx-4">
            <h2 class="text-lg font-bold text-slate-800 mb-4">Reject Borrow Request</h2>
            <form id="rejectForm" method="POST" action="">
                @csrf
                <div class="mb-4">
                    <label for="admin_notes" class="block text-sm font-medium text-gray-700">Reason (Optional)</label>
                    <textarea id="admin_notes" name="admin_notes" rows="3" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2"></textarea>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">Reject</button>
                    <button type="button" onclick="closeRejectModal()" class="flex-1 px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Reject Return Modal -->
    <div id="returnRejectModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
        <div class="bg-white rounded-lg shadow-lg p-6 max-w-md w-full mx-4">
            <h2 class="text-lg font-bold text-slate-800 mb-4">Reject Return Request</h2>
            <form id="returnRejectForm" method="POST" action="">
                @csrf
                <div class="mb-4">
                    <label for="admin_return_notes" class="block text-sm font-medium text-gray-700">Reason (Optional)</label>
                    <textarea id="admin_return_notes" name="admin_return_notes" rows="3" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2"></textarea>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">Reject</button>
                    <button type="button" onclick="closeReturnRejectModal()" class="flex-1 px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const SCROLL_KEY = 'managerScrollPos';

        function showRejectModal(requestId) {
            const modal = document.getElementById('rejectModal');
            const form = document.getElementById('rejectForm');
            form.action = `/manager/borrow-request/${requestId}/reject`;
            modal.classList.remove('hidden');
        }

        function closeRejectModal() {
            document.getElementById('rejectModal').classList.add('hidden');
        }

        function showReturnRejectModal(returnId) {
            const modal = document.getElementById('returnRejectModal');
            const form = document.getElementById('returnRejectForm');
            form.action = `/manager/return/${returnId}/reject`;
            modal.classList.remove('hidden');
        }

        function closeReturnRejectModal() {
            document.getElementById('returnRejectModal').classList.add('hidden');
        }

        function saveScrollPosition() {
            sessionStorage.setItem(SCROLL_KEY, window.scrollY);
        }

        document.addEventListener('DOMContentLoaded', () => {
            const savedScroll = sessionStorage.getItem(SCROLL_KEY);
            if (savedScroll !== null) {
                window.scrollTo(0, parseInt(savedScroll, 10));
                sessionStorage.removeItem(SCROLL_KEY);
            }

            document.querySelectorAll('form').forEach((form) => {
                form.addEventListener('submit', saveScrollPosition);
            });
        });

        // Auto-refresh manager dashboard so new user requests appear without manual reload
        // Store initial count
        let initialPendingRequests = {{ $borrowRequests->count() }};

        setInterval(() => {
            if (!document.hidden) {
                // Check for changes before reloading
                fetch('/manager/check-changes', {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.pending_requests !== initialPendingRequests) {
                        window.location.reload();
                    }
                })
                .catch(error => {
                    console.log('Error checking for changes:', error);
                });
            }
        }, 10000);

        // Real-time search functionality
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            let searchTimeout;
            searchInput.addEventListener('keyup', function() {
                clearTimeout(searchTimeout);
                const query = this.value.trim();
                
                searchTimeout = setTimeout(() => {
                    const perPage = new URLSearchParams(window.location.search).get('per_page') || '5';
                    
                    fetch(`/manager/search-items?search=${encodeURIComponent(query)}&per_page=${perPage}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        updateItemsTable(data.items);
                        document.getElementById('itemsStart').textContent = data.items.length > 0 ? 1 : 0;
                        document.getElementById('itemsEnd').textContent = data.items.length;
                        document.getElementById('itemsTotal').textContent = data.total;
                    })
                    .catch(error => console.error('Search error:', error));
                }, 300);
            });
        }

        function updateItemsTable(items) {
            const tbody = document.getElementById('itemsTableBody');
            if (!tbody) return;

            if (items.length === 0) {
                tbody.innerHTML = '<tr><td colspan="16" class="px-4 py-8 text-center text-gray-600">No items found</td></tr>';
                return;
            }

            tbody.innerHTML = items.map(item => {
                const isAvailable = (item.physical_stock ?? 0) > 0 && item.availability === 'available';
                const availabilityClass = isAvailable ? 'bg-green-100 text-green-800' : 
                    (item.availability === 'unavailable' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800');
                const availabilityText = isAvailable ? 'Ava' : item.availability.replace(/_/g, ' ').charAt(0).toUpperCase() + item.availability.replace(/_/g, ' ').slice(1);

                return `
                    <tr class="hover:bg-gray-50">
                        <td class="px-3 py-2 text-gray-800">${item.sr_number}</td>
                        <td class="px-3 py-2 text-gray-800 line-clamp-2">${item.item_description}</td>
                        <td class="px-3 py-2 text-gray-700">${item.category_name || '–'}</td>
                        <td class="px-3 py-2 text-gray-800">${item.supplier || '–'}</td>
                        <td class="px-3 py-2 text-gray-800">${item.venue || '–'}</td>
                        <td class="px-3 py-2 text-gray-800">${item.barcode || '–'}</td>
                        <td class="px-3 py-2 text-right text-gray-800">${item.total_in || '–'}</td>
                        <td class="px-3 py-2 text-right text-gray-800">${item.total_out || '–'}</td>
                        <td class="px-3 py-2 text-right text-gray-800">${item.total_return || '–'}</td>
                        <td class="px-3 py-2 text-right text-gray-800 font-semibold">${item.quantity_in_hand_current}</td>
                        <td class="px-3 py-2 text-right text-gray-800">${item.physical_stock || '–'}</td>
                        <td class="px-3 py-2 text-gray-700">${item.reconciliation || '–'}</td>
                        <td class="px-3 py-2 text-right text-gray-800">${item.difference || '–'}</td>
                        <td class="px-3 py-2 text-gray-700 line-clamp-2">${item.remarks || '–'}</td>
                        <td class="px-3 py-2 text-center">
                            <span class="inline-block px-2 py-1 text-xs font-medium rounded-full ${availabilityClass}">
                                ${availabilityText.substring(0, 3)}
                            </span>
                        </td>
                        <td class="px-3 py-2 text-gray-600 whitespace-nowrap">${new Date(item.created_at).toLocaleString('en-US', {month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit'})}</td>
                    </tr>
                `;
            }).join('');
        }

        function clearSearch() {
            document.getElementById('searchInput').value = '';
            const perPage = new URLSearchParams(window.location.search).get('per_page') || '5';
            window.location.href = `?per_page=${perPage}`;
        }
    </script>
</x-layout>