<?php include TEMPLATE_DIR . 'layouts/header.tpl'; ?>

    <div class="page-container-narrow">
        <div class="form-container">
            <div class="form-header">
                <h2><i class="bi bi-pencil-square"></i> Edit Flight Ticket</h2>
                <p>Reference: <strong style="color: var(--primary);"><?php echo htmlspecialchars($ticket['booking_reference']); ?></strong></p>
            </div>

            <div class="form-body">
                <?php if (!empty($success)): ?>
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle-fill"></i> <?php echo htmlspecialchars($success); ?>
                        <button class="alert-close">×</button>
                    </div>
                <?php endif; ?>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle-fill"></i> <?php echo htmlspecialchars($error); ?>
                        <button class="alert-close">×</button>
                    </div>
                <?php endif; ?>

                <form method="POST" id="editTicketForm" onsubmit="App.submitForm('edit_ticket', <?php echo $ticket_id; ?>); return false;">
                    <!-- Client Information -->
                    <div class="form-section">
                        <div class="form-section-title">
                            <i class="bi bi-person-fill"></i> Client Information
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label required">Client Name</label>
                                <input type="text" class="form-control" name="client_name" id="client_name" value="<?php echo htmlspecialchars($ticket['client_name']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="client_email" id="client_email" value="<?php echo htmlspecialchars($ticket['client_email']); ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Phone</label>
                            <input type="tel" class="form-control" name="client_phone" id="client_phone" value="<?php echo htmlspecialchars($ticket['client_phone']); ?>">
                        </div>
                    </div>

                    <!-- Flight Details -->
                    <div class="form-section">
                        <div class="form-section-title">
                            <i class="bi bi-airplane-fill"></i> Flight Details
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label required">Departure City</label>
                                <div style="display: flex; gap: 8px;">
                                    <div style="flex: 1;">
                                        <select name="departure_city" id="departure_city" required>
                                            <option value="">Select City...</option>
                                            <?php foreach ($airports as $ap): ?>
                                                <option value="<?php echo htmlspecialchars($ap['city_name']); ?>" <?php echo $ticket['departure_city'] === $ap['city_name'] ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($ap['city_name']); ?> (<?php echo htmlspecialchars($ap['iata_code']); ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <button type="button" class="btn btn-secondary" style="padding: 0 10px;" onclick="openQuickAdd('airport', 'departure')" title="Add New City">
                                        <i class="bi bi-plus-lg"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label required">Arrival City</label>
                                <div style="display: flex; gap: 8px;">
                                    <div style="flex: 1;">
                                        <select name="arrival_city" id="arrival_city" required>
                                            <option value="">Select City...</option>
                                            <?php foreach ($airports as $ap): ?>
                                                <option value="<?php echo htmlspecialchars($ap['city_name']); ?>" <?php echo $ticket['arrival_city'] === $ap['city_name'] ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($ap['city_name']); ?> (<?php echo htmlspecialchars($ap['iata_code']); ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <button type="button" class="btn btn-secondary" style="padding: 0 10px;" onclick="openQuickAdd('airport', 'arrival')" title="Add New City">
                                        <i class="bi bi-plus-lg"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label required">Departure Date</label>
                                <input type="text" class="form-control" name="departure_date" id="departure_date" value="<?php echo $ticket['departure_date']; ?>" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label required">Departure Time</label>
                                <input type="text" class="form-control" name="departure_time" id="departure_time" value="<?php echo $ticket['departure_time']; ?>" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label required">Arrival Time</label>
                            <input type="text" class="form-control" name="arrival_time" id="arrival_time" value="<?php echo $ticket['arrival_time']; ?>" required>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label required">Airline Name</label>
                                <div style="display: flex; gap: 8px;">
                                    <div style="flex: 1;">
                                        <select name="airline_name" id="airline_name" required>
                                            <option value="">Select Airline...</option>
                                            <?php foreach ($airlines as $al): ?>
                                                <option value="<?php echo htmlspecialchars($al['name']); ?>" <?php echo $ticket['airline_name'] === $al['name'] ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($al['name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <button type="button" class="btn btn-secondary" style="padding: 0 10px;" onclick="openQuickAdd('airline')" title="Add New Airline">
                                        <i class="bi bi-plus-lg"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label required">Flight Number</label>
                                <input type="text" class="form-control" name="flight_number" id="flight_number" value="<?php echo htmlspecialchars($ticket['flight_number']); ?>" required>
                            </div>
                        </div>
                    </div>

                    <!-- Pricing & Status -->
                    <div class="form-section">
                        <div class="form-section-title">
                            <i class="bi bi-currency-rupee"></i> Pricing & Status
                        </div>
                        <div class="form-row" style="grid-template-columns: 1fr 1fr 1fr;">
                            <div class="form-group">
                                <label class="form-label required">Passengers</label>
                                <input type="number" class="form-control" name="passenger_count" id="passenger_count" value="<?php echo $ticket['passenger_count']; ?>" min="1" required oninput="calcTotal()">
                            </div>
                            <div class="form-group">
                                <label class="form-label required">Price / Passenger</label>
                                <input type="number" class="form-control" name="ticket_price" id="ticket_price" value="<?php echo $ticket['ticket_price']; ?>" step="0.01" min="0" required oninput="calcTotal()">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Status</label>
                                <select class="form-select" name="status" id="status">
                                    <option value="pending" <?php echo $ticket['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="confirmed" <?php echo $ticket['status'] === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                    <option value="cancelled" <?php echo $ticket['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                            </div>
                        </div>
                        <div style="font-size:14px; color: var(--primary); font-weight:600; margin-bottom:12px; padding:10px 14px; background:var(--primary-50); border-radius:var(--radius);">
                            Total: <span id="totalDisplay">₹<?php echo number_format($ticket['total_price'], 2); ?></span>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control" name="notes" id="notes" rows="3"><?php echo htmlspecialchars($ticket['notes']); ?></textarea>
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary" id="updateBtn">
                            <i class="bi bi-check-lg"></i> Update Ticket
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="App.route('view_ticket', <?php echo $ticket_id; ?>)" id="cancelEditBtn">
                            <i class="bi bi-x-lg"></i> Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Quick Add Airport Modal -->
    <div class="modal-overlay" id="airportModal">
        <div class="modal-box" style="width: 400px; text-align: left;">
            <h3>Add New City/Airport</h3>
            <div class="form-group" style="margin-top: 15px;">
                <label class="form-label">City Name</label>
                <input type="text" id="new_city_name" class="form-control" placeholder="e.g. Chennai">
            </div>
            <div class="form-group">
                <label class="form-label">Airport Code (IATA)</label>
                <input type="text" id="new_iata_code" class="form-control" placeholder="e.g. MAA" maxlength="3">
            </div>
            <div class="modal-actions" style="margin-top: 20px;">
                <button class="btn btn-secondary" onclick="closeQuickAdd('airport')">Cancel</button>
                <button class="btn btn-primary" onclick="submitQuickAdd('airport')">Add City</button>
            </div>
        </div>
    </div>

    <!-- Quick Add Airline Modal -->
    <div class="modal-overlay" id="airlineModal">
        <div class="modal-box" style="width: 400px; text-align: left;">
            <h3>Add New Airline</h3>
            <div class="form-group" style="margin-top: 15px;">
                <label class="form-label">Airline Name</label>
                <input type="text" id="new_airline_name" class="form-control" placeholder="e.g. Emirates">
            </div>
            <div class="modal-actions" style="margin-top: 20px;">
                <button class="btn btn-secondary" onclick="closeQuickAdd('airline')">Cancel</button>
                <button class="btn btn-primary" onclick="submitQuickAdd('airline')">Add Airline</button>
            </div>
        </div>
    </div>

    <script>
        var tsDeparture, tsArrival, tsAirline;

        document.addEventListener('DOMContentLoaded', function() {
            tsDeparture = new TomSelect('#departure_city', { create: false, sortField: {field: 'text', direction: 'asc'} });
            tsArrival = new TomSelect('#arrival_city', { create: false, sortField: {field: 'text', direction: 'asc'} });
            tsAirline = new TomSelect('#airline_name', { create: false, sortField: {field: 'text', direction: 'asc'} });

            // Initialize Flatpickr
            flatpickr("#departure_date", {
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "d M Y",
                allowInput: true
            });

            flatpickr("#departure_time", {
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                altInput: true,
                altFormat: "h:i K",
                allowInput: true
            });

            flatpickr("#arrival_time", {
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                altInput: true,
                altFormat: "h:i K",
                allowInput: true
            });
        });

        var lastQuickAddSource = '';

        function openQuickAdd(type, source = '') {
            lastQuickAddSource = source;
            document.getElementById(type + 'Modal').classList.add('active');
        }

        function closeQuickAdd(type) {
            document.getElementById(type + 'Modal').classList.remove('active');
        }

        function submitQuickAdd(type) {
            let data = {};
            let url = '';
            
            if (type === 'airport') {
                data = {
                    city_name: document.getElementById('new_city_name').value,
                    iata_code: document.getElementById('new_iata_code').value
                };
                url = BASE_URL + 'ajax_add_airport';
                if (!data.city_name || !data.iata_code) return alert('Please fill all fields');
            } else {
                data = { name: document.getElementById('new_airline_name').value };
                url = BASE_URL + 'ajax_add_airline';
                if (!data.name) return alert('Please fill airline name');
            }

            fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams(data)
            })
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    if (type === 'airport') {
                        let text = data.city_name + ' (' + data.iata_code.toUpperCase() + ')';
                        tsDeparture.addOption({ value: data.city_name, text: text });
                        tsArrival.addOption({ value: data.city_name, text: text });
                        
                        // Set value so user sees it selected in the field they clicked
                        if (lastQuickAddSource === 'departure') {
                            tsDeparture.setValue(data.city_name);
                        } else if (lastQuickAddSource === 'arrival') {
                            tsArrival.setValue(data.city_name);
                        }
                        document.getElementById('new_city_name').value = '';
                        document.getElementById('new_iata_code').value = '';
                    } else {
                        tsAirline.addOption({ value: data.name, text: data.name });
                        tsAirline.setValue(data.name);
                        document.getElementById('new_airline_name').value = '';
                    }
                    closeQuickAdd(type);
                } else {
                    alert('Error adding entry. It might already exist.');
                }
            })
            .catch(err => {
                console.error('Fetch error:', err);
                alert('Connection error. Please check if the database tables exist.');
            });
        }

        function calcTotal() {
            var p = parseFloat(document.getElementById('ticket_price').value) || 0;
            var c = parseInt(document.getElementById('passenger_count').value) || 1;
            document.getElementById('totalDisplay').textContent = '₹' + (p * c).toFixed(2);
        }
    </script>
<?php include TEMPLATE_DIR . 'layouts/footer.tpl'; ?>
