<x-layout>
    <div class="py-10 px-4 sm:px-6 lg:px-8">
        <div class="max-w-6xl mx-auto">
            <div class="mb-6 flex items-center justify-between">
                <h1 class="text-2xl font-bold text-slate-800">Available Electrical Devices / Equipments</h1>
            </div>

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            @if(!$pendingRequests->isEmpty())
                <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <h2 class="text-sm font-semibold text-blue-800 mb-3">Pending Borrow Requests</h2>
                    <div class="space-y-2">
                        @foreach($pendingRequests as $req)
                            <div class="flex items-center justify-between bg-white p-3 rounded border border-blue-200">
                                <div>
                                    <p class="text-sm font-medium text-gray-800">{{ $req->item_name }}</p>
                                    <p class="text-xs text-gray-600">Requested on {{ $req->created_at->format('Y-m-d H:i') }}</p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">Pending Review</span>
                                    <form action="{{ route('users.borrow-request.cancel', $req) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to cancel this request?');">
                                        @csrf
                                        <button type="submit" class="px-3 py-1 text-xs font-medium rounded bg-red-100 text-red-700 hover:bg-red-200">Cancel</button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Available Items Accordion -->
            <div class="mb-6">
                <details class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden" open>
                    <summary class="px-6 py-4 bg-gray-50 hover:bg-gray-100 cursor-pointer font-semibold text-slate-800 border-b border-gray-200">
                        Available Items
                        <span class="float-right text-sm font-normal text-gray-600">Click to expand/collapse</span>
                    </summary>
                    <div class="p-6">
                        @if($items->isEmpty())
                            <div class="p-6 bg-gray-50 rounded-lg border border-gray-200 text-gray-600">
                                No inventory items are available right now.
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
                                        <input type="hidden" name="per_page" value="{{ $perPage ?? 10 }}" />
                                        <label for="location" class="text-sm text-slate-700">Location:</label>
                                        <select id="location" name="location" onchange="this.form.submit()" class="px-3 py-1 rounded border border-gray-300">
                                            <option value="">All</option>
                                            @foreach($locations as $location)
                                                <option value="{{ $location }}" {{ ($locationFilter ?? '') === $location ? 'selected' : '' }}>{{ $location }}</option>
                                            @endforeach
                                        </select>
                                    </form>
                                    <form method="GET" action="{{ url()->current() }}" class="flex items-center gap-2">
                                        <input type="hidden" name="location" value="{{ $locationFilter ?? '' }}" />
                                        <label for="per_page" class="text-sm text-slate-700">Items per page:</label>
                                        <select id="per_page" name="per_page" onchange="this.form.submit()" class="px-3 py-1 rounded border border-gray-300">
                                            <option value="5" {{ ($perPage ?? 10) == 5 ? 'selected' : '' }}>5</option>
                                            <option value="10" {{ ($perPage ?? 10) == 10 ? 'selected' : '' }}>10</option>
                                            <option value="100" {{ ($perPage ?? 10) == 100 ? 'selected' : '' }}>100</option>
                                        </select>
                                    </form>
                                </div>
                            </div>
                            <div class="overflow-x-auto rounded-xl shadow-sm border border-gray-200">
                                <table class="min-w-full divide-y divide-gray-200 bg-white">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Sr#</th>
                                            <th class="px-4 py-2 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Category</th>
                                            <th class="px-4 py-2 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Description</th>
                                            <th class="px-4 py-2 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Supplier</th>
                                            <th class="px-4 py-2 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Venue</th>
                                            <th class="px-4 py-2 text-center text-xs font-semibold uppercase tracking-wide text-gray-500">Quantity</th>
                                            <th class="px-4 py-2 text-center text-xs font-semibold uppercase tracking-wide text-gray-500">Availability</th>
                                            <th class="px-4 py-2 text-center text-xs font-semibold uppercase tracking-wide text-gray-500">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100" id="itemsTableBody">
                                        @foreach($items as $item)
                                            <tr>
                                                <td class="px-4 py-3 text-sm text-gray-800">{{ $item->sr_number }}</td>
                                                <td class="px-4 py-3 text-sm text-gray-800">{{ $item->category_name }}</td>
                                                <td class="px-4 py-3 text-sm text-gray-800">{{ $item->item_description }}</td>
                                                <td class="px-4 py-3 text-sm text-gray-800">{{ $item->supplier ?? '–' }}</td>
                                                <td class="px-4 py-3 text-sm text-gray-800">{{ $item->venue ?? '–' }}</td>
                                                <td class="px-4 py-3 text-center text-sm text-gray-800">{{ $item->physical_stock }}</td>
                                                <td class="px-4 py-3 text-center text-sm">
                                                    @php
                                                        $isAvailable = ($item->physical_stock ?? 0) > 0 && $item->availability === 'available';
                                                    @endphp
                                                    <span class="px-2 py-1 text-xs font-medium rounded-full 
                                                        {{ $isAvailable ? 'bg-green-100 text-green-800' : 
                                                           ($item->availability == 'unavailable' || $item->availability == 'out_of_stock' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                                        {{ $isAvailable ? 'Available' : ucfirst(str_replace('_', ' ', $item->availability)) }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    @php
                                                        $hasPendingRequest = in_array($item->sr_number, $pendingRequestItemIds ?? []);
                                                        $hasApprovedRequest = in_array($item->sr_number, $approvedRequestItemIds ?? []);
                                                    @endphp

                                                    @if($hasPendingRequest)
                                                        <span class="inline-flex items-center px-3 py-2 text-xs font-medium rounded bg-yellow-100 text-yellow-800">Pending Request</span>
                                                    @elseif($hasApprovedRequest)
                                                        <span class="inline-flex items-center px-3 py-2 text-xs font-medium rounded bg-blue-100 text-blue-800">Approved - Waiting Release</span>
                                                    @elseif(($item->physical_stock ?? 0) > 0 && $item->availability === 'available')
                                                        <button type="button" onclick="showBorrowModal({{ $item->sr_number }}, '{{ $item->item_description }}', {{ $item->quantity_in_hand_current }})" class="px-3 py-2 text-xs font-medium rounded bg-indigo-600 text-white hover:bg-indigo-700">Borrow Item</button>
                                                    @else
                                                        <span class="inline-flex items-center px-3 py-2 text-xs font-medium rounded bg-gray-200 text-gray-600">Unavailable</span>
                                                    @endif
                                                </td>
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
                </details>
            </div>

            <!-- Current Borrowed Items Accordion -->
            <div class="mb-6">
                <details class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <summary class="px-6 py-4 bg-gray-50 hover:bg-gray-100 cursor-pointer font-semibold text-slate-800 border-b border-gray-200">
                        Current Borrowed Items
                        <span class="float-right text-sm font-normal text-gray-600">Click to expand/collapse</span>
                    </summary>
                    <div class="p-6">
                        <div class="mb-4 flex items-center justify-between">
                            <form method="GET" action="{{ url()->current() }}" class="flex items-center gap-2">
                                <label for="current_per_page" class="text-sm text-slate-700">Per page:</label>
                                <select id="current_per_page" name="current_per_page" onchange="this.form.submit()" class="px-3 py-1 rounded border border-gray-300">
                                    <option value="5" {{ ($currentPerPage ?? 5) == 5 ? 'selected' : '' }}>5</option>
                                    <option value="10" {{ ($currentPerPage ?? 5) == 10 ? 'selected' : '' }}>10</option>
                                    <option value="100" {{ ($currentPerPage ?? 5) == 100 ? 'selected' : '' }}>100</option>
                                </select>
                            </form>
                        </div>

                        @if($currentBorrowed->isEmpty())
                            <div class="p-6 bg-gray-50 rounded-lg border border-gray-200 text-gray-600">
                                You have no items currently borrowed.
                            </div>
                        @else
                            <div class="overflow-x-auto rounded-xl shadow-sm border border-gray-200">
                                <table class="min-w-full divide-y divide-gray-200 bg-white">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Date</th>
                                            <th class="px-4 py-2 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Item</th>
                                            <th class="px-4 py-2 text-center text-xs font-semibold uppercase tracking-wide text-gray-500">Return Status</th>
                                            <th class="px-4 py-2 text-center text-xs font-semibold uppercase tracking-wide text-gray-500">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        @foreach($currentBorrowed as $entry)
                                            <tr>
                                                <td class="px-4 py-3 text-sm text-gray-800">{{ $entry->borrowed_at->format('Y-m-d H:i') }}</td>
                                                <td class="px-4 py-3 text-sm text-gray-800">{{ $entry->item_description ?? '–' }}</td>
                                                <td class="px-4 py-3 text-center">
                                                    @if($entry->return_status === 'pending')
                                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">Pending Approval</span>
                                                    @elseif($entry->return_status === 'approved')
                                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Approved</span>
                                                    @elseif($entry->return_status === 'rejected')
                                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">Rejected</span>
                                                    @else
                                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">—</span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    @if($entry->return_status !== 'pending')
                                                        <form action="{{ route('users.borrow-history.return', $entry) }}" method="POST" class="inline-block">
                                                            @csrf
                                                            <button type="submit" class="px-3 py-2 text-xs font-medium rounded bg-yellow-500 text-white hover:bg-yellow-600">Return Item</button>
                                                        </form>
                                                    @else
                                                        <span class="text-xs text-gray-500">Awaiting approval</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-4">
                                {{ $currentBorrowed->appends(request()->except('current_page', 'current_per_page'))->links() }}
                            </div>
                        @endif
                    </div>
                </details>
            </div>

            <!-- Borrow History Accordion -->
            <div class="mb-6">
                <details class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <summary class="px-6 py-4 bg-gray-50 hover:bg-gray-100 cursor-pointer font-semibold text-slate-800 border-b border-gray-200">
                        Borrow History
                        <span class="float-right text-sm font-normal text-gray-600">Click to expand/collapse</span>
                    </summary>
                    <div class="p-6">
                        <div class="mb-4 flex items-center justify-between">
                            <form method="GET" action="{{ url()->current() }}" class="flex items-center gap-2">
                                <label for="history_per_page" class="text-sm text-slate-700">Per page:</label>
                                <select id="history_per_page" name="history_per_page" onchange="this.form.submit()" class="px-3 py-1 rounded border border-gray-300">
                                    <option value="5" {{ ($historyPerPage ?? 5) == 5 ? 'selected' : '' }}>5</option>
                                    <option value="10" {{ ($historyPerPage ?? 5) == 10 ? 'selected' : '' }}>10</option>
                                    <option value="100" {{ ($historyPerPage ?? 5) == 100 ? 'selected' : '' }}>100</option>
                                </select>
                            </form>
                        </div>

                        @if($borrowHistory->isEmpty())
                            <div class="p-6 bg-gray-50 rounded-lg border border-gray-200 text-gray-600">
                                You have not returned any items yet.
                            </div>
                        @else
                            <div class="overflow-x-auto rounded-xl shadow-sm border border-gray-200">
                                <table class="min-w-full divide-y divide-gray-200 bg-white">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Date</th>
                                            <th class="px-4 py-2 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Item</th>
                                            <th class="px-4 py-2 text-center text-xs font-semibold uppercase tracking-wide text-gray-500">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        @foreach($borrowHistory as $entry)
                                            <tr>
                                                <td class="px-4 py-3 text-sm text-gray-800">{{ $entry->borrowed_at->format('Y-m-d H:i') }}</td>
                                                <td class="px-4 py-3 text-sm text-gray-800">{{ $entry->item_description ?? '–' }}</td>
                                                <td class="px-4 py-3 text-center">
                                                    @if($entry->returned_at)
                                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Returned Successfully</span>
                                                    @elseif($entry->return_status === 'rejected')
                                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">Rejected - Out of Stock</span>
                                                    @else
                                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">—</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-4">
                                {{ $borrowHistory->appends(request()->except('history_page', 'history_per_page'))->links() }}
                            </div>
                        @endif
                    </div>
                </details>
            </div>
        </div>
    </div>
        </div>
    </div>

    <!-- Borrow Modal -->
    <div id="borrowModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg p-6 max-w-md w-full mx-4">
            <h2 class="text-lg font-bold text-slate-800 mb-4">Borrow Item</h2>
            <form id="borrowForm" method="POST" action="">
                @csrf
                <div class="mb-4">
                    <p class="text-sm text-gray-700 mb-2"><strong>Item:</strong> <span id="borrowItemName"></span></p>
                    <div>
                        <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">Quantity</label>
                        <div class="flex items-center gap-2">
                            <input type="number" id="quantity" name="quantity" min="1" value="1" class="flex-1 px-3 py-2 rounded-md border border-gray-300" />
                            <span id="maxQuantity" class="text-sm text-gray-600">/ items available</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">Maximum available: <span id="maxQuantityDisplay"></span></p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Borrow</button>
                    <button type="button" onclick="closeBorrowModal()" class="flex-1 px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const USER_SCROLL_KEY = 'userScrollPos';

        // Real-time search functionality
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            let searchTimeout;
            searchInput.addEventListener('keyup', function() {
                clearTimeout(searchTimeout);
                const query = this.value.trim();
                
                searchTimeout = setTimeout(() => {
                    const locationFilter = new URLSearchParams(window.location.search).get('location') || '';
                    const perPage = new URLSearchParams(window.location.search).get('per_page') || '10';
                    
                    fetch(`/users/search-items?search=${encodeURIComponent(query)}&location=${encodeURIComponent(locationFilter)}&per_page=${perPage}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        updateItemsTable(data.items, data.pending_item_ids, data.approved_item_ids);
                        document.getElementById('itemsStart').textContent = data.items.length > 0 ? 1 : 0;
                        document.getElementById('itemsEnd').textContent = data.items.length;
                        document.getElementById('itemsTotal').textContent = data.total;
                    })
                    .catch(error => console.error('Search error:', error));
                }, 300);
            });
        }

        function updateItemsTable(items, pendingIds, approvedIds) {
            const tbody = document.getElementById('itemsTableBody');
            if (!tbody) return;

            if (items.length === 0) {
                tbody.innerHTML = '<tr><td colspan="8" class="px-4 py-8 text-center text-gray-600">No items found</td></tr>';
                return;
            }

            tbody.innerHTML = items.map(item => {
                const hasPending = pendingIds.includes(item.sr_number);
                const hasApproved = approvedIds.includes(item.sr_number);
                const isAvailable = (item.physical_stock ?? 0) > 0 && item.availability === 'available';
                
                let actionHtml = '';
                if (hasPending) {
                    actionHtml = '<span class="inline-flex items-center px-3 py-2 text-xs font-medium rounded bg-yellow-100 text-yellow-800">Pending Request</span>';
                } else if (hasApproved) {
                    actionHtml = '<span class="inline-flex items-center px-3 py-2 text-xs font-medium rounded bg-blue-100 text-blue-800">Approved - Waiting Release</span>';
                } else if (isAvailable) {
                    actionHtml = `<button type="button" onclick="showBorrowModal(${item.sr_number}, '${item.item_description.replace(/'/g, "\\'")}', ${item.quantity_in_hand_current})" class="px-3 py-2 text-xs font-medium rounded bg-indigo-600 text-white hover:bg-indigo-700">Borrow Item</button>`;
                } else {
                    actionHtml = '<span class="inline-flex items-center px-3 py-2 text-xs font-medium rounded bg-gray-200 text-gray-600">Unavailable</span>';
                }

                const availabilityClass = isAvailable ? 'bg-green-100 text-green-800' : 
                    (item.availability === 'unavailable' || item.availability === 'out_of_stock' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800');
                const availabilityText = isAvailable ? 'Available' : item.availability.replace(/_/g, ' ').charAt(0).toUpperCase() + item.availability.replace(/_/g, ' ').slice(1);

                return `
                    <tr>
                        <td class="px-4 py-3 text-sm text-gray-800">${item.sr_number}</td>
                        <td class="px-4 py-3 text-sm text-gray-800">${item.category_name}</td>
                        <td class="px-4 py-3 text-sm text-gray-800">${item.item_description}</td>
                        <td class="px-4 py-3 text-sm text-gray-800">${item.supplier || '–'}</td>
                        <td class="px-4 py-3 text-sm text-gray-800">${item.venue || '–'}</td>
                        <td class="px-4 py-3 text-center text-sm text-gray-800">${item.physical_stock}</td>
                        <td class="px-4 py-3 text-center text-sm">
                            <span class="px-2 py-1 text-xs font-medium rounded-full ${availabilityClass}">
                                ${availabilityText}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            ${actionHtml}
                        </td>
                    </tr>
                `;
            }).join('');
        }

        function clearSearch() {
            document.getElementById('searchInput').value = '';
            const locationFilter = new URLSearchParams(window.location.search).get('location') || '';
            const perPage = new URLSearchParams(window.location.search).get('per_page') || '10';
            window.location.href = `?per_page=${perPage}&location=${locationFilter}`;
        }

        function showBorrowModal(itemId, itemName, maxQuantity) {
            const modal = document.getElementById('borrowModal');
            const form = document.getElementById('borrowForm');
            const quantityInput = document.getElementById('quantity');
            const maxQuantitySpan = document.getElementById('maxQuantityDisplay');
            const itemNameSpan = document.getElementById('borrowItemName');

            form.action = `/users/item/${itemId}/borrow`;
            itemNameSpan.textContent = itemName;
            quantityInput.max = maxQuantity;
            quantityInput.value = 1;
            maxQuantitySpan.textContent = maxQuantity;

            modal.classList.remove('hidden');
        }

        function closeBorrowModal() {
            document.getElementById('borrowModal').classList.add('hidden');
        }

        // Accordion functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Add smooth transitions to accordion details
            const details = document.querySelectorAll('details');
            details.forEach(detail => {
                detail.addEventListener('toggle', function() {
                    // Optional: Add any additional behavior when accordion opens/closes
                    console.log('Accordion toggled:', this.querySelector('summary').textContent.trim());
                });
            });

            // Scroll position functionality
            function saveUserScrollPosition() {
                sessionStorage.setItem(USER_SCROLL_KEY, window.scrollY);
            }

            const storedScroll = sessionStorage.getItem(USER_SCROLL_KEY);
            if (storedScroll !== null) {
                window.scrollTo(0, parseInt(storedScroll, 10));
                sessionStorage.removeItem(USER_SCROLL_KEY);
            }

            document.querySelectorAll('form').forEach((form) => {
                form.addEventListener('submit', saveUserScrollPosition);
            });

            @if($pendingRequests->isNotEmpty())
                // Store initial counts
                let initialPendingRequests = {{ $pendingRequests->count() }};
                let initialApprovedRequests = {{ $approvedRequestItemIds ? count($approvedRequestItemIds) : 0 }};

                setInterval(() => {
                    if (!document.hidden) {
                        // Check for changes before reloading
                        fetch('/users/check-changes', {
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.pending_requests !== initialPendingRequests ||
                                data.approved_requests !== initialApprovedRequests) {
                                window.location.reload();
                            }
                        })
                        .catch(error => {
                            console.log('Error checking for changes:', error);
                        });
                    }
                }, 8000);
            @endif
        });
    </script>

    <style>
        /* Custom accordion styles */
        details {
            transition: all 0.3s ease;
        }

        details summary {
            list-style: none;
            position: relative;
        }

        details summary::-webkit-details-marker {
            display: none;
        }

        details summary::after {
            content: '▼';
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            font-size: 0.75rem;
            color: #6b7280;
            transition: transform 0.3s ease;
        }

        details[open] summary::after {
            transform: translateY(-50%) rotate(180deg);
        }

        details summary:hover {
            background-color: #f9fafb !important;
        }

        /* Smooth animation for accordion content */
        details > div {
            animation: slideDown 0.3s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</x-layout>