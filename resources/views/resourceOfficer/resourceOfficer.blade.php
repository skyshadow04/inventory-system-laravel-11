<x-layout>
    <div class="py-10 px-3 sm:px-6 lg:px-8">
        <div class="w-full">
            <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <h1 class="text-2xl font-bold text-slate-800">Inventory Management</h1>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('resource-officer.form') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700">Add Items</a>
                    <a href="{{ route('resource-officer.import') }}" class="inline-flex items-center px-4 py-2 bg-slate-100 text-slate-800 rounded-lg shadow hover:bg-slate-200">Import Items</a>
                    <!-- <a href="{{ route('resource-officer.borrow-history.export') }}" class="inline-flex items-center px-4 py-2 bg-slate-800 text-white rounded-lg shadow hover:bg-slate-900">Export Borrow History</a> -->
                </div>
            </div>

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if($items->isEmpty())
                <div class="p-6 bg-white rounded-xl shadow-sm border border-gray-200 text-gray-600">
                    No inventory items found.
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
                            <input type="hidden" name="per_page" value="{{ $perPage ?? 5 }}" />
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
                                <option value="5" {{ ($perPage ?? 5) == 5 ? 'selected' : '' }}>5</option>
                                <option value="10" {{ ($perPage ?? 5) == 10 ? 'selected' : '' }}>10</option>
                                <option value="100" {{ ($perPage ?? 5) == 100 ? 'selected' : '' }}>100</option>
                            </select>
                        </form>
                    </div>
                </div>
                <div class="rounded-xl shadow-sm border border-gray-200 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 bg-white text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <!-- <th class="px-3 py-2 text-left font-semibold text-gray-600 whitespace-nowrap">#</th> -->
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
                                <th class="px-3 py-2 text-center font-semibold text-gray-600 whitespace-nowrap">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100" id="itemsTableBody">
                            @foreach($items as $item)
                                <tr class="hover:bg-gray-50">
                                    <!-- <td class="px-3 py-2 text-gray-800">{{ $items->firstItem() + $loop->index }}</td> -->
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
                                    <td class="px-3 py-2 text-center space-x-1">
                                        <a href="{{ route('resource-officer.item.edit', $item) }}" class="px-2 py-1 text-xs font-medium rounded bg-blue-500 text-white hover:bg-blue-600 inline-block">Edit</a>
                                        <form action="{{ route('resource-officer.item.destroy', $item) }}" method="POST" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" onclick="return confirm('Delete this item?')" class="px-2 py-1 text-xs font-medium rounded bg-red-500 text-white hover:bg-red-600">Del</button>
                                        </form>
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

            <div class="mt-10">
                <div class="mb-4 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-slate-800">Approved Borrow Requests</h2>
                        <p class="text-sm text-slate-600">These requests were approved by manager and are waiting for resource officer release.</p>
                    </div>
                    <form method="GET" action="{{ url()->current() }}" class="flex items-center gap-2">
                        <label for="requests_per_page" class="text-sm text-slate-700">Per page:</label>
                        <select id="requests_per_page" name="requests_per_page" onchange="this.form.submit()" class="px-3 py-1 rounded border border-gray-300">
                            <option value="5" {{ ($requestsPerPage ?? 5) == 5 ? 'selected' : '' }}>5</option>
                            <option value="10" {{ ($requestsPerPage ?? 5) == 10 ? 'selected' : '' }}>10</option>
                            <option value="100" {{ ($requestsPerPage ?? 5) == 100 ? 'selected' : '' }}>100</option>
                        </select>
                    </form>
                </div>

                @if($approvedRequests->isEmpty())
                    <div class="p-6 bg-white rounded-xl shadow-sm border border-gray-200 text-gray-600">
                        No approved borrow requests awaiting release.
                    </div>
                @else
                    <div class="rounded-xl shadow-sm border border-gray-200 overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 bg-white text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-left font-semibold text-gray-600 whitespace-nowrap">User</th>
                                    <th class="px-3 py-2 text-left font-semibold text-gray-600 whitespace-nowrap">Item</th>
                                    <th class="px-3 py-2 text-left font-semibold text-gray-600 whitespace-nowrap">Description</th>
                                    <th class="px-3 py-2 text-center font-semibold text-gray-600 whitespace-nowrap">Qty</th>
                                    <th class="px-3 py-2 text-left font-semibold text-gray-600 whitespace-nowrap">Requested</th>
                                    <th class="px-3 py-2 text-center font-semibold text-gray-600 whitespace-nowrap">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($approvedRequests as $request)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-3 py-2 text-gray-800">{{ $request->user->name }}</td>
                                        <td class="px-3 py-2 text-gray-800">{{ $request->item_name }}</td>
                                        <td class="px-3 py-2 text-gray-700 line-clamp-2">{{ $request->item_description ?? '–' }}</td>
                                        <td class="px-3 py-2 text-center text-gray-800">{{ $request->quantity }}</td>
                                        <td class="px-3 py-2 text-gray-600 whitespace-nowrap">{{ $request->created_at->format('m-d H:i') }}</td>
                                        <td class="px-3 py-2 text-center space-x-1">
                                            <span class="inline-block px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">Approved</span>
                                            <form action="{{ route('resource-officer.borrow-request.release', $request) }}" method="POST" class="inline-block">
                                                @csrf
                                                <button type="submit" class="px-2 py-1 text-xs font-medium rounded bg-indigo-600 text-white hover:bg-indigo-700">Release</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $approvedRequests->appends(request()->except('requests_page'))->links() }}
                    </div>
                @endif
            </div>

            <div class="mt-10">
                <div class="mb-4 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-slate-800">Currently Borrowed Items</h2>
                        <p class="text-sm text-slate-600">Items currently out on loan that the resource officer can track.</p>
                    </div>
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
                    <div class="p-6 bg-white rounded-xl shadow-sm border border-gray-200 text-gray-600">
                        There are no items currently borrowed.
                    </div>
                @else
                    <div class="rounded-xl shadow-sm border border-gray-200 overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 bg-white text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-left font-semibold text-gray-600 whitespace-nowrap">User</th>
                                    <th class="px-3 py-2 text-left font-semibold text-gray-600 whitespace-nowrap">Item</th>
                                    <th class="px-3 py-2 text-left font-semibold text-gray-600 whitespace-nowrap">Description</th>
                                    <th class="px-3 py-2 text-center font-semibold text-gray-600 whitespace-nowrap">Qty</th>
                                    <th class="px-3 py-2 text-left font-semibold text-gray-600 whitespace-nowrap">Borrowed</th>
                                    <th class="px-3 py-2 text-center font-semibold text-gray-600 whitespace-nowrap">Return Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($currentBorrowed as $borrowed)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-3 py-2 text-gray-800">{{ $borrowed->user->name }}</td>
                                        <td class="px-3 py-2 text-gray-800">{{ $borrowed->item_name }}</td>
                                        <td class="px-3 py-2 text-gray-700 line-clamp-2">{{ $borrowed->item_description ?? '–' }}</td>
                                        <td class="px-3 py-2 text-center text-gray-800">{{ $borrowed->count }}</td>
                                        <td class="px-3 py-2 text-gray-600 whitespace-nowrap">{{ $borrowed->borrowed_at->format('m-d H:i') }}</td>
                                        <td class="px-3 py-2 text-center">
                                            @if($borrowed->return_status === 'pending')
                                                <span class="inline-block px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                                            @elseif($borrowed->return_status === 'approved')
                                                <span class="inline-block px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Returned</span>
                                            @elseif($borrowed->return_status === 'rejected')
                                                <span class="inline-block px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">Rejected</span>
                                            @else
                                                <span class="inline-block px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">Active</span>
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

            <div class="mt-10">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-2xl font-bold text-slate-800">Pending Return Requests</h2>
                    <form method="GET" action="{{ url()->current() }}" class="flex items-center gap-2">
                        <label for="returns_per_page" class="text-sm text-slate-700">Per page:</label>
                        <select id="returns_per_page" name="returns_per_page" onchange="this.form.submit()" class="px-3 py-1 rounded border border-gray-300">
                            <option value="5" {{ ($returnsPerPage ?? 5) == 5 ? 'selected' : '' }}>5</option>
                            <option value="10" {{ ($returnsPerPage ?? 5) == 10 ? 'selected' : '' }}>10</option>
                            <option value="100" {{ ($returnsPerPage ?? 5) == 100 ? 'selected' : '' }}>100</option>
                        </select>
                    </form>
                </div>

                @if($pendingReturns->isEmpty())
                    <div class="p-6 bg-white rounded-xl shadow-sm border border-gray-200 text-gray-600">
                        No pending return requests.
                    </div>
                @else
                    <div class="rounded-xl shadow-sm border border-gray-200 overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 bg-white text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-left font-semibold text-gray-600 whitespace-nowrap">User</th>
                                    <th class="px-3 py-2 text-left font-semibold text-gray-600 whitespace-nowrap">Item</th>
                                    <th class="px-3 py-2 text-left font-semibold text-gray-600 whitespace-nowrap">Description</th>
                                    <th class="px-3 py-2 text-center font-semibold text-gray-600 whitespace-nowrap">Qty</th>
                                    <th class="px-3 py-2 text-left font-semibold text-gray-600 whitespace-nowrap">Borrowed</th>
                                    <th class="px-3 py-2 text-left font-semibold text-gray-600 whitespace-nowrap">Return Req</th>
                                    <th class="px-3 py-2 text-center font-semibold text-gray-600 whitespace-nowrap">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($pendingReturns as $return)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-3 py-2 text-gray-800">{{ $return->user->name }}</td>
                                        <td class="px-3 py-2 text-gray-800">{{ $return->item_name }}</td>
                                        <td class="px-3 py-2 text-gray-700 line-clamp-2">{{ $return->item_description ?? '–' }}</td>
                                        <td class="px-3 py-2 text-center text-gray-800">{{ $return->count }}</td>
                                        <td class="px-3 py-2 text-gray-600 whitespace-nowrap">{{ $return->borrowed_at->format('m-d H:i') }}</td>
                                        <td class="px-3 py-2 text-gray-600 whitespace-nowrap">{{ $return->return_requested_at->format('m-d H:i') }}</td>
                                        <td class="px-3 py-2 text-center space-x-1">
                                            <form action="{{ route('resource-officer.return.approve', $return) }}" method="POST" class="inline-block">
                                                @csrf
                                                <button type="submit" class="px-2 py-1 text-xs font-medium rounded bg-green-500 text-white hover:bg-green-600">OK</button>
                                            </form>
                                            <form action="{{ route('resource-officer.return.reject', $return) }}" method="POST" class="inline-block">
                                                @csrf
                                                <button type="submit" class="px-2 py-1 text-xs font-medium rounded bg-red-500 text-white hover:bg-red-600">Rej</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $pendingReturns->appends(request()->except('returns_page'))->links() }}
                    </div>
                @endif
            </div>

            <div class="mt-10">
                <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <h2 class="text-2xl font-bold text-slate-800">Borrow History</h2>
                    <div class="flex flex-wrap items-center gap-2">
                        <a href="{{ route('resource-officer.borrow-history.export') }}" class="inline-flex items-center px-4 py-2 bg-yellow-100 text-black rounded-lg shadow hover:bg-slate-900">Export History</a>
                        <form method="GET" action="{{ url()->current() }}" class="flex items-center gap-2">
                            <label for="history_per_page" class="text-sm text-slate-700">Per page:</label>
                            <select id="history_per_page" name="history_per_page" onchange="this.form.submit()" class="px-3 py-1 rounded border border-gray-300">
                                <option value="5" {{ ($historyPerPage ?? 5) == 5 ? 'selected' : '' }}>5</option>
                                <option value="10" {{ ($historyPerPage ?? 5) == 10 ? 'selected' : '' }}>10</option>
                                <option value="100" {{ ($historyPerPage ?? 5) == 100 ? 'selected' : '' }}>100</option>
                            </select>
                        </form>
                    </div>
                </div>

                @if($borrowHistory->isEmpty())
                    <div class="p-6 bg-white rounded-xl shadow-sm border border-gray-200 text-gray-600">
                        No borrow history available.
                    </div>
                @else
                    <div class="rounded-xl shadow-sm border border-gray-200 overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 bg-white text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-left font-semibold text-gray-600 whitespace-nowrap">User</th>
                                    <th class="px-3 py-2 text-left font-semibold text-gray-600 whitespace-nowrap">Item</th>
                                    <th class="px-3 py-2 text-left font-semibold text-gray-600 whitespace-nowrap">Description</th>
                                    <th class="px-3 py-2 text-center font-semibold text-gray-600 whitespace-nowrap">Qty</th>
                                    <th class="px-3 py-2 text-left font-semibold text-gray-600 whitespace-nowrap">Borrowed</th>
                                    <th class="px-3 py-2 text-left font-semibold text-gray-600 whitespace-nowrap">Returned</th>
                                    <th class="px-3 py-2 text-center font-semibold text-gray-600 whitespace-nowrap">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($borrowHistory as $history)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-3 py-2 text-gray-800">{{ $history->user->name }}</td>
                                        <td class="px-3 py-2 text-gray-800">{{ $history->item_name }}</td>
                                        <td class="px-3 py-2 text-gray-700 line-clamp-2">{{ $history->item_description ?? '–' }}</td>
                                        <td class="px-3 py-2 text-center text-gray-800">{{ $history->count }}</td>
                                        <td class="px-3 py-2 text-gray-600 whitespace-nowrap">{{ $history->borrowed_at->format('m-d H:i') }}</td>
                                        <td class="px-3 py-2 text-gray-600 whitespace-nowrap">{{ $history->returned_at->format('m-d H:i') }}</td>
                                        <td class="px-3 py-2 text-center">
                                            @if($history->return_status === 'approved')
                                                <span class="inline-block px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Returned</span>
                                            @elseif($history->return_status === 'rejected')
                                                <span class="inline-block px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">Rejected</span>
                                            @else
                                                <span class="inline-block px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">Completed</span>
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
        </div>
    </div>

    <script>
        const RESOURCE_OFFICER_SCROLL_KEY = 'resourceOfficerScrollPos';

        function saveScrollPosition() {
            sessionStorage.setItem(RESOURCE_OFFICER_SCROLL_KEY, window.scrollY);
        }

        function restoreScrollPosition() {
            const storedScroll = sessionStorage.getItem(RESOURCE_OFFICER_SCROLL_KEY);
            if (storedScroll !== null) {
                window.scrollTo(0, parseInt(storedScroll, 10));
                sessionStorage.removeItem(RESOURCE_OFFICER_SCROLL_KEY);
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            // Restore scroll position on page load
            restoreScrollPosition();

            // Save scroll position before form submission
            document.querySelectorAll('form').forEach((form) => {
                form.addEventListener('submit', saveScrollPosition);
            });

            // Auto-refresh functionality for dynamic content
            @if(!$approvedRequests->isEmpty() || !$pendingReturns->isEmpty())
                // Store initial counts
                let initialApprovedRequests = {{ $approvedRequests->count() }};
                let initialPendingReturns = {{ $pendingReturns->count() }};

                setInterval(() => {
                    if (!document.hidden) {
                        // Check for changes before reloading
                        fetch('/resource-officer/check-changes', {
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.approved_requests !== initialApprovedRequests ||
                                data.pending_returns !== initialPendingReturns) {
                                window.location.reload();
                            }
                        })
                        .catch(error => {
                            console.log('Error checking for changes:', error);
                        });
                    }
                }, 10000); // Check every 10 seconds if there are pending items
            @endif

            // Real-time search functionality
            const searchInput = document.getElementById('searchInput');
            if (searchInput) {
                let searchTimeout;
                searchInput.addEventListener('keyup', function() {
                    clearTimeout(searchTimeout);
                    const query = this.value.trim();
                    
                    searchTimeout = setTimeout(() => {
                        const locationFilter = new URLSearchParams(window.location.search).get('location') || '';
                        const perPage = new URLSearchParams(window.location.search).get('per_page') || '5';
                        
                        fetch(`/resource-officer/search-items?search=${encodeURIComponent(query)}&location=${encodeURIComponent(locationFilter)}&per_page=${perPage}`, {
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
                    tbody.innerHTML = '<tr><td colspan="17" class="px-4 py-8 text-center text-gray-600">No items found</td></tr>';
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
                            <td class="px-3 py-2 text-center space-x-1">
                                <a href="/resource-officer/item/${item.sr_number}/edit" class="text-blue-600 hover:underline">Edit</a>
                                <form action="/resource-officer/item/${item.sr_number}" method="POST" class="inline" onsubmit="return confirm('Delete this item?')">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <input type="hidden" name="_token" value="${document.querySelector('meta[name=\"csrf-token\"]').getAttribute('content')}">
                                    <button type="submit" class="text-red-600 hover:underline">Delete</button>
                                </form>
                            </td>
                        </tr>
                    `;
                }).join('');
            }

            function clearSearch() {
                document.getElementById('searchInput').value = '';
                const locationFilter = new URLSearchParams(window.location.search).get('location') || '';
                const perPage = new URLSearchParams(window.location.search).get('per_page') || '5';
                window.location.href = `?per_page=${perPage}&location=${locationFilter}`;
            }
        });
    </script>
</x-layout>