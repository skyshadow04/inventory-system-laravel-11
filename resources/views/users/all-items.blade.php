<x-layout>
    <div class="py-10 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">All Inventory Items</h1>
                    <p class="text-sm text-slate-600">Browse every item across all locations and submit a borrow request.</p>
                    <p class="text-sm text-slate-500 mt-1">Requests are routed to the manager responsible for the item location.</p>
                </div>
                <div>
                    <a href="{{ route('users') }}" class="inline-flex items-center justify-center rounded-md bg-gray-200 px-4 py-2 text-sm font-semibold text-slate-800 shadow-sm hover:bg-gray-300">Back to Dashboard</a>
                </div>
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

            <div class="mb-6 p-6 bg-white rounded-xl shadow-sm border border-gray-200">
                <form method="GET" action="{{ route('users.all-items') }}" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4 items-end">
                    <div>
                        <label for="search" class="block text-sm font-medium text-slate-700">Search</label>
                        <input type="text" id="search" name="search" value="{{ $searchQuery ?? '' }}" placeholder="Search items..." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
                    </div>

                    <div>
                        <label for="location" class="block text-sm font-medium text-slate-700">Location</label>
                        <select id="location" name="location" class="mt-1 block w-full rounded-md border-gray-300 bg-white py-2 px-3 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm">
                            <option value="">All</option>
                            @foreach($locations as $location)
                                <option value="{{ $location }}" {{ ($locationFilter ?? '') === $location ? 'selected' : '' }}>{{ $location }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="venue" class="block text-sm font-medium text-slate-700">Venue</label>
                        <select id="venue" name="venue" class="mt-1 block w-full rounded-md border-gray-300 bg-white py-2 px-3 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm">
                            <option value="">All</option>
                            @foreach($venues as $venue)
                                <option value="{{ $venue }}" {{ ($venueFilter ?? '') === $venue ? 'selected' : '' }}>{{ $venue }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="per_page" class="block text-sm font-medium text-slate-700">Items per page</label>
                        <select id="per_page" name="per_page" class="mt-1 block w-full rounded-md border-gray-300 bg-white py-2 px-3 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm">
                            <option value="5" {{ ($perPage ?? 10) == 5 ? 'selected' : '' }}>5</option>
                            <option value="10" {{ ($perPage ?? 10) == 10 ? 'selected' : '' }}>10</option>
                            <option value="100" {{ ($perPage ?? 10) == 100 ? 'selected' : '' }}>100</option>
                        </select>
                    </div>

                    <div class="sm:col-span-2 lg:col-span-4 flex justify-end">
                        <button type="submit" class="inline-flex items-center justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Apply filters</button>
                    </div>
                </form>
            </div>

            <div class="overflow-x-auto rounded-xl shadow-sm border border-gray-200 bg-white">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Sr#</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Category</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Description</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Location</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Venue / Remarks</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-500">Quantity</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-500">Availability</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-500">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($items as $item)
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-800">{{ $item->sr_number }}</td>
                                <td class="px-4 py-3 text-sm text-gray-800">{{ $item->category_name }}</td>
                                <td class="px-4 py-3 text-sm text-gray-800">{{ $item->item_description }}</td>
                                <td class="px-4 py-3 text-sm text-gray-800">{{ $item->location ?? '–' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-800 max-w-xs truncate" title="{{ $item->venue ?? $item->remarks ?? '–' }}">
                                    @if($item->venue)
                                        <span class="block font-medium">{{ $item->venue }}</span>
                                        <span class="text-xs text-slate-500">{{ $item->supplier ?? '–' }}</span>
                                    @elseif($item->remarks)
                                        {{ $item->remarks }}
                                    @else
                                        –
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center text-sm text-gray-800">{{ $item->physical_stock }}</td>
                                <td class="px-4 py-3 text-center text-sm">
                                    @php $isAvailable = ($item->physical_stock ?? 0) > 0; @endphp
                                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $isAvailable ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $isAvailable ? 'Available' : 'Out of Stock' }}
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
                                    @elseif($isAvailable)
                                        <button type="button" onclick="showBorrowModal('{{ $item->sr_number }}', {{ json_encode($item->item_description) }}, {{ $item->physical_stock }})" class="px-3 py-2 text-xs font-medium rounded bg-indigo-600 text-white hover:bg-indigo-700">Borrow</button>
                                    @else
                                        <span class="inline-flex items-center px-3 py-2 text-xs font-medium rounded bg-gray-200 text-gray-600">Out of Stock</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-10 text-center text-sm text-gray-600">No inventory items match your filters.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $items->appends(request()->except('page'))->links() }}
            </div>
        </div>
    </div>

    <!-- Borrow Modal -->
    <div id="borrowModal" class="hidden fixed inset-0 bg-black bg-opacity-50 items-center justify-center z-50" onclick="closeBorrowModal()">
        <div class="bg-white rounded-lg shadow-lg p-6 max-w-md w-full mx-4" onclick="event.stopPropagation()">
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
        const borrowRouteBase = "{{ url('users/all-items/item') }}";

        function showBorrowModal(itemId, itemName, maxQuantity) {
            const modal = document.getElementById('borrowModal');
            const form = document.getElementById('borrowForm');
            const quantityInput = document.getElementById('quantity');
            const maxQuantitySpan = document.getElementById('maxQuantityDisplay');
            const itemNameSpan = document.getElementById('borrowItemName');

            form.action = `${borrowRouteBase}/${encodeURIComponent(itemId)}/borrow`;
            itemNameSpan.textContent = itemName;
            quantityInput.max = maxQuantity;
            quantityInput.value = 1;
            maxQuantitySpan.textContent = maxQuantity;

            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeBorrowModal() {
            const modal = document.getElementById('borrowModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        document.getElementById('borrowForm').addEventListener('submit', function() {
            closeBorrowModal();
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !document.getElementById('borrowModal').classList.contains('hidden')) {
                closeBorrowModal();
            }
        });
    </script>
</x-layout>
