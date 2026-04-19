<?php

namespace App\Http\Controllers;

use App\Models\BorrowHistory;
use App\Models\BorrowRequest;
use App\Models\Item;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 5);
        $perPage = in_array($perPage, [5, 10, 100]) ? (int) $perPage : 5;
        $borrowedPerPage = $request->query('borrowed_per_page', 5);
        $borrowedPerPage = in_array($borrowedPerPage, [5, 10, 100]) ? (int) $borrowedPerPage : 5;
        $requestsPerPage = $request->query('requests_per_page', 5);
        $requestsPerPage = in_array($requestsPerPage, [5, 10, 100]) ? (int) $requestsPerPage : 5;
        $returnsPerPage = $request->query('returns_per_page', 5);
        $returnsPerPage = in_array($returnsPerPage, [5, 10, 100]) ? (int) $returnsPerPage : 5;
        $searchQuery = $request->query('search');
        $locationFilter = $request->query('location');
        $venueFilter = $request->query('venue');

        $locations = Item::whereNotNull('location')
            ->where('location', '!=', '')
            ->distinct()
            ->orderBy('location')
            ->pluck('location');
        $venues = Item::whereNotNull('venue')
            ->where('venue', '!=', '')
            ->distinct()
            ->orderBy('venue')
            ->pluck('venue');

        $itemsQuery = Item::orderBy('created_at', 'desc');
        if ($locationFilter) {
            $itemsQuery->where('location', $locationFilter);
        }
        if ($venueFilter) {
            $itemsQuery->where('venue', $venueFilter);
        }
        if ($searchQuery) {
            $itemsQuery->where(function ($query) use ($searchQuery) {
                $query->where('item_description', 'like', '%' . $searchQuery . '%')
                    ->orWhere('category_name', 'like', '%' . $searchQuery . '%')
                    ->orWhere('supplier', 'like', '%' . $searchQuery . '%');
            });
        }

        $items = $itemsQuery->paginate($perPage);
        $borrowRequests = BorrowRequest::with('user', 'item')
            ->where('status', 'pending')
            ->latest('created_at')
            ->paginate($requestsPerPage, ['*'], 'requests_page');
        $borrowedItems = BorrowHistory::with('user', 'item')
            ->whereNull('returned_at')
            ->latest('borrowed_at')
            ->paginate($borrowedPerPage, ['*'], 'borrowed_page');

        return view('admin.adminView', compact('items', 'perPage', 'borrowRequests', 'borrowedItems', 'borrowedPerPage', 'requestsPerPage', 'searchQuery', 'locationFilter', 'venueFilter', 'locations', 'venues'));
    }

    public function approveBorrowRequest(BorrowRequest $borrowRequest)
    {
        if ($borrowRequest->status !== 'pending') {
            return redirect()->route('manager')->with('error', 'This request has already been processed.');
        }

        $item = $borrowRequest->item->fresh();
        $requestedQuantity = $borrowRequest->quantity;
        
        if ($item->physical_stock <= 0 || $item->availability !== 'available' || $item->physical_stock < $requestedQuantity) {
            // Create a borrow history record with rejected status
            BorrowHistory::create([
                'user_id' => $borrowRequest->user_id,
                'item_id' => $borrowRequest->item_id,
                'item_name' => $borrowRequest->item_name,
                'item_description' => $borrowRequest->item_description,
                'count' => $requestedQuantity,
                'borrowed_at' => now(),
                'returned_at' => now(),
                'return_status' => 'rejected',
                'admin_return_notes' => 'Item was out of stock or insufficient quantity available at time of approval.',
            ]);

            // Reject the borrow request
            $borrowRequest->status = 'rejected';
            $borrowRequest->admin_notes = 'Item is out of stock or insufficient quantity available.';
            $borrowRequest->save();
            return redirect()->route('manager')->with('error', 'Item is out of stock or insufficient quantity available. Request has been recorded in user history as rejected.');
        }

        // Reduce physical stock immediately when the manager approves the request.
        $item->physical_stock -= $requestedQuantity;
        $item->availability = $item->physical_stock > 0 ? 'available' : 'out_of_stock';
        $item->save();

        $borrowRequest->status = 'accepted';
        $borrowRequest->save();

        return redirect()->route('manager')->with('success', 'Borrow request approved.');
    }

    public function approveReturn(BorrowHistory $borrowHistory, Request $request)
    {
        if ($borrowHistory->return_status !== 'pending') {
            return redirect()->route('manager')->with('error', 'This return request has already been processed.');
        }

        $item = $borrowHistory->item;
        $item->physical_stock += $borrowHistory->count;
        $item->availability = $item->physical_stock > 0 ? 'available' : 'out_of_stock';
        $item->save();

        $borrowHistory->return_status = 'approved';
        $borrowHistory->returned_at = now();
        $borrowHistory->save();

        return redirect()->route('manager')->with('success', 'Return request approved. Item marked as returned.');
    }

    public function rejectReturn(BorrowHistory $borrowHistory, Request $request)
    {
        if ($borrowHistory->return_status !== 'pending') {
            return redirect()->route('manager')->with('error', 'This return request has already been processed.');
        }

        $request->validate([
            'admin_return_notes' => 'nullable|string|max:500',
        ]);

        $borrowHistory->return_status = null;
        $borrowHistory->return_requested_at = null;
        $borrowHistory->admin_return_notes = null;
        $borrowHistory->save();

        return redirect()->route('manager')->with('success', 'Return request rejected. Item remains borrowed.');
    }

    public function rejectBorrowRequest(Request $request, BorrowRequest $borrowRequest)
    {
        if ($borrowRequest->status !== 'pending') {
            return redirect()->route('manager')->with('error', 'This request has already been processed.');
        }

        $validated = $request->validate([
            'admin_notes' => 'nullable|string|max:500',
        ]);

        $borrowRequest->status = 'rejected';
        $borrowRequest->admin_notes = $validated['admin_notes'] ?? null;
        $borrowRequest->save();

        return redirect()->route('manager')->with('success', 'Borrow request rejected.');
    }

    public function adminView(Request $request)
    {
        $perPage = $request->query('per_page', 5);
        $perPage = in_array($perPage, [5, 10, 100]) ? (int) $perPage : 5;
        $searchQuery = $request->query('search');
        $locationFilter = $request->query('location');
        $venueFilter = $request->query('venue');

        $locations = Item::whereNotNull('location')
            ->where('location', '!=', '')
            ->distinct()
            ->orderBy('location')
            ->pluck('location');
        $venues = Item::whereNotNull('venue')
            ->where('venue', '!=', '')
            ->distinct()
            ->orderBy('venue')
            ->pluck('venue');

        $itemsQuery = Item::orderBy('created_at', 'desc');
        if ($locationFilter) {
            $itemsQuery->where('location', $locationFilter);
        }
        if ($venueFilter) {
            $itemsQuery->where('venue', $venueFilter);
        }
        if ($searchQuery) {
            $itemsQuery->where(function ($query) use ($searchQuery) {
                $query->where('item_description', 'like', '%' . $searchQuery . '%')
                    ->orWhere('category_name', 'like', '%' . $searchQuery . '%')
                    ->orWhere('supplier', 'like', '%' . $searchQuery . '%');
            });
        }
        $items = $itemsQuery->paginate($perPage);

        return view('admin.adminView', compact('items', 'perPage', 'searchQuery', 'locationFilter', 'venueFilter', 'locations', 'venues'));
    }

    public function searchAdminItems(Request $request)
    {
        $search = $request->query('search', '');
        $locationFilter = $request->query('location');
        $venueFilter = $request->query('venue');
        $perPage = $request->query('per_page', 5);

        $itemsQuery = Item::orderBy('created_at', 'desc');
        if ($locationFilter) {
            $itemsQuery->where('location', $locationFilter);
        }
        if ($venueFilter) {
            $itemsQuery->where('venue', $venueFilter);
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

        return response()->json([
            'items' => $items,
            'total' => $total,
        ]);
    }

    public function edit(Item $item)
    {
        return view('admin.adminEdit', compact('item'));
    }

    public function update(Request $request, Item $item)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'count' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $validated['availability'] = $validated['count'] > 0 ? 'available' : 'out_of_stock';

        $item->update($validated);

        return redirect()->route('manager')->with('success', 'Item updated successfully!');
    }

    public function destroy(Item $item)
    {
        $item->delete();

        return redirect('/manager')->with('success', 'Item deleted successfully!');
    }

    public function adminForms()
    {
        return view('admin.adminForms');
    }

    public function inventory(Request $request)
    {
        $validated = $request->validate([
            'sr_number' => 'required|array',
            'sr_number.*' => 'required|integer|min:1',
            'item_description' => 'required|array',
            'item_description.*' => 'required|string|max:255',
            'category_name' => 'nullable|array',
            'category_name.*' => 'nullable|string|max:255',
            'supplier' => 'nullable|array',
            'supplier.*' => 'nullable|string|max:255',
            'venue' => 'nullable|array',
            'venue.*' => 'nullable|string|max:255',
            'barcode' => 'nullable|array',
            'barcode.*' => 'nullable|string|max:255',
            'total_in' => 'nullable|array',
            'total_in.*' => 'nullable|integer|min:0',
            'total_out' => 'nullable|array',
            'total_out.*' => 'nullable|integer|min:0',
            'total_return' => 'nullable|array',
            'total_return.*' => 'nullable|integer|min:0',
            'quantity_in_hand_current' => 'nullable|array',
            'quantity_in_hand_current.*' => 'nullable|integer|min:0',
            'physical_stock' => 'required|array',
            'physical_stock.*' => 'required|integer|min:0',
            'reconciliation' => 'nullable|array',
            'reconciliation.*' => 'nullable|string|max:255',
            'difference' => 'nullable|array',
            'difference.*' => 'nullable|integer',
            'remarks' => 'nullable|array',
            'remarks.*' => 'nullable|string|max:500',
        ]);

        $itemsCreated = 0;
        $errors = [];

        foreach ($validated['sr_number'] as $index => $srNumber) {
            try {
                Item::create([
                    'sr_number' => $srNumber,
                    'item_description' => $validated['item_description'][$index] ?? '',
                    'category_name' => $validated['category_name'][$index] ?? null,
                    'supplier' => $validated['supplier'][$index] ?? null,
                    'venue' => $validated['venue'][$index] ?? null,
                    'barcode' => $validated['barcode'][$index] ?? null,
                    'total_in' => $validated['total_in'][$index] ?? 0,
                    'total_out' => $validated['total_out'][$index] ?? 0,
                    'total_return' => $validated['total_return'][$index] ?? 0,
                    'quantity_in_hand_current' => $validated['quantity_in_hand_current'][$index] ?? 0,
                    'physical_stock' => $validated['physical_stock'][$index],
                    'reconciliation' => $validated['reconciliation'][$index] ?? null,
                    'difference' => $validated['difference'][$index] ?? 0,
                    'remarks' => $validated['remarks'][$index] ?? null,
                    'availability' => ($validated['physical_stock'][$index] > 0) ? 'available' : 'out_of_stock',
                ]);
                $itemsCreated++;
            } catch (\Exception $e) {
                $errors[] = "Error creating item with Sr# {$srNumber}: " . $e->getMessage();
            }
        }

        if ($itemsCreated > 0) {
            $message = "{$itemsCreated} item(s) created successfully!";
            if (!empty($errors)) {
                $message .= " However, some errors occurred: " . implode(', ', $errors);
            }
            return redirect()->route('admin.forms')->with('success', $message);
        } else {
            return redirect()->route('admin.forms')->with('error', 'No items were created. Errors: ' . implode(', ', $errors));
        }
    }
    
}
