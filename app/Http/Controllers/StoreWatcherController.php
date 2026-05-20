<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BorrowHistory;
use App\Models\BorrowRequest;
use App\Models\ElectricalItem;
use App\Models\EngineeringItem;
use App\Models\InstrumentItem;
use App\Models\Item;
use App\Models\MechanicalItem;
use App\Models\OperationItem;
use App\Models\User;

class StoreWatcherController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 5);
        $perPage = in_array($perPage, [5, 10, 100]) ? (int) $perPage : 5;
        $requestsPerPage = $request->query('requests_per_page', 5);
        $requestsPerPage = in_array($requestsPerPage, [5, 10, 100]) ? (int) $requestsPerPage : 5;
        $returnsPerPage = $request->query('returns_per_page', 5);
        $returnsPerPage = in_array($returnsPerPage, [5, 10, 100]) ? (int) $returnsPerPage : 5;
        $currentPerPage = $request->query('current_per_page', 5);
        $currentPerPage = in_array($currentPerPage, [5, 10, 100]) ? (int) $currentPerPage : 5;
        $historyPerPage = $request->query('history_per_page', 5);
        $historyPerPage = in_array($historyPerPage, [5, 10, 100]) ? (int) $historyPerPage : 5;
        $pendingApprovalsPerPage = $request->query('pending_approvals_per_page', 5);
        $pendingApprovalsPerPage = in_array($pendingApprovalsPerPage, [5, 10, 100]) ? (int) $pendingApprovalsPerPage : 5;

        $locationFilter = $request->query('location');
        $venueFilter = $request->query('venue');
        $searchQuery = $request->query('search');

        // Get unique locations and venues from all item tables
        $locationsQuery = Item::whereNotNull('location')->where('location', '!=', '');
        $locationsQuery->union(EngineeringItem::whereNotNull('location')->where('location', '!=', '')->select('location'));
        $locationsQuery->union(OperationItem::whereNotNull('location')->where('location', '!=', '')->select('location'));
        $locationsQuery->union(MechanicalItem::whereNotNull('location')->where('location', '!=', '')->select('location'));
        $locationsQuery->union(ElectricalItem::whereNotNull('location')->where('location', '!=', '')->select('location'));
        $locationsQuery->union(InstrumentItem::whereNotNull('location')->where('location', '!=', '')->select('location'));
        $locations = $locationsQuery->distinct()->orderBy('location')->pluck('location');

        $venuesQuery = Item::whereNotNull('venue')->where('venue', '!=', '');
        $venuesQuery->union(EngineeringItem::whereNotNull('venue')->where('venue', '!=', '')->select('venue'));
        $venuesQuery->union(OperationItem::whereNotNull('venue')->where('venue', '!=', '')->select('venue'));
        $venuesQuery->union(ElectricalItem::whereNotNull('venue')->where('venue', '!=', '')->select('venue'));
        $venuesQuery->union(InstrumentItem::whereNotNull('venue')->where('venue', '!=', '')->select('venue'));
        $venues = $venuesQuery->distinct()->orderBy('venue')->pluck('venue');

        // Query all item types with filters
        $itemsCollections = [];

        // Main items
        $mainItemsQuery = Item::orderBy('created_at', 'desc');
        if ($locationFilter) {
            $mainItemsQuery->where('location', $locationFilter);
        }
        if ($venueFilter) {
            $mainItemsQuery->where('venue', $venueFilter);
        }
        if ($searchQuery) {
            $mainItemsQuery->where(function ($query) use ($searchQuery) {
                $query->where('item_description', 'like', '%' . $searchQuery . '%')
                    ->orWhere('category_name', 'like', '%' . $searchQuery . '%')
                    ->orWhere('supplier', 'like', '%' . $searchQuery . '%');
            });
        }
        $mainItems = $mainItemsQuery->get()->map(function ($item) {
            $item->item_type = 'main';
            return $item;
        });
        $itemsCollections[] = $mainItems;

        // Engineering items
        $engItemsQuery = EngineeringItem::orderBy('created_at', 'desc');
        if ($locationFilter) {
            $engItemsQuery->where('location', $locationFilter);
        }
        if ($venueFilter) {
            $engItemsQuery->where('venue', $venueFilter);
        }
        if ($searchQuery) {
            $engItemsQuery->where(function ($query) use ($searchQuery) {
                $query->where('item_description', 'like', '%' . $searchQuery . '%')
                    ->orWhere('category_name', 'like', '%' . $searchQuery . '%');
            });
        }
        $engItems = $engItemsQuery->get()->map(function ($item) {
            $item->item_type = 'engineering';
            // Map fields to match main items structure
            $item->sr_number = $item->sr_number;
            $item->item_description = $item->item_description;
            $item->supplier = null; // Not available in eng items
            $item->total_in = null;
            $item->total_out = null;
            $item->total_return = null;
            $item->quantity_in_hand_current = $item->quantity_in_hand ?? 0;
            $item->reconciliation = null;
            $item->difference = null;
            return $item;
        });
        $itemsCollections[] = $engItems;

        // Operations items
        $opsItemsQuery = OperationItem::orderBy('created_at', 'desc');
        if ($locationFilter) {
            $opsItemsQuery->where('location', $locationFilter);
        }
        if ($venueFilter) {
            $opsItemsQuery->where('venue', $venueFilter);
        }
        if ($searchQuery) {
            $opsItemsQuery->where(function ($query) use ($searchQuery) {
                $query->where('item_description', 'like', '%' . $searchQuery . '%')
                    ->orWhere('category_name', 'like', '%' . $searchQuery . '%')
                    ->orWhere('supplier', 'like', '%' . $searchQuery . '%');
            });
        }
        $opsItems = $opsItemsQuery->get()->map(function ($item) {
            $item->item_type = 'operations';
            // Map fields to match main items structure
            $item->sr_number = $item->sr_no;
            $item->item_description = $item->item_description;
            $item->quantity_in_hand_current = $item->quantity_in_hand ?? 0;
            $item->reconciliation = $item->reconciliation ?? 0;
            $item->difference = $item->difference ?? 0;
            return $item;
        });
        $itemsCollections[] = $opsItems;

        // Mechanical items
        $mechItemsQuery = MechanicalItem::orderBy('created_at', 'desc');
        if ($locationFilter) {
            $mechItemsQuery->where('location', $locationFilter);
        }
        if ($searchQuery) {
            $mechItemsQuery->where(function ($query) use ($searchQuery) {
                $query->where('description', 'like', '%' . $searchQuery . '%')
                    ->orWhere('category_name', 'like', '%' . $searchQuery . '%');
            });
        }
        $mechItems = $mechItemsQuery->get()->map(function ($item) {
            $item->item_type = 'mechanical';
            // Map fields to match main items structure
            $item->sr_number = $item->sr_no;
            $item->item_description = $item->description;
            $item->supplier = null;
            $item->venue = null; // Not available in mech items
            $item->barcode = null;
            $item->total_in = null;
            $item->total_out = null;
            $item->total_return = null;
            $item->quantity_in_hand_current = $item->balance_qty_in_store ?? 0;
            $item->physical_stock = $item->balance_qty_in_store ?? 0;
            $item->reconciliation = null;
            $item->difference = null;
            return $item;
        });
        $itemsCollections[] = $mechItems;

        // Electrical items
        $elecItemsQuery = ElectricalItem::orderBy('created_at', 'desc');
        if ($locationFilter) {
            $elecItemsQuery->where('location', $locationFilter);
        }
        if ($venueFilter) {
            $elecItemsQuery->where('venue', $venueFilter);
        }
        if ($searchQuery) {
            $elecItemsQuery->where(function ($query) use ($searchQuery) {
                $query->where('item_description', 'like', '%' . $searchQuery . '%')
                    ->orWhere('category_name', 'like', '%' . $searchQuery . '%');
            });
        }
        $elecItems = $elecItemsQuery->get()->map(function ($item) {
            $item->item_type = 'electrical';
            // Map fields to match main items structure
            $item->sr_number = $item->sr_number;
            $item->item_description = $item->item_description;
            $item->supplier = null; // Not available in electrical items
            $item->total_in = null;
            $item->total_out = null;
            $item->total_return = null;
            $item->quantity_in_hand_current = $item->quantity_in_hand ?? 0;
            $item->reconciliation = null;
            $item->difference = null;
            return $item;
        });
        $itemsCollections[] = $elecItems;

        // Combine all collections and sort by created_at
        $allItems = collect();
        foreach ($itemsCollections as $collection) {
            $allItems = $allItems->merge($collection);
        }
        $allItems = $allItems->sortByDesc('created_at');

        // Manual pagination
        $page = $request->query('page', 1);
        $offset = ($page - 1) * $perPage;
        $paginatedItems = $allItems->slice($offset, $perPage);
        $totalItems = $allItems->count();

        // Create a LengthAwarePaginator manually
        $items = new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedItems,
            $totalItems,
            $perPage,
            $page,
            ['path' => $request->url(), 'pageName' => 'page']
        );
        $items->appends($request->except('page'));

        // Pending approvals for all managers (all pending borrow requests)
        $pendingApprovals = BorrowRequest::with('user', 'item')
            ->where('status', 'pending')
            ->latest('created_at')
            ->paginate($pendingApprovalsPerPage, ['*'], 'pending_approvals_page');

        // Add manager information to each pending approval
        $pendingApprovals->getCollection()->transform(function ($request) {
            $itemGroup = $request->getItemGroup();
            $managers = $this->getManagersForGroup($itemGroup);
            $request->manager_names = $managers->pluck('name')->join(', ');
            return $request;
        });

        $approvedRequests = BorrowRequest::with('user', 'item')
            ->where('status', 'accepted')
            ->latest('created_at')
            ->paginate($requestsPerPage, ['*'], 'requests_page');
        $currentBorrowed = BorrowHistory::with('user', 'item')
            ->whereNull('returned_at')
            ->latest('borrowed_at')
            ->paginate($currentPerPage, ['*'], 'current_page');
        $pendingReturns = BorrowHistory::with('user', 'item')
            ->where('return_status', 'pending')
            ->latest('return_requested_at')
            ->paginate($returnsPerPage, ['*'], 'returns_page');
        $borrowHistory = BorrowHistory::with('user', 'item')
            ->whereNotNull('returned_at')
            ->latest('returned_at')
            ->paginate($historyPerPage, ['*'], 'history_page');

        return view('storeWatcher.storeWatcher', compact('items', 'perPage', 'locationFilter', 'venueFilter', 'locations', 'venues', 'pendingApprovals', 'pendingApprovalsPerPage', 'approvedRequests', 'requestsPerPage', 'currentBorrowed', 'currentPerPage', 'pendingReturns', 'returnsPerPage', 'borrowHistory', 'historyPerPage', 'searchQuery'));
    }

    /**
     * Release an approved borrow request (move item to borrowed status)
     */
    public function releaseBorrowRequest(BorrowRequest $borrowRequest)
    {
        if ($borrowRequest->status !== 'accepted') {
            return redirect()->back()->with('error', 'Only approved borrow requests can be released.');
        }

        $requestedQuantity = $borrowRequest->quantity;
        $item = $borrowRequest->getItem();

        if (!$item) {
            return redirect()->back()->with('error', 'Associated inventory item could not be found.');
        }

        $currentStock = (int) $item->physical_stock;
        $item->physical_stock = max(0, $currentStock - $requestedQuantity);
        $item->availability = ($item->physical_stock > 0) ? 'available' : 'out_of_stock';
        $item->save();

        BorrowHistory::create([
            'user_id' => $borrowRequest->user_id,
            'item_id' => $borrowRequest->item_id,
            'item_name' => $borrowRequest->item_name,
            'item_description' => $borrowRequest->item_description,
            'count' => $requestedQuantity,
            'borrowed_at' => now(),
            'returned_at' => null,
        ]);

        $borrowRequest->status = 'released';
        $borrowRequest->save();

        return redirect()->back()->with('success', 'Borrow request released successfully.');
    }

    /**
     * Approve a pending return request
     */
    public function approveReturn(BorrowHistory $borrowHistory)
    {
        if ($borrowHistory->return_status !== 'pending') {
            return redirect()->back()->with('error', 'This return request has already been processed.');
        }

        $item = $borrowHistory->getItem();
        $item->physical_stock += $borrowHistory->count;
        $item->availability = ($item->physical_stock ?? 0) > 0 ? 'available' : 'out_of_stock';
        $item->save();

        $borrowHistory->return_status = 'approved';
        $borrowHistory->returned_at = now();
        $borrowHistory->save();

        return redirect()->back()->with('success', 'Return request approved. Item marked as returned.');
    }

    /**
     * Reject a pending return request
     */
    public function rejectReturn(BorrowHistory $borrowHistory, Request $request)
    {
        if ($borrowHistory->return_status !== 'pending') {
            return redirect()->back()->with('error', 'This return request has already been processed.');
        }

        $request->validate([
            'admin_return_notes' => 'nullable|string|max:500',
        ]);

        $borrowHistory->return_status = null;
        $borrowHistory->return_requested_at = null;
        $borrowHistory->admin_return_notes = null;
        $borrowHistory->save();

        return redirect()->back()->with('success', 'Return request rejected. Item remains borrowed.');
    }

    public function exportBorrowHistory(Request $request)
    {
        $borrowHistory = BorrowHistory::with('user', 'item')
            ->whereNotNull('returned_at')
            ->latest('returned_at')
            ->get();

        $filename = 'borrow_history_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($borrowHistory) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['User', 'Item ID', 'Description', 'Quantity', 'Borrowed At', 'Returned At', 'Status', 'Return Notes']);

            foreach ($borrowHistory as $history) {
                $status = $history->return_status === 'approved' ? 'Success' : ($history->return_status === 'rejected' ? 'Rejected' : 'Completed');
                $returnNotes = $history->admin_return_notes;
                if (empty($returnNotes)) {
                    if ($status === 'Success') {
                        $returnNotes = 'Successfully returned';
                    } elseif ($status === 'Rejected') {
                        $returnNotes = 'Return rejected';
                    } else {
                        $returnNotes = '–';
                    }
                }

                fputcsv($handle, [
                    $history->user->name ?? '–',
                    $history->item_id,
                    $history->item_description ?? '–',
                    $history->count,
                    $history->borrowed_at->format('Y-m-d H:i:s'),
                    $history->returned_at->format('Y-m-d H:i:s'),
                    $status,
                    $returnNotes,
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get managers who can manage a specific item group
     */
    private function getManagersForGroup(string $itemGroup): \Illuminate\Database\Eloquent\Collection
    {
        return User::where('is_manager', true)
            ->where(function ($query) use ($itemGroup) {
                $query->where('user_group', $itemGroup)
                    ->orWhere(function ($subQuery) use ($itemGroup) {
                        // Engineering and Instrument managers can manage Engineering, Mechanical, Electrical, Instrument
                        if (in_array($itemGroup, ['Engineering', 'Mechanical', 'Electrical', 'Instrument'])) {
                            $subQuery->whereIn('user_group', ['Engineering', 'Instrument']);
                        }
                    });
            })
            ->get();
    }
}
