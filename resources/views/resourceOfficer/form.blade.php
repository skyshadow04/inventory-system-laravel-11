<x-layout>
    <div class="container">
        <h1 class="text-2xl font-bold mb-4">Resource Officer Forms</h1>
        <p>Welcome to the resource officer forms page. Here you can manage your inventory.</p>
    </div>
    <div class="container mt-4">
        <h2 class="text-xl font-semibold mb-2">Inventory Management</h2>
        <p>Use the form below to add new items to your inventory.</p>
        <p class="text-sm text-gray-500 mb-4">💡 Availability status is automatically set based on physical stock (Available > 0, Out of Stock = 0)</p>
        
        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                <h3 class="text-sm font-semibold text-red-800 mb-2">Validation Errors:</h3>
                <ul class="list-disc list-inside text-red-700 text-sm space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <!-- Inventory form goes here -->
        <form id="inventory-form" class="space-y-4" action="{{ route('resource-officer.inventory') }}" method="POST">
            @csrf

            <!-- Location Selection Section -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <label for="location-select" class="block text-sm font-semibold text-gray-700 mb-2">Select Location *</label>
                <select id="location-select" class="block w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <option value="">-- Choose a location --</option>
                    <option value="APP">APP</option>
                    <option value="Engg / INS">Engineering Items</option>
                    <option value="ENGG / MEC">Mechanical Items</option>
                    <option value="OPTNS">Operation Items</option>
                </select>
                <p class="text-sm text-gray-600 mt-2">Each location has different required fields. Select a location above to proceed.</p>
                <div id="form-debug" class="mt-3 p-2 bg-white rounded border border-blue-300 text-xs font-mono hidden">
                    <div>Location: <span id="debug-location">None</span></div>
                    <div>Items: <span id="debug-items">0</span></div>
                    <div>Submit Enabled: <span id="debug-submit-btn">false</span></div>
                </div>
            </div>

            <!-- Hidden input to store selected location -->
            <input type="hidden" id="selected-location" name="selected_location" value="">

            <div id="items-wrapper" class="space-y-6">
            </div>

            <div class="flex items-center gap-2 mb-4">
                <button id="add-item-btn" type="button" class="inline-flex items-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-black bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                    + Add another item
                </button>
                <button id="submit-btn" type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-black bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                    Save Items
                </button>

                <a href="{{ route('resource-officer') }}" class="inline-flex items-center px-4 py-2 rounded-lg bg-slate-100 text-slate-800 border border-slate-200 hover:bg-slate-200">Back to Inventory</a>
            </div>
        </form>

        <script>
            (function () {
                const locationSelect = document.getElementById('location-select');
                const selectedLocationInput = document.getElementById('selected-location');
                const itemsWrapper = document.getElementById('items-wrapper');
                const addBtn = document.getElementById('add-item-btn');
                const submitBtn = document.getElementById('submit-btn');
                const debugBox = document.getElementById('form-debug');
                const debugLocation = document.getElementById('debug-location');
                const debugItems = document.getElementById('debug-items');
                const debugSubmitBtn = document.getElementById('debug-submit-btn');
                let currentLocation = '';
                const initialAppSrNumber = {{ $nextAppSrNumber ?? 1000 }};
                let currentAppSrNumber = initialAppSrNumber;

                function updateDebugInfo() {
                    debugLocation.textContent = currentLocation || 'None';
                    debugItems.textContent = itemsWrapper.querySelectorAll('.item-row').length;
                    debugSubmitBtn.textContent = !submitBtn.disabled;
                }

                // Define form templates for each location
                const formTemplates = {
                    APP: {
                        label: 'APP',
                        fields: [
                            { name: 'sr_number[]', label: 'SR# (Item ID)', type: 'text', readonly: true, placeholder: 'Auto-generated on save' },
                            { name: 'item_description[]', label: 'Item Description', type: 'text', required: true, placeholder: 'e.g. Dell Laptop' },
                            { name: 'category_name[]', label: 'Category', type: 'text', placeholder: 'e.g. Electronics' },
                            { name: 'supplier[]', label: 'Supplier', type: 'text', placeholder: 'e.g. Dell Inc.' },
                            { name: 'venue[]', label: 'Venue', type: 'text', placeholder: 'e.g. Room 101' },
                            { name: 'barcode[]', label: 'Barcode', type: 'text', placeholder: 'e.g. 123456789' },
                            { name: 'total_in[]', label: 'Total In', type: 'number', step: '0.01', placeholder: '0' },
                            { name: 'total_out[]', label: 'Total Out', type: 'number', step: '0.01', placeholder: '0' },
                            { name: 'total_return[]', label: 'Total Return', type: 'number', step: '0.01', placeholder: '0' },
                            { name: 'quantity_in_hand_current[]', label: 'Quantity in Hand', type: 'number', step: '0.01', placeholder: '0' },
                            { name: 'physical_stock[]', label: 'Physical Stock', type: 'number', required: true, step: '0.01', placeholder: '0' },
                            { name: 'reconciliation[]', label: 'Reconciliation', type: 'text', placeholder: 'e.g. Matched' },
                            { name: 'difference[]', label: 'Difference', type: 'number', step: '0.01', placeholder: '0' },
                            { name: 'remarks[]', label: 'Remarks', type: 'textarea', placeholder: 'Additional notes...' }
                        ]
                    },
                    'Engg / INS': {
                        label: 'Engineering Items',
                        fields: [
                            { name: 'sr_number_eng[]', label: 'SR# (SR Number)', type: 'text', readonly: true, placeholder: 'Auto-generated on save' },
                            { name: 'item_description_eng[]', label: 'Item Description', type: 'text', required: true, placeholder: 'e.g. Measuring Tool' },
                            { name: 'category_name_eng[]', label: 'Category', type: 'text', placeholder: 'e.g. Tools' },
                            { name: 'make_eng[]', label: 'Make', type: 'text', placeholder: 'e.g. Brand Name' },
                            { name: 'venue_eng[]', label: 'Venue', type: 'text', placeholder: 'e.g. Lab 1' },
                            { name: 'barcode_eng[]', label: 'Barcode', type: 'text', placeholder: 'e.g. 123456789' },
                            { name: 'quantity_in_hand_eng[]', label: 'Quantity in Hand', type: 'number', step: '0.01', placeholder: '0' },
                            { name: 'physical_stock_eng[]', label: 'Physical Stock', type: 'number', required: true, step: '0.01', placeholder: '0' },
                            { name: 'remarks_eng[]', label: 'Remarks', type: 'textarea', placeholder: 'Additional notes...' }
                        ]
                    },
                    'ENGG / MEC': {
                        label: 'Mechanical Items',
                        fields: [
                            { name: 'sr_no_mech[]', label: 'SR# (Serial Number)', type: 'text', readonly: true, placeholder: 'Auto-generated on save' },
                            { name: 'description_mech[]', label: 'Description', type: 'text', required: true, placeholder: 'e.g. Precision Tool' },
                            { name: 'category_name_mech[]', label: 'Category', type: 'text', placeholder: 'e.g. Precision Tools' },
                            { name: 'total_qty_mech[]', label: 'Total Quantity', type: 'number', step: '0.01', placeholder: '0' },
                            { name: 'precision_measurement_class_1_mech[]', label: 'Precision Measurement Class 1', type: 'text', placeholder: 'e.g. Class A' },
                            { name: 'w_18_b_mech[]', label: 'W 18 B', type: 'text', placeholder: 'Value' },
                            { name: 'w_17_mech[]', label: 'W 17', type: 'text', placeholder: 'Value' },
                            { name: 'w_18_a_compressor_area_mech[]', label: 'W 18 A (Compressor Area)', type: 'text', placeholder: 'Value' },
                            { name: 'balance_qty_in_store_mech[]', label: 'Balance Qty in Store', type: 'number', step: '0.01', placeholder: '0' },
                            { name: 'physical_stock_mech[]', label: 'Physical Stock', type: 'number', required: true, step: '0.01', placeholder: '0' },
                            { name: 'remarks_mech[]', label: 'Remarks', type: 'textarea', placeholder: 'Additional notes...' }
                        ]
                    },
                    OPTNS: {
                        label: 'Operation Items',
                        fields: [
                            { name: 'sr_no_ops[]', label: 'SR# (Serial Number)', type: 'text', readonly: true, placeholder: 'Auto-generated on save' },
                            { name: 'item_description_ops[]', label: 'Item Description', type: 'text', required: true, placeholder: 'e.g. Operation Equipment' },
                            { name: 'category_name_ops[]', label: 'Category', type: 'text', placeholder: 'e.g. Equipment' },
                            { name: 'supplier_ops[]', label: 'Supplier', type: 'text', placeholder: 'e.g. Supplier Name' },
                            { name: 'venue_ops[]', label: 'Venue', type: 'text', placeholder: 'e.g. Workshop' },
                            { name: 'barcode_ops[]', label: 'Barcode', type: 'text', placeholder: 'e.g. 123456789' },
                            { name: 'total_in_ops[]', label: 'Total In', type: 'number', step: '0.01', placeholder: '0' },
                            { name: 'total_out_ops[]', label: 'Total Out', type: 'number', step: '0.01', placeholder: '0' },
                            { name: 'total_return_ops[]', label: 'Total Return', type: 'number', step: '0.01', placeholder: '0' },
                            { name: 'quantity_in_hand_ops[]', label: 'Quantity in Hand', type: 'number', step: '0.01', placeholder: '0' },
                            { name: 'physical_stock_ops[]', label: 'Physical Stock', type: 'number', required: true, step: '0.01', placeholder: '0' },
                            { name: 'reconciliation_ops[]', label: 'Reconciliation', type: 'text', placeholder: 'e.g. Matched' },
                            { name: 'difference_ops[]', label: 'Difference', type: 'number', step: '0.01', placeholder: '0' },
                            { name: 'remarks_ops[]', label: 'Remarks', type: 'textarea', placeholder: 'Additional notes...' }
                        ]
                    }
                };

                function updateRemoveButtons() {
                    const removeButtons = itemsWrapper.querySelectorAll('.remove-item');
                    removeButtons.forEach((btn) => {
                        btn.style.display = removeButtons.length > 1 ? 'inline-flex' : 'none';
                    });
                    updateDebugInfo();
                }

                function updateItemNumbers() {
                    const rows = itemsWrapper.querySelectorAll('.item-row');
                    rows.forEach((row, index) => {
                        row.querySelector('h3').textContent = `Item #${index + 1}`;
                    });
                }

                function createFieldHTML(field) {
                    const isRequired = field.required ? ' required' : '';
                    const requiredMark = field.required ? ' *' : '';
                    const isReadonly = field.readonly ? ' readonly' : '';
                    const readonlyClass = field.readonly ? ' bg-gray-100 cursor-not-allowed' : '';
                    
                    if (field.type === 'textarea') {
                        return `
                            <div class="md:col-span-2 lg:col-span-3 xl:col-span-4">
                                <label class="block text-sm font-medium text-gray-700">${field.label}${requiredMark}</label>
                                <textarea name="${field.name}" rows="2"${isRequired}${isReadonly} class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm${readonlyClass}" placeholder="${field.placeholder}"></textarea>
                            </div>
                        `;
                    } else {
                        const step = field.step ? ` step="${field.step}"` : '';
                        return `
                            <div>
                                <label class="block text-sm font-medium text-gray-700">${field.label}${requiredMark}</label>
                                <input type="${field.type}" name="${field.name}"${isRequired}${step}${isReadonly} min="0" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm${readonlyClass}" placeholder="${field.placeholder}" />
                            </div>
                        `;
                    }
                }

                function createRow() {
                    if (!currentLocation) return null;
                    
                    const index = itemsWrapper.querySelectorAll('.item-row').length;
                    const row = document.createElement('section');
                    row.className = 'item-row bg-white p-6 rounded-lg border border-gray-200';
                    row.setAttribute('data-index', index);
                    row.setAttribute('data-location', currentLocation);

                    const template = formTemplates[currentLocation];
                    let fieldsHTML = '';
                    
                    template.fields.forEach(field => {
                        fieldsHTML += createFieldHTML(field);
                    });

                    row.innerHTML = `
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">Item #${index + 1}</h3>
                                <p class="text-sm text-gray-500">${template.label}</p>
                            </div>
                            <button type="button" class="remove-item inline-flex items-center px-3 py-2 rounded-md text-white bg-red-500 hover:bg-red-600 focus:outline-none">
                                Remove
                            </button>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                            ${fieldsHTML}
                        </div>
                    `;

                    // SR values are generated by the database id and identifier after save
                    const srField = row.querySelector('[name="sr_number[]"], [name="sr_number_eng[]"], [name="sr_no_mech[]"], [name="sr_no_ops[]"]');
                    if (srField) {
                        if (currentLocation === 'APP') {
                            srField.value = currentAppSrNumber;
                            currentAppSrNumber++;
                        } else {
                            srField.value = '';
                        }
                    }

                    row.querySelector('.remove-item').addEventListener('click', () => {
                        row.remove();
                        updateItemNumbers();
                        updateRemoveButtons();
                    });

                    return row;
                }

                // Location selection handler
                locationSelect.addEventListener('change', () => {
                    const selectedLocation = locationSelect.value;
                    
                    if (!selectedLocation) {
                        itemsWrapper.innerHTML = '';
                        addBtn.disabled = true;
                        submitBtn.disabled = true;
                        currentLocation = '';
                        selectedLocationInput.value = '';
                        debugBox.classList.add('hidden');
                        console.log('Location cleared');
                        return;
                    }

                    debugBox.classList.remove('hidden');
                    currentLocation = selectedLocation;
                    selectedLocationInput.value = selectedLocation;
                    if (currentLocation === 'APP') {
                        currentAppSrNumber = initialAppSrNumber;
                    }
                    console.log('Location selected:', selectedLocation, 'Hidden input value:', selectedLocationInput.value);
                    
                    // Clear existing items and add first one
                    itemsWrapper.innerHTML = '';
                    const firstRow = createRow();
                    if (firstRow) {
                        itemsWrapper.appendChild(firstRow);
                        addBtn.disabled = false;
                        submitBtn.disabled = false;
                        updateRemoveButtons();
                        console.log('First item row created');
                    }
                    updateDebugInfo();
                });

                // Add button handler
                addBtn.addEventListener('click', () => {
                    const newRow = createRow();
                    if (newRow) {
                        itemsWrapper.appendChild(newRow);
                        updateRemoveButtons();
                    }
                });

                // Form submission handler
                document.getElementById('inventory-form').addEventListener('submit', (e) => {
                    console.log('Form submit triggered');
                    console.log('Current location:', currentLocation);
                    console.log('Selected location input value:', selectedLocationInput.value);
                    console.log('Items count:', itemsWrapper.querySelectorAll('.item-row').length);
                    
                    // Check if location is selected
                    if (!currentLocation || !selectedLocationInput.value) {
                        e.preventDefault();
                        alert('Please select a location first');
                        console.error('Location not selected');
                        return false;
                    }

                    // Check if at least one item has been added
                    const items = itemsWrapper.querySelectorAll('.item-row');
                    if (items.length === 0) {
                        e.preventDefault();
                        alert('Please add at least one item');
                        console.error('No items added');
                        return false;
                    }

                    // Check if all required fields are filled
                    let hasErrors = false;
                    let errorMessages = [];
                    items.forEach((item, index) => {
                        const requiredInputs = item.querySelectorAll('[required]');
                        requiredInputs.forEach(input => {
                            if (!input.value || input.value.trim() === '') {
                                hasErrors = true;
                                const label = input.previousElementSibling?.textContent || input.name;
                                errorMessages.push(`Item ${index + 1}: ${label} is required`);
                                input.style.borderColor = '#ef4444';
                                input.style.backgroundColor = '#fee2e2';
                            } else {
                                input.style.borderColor = '';
                                input.style.backgroundColor = '';
                            }
                        });
                    });

                    if (hasErrors) {
                        e.preventDefault();
                        const message = errorMessages.length > 0 
                            ? 'Errors:\n' + errorMessages.join('\n')
                            : 'Please fill in all required fields (marked with *)';
                        alert(message);
                        console.error('Validation errors:', errorMessages);
                        return false;
                    }

                    console.log('Form validation passed, submitting...');
                });
            })();
        </script>
    </div>
</x-layout>
