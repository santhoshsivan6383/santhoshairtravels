<?php include TEMPLATE_DIR . 'layouts/header.tpl'; ?>

    <div class="page-container-narrow">
        <div class="ticket-detail-card">
            <div class="ticket-detail-header">
                <h2><i class="bi bi-ticket-detailed"></i> Flight Ticket Details</h2>
                <div class="booking-ref-badge">
                    <i class="bi bi-upc-scan"></i>
                    <?php echo htmlspecialchars($ticket['booking_reference']); ?>
                </div>
                <div style="margin-top: 12px;">
                    <span class="status-badge status-<?php echo $ticket['status']; ?>">
                        <?php echo ucfirst($ticket['status']); ?>
                    </span>
                </div>
            </div>

            <div class="ticket-detail-body">
                <!-- Client Information -->
                <div class="detail-section">
                    <div class="detail-section-title">
                        <i class="bi bi-person-fill"></i> Client Information
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Client Name</span>
                        <span class="detail-value"><?php echo htmlspecialchars($ticket['client_name']); ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Email</span>
                        <span class="detail-value"><?php echo htmlspecialchars($ticket['client_email'] ?: 'N/A'); ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Phone</span>
                        <span class="detail-value"><?php echo htmlspecialchars($ticket['client_phone'] ?: 'N/A'); ?></span>
                    </div>
                </div>

                <!-- Flight Information -->
                <div class="detail-section">
                    <div class="detail-section-title">
                        <i class="bi bi-airplane-fill"></i> Flight Information
                    </div>

                    <div class="route-display">
                        <div class="route-city">
                            <div class="city-name"><?php echo htmlspecialchars($ticket['departure_city']); ?></div>
                            <div class="city-time"><?php echo date('h:i A', strtotime($ticket['departure_time'])); ?></div>
                            <div class="city-date"><?php echo date('d M Y', strtotime($ticket['departure_date'])); ?></div>
                        </div>
                        <div class="route-arrow">
                            <div class="arrow-line"></div>
                        </div>
                        <div class="route-city">
                            <div class="city-name"><?php echo htmlspecialchars($ticket['arrival_city']); ?></div>
                            <div class="city-time"><?php echo date('h:i A', strtotime($ticket['arrival_time'])); ?></div>
                            <div class="city-date"><?php echo date('d M Y', strtotime($ticket['departure_date'])); ?></div>
                        </div>
                    </div>

                    <div class="detail-row">
                        <span class="detail-label">Airline</span>
                        <span class="detail-value"><?php echo htmlspecialchars($ticket['airline_name']); ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Flight Number</span>
                        <span class="detail-value"><?php echo htmlspecialchars($ticket['flight_number']); ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Passengers</span>
                        <span class="detail-value"><?php echo $ticket['passenger_count']; ?></span>
                    </div>
                </div>

                <!-- Pricing -->
                <div class="detail-section">
                    <div class="detail-section-title">
                        <i class="bi bi-currency-rupee"></i> Pricing
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Price per Passenger</span>
                        <span class="detail-value"><?php echo formatCurrency($ticket['ticket_price']); ?></span>
                    </div>
                    <div class="detail-row" style="padding: 16px 0; border-bottom: 2px solid var(--primary-200);">
                        <span class="detail-label" style="font-size: 15px; font-weight: 600;">Total Price</span>
                        <span class="price-highlight"><?php echo formatCurrency($ticket['total_price']); ?></span>
                    </div>
                </div>

                <!-- Notes -->
                <?php if (!empty($ticket['notes'])): ?>
                    <div class="notes-box">
                        <div class="notes-title"><i class="bi bi-sticky-fill"></i> Notes</div>
                        <p><?php echo nl2br(htmlspecialchars($ticket['notes'])); ?></p>
                    </div>
                <?php endif; ?>

                <!-- Timestamps -->
                <div class="timestamps">
                    Created: <?php echo date('d M Y H:i', strtotime($ticket['created_at'])); ?> |
                    Updated: <?php echo date('d M Y H:i', strtotime($ticket['updated_at'])); ?>
                </div>

                <!-- Action Buttons -->
                <div class="form-actions">
                    <button type="button" class="btn btn-success" onclick="App.route('download_pdf', <?php echo $ticket['id']; ?>)" id="downloadPdfBtn">
                        <i class="bi bi-file-earmark-pdf-fill"></i> Download PDF
                    </button>
                    <button type="button" class="btn btn-primary" onclick="App.route('edit_ticket', <?php echo $ticket['id']; ?>)" id="editBtn">
                        <i class="bi bi-pencil-fill"></i> Edit Ticket
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="App.route('dashboard')" id="backBtn">
                        <i class="bi bi-arrow-left"></i> Back to List
                    </button>
                </div>
            </div>
        </div>
    </div>

<?php include TEMPLATE_DIR . 'layouts/footer.tpl'; ?>
