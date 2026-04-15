<x-layout>
    <div class="py-10 px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl mx-auto bg-white rounded-2xl shadow-lg border border-gray-200 p-6 sm:p-8">
            <h1 class="text-2xl font-bold mb-4">Edit Inventory Item</h1>
            @if (session('success'))
                <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded-lg">{{ session('success') }}</div>
            @endif
            <form action="{{ route('resource-officer.item.update', $item) }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-medium text-gray-700">Sr# (ID)</label>
                    <input type="number" name="sr_number" value="{{ old('sr_number', $item->sr_number) }}" required disabled class="mt-1 block w-full border border-gray-300 rounded-lg px-3 py-2 bg-gray-100 text-gray-700" />
                    <p class="text-xs text-gray-500 mt-1">Primary ID - cannot be edited</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Item Description *</label>
                    <input type="text" name="item_description" value="{{ old('item_description', $item->item_description) }}" required class="mt-1 block w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-blue-500 focus:border-blue-500" />
                    @error('item_description')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Category Name</label>
                    <input type="text" name="category_name" value="{{ old('category_name', $item->category_name) }}" class="mt-1 block w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-blue-500 focus:border-blue-500" />
                    @error('category_name')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Supplier</label>
                    <input type="text" name="supplier" value="{{ old('supplier', $item->supplier) }}" class="mt-1 block w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-blue-500 focus:border-blue-500" />
                    @error('supplier')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Venue</label>
                    <input type="text" name="venue" value="{{ old('venue', $item->venue) }}" class="mt-1 block w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-blue-500 focus:border-blue-500" />
                    @error('venue')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Barcode#</label>
                    <input type="text" name="barcode" value="{{ old('barcode', $item->barcode) }}" class="mt-1 block w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-blue-500 focus:border-blue-500" />
                    @error('barcode')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Total In</label>
                        <input type="number" name="total_in" value="{{ old('total_in', $item->total_in) }}" step="0.01" class="mt-1 block w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-blue-500 focus:border-blue-500" />
                        @error('total_in')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Total Out</label>
                        <input type="number" name="total_out" value="{{ old('total_out', $item->total_out) }}" step="0.01" class="mt-1 block w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-blue-500 focus:border-blue-500" />
                        @error('total_out')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Total Return</label>
                        <input type="number" name="total_return" value="{{ old('total_return', $item->total_return) }}" step="0.01" class="mt-1 block w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-blue-500 focus:border-blue-500" />
                        @error('total_return')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Quantity In Hand (Current)</label>
                        <input type="number" name="quantity_in_hand_current" value="{{ old('quantity_in_hand_current', $item->quantity_in_hand_current) }}" step="0.01" min="0" class="mt-1 block w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-blue-500 focus:border-blue-500" />
                        @error('quantity_in_hand_current')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Physical Stock *</label>
                        <input type="number" name="physical_stock" value="{{ old('physical_stock', $item->physical_stock) }}" required step="0.01" min="0" class="mt-1 block w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-blue-500 focus:border-blue-500" />
                        @error('physical_stock')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Difference</label>
                        <input type="number" name="difference" value="{{ old('difference', $item->difference) }}" step="0.01" class="mt-1 block w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-blue-500 focus:border-blue-500" />
                        @error('difference')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Reconciliation</label>
                    <input type="text" name="reconciliation" value="{{ old('reconciliation', $item->reconciliation) }}" class="mt-1 block w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-blue-500 focus:border-blue-500" />
                    @error('reconciliation')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Remarks</label>
                    <textarea name="remarks" rows="3" class="mt-1 block w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-blue-500 focus:border-blue-500">{{ old('remarks', $item->remarks) }}</textarea>
                    @error('remarks')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Availability</label>
                    <div class="mt-1 p-3 bg-gray-50 border border-gray-300 rounded-lg">
                        @php
                            $isAvailable = ($item->physical_stock ?? 0) > 0 && $item->availability === 'available';
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            {{ $isAvailable ? 'bg-green-100 text-green-800' :
                               ($item->availability == 'unavailable' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                            {{ $isAvailable ? 'Available' : ucfirst(str_replace('_', ' ', $item->availability)) }}
                        </span>
                        <p class="text-xs text-gray-500 mt-1">Availability is automatically set based on physical stock</p>
                    </div>
                </div>

                <div class="flex gap-3 mt-4">
                    <button type="submit" class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700">Update</button>
                    <a href="{{ route('resource-officer') }}" class="px-4 py-2 rounded-lg bg-gray-200 text-gray-700 hover:bg-gray-300">Back to list</a>
                </div>
            </form>
        </div>
    </div>
</x-layout>
