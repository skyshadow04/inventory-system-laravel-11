<x-layout>
    <div class="py-10 px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto">
            <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">{{ $importTitle ?? 'Import Inventory from Excel' }}</h1>
                    <p class="mt-1 text-sm text-slate-600">Upload a file containing the item list and add all rows to the inventory.</p>
                </div>
                <a href="{{ route('resource-officer') }}" class="inline-flex items-center px-4 py-2 rounded-lg bg-slate-100 text-slate-800 border border-slate-200 hover:bg-slate-200">Back to Inventory</a>
            </div>

            @if (session('success'))
                <div class="mb-4 rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-700">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('info'))
                <div class="mb-4 rounded-xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-700">
                    {{ session('info') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="mb-4 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                    <p class="font-semibold">Please fix the following errors:</p>
                    <ul class="mt-2 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
                <form action="{{ $importAction ?? route('resource-officer.import.upload') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <div>
                        <label for="inventory_file" class="block text-sm font-semibold text-slate-700">Excel or CSV file</label>
                        <input id="inventory_file" name="inventory_file" type="file" accept=".xlsx,.csv" required class="mt-2 block w-full text-sm text-slate-700 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white" />
                        <p class="mt-2 text-sm text-slate-500">Accepted formats: <strong>.xlsx</strong>, <strong>.csv</strong>. The first row must contain headers.</p>
                    </div>

                    <div class="rounded-2xl bg-slate-50 border border-slate-200 p-4 text-sm text-slate-700">
                        <p class="font-semibold mb-2">Expected columns from your inventory file</p>
                        <ul class="list-disc list-inside space-y-1">
                            <li><strong>Sr#</strong> &mdash; will be used as the item ID (required, unique)</li>
                            <li><strong>Item Description</strong> &mdash; item description (required)</li>
                            <li><strong>Quantity In Hand (Current)</strong> &mdash; current quantity (required)</li>
                            <li><strong>Operation imports</strong> automatically prefix SR# values with <strong>OP</strong></li>
                            <li><strong>Category Name</strong> &mdash; item category</li>
                            <li><strong>Location</strong> &mdash; item location</li>
                            <li><strong>Venue</strong> &mdash; location</li>
                            <li><strong>Barcode#</strong> &mdash; barcode</li>
                            <li><strong>Supplier</strong> &mdash; supplier</li>
                            <li><strong>Total (In)</strong>, <strong>Total (Out)</strong>, <strong>Total (Return)</strong> &mdash; totals</li>
                            <li><strong>Physical Stock</strong>, <strong>Reconcilation</strong>, <strong>Difference</strong> &mdash; stock details</li>
                            <li><strong>Remarks</strong> &mdash; additional notes</li>
                        </ul>
                        <p class="mt-2 text-slate-600">All columns are optional except Sr#, Item Description, and Quantity In Hand (Current).</p>
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
                        <button type="submit" class="inline-flex justify-center rounded-xl bg-blue-600 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-blue-700">Upload and Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout>
