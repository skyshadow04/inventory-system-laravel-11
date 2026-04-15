<x-layout>
    <div class="py-10 px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl mx-auto bg-white rounded-2xl shadow-lg border border-gray-200 p-6 sm:p-8">
            <h1 class="text-2xl font-bold mb-4">Edit Inventory Item</h1>
            @if (session('success'))
                <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded-lg">{{ session('success') }}</div>
            @endif
            <form action="{{ route('manager.item.update', $item) }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-medium text-gray-700">Item Name</label>
                    <input type="text" name="name" value="{{ old('name', $item->name) }}" required class="mt-1 block w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-blue-500 focus:border-blue-500" />
                    @error('name')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Quantity</label>
                    <input type="number" name="count" value="{{ old('count', $item->count) }}" required min="0" class="mt-1 block w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-blue-500 focus:border-blue-500" />
                    @error('count')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" class="mt-1 block w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-blue-500 focus:border-blue-500">{{ old('description', $item->description) }}</textarea>
                    @error('description')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
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
                    <a href="/manager" class="px-4 py-2 rounded-lg bg-gray-200 text-gray-700 hover:bg-gray-300">Back to list</a>
                </div>
            </form>
        </div>
    </div>
</x-layout>