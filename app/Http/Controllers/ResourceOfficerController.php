<?php

namespace App\Http\Controllers;

use App\Models\BorrowHistory;
use App\Models\BorrowRequest;
use App\Models\Item;
use App\Support\ExcelImporter;
use Illuminate\Http\Request;

class ResourceOfficerController extends Controller
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

        return view('resourceOfficer.resourceOfficer', compact('items', 'perPage', 'locationFilter', 'locations', 'approvedRequests', 'requestsPerPage', 'currentBorrowed', 'currentPerPage', 'pendingReturns', 'returnsPerPage', 'borrowHistory', 'historyPerPage', 'searchQuery'));
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
                $status = $history->return_status === 'approved' ? 'Returned' : ($history->return_status === 'rejected' ? 'Rejected' : 'Completed');
                fputcsv($handle, [
                    $history->user->name ?? '–',
                    $history->item_id,
                    $history->item_description ?? '–',
                    $history->count,
                    $history->borrowed_at->format('Y-m-d H:i:s'),
                    $history->returned_at->format('Y-m-d H:i:s'),
                    $status,
                    $history->admin_return_notes ?? '–',
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function edit(Item $item)
    {
        return view('resourceOfficer.edit', compact('item'));
    }

    public function update(Request $request, Item $item)
    {
        $validated = $request->validate([
            'sr_number' => 'required|integer|unique:items,sr_number,' . $item->sr_number . ',sr_number',
            'item_description' => 'required|string|max:255',
            'quantity_in_hand_current' => 'nullable|numeric|min:0',
            'category_name' => 'nullable|string|max:255',
            'supplier' => 'nullable|string|max:255',
            'venue' => 'nullable|string|max:255',
            'barcode' => 'nullable|string|max:255',
            'total_in' => 'nullable|numeric|min:0',
            'total_out' => 'nullable|numeric|min:0',
            'total_return' => 'nullable|numeric|min:0',
            'physical_stock' => 'required|numeric|min:0',
            'reconciliation' => 'nullable|string|max:255',
            'difference' => 'nullable|numeric',
            'remarks' => 'nullable|string',
        ]);

        $validated['availability'] = ($validated['physical_stock'] ?? 0) > 0 ? 'available' : 'out_of_stock';

        $item->update($validated);

        return redirect()->route('resource-officer.item.edit', $item)->with('success', 'Item updated successfully!');
    }

    public function destroy(Item $item)
    {
        $item->delete();

        return redirect()->route('resource-officer')->with('success', 'Item deleted successfully!');
    }

    public function releaseBorrowRequest(BorrowRequest $borrowRequest)
    {
        if ($borrowRequest->status !== 'accepted') {
            return redirect()->back()->with('error', 'Only approved borrow requests can be released.');
        }

        $requestedQuantity = $borrowRequest->quantity;

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

    public function approveReturn(BorrowHistory $borrowHistory)
    {
        if ($borrowHistory->return_status !== 'pending') {
            return redirect()->back()->with('error', 'This return request has already been processed.');
        }

        $item = $borrowHistory->item;
        $item->physical_stock += $borrowHistory->count;
        $item->availability = ($item->physical_stock ?? 0) > 0 ? 'available' : 'out_of_stock';
        $item->save();

        $borrowHistory->return_status = 'approved';
        $borrowHistory->returned_at = now();
        $borrowHistory->save();

        return redirect()->back()->with('success', 'Return request approved. Item marked as returned.');
    }

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

    public function form()
    {
        return view('resourceOfficer.form');
    }

    public function importForm()
    {
        return view('resourceOfficer.import');
    }

    public function import(Request $request)
    {
        $validated = $request->validate([
            'inventory_file' => 'required|file|mimes:xlsx,csv,txt|max:20480',
        ]);

        $file = $request->file('inventory_file');

        try {
            $rows = ExcelImporter::readSpreadsheet($file->getPathname());
        } catch (\RuntimeException $exception) {
            return back()->withErrors(['inventory_file' => $exception->getMessage()]);
        }

        if (empty($rows) || count($rows) < 2) {
            return back()->withErrors(['inventory_file' => 'The uploaded file must contain a header row and at least one item row.']);
        }

        $headers = array_map('strtolower', array_map('trim', $rows[0]));
        $headerMap = [
            'sr#' => 'sr_number',
            'category name' => 'category_name',
            'item description' => 'item_description',
            'venue' => 'venue',
            'barcode#' => 'barcode',
            'supplier' => 'supplier',
            'total (in)' => 'total_in',
            'total (out)' => 'total_out',
            'total (return)' => 'total_return',
            'quantity in hand (current)' => 'quantity_in_hand_current',
            'physical stock' => 'physical_stock',
            'reconcilation' => 'reconciliation',
            'difference' => 'difference',
            'remarks' => 'remarks',
        ];

        $missingRequired = [];
        if (!in_array('sr#', $headers, true)) {
            $missingRequired[] = 'Sr#';
        }
        if (!in_array('item description', $headers, true)) {
            $missingRequired[] = 'Item Description';
        }
        if (!in_array('quantity in hand (current)', $headers, true)) {
            $missingRequired[] = 'Quantity In Hand (Current)';
        }

        if (!empty($missingRequired)) {
            return back()->withErrors(['inventory_file' => 'The uploaded file is missing required columns: ' . implode(', ', $missingRequired) . '.']);
        }

        $importRows = [];
        $rowSrNumbers = [];
        $duplicateSrNumbers = [];

        foreach (array_slice($rows, 1) as $rowIndex => $row) {
            $row = array_map('trim', $row);

            if (empty(array_filter($row, fn($value) => $value !== ''))) {
                continue;
            }

            $itemData = [];

            foreach ($headers as $index => $columnName) {
                if (!isset($headerMap[$columnName])) {
                    continue;
                }

                $mappedColumn = $headerMap[$columnName];
                $value = $row[$index] ?? null;

                if (in_array($mappedColumn, ['total_in', 'total_out', 'total_return', 'quantity_in_hand_current', 'physical_stock', 'difference'])) {
                    $itemData[$mappedColumn] = is_numeric($value) ? (float) $value : null;
                } elseif ($mappedColumn === 'sr_number') {
                    $itemData[$mappedColumn] = is_numeric($value) ? (int) $value : null;
                } else {
                    $itemData[$mappedColumn] = $value;
                }
            }

            if (empty($itemData['sr_number']) || empty($itemData['item_description'])) {
                continue;
            }

            $srNumber = $itemData['sr_number'];
            if (isset($rowSrNumbers[$srNumber])) {
                $duplicateSrNumbers[] = $srNumber;
                continue;
            }

            $rowSrNumbers[$srNumber] = true;
            $importRows[] = $itemData;
        }

        if (!empty($duplicateSrNumbers)) {
            return back()->withErrors(['inventory_file' => 'Duplicate SR# values found in uploaded file: ' . implode(', ', array_unique($duplicateSrNumbers)) . '. Please remove duplicate rows and try again.']);
        }

        if (!empty($rowSrNumbers)) {
            $existingSrNumbers = Item::whereIn('sr_number', array_keys($rowSrNumbers))
                ->pluck('sr_number')
                ->toArray();

            if (!empty($existingSrNumbers)) {
                return back()->withErrors(['inventory_file' => 'The following SR# values already exist in inventory: ' . implode(', ', $existingSrNumbers) . '. Please remove or update those items before importing.']);
            }
        }

        $imported = 0;
        foreach ($importRows as $itemData) {
            $quantity = $itemData['physical_stock'] ?? 0;
            $availability = $quantity > 0 ? 'available' : 'out_of_stock';

            Item::create(array_merge($itemData, ['availability' => $availability]));
            $imported++;
        }

        if ($imported === 0) {
            return back()->withErrors(['inventory_file' => 'No valid inventory rows were found in the uploaded file.']);
        }

        return redirect()->route('resource-officer')->with('success', "$imported item(s) imported successfully!");
    }

    public function inventory(Request $request)
    {
        // Validate the incoming array data
        $validated = $request->validate([
            'item_description' => 'required|array|min:1',
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
            'total_in.*' => 'nullable|numeric|min:0',
            'total_out' => 'nullable|array',
            'total_out.*' => 'nullable|numeric|min:0',
            'total_return' => 'nullable|array',
            'total_return.*' => 'nullable|numeric|min:0',
            'quantity_in_hand_current' => 'nullable|array',
            'quantity_in_hand_current.*' => 'nullable|numeric|min:0',
            'physical_stock' => 'required|array|min:1',
            'physical_stock.*' => 'required|numeric|min:0',
            'reconciliation' => 'nullable|array',
            'reconciliation.*' => 'nullable|string|max:255',
            'difference' => 'nullable|array',
            'difference.*' => 'nullable|numeric',
            'remarks' => 'nullable|array',
            'remarks.*' => 'nullable|string',
        ]);

        // Save each item
        foreach ($validated['item_description'] as $index => $description) {
            $quantity = $validated['physical_stock'][$index];
            $availability = $quantity > 0 ? 'available' : 'out_of_stock';

            Item::create([
                'item_description' => $description,
                'category_name' => $validated['category_name'][$index] ?? null,
                'supplier' => $validated['supplier'][$index] ?? null,
                'venue' => $validated['venue'][$index] ?? null,
                'barcode' => $validated['barcode'][$index] ?? null,
                'total_in' => $validated['total_in'][$index] ?? 0,
                'total_out' => $validated['total_out'][$index] ?? 0,
                'total_return' => $validated['total_return'][$index] ?? 0,
                'quantity_in_hand_current' => $validated['quantity_in_hand_current'][$index] ?? 0,
                'physical_stock' => $quantity,
                'reconciliation' => $validated['reconciliation'][$index] ?? null,
                'difference' => $validated['difference'][$index] ?? 0,
                'remarks' => $validated['remarks'][$index] ?? null,
                'availability' => $availability,
            ]);
        }

        return redirect()->route('resource-officer')->with('success', 'Inventory items added successfully!');
    }

    public function searchItems(Request $request)
    {
        $search = $request->query('search', '');
        $locationFilter = $request->query('location');
        $perPage = $request->query('per_page', 5);

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

        return response()->json([
            'items' => $items,
            'total' => $total,
        ]);
    }
}
