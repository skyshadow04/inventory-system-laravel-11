<?php

namespace App\Http\Controllers;

use App\Models\BorrowHistory;
use App\Models\BorrowRequest;
use App\Models\ElectricalItem;
use App\Models\EngineeringItem;
use App\Models\InstrumentItem;
use App\Models\Item;
use App\Models\MechanicalItem;
use App\Models\OperationItem;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Schema;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10);
        $currentPerPage = $request->query('current_per_page', 5);
        $historyPerPage = $request->query('history_per_page', 5);
        $currentBorrowedPage = $request->query('current_page', 1);
        $historyPage = $request->query('history_page', 1);
        $locationFilter = $request->query('location');
        $venueFilter = $request->query('venue');
        $searchQuery = $request->query('search');

        // Get user's group and determine which model/table to query
        $user = auth()->user();
        $userGroup = $user->user_group ?? 'APP'; // Default to APP if no group set

        // Map user groups to their respective item models and allowed locations
        $groupModelMapping = [
            'APP' => [Item::class, ['APP']],
            'Engineering' => [EngineeringItem::class, ['Engg / INS']],
            'Mechanical' => [MechanicalItem::class, ['ENGG / MEC']],
            'Operations' => [OperationItem::class, ['OPTNS']],
            'Electrical' => [ElectricalItem::class, ['Electrical']],
            'Instrument' => [EngineeringItem::class, ['Engg / INS']],
        ];

        [$itemModel, $allowedLocations] = $groupModelMapping[$userGroup] ?? [Item::class, ['APP']];

        // Get locations and venues filtered by user's allowed locations
        $locations = $itemModel::whereNotNull('location')
            ->where('location', '!=', '')
            ->whereIn('location', $allowedLocations)
            ->distinct()
            ->orderBy('location')
            ->pluck('location');

        // Check if the table has a venue column before querying
        $tableName = (new $itemModel())->getTable();
        $hasVenueColumn = Schema::hasColumn($tableName, 'venue');

        $venues = collect();
        if ($hasVenueColumn) {
            $venues = $itemModel::whereNotNull('venue')
                ->where('venue', '!=', '')
                ->whereIn('location', $allowedLocations)
                ->distinct()
                ->orderBy('venue')
                ->pluck('venue');
        }

        $itemsQuery = $itemModel::whereIn('location', $allowedLocations)->orderBy('created_at', 'asc');

        if ($locationFilter && in_array($locationFilter, $allowedLocations)) {
            $itemsQuery->where('location', $locationFilter);
        }
        if ($venueFilter && $hasVenueColumn) {
            $itemsQuery->where('venue', $venueFilter);
        }
        if ($searchQuery) {
            $searchColumns = ['item_description', 'category_name'];
            if ($itemModel === Item::class) {
                $searchColumns = array_merge($searchColumns, ['sr_number', 'supplier', 'remarks', 'venue']);
            } elseif ($itemModel === EngineeringItem::class || $itemModel === ElectricalItem::class) {
                $searchColumns = array_merge($searchColumns, ['sr_number', 'remarks', 'venue']);
            } elseif ($itemModel === OperationItem::class) {
                $searchColumns = array_merge($searchColumns, ['sr_no', 'supplier', 'remarks', 'venue']);
            } elseif ($itemModel === MechanicalItem::class) {
                $searchColumns = array_merge($searchColumns, ['sr_no', 'remarks']);
            }

            $itemsQuery->where(function ($query) use ($searchQuery, $searchColumns) {
                foreach ($searchColumns as $index => $column) {
                    if ($index === 0) {
                        $query->where($column, 'like', '%' . $searchQuery . '%');
                    } else {
                        $query->orWhere($column, 'like', '%' . $searchQuery . '%');
                    }
                }
            });
        }
        $items = $itemsQuery->paginate($perPage)->withQueryString();

        $currentBorrowed = auth()->user()->borrowHistories()
            ->with('item')
            ->whereNull('returned_at')
            ->where(function ($query) {
                $query->whereNull('return_status')
                    ->orWhereIn('return_status', ['pending', 'approved']);
            })
            ->latest('borrowed_at')
            ->paginate($currentPerPage, ['*'], 'current_page', $currentBorrowedPage);
        $borrowHistory = auth()->user()->borrowHistories()
            ->with('item')
            ->where(function ($query) {
                $query->whereNotNull('returned_at')
                    ->orWhere('return_status', 'rejected');
            })
            ->latest('borrowed_at')
            ->paginate($historyPerPage, ['*'], 'history_page', $historyPage);
        $pendingRequests = auth()->user()->borrowRequests()->where('status', 'pending')->latest()->get();
        $approvedRequests = auth()->user()->borrowRequests()->where('status', 'accepted')->latest()->get();
        $pendingRequestItemIds = $pendingRequests->pluck('item_id')->toArray();
        $approvedRequestItemIds = $approvedRequests->pluck('item_id')->toArray();

        return view('users.user', compact('items', 'perPage', 'borrowHistory', 'currentBorrowed', 'currentPerPage', 'historyPerPage', 'pendingRequests', 'pendingRequestItemIds', 'approvedRequestItemIds', 'locationFilter', 'venueFilter', 'locations', 'venues', 'searchQuery'));
    }

    public function borrowAllItems($itemId, Request $request)
    {
        $item = $this->findItemByIdAcrossModels($itemId);
        if (!$item) {
            return redirect()->back()->with('error', 'Item not found.');
        }

        // No location restrictions for "View All Items" - all users can borrow any items
        // Requests will be routed to the appropriate managers based on item type

        $existingRequest = auth()->user()->borrowRequests()
            ->where('item_id', $item->sr_number)
            ->where('status', 'pending')
            ->exists();

        if ($existingRequest) {
            return redirect()->back()->with('error', 'You already have a pending request for this item.');
        }

        $validated = $request->validate([
            'quantity' => 'required|integer|min:1|max:' . $item->physical_stock,
        ], [
            'quantity.max' => 'Quantity cannot exceed available items (' . $item->physical_stock . ')',
        ]);

        BorrowRequest::create([
            'user_id' => auth()->id(),
            'item_id' => $item->sr_number,
            'item_name' => $item->sr_number,
            'item_description' => $item->item_description,
            'quantity' => $validated['quantity'],
            'status' => 'pending',
        ]);

        return redirect()->back()->with('success', 'Borrow request submitted. Please wait for manager approval.');
    }

    public function borrow($itemId, Request $request)
    {
        // Find the item in any table based on the item_id format
        $item = null;

        // Check APP items first (numeric IDs)
        if (is_numeric($itemId)) {
            $item = Item::where('sr_number', $itemId)->first();
        }

        // Check Engineering items (E prefix)
        if (!$item && str_starts_with($itemId, 'E')) {
            $item = EngineeringItem::where('sr_number', $itemId)->first();
        }

        // Check Mechanical items (ME prefix)
        if (!$item && str_starts_with($itemId, 'ME')) {
            $item = MechanicalItem::where('sr_no', $itemId)->first();
        }

        // Check Operations items (OP prefix)
        if (!$item && str_starts_with($itemId, 'OP')) {
            $item = OperationItem::where('sr_no', $itemId)->first();
        }

        // Check Electrical items (ELC prefix)
        if (!$item && str_starts_with($itemId, 'ELC')) {
            $item = ElectricalItem::where('sr_number', $itemId)->first();
        }

        if (!$item) {
            return redirect()->back()->with('error', 'Item not found.');
        }

        // Check if user has access to this item's location
        $user = auth()->user();
        $userGroup = $user->user_group ?? 'APP';

        $groupLocationMapping = [
            'APP' => ['APP', 'Electrical'], // APP users can borrow from APP and Electrical locations
            'Engineering' => ['Engg / INS'],
            'Instrument' => ['Engg / INS'],
            'Mechanical' => ['ENGG / MEC'],
            'Operations' => ['OPTNS'],
            'Electrical' => ['APP', 'Engg / INS', 'ENGG / MEC', 'OPTNS', 'Electrical'], // Electrical users can borrow from any location
        ];

        $allowedLocations = $groupLocationMapping[$userGroup] ?? ['APP'];

        if (!in_array($item->location, $allowedLocations)) {
            return redirect()->back()->with('error', 'You do not have access to borrow items from this location.');
        }

        $existingRequest = auth()->user()->borrowRequests()
            ->where('item_id', $item->sr_number)
            ->where('status', 'pending')
            ->exists();

        if ($existingRequest) {
            return redirect()->back()->with('error', 'You already have a pending request for this item.');
        }

        $validated = $request->validate([
            'quantity' => 'required|integer|min:1|max:' . $item->physical_stock,
        ], [
            'quantity.max' => 'Quantity cannot exceed available items (' . $item->physical_stock . ')',
        ]);

        // Create a borrow request instead of directly borrowing
        BorrowRequest::create([
            'user_id' => auth()->id(),
            'item_id' => $item->sr_number,
            'item_name' => $item->sr_number,
            'item_description' => $item->item_description,
            'quantity' => $validated['quantity'],
            'status' => 'pending',
        ]);
        return redirect()->back()->with('success', 'Borrow request submitted. Please wait for manager approval.');
    }

    public function returnItem(BorrowHistory $borrowHistory)
    {
        if ($borrowHistory->user_id !== auth()->id() || $borrowHistory->returned_at) {
            return redirect()->back()->with('error', 'Unable to return this item.');
        }

        if ($borrowHistory->return_status === 'pending') {
            return redirect()->back()->with('error', 'Return request is already pending manager approval.');
        }

        $borrowHistory->return_status = 'pending';
        $borrowHistory->return_requested_at = now();
        $borrowHistory->save();

        return redirect()->back()->with('success', 'Return request submitted. Waiting for manager approval.');
    }

    public function cancelBorrowRequest(BorrowRequest $borrowRequest)
    {
        if ($borrowRequest->user_id !== auth()->id()) {
            return redirect()->back()->with('error', 'Unable to cancel this request.');
        }

        if ($borrowRequest->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending requests can be cancelled.');
        }

        $borrowRequest->delete();

        return redirect()->back()->with('success', 'Borrow request cancelled successfully.');
    }

    protected function findItemByIdAcrossModels($itemId)
    {
        if (is_numeric($itemId)) {
            $item = Item::where('sr_number', $itemId)->first();
            if ($item) {
                return $item;
            }
        }

        $itemId = strtoupper($itemId);

        if (str_starts_with($itemId, 'E')) {
            $item = EngineeringItem::where('sr_number', $itemId)->first();
            if ($item) {
                return $item;
            }
        }

        if (str_starts_with($itemId, 'ME')) {
            $item = MechanicalItem::where('sr_no', $itemId)->first();
            if ($item) {
                return $item;
            }
        }

        if (str_starts_with($itemId, 'OP')) {
            $item = OperationItem::where('sr_no', $itemId)->first();
            if ($item) {
                return $item;
            }
        }

        if (str_starts_with($itemId, 'ELC')) {
            $item = ElectricalItem::where('sr_number', $itemId)->first();
            if ($item) {
                return $item;
            }
        }

        return null;
    }

    public function searchItems(Request $request)
    {
        $search = $request->query('search', '');
        $locationFilter = $request->query('location');
        $venueFilter = $request->query('venue');
        $perPage = $request->query('per_page', 10);

        // Get user's group and determine which model/table to query
        $user = auth()->user();
        $userGroup = $user->user_group ?? 'APP'; // Default to APP if no group set

        // Map user groups to their respective item models and allowed locations
        $groupModelMapping = [
            'APP' => [Item::class, ['APP']],
            'Engineering' => [EngineeringItem::class, ['Engg / INS']],
            'Mechanical' => [MechanicalItem::class, ['ENGG / MEC']],
            'Operations' => [OperationItem::class, ['OPTNS']],
            'Electrical' => [ElectricalItem::class, ['Electrical']],
            'Instrument' => [EngineeringItem::class, ['Engg / INS']],
        ];

        [$itemModel, $allowedLocations] = $groupModelMapping[$userGroup] ?? [Item::class, ['APP']];

        // Check if the table has a venue column
        $tableName = (new $itemModel())->getTable();
        $hasVenueColumn = Schema::hasColumn($tableName, 'venue');

        $itemsQuery = $itemModel::whereIn('location', $allowedLocations)->orderBy('created_at', 'asc');

        if ($locationFilter && in_array($locationFilter, $allowedLocations)) {
            $itemsQuery->where('location', $locationFilter);
        }
        if ($venueFilter && $hasVenueColumn) {
            $itemsQuery->where('venue', $venueFilter);
        }

        if ($search) {
            $searchColumns = ['item_description', 'category_name'];
            if ($itemModel === Item::class) {
                $searchColumns = array_merge($searchColumns, ['sr_number', 'supplier', 'remarks', 'venue']);
            } elseif ($itemModel === EngineeringItem::class || $itemModel === ElectricalItem::class) {
                $searchColumns = array_merge($searchColumns, ['sr_number', 'remarks', 'venue']);
            } elseif ($itemModel === OperationItem::class) {
                $searchColumns = array_merge($searchColumns, ['sr_no', 'supplier', 'remarks', 'venue']);
            } elseif ($itemModel === MechanicalItem::class) {
                $searchColumns = array_merge($searchColumns, ['sr_no', 'remarks']);
            }

            $searchColumns = array_values(array_filter($searchColumns, function ($column) use ($tableName) {
                return Schema::hasColumn($tableName, $column);
            }));

            if (!empty($searchColumns)) {
                $itemsQuery->where(function ($query) use ($search, $searchColumns) {
                    foreach ($searchColumns as $index => $column) {
                        if ($index === 0) {
                            $query->where($column, 'like', '%' . $search . '%');
                        } else {
                            $query->orWhere($column, 'like', '%' . $search . '%');
                        }
                    }
                });
            }
        }

        $total = $itemsQuery->count();
        $items = $itemsQuery->get();

        $pendingRequests = auth()->user()->borrowRequests()->where('status', 'pending')->latest()->get();
        $approvedRequests = auth()->user()->borrowRequests()->where('status', 'accepted')->latest()->get();
        $pendingRequestItemIds = $pendingRequests->pluck('item_id')->toArray();
        $approvedRequestItemIds = $approvedRequests->pluck('item_id')->toArray();

        return response()->json([
            'items' => $items,
            'total' => $total,
            'pending_item_ids' => $pendingRequestItemIds,
            'approved_item_ids' => $approvedRequestItemIds,
        ]);
    }

    public function allItems(Request $request)
    {
        $perPage = $request->query('per_page', 10);
        $locationFilter = $request->query('location');
        $venueFilter = $request->query('venue');
        $searchQuery = $request->query('search');

        // Show all items from all categories
        $items = collect()
            ->merge(Item::orderBy('created_at', 'asc')->get())
            ->merge(EngineeringItem::orderBy('created_at', 'asc')->get())
            ->merge(MechanicalItem::orderBy('created_at', 'asc')->get())
            ->merge(OperationItem::orderBy('created_at', 'asc')->get())
            ->merge(ElectricalItem::orderBy('created_at', 'asc')->get());

        $locations = $items->pluck('location')->filter()->unique()->sort()->values();
        $venues = $items->pluck('venue')->filter()->unique()->sort()->values();

        if ($locationFilter) {
            $items = $items->where('location', $locationFilter);
        }
        if ($venueFilter) {
            $items = $items->where('venue', $venueFilter);
        }
        if ($searchQuery) {
            $searchQuery = strtolower($searchQuery);
            $items = $items->filter(function ($item) use ($searchQuery) {
                return str_contains(strtolower($item->item_description ?? ''), $searchQuery)
                    || str_contains(strtolower($item->category_name ?? ''), $searchQuery)
                    || str_contains(strtolower($item->supplier ?? ''), $searchQuery)
                    || str_contains(strtolower($item->remarks ?? ''), $searchQuery)
                    || str_contains(strtolower($item->sr_number ?? ''), $searchQuery)
                    || str_contains(strtolower($item->sr_no ?? ''), $searchQuery);
            });
        }

        $items = $items->sortBy('created_at')->values();

        $page = LengthAwarePaginator::resolveCurrentPage();
        $itemsForPage = $items->forPage($page, $perPage);
        $items = new LengthAwarePaginator($itemsForPage, $items->count(), $perPage, $page, [
            'path' => url()->current(),
            'query' => $request->query(),
        ]);

        $pendingRequests = auth()->user()->borrowRequests()->where('status', 'pending')->latest()->get();
        $approvedRequests = auth()->user()->borrowRequests()->where('status', 'accepted')->latest()->get();
        $pendingRequestItemIds = $pendingRequests->pluck('item_id')->toArray();
        $approvedRequestItemIds = $approvedRequests->pluck('item_id')->toArray();

        return view('users.all-items', compact('items', 'perPage', 'pendingRequests', 'pendingRequestItemIds', 'approvedRequestItemIds', 'locationFilter', 'venueFilter', 'locations', 'venues', 'searchQuery'));
    }
}