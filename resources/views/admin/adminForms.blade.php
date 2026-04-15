<x-layout>
    <div class="container">
        <h1 class="text-2xl font-bold mb-4">Manager Forms</h1>
        <p>Welcome to the manager forms page. Here you can manage your inventory and other operational tasks.</p>
    </div>
    <div class="container mt-4">
        <h2 class="text-xl font-semibold mb-2">Inventory Management</h2>
        <p>Use the form below to add new items to your inventory.</p>
        <p class="text-sm text-gray-500 mb-4">💡 Availability status is automatically set based on physical stock (Available > 0, Out of Stock = 0)</p>
        <!-- Inventory form goes here -->
        <form id="inventory-form" class="space-y-4" action="{{ route('admin.inventory') }}" method="POST">
            @csrf

            <div id="items-wrapper" class="space-y-6">
                <section class="item-row bg-white p-6 rounded-lg border border-gray-200" data-index="0">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Item #1</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Sr# *</label>
                            <input type="number" name="sr_number[]" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="e.g. 1001" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Item Description *</label>
                            <input type="text" name="item_description[]" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="e.g. Dell Laptop" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Category</label>
                            <input type="text" name="category_name[]" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="e.g. Electronics" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Supplier</label>
                            <input type="text" name="supplier[]" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="e.g. Dell Inc." />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Venue</label>
                            <input type="text" name="venue[]" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="e.g. Room 101" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Barcode</label>
                            <input type="text" name="barcode[]" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="e.g. 123456789" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Total In</label>
                            <input type="number" name="total_in[]" min="0" step="0.01" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="0" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Total Out</label>
                            <input type="number" name="total_out[]" min="0" step="0.01" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="0" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Total Return</label>
                            <input type="number" name="total_return[]" min="0" step="0.01" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="0" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Quantity in Hand</label>
                            <input type="number" name="quantity_in_hand_current[]" min="0" step="0.01" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="0" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Physical Stock *</label>
                            <input type="number" name="physical_stock[]" required min="0" step="0.01" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="0" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Reconciliation</label>
                            <input type="text" name="reconciliation[]" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="e.g. Matched" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Difference</label>
                            <input type="number" name="difference[]" step="0.01" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="0" />
                        </div>
                        <div class="md:col-span-2 lg:col-span-3 xl:col-span-4">
                            <label class="block text-sm font-medium text-gray-700">Remarks</label>
                            <textarea name="remarks[]" rows="2" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Additional notes..."></textarea>
                        </div>
                    </div>
                </section>
            </div>

            <div class="flex items-center gap-2">
                <button id="add-item-btn" type="button" class="inline-flex items-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-black bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    + Add another item
                </button>
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-black bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Save Items
                </button>
            </div>
        </form>

        <script>
            (function () {
                const itemsWrapper = document.getElementById('items-wrapper');
                const addBtn = document.getElementById('add-item-btn');

                function updateRemoveButtons() {
                    const removeButtons = itemsWrapper.querySelectorAll('.remove-item');
                    removeButtons.forEach((btn, index) => {
                        btn.style.display = removeButtons.length > 1 ? 'inline-flex' : 'none';
                    });
                }

                function createRow(index) {
                    const row = document.createElement('section');
                    row.className = 'item-row bg-white p-6 rounded-lg border border-gray-200';
                    row.setAttribute('data-index', index);

                    row.innerHTML = `
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Item #${index + 1}</h3>
                            <button type="button" class="remove-item inline-flex items-center px-3 py-2 rounded-md text-white bg-red-500 hover:bg-red-600 focus:outline-none">
                                Remove
                            </button>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Sr# *</label>
                                <input type="number" name="sr_number[]" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="e.g. 1001" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Item Description *</label>
                                <input type="text" name="item_description[]" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="e.g. Dell Laptop" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Category</label>
                                <input type="text" name="category_name[]" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="e.g. Electronics" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Supplier</label>
                                <input type="text" name="supplier[]" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="e.g. Dell Inc." />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Venue</label>
                                <input type="text" name="venue[]" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="e.g. Room 101" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Barcode</label>
                                <input type="text" name="barcode[]" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="e.g. 123456789" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Total In</label>
                                <input type="number" name="total_in[]" min="0" step="0.01" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="0" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Total Out</label>
                                <input type="number" name="total_out[]" min="0" step="0.01" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="0" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Total Return</label>
                                <input type="number" name="total_return[]" min="0" step="0.01" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="0" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Quantity in Hand</label>
                                <input type="number" name="quantity_in_hand_current[]" min="0" step="0.01" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="0" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Physical Stock *</label>
                                <input type="number" name="physical_stock[]" required min="0" step="0.01" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="0" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Reconciliation</label>
                                <input type="text" name="reconciliation[]" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="e.g. Matched" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Difference</label>
                                <input type="number" name="difference[]" step="0.01" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="0" />
                            </div>
                            <div class="md:col-span-2 lg:col-span-3 xl:col-span-4">
                                <label class="block text-sm font-medium text-gray-700">Remarks</label>
                                <textarea name="remarks[]" rows="2" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Additional notes..."></textarea>
                            </div>
                        </div>
                    `;

                    row.querySelector('.remove-item').addEventListener('click', () => {
                        row.remove();
                        updateItemNumbers();
                        updateRemoveButtons();
                    });

                    return row;
                }

                function updateItemNumbers() {
                    const rows = itemsWrapper.querySelectorAll('.item-row');
                    rows.forEach((row, index) => {
                        const title = row.querySelector('h3');
                        if (title) {
                            title.textContent = `Item #${index + 1}`;
                        }
                        row.setAttribute('data-index', index);
                    });
                }

                function updateRemoveButtons() {
                    const rows = itemsWrapper.querySelectorAll('.item-row');
                    rows.forEach((row, index) => {
                        const removeBtn = row.querySelector('.remove-item');
                        if (removeBtn) {
                            removeBtn.style.display = rows.length > 1 ? 'inline-flex' : 'none';
                        }
                    });
                }

                addBtn.addEventListener('click', () => {
                    const currentRows = itemsWrapper.querySelectorAll('.item-row').length;
                    const newRow = createRow(currentRows);
                    itemsWrapper.appendChild(newRow);
                    updateRemoveButtons();
                });

                // Update initial row structure
                const initialRow = itemsWrapper.querySelector('.item-row');
                if (initialRow) {
                    initialRow.innerHTML = `
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Item #1</h3>
                            <button type="button" class="remove-item inline-flex items-center px-3 py-2 rounded-md text-white bg-red-500 hover:bg-red-600 focus:outline-none">
                                Remove
                            </button>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Sr# *</label>
                                <input type="number" name="sr_number[]" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="e.g. 1001" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Item Description *</label>
                                <input type="text" name="item_description[]" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="e.g. Dell Laptop" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Category</label>
                                <input type="text" name="category_name[]" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="e.g. Electronics" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Supplier</label>
                                <input type="text" name="supplier[]" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="e.g. Dell Inc." />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Venue</label>
                                <input type="text" name="venue[]" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="e.g. Room 101" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Barcode</label>
                                <input type="text" name="barcode[]" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="e.g. 123456789" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Total In</label>
                                <input type="number" name="total_in[]" min="0" step="0.01" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="0" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Total Out</label>
                                <input type="number" name="total_out[]" min="0" step="0.01" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="0" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Total Return</label>
                                <input type="number" name="total_return[]" min="0" step="0.01" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="0" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Quantity in Hand</label>
                                <input type="number" name="quantity_in_hand_current[]" min="0" step="0.01" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="0" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Physical Stock *</label>
                                <input type="number" name="physical_stock[]" required min="0" step="0.01" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="0" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Reconciliation</label>
                                <input type="text" name="reconciliation[]" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="e.g. Matched" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Difference</label>
                                <input type="number" name="difference[]" step="0.01" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="0" />
                            </div>
                            <div class="md:col-span-2 lg:col-span-3 xl:col-span-4">
                                <label class="block text-sm font-medium text-gray-700">Remarks</label>
                                <textarea name="remarks[]" rows="2" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Additional notes..."></textarea>
                            </div>
                        </div>
                    `;
                    initialRow.querySelector('.remove-item').addEventListener('click', function () {
                        this.closest('.item-row').remove();
                        updateItemNumbers();
                        updateRemoveButtons();
                    });
                }

                updateRemoveButtons();
            })();
        </script>
    </div>


</x-layout>

