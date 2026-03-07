<?php include TEMPLATE_DIR . 'layouts/header.tpl'; ?>

    <div class="page-container">

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

        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon"><i class="bi bi-ticket-perforated"></i></div>
                <div class="stat-number"><?php echo $total_tickets; ?></div>
                <div class="stat-label">Total Tickets</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="bi bi-check-circle"></i></div>
                <div class="stat-number"><?php echo $confirmed_count; ?></div>
                <div class="stat-label">Confirmed</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="bi bi-currency-rupee"></i></div>
                <div class="stat-number"><?php echo formatCurrency($total_revenue); ?></div>
                <div class="stat-label">Total Revenue</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="bi bi-clock-history"></i></div>
                <div class="stat-number"><?php echo $pending_count; ?></div>
                <div class="stat-label">Pending</div>
            </div>
        </div>

        <div class="card" style="margin-bottom: 28px;">
            <div class="card-header">
                <h3>Ticket Statistics (Last 6 Months)</h3>
            </div>
            <div class="card-body">
                <canvas id="ticketChart" height="90"></canvas>
            </div>
        </div>

        <!-- Page Header -->
        <div class="page-header">
            <div>
                <h1>Recent Tickets</h1>
                <p>A quick look at your latest ticket bookings</p>
            </div>
        </div>

        <!-- Tickets Table -->
        <div class="table-container">
            <?php if (empty($tickets)): ?>
                <div class="empty-state">
                    <div class="empty-icon">✈️</div>
                    <h4>No tickets yet</h4>
                    <p>Start by creating your first flight ticket booking</p>
                    <button onclick="App.route('add_ticket')" class="btn btn-primary" id="createFirstBtn">
                        <i class="bi bi-plus-circle"></i> Create First Ticket
                    </button>
                </div>
            <?php else: ?>
                <table class="data-table" id="ticketsTable">
                    <thead>
                        <tr>
                            <th>Booking Ref</th>
                            <th>Client Name</th>
                            <th>Route</th>
                            <th>Date</th>
                            <th>Total Price</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tickets as $t): ?>
                            <tr>
                                <td>
                                    <strong style="color: var(--primary); font-family: 'Outfit', sans-serif; letter-spacing: 0.3px;">
                                        <?php echo htmlspecialchars($t['booking_reference']); ?>
                                    </strong>
                                </td>
                                <td><?php echo htmlspecialchars($t['client_name']); ?></td>
                                <td>
                                    <span class="route-cell">
                                        <?php echo htmlspecialchars($t['departure_city']); ?>
                                        <span class="route-arrow">→</span>
                                        <?php echo htmlspecialchars($t['arrival_city']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('d M Y', strtotime($t['departure_date'])); ?></td>
                                <td><strong><?php echo formatCurrency($t['total_price']); ?></strong></td>
                                <td>
                                    <span class="status-badge status-<?php echo $t['status']; ?>">
                                        <?php echo ucfirst($t['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <button onclick="App.route('view_ticket', <?php echo $t['id']; ?>)" class="btn btn-secondary btn-sm" title="View" id="viewBtn_<?php echo $t['id']; ?>">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button onclick="App.route('edit_ticket', <?php echo $t['id']; ?>)" class="btn btn-secondary btn-sm" title="Edit" id="editBtn_<?php echo $t['id']; ?>">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button onclick="App.route('download_pdf', <?php echo $t['id']; ?>)" class="btn btn-danger btn-sm" title="PDF" id="pdfBtn_<?php echo $t['id']; ?>">
                                            <i class="bi bi-file-earmark-pdf"></i>
                                        </button>
                                        <button class="btn btn-secondary btn-sm" onclick="confirmDelete(<?php echo $t['id']; ?>, '<?php echo htmlspecialchars($t['booking_reference']); ?>')" title="Delete" id="delBtn_<?php echo $t['id']; ?>">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal-overlay" id="deleteModal">
        <div class="modal-box">
            <div class="modal-icon">🗑️</div>
            <h3>Delete Ticket?</h3>
            <p>Are you sure you want to delete ticket <strong id="deleteRef"></strong>? This cannot be undone.</p>
            <div class="modal-actions">
                <button class="btn btn-secondary" onclick="closeDeleteModal()" id="cancelDelBtn">Cancel</button>
                <button class="btn btn-danger" id="confirmDelBtn" onclick="">
                    <i class="bi bi-trash"></i> Delete
                </button>
            </div>
        </div>
    </div>

    <script>
        function confirmDelete(id, ref) {
            document.getElementById('deleteRef').textContent = ref;
            let delBtn = document.getElementById('confirmDelBtn');
            delBtn.onclick = function() {
                App.route('delete_ticket', id);
            };
            document.getElementById('deleteModal').classList.add('active');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.remove('active');
        }

        document.getElementById('deleteModal').addEventListener('click', function(e) {
            if (e.target === this) closeDeleteModal();
        });

        // Keyboard shortcut: Escape to close modal
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeDeleteModal();
        });

        // Initialize Chart
        document.addEventListener('DOMContentLoaded', function() {
            const chartData = <?php echo $chart_data ?? '{}'; ?>;
            if(chartData.labels && chartData.labels.length > 0) {
                const ctx = document.getElementById('ticketChart').getContext('2d');
                
                // Reverse arrays to show oldest left, newest right
                const labels = chartData.labels.reverse();
                const dataConfirmed = chartData.confirmed.reverse();
                const dataPending = chartData.pending.reverse();

                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: 'Confirmed',
                                data: dataConfirmed,
                                backgroundColor: 'rgba(16, 185, 129, 0.8)',
                                borderRadius: 4,
                            },
                            {
                                label: 'Pending',
                                data: dataPending,
                                backgroundColor: 'rgba(245, 158, 11, 0.8)',
                                borderRadius: 4,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { 
                                position: 'top',
                                labels: {
                                    font: { family: "'Inter', sans-serif", size: 12 },
                                    usePointStyle: true,
                                    padding: 20
                                }
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false
                            }
                        },
                        scales: {
                            y: {
                                stacked: true,
                                beginAtZero: true,
                                grid: {
                                    color: '#f1f5f9'
                                },
                                ticks: {
                                    font: { family: "'Inter', sans-serif", size: 11 },
                                    color: '#94a3b8',
                                    stepSize: 1
                                }
                            },
                            x: {
                                stacked: true,
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    font: { family: "'Inter', sans-serif", size: 11 },
                                    color: '#94a3b8'
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<?php include TEMPLATE_DIR . 'layouts/footer.tpl'; ?>
