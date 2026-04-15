<?php

namespace App\Http\Controllers;

use App\Models\BorrowHistory;
use App\Models\BorrowRequest;
use App\Models\Item;
use Illuminate\Http\Request;

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
        $searchQuery = $request->query('search');
        $locations = Item::whereNotNull('venue')
            ->where('venue', '!=', '')
            ->distinct()
            ->orderBy('venue')
            ->pluck('venue');

        $itemsQuery = Item::orderBy('created_at', 'desc');
        if ($locationFilter) {
            $itemsQuery->where('venue', $locationFilter);
        }
        if ($searchQuery) {
            $itemsQuery->where(function ($query) use ($searchQuery) {
                $query->where('item_description', 'like', '%' . $searchQuery . '%')
                    ->orWhere('category_name', 'like', '%' . $searchQuery . '%')
                    ->orWhere('supplier', 'like', '%' . $searchQuery . '%');
            });
        }
        $items = $itemsQuery->paginate($perPage)->withQueryString();

        $currentBorrowed = auth()->user()->borrowHistories()
            ->whereNull('returned_at')
            ->where(function ($query) {
                $query->whereNull('return_status')
                    ->orWhereIn('return_status', ['pending', 'approved']);
            })
            ->latest('borrowed_at')
            ->paginate($currentPerPage, ['*'], 'current_page', $currentBorrowedPage);
        $borrowHistory = auth()->user()->borrowHistories()
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

        return view('users.user', compact('items', 'perPage', 'borrowHistory', 'currentBorrowed', 'currentPerPage', 'historyPerPage', 'pendingRequests', 'pendingRequestItemIds', 'approvedRequestItemIds', 'locationFilter', 'locations', 'searchQuery'));
    }

    public function borrow(Item $item, Request $request)
    {
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

    public function searchItems(Request $request)
    {
        $search = $request->query('search', '');
        $locationFilter = $request->query('location');
        $perPage = $request->query('per_page', 10);

        $itemsQuery = Item::orderBy('created_at', 'desc');
        
        if ($locationFilter) {
            $itemsQuery->where('venue', $locationFilter);
        }
        
        if ($search) {
            $itemsQuery->where(function ($query) use ($search) {
                $query->where('item_description', 'like', '%' . $search . '%')
                    ->orWhere('category_name', 'like', '%' . $search . '%')
                    ->orWhere('supplier', 'like', '%' . $search . '%');
            });
        }

        $total = $itemsQuery->count();
        $items = $itemsQuery->limit($perPage)->get();

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
}