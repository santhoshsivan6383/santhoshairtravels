<?php
/**
 * View Ticket Details
 * @var array $ticket
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Ticket - Santhosh Air Travels</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .navbar-brand {
            font-weight: bold;
            color: #007bff !important;
        }
        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border: 1px solid rgba(0, 0, 0, 0.125);
        }
        .info-label {
            font-weight: 600;
            color: #495057;
        }
        .info-value {
            color: #212529;
        }
        .status-badge {
            font-size: 0.875rem;
            padding: 0.375rem 0.75rem;
        }
        .action-btn {
            margin-right: 0.5rem;
        }
    </style>
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
        <div class="container">
            <a class="navbar-brand" href="<?php echo BASE_URL; ?>ticket/dashboard">
                <i class="fas fa-plane"></i> Santhosh Air Travels
            </a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3">
                    Welcome, <?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?>
                </span>
                <a class="nav-link" href="<?php echo BASE_URL; ?>auth/logout">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card">
                    <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-ticket-alt"></i> Ticket Details
                        </h5>
                        <div>
                            <a href="<?php echo BASE_URL; ?>ticket/edit/<?php echo $ticket['id']; ?>" class="btn btn-light btn-sm action-btn">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="<?php echo BASE_URL; ?>ticket/downloadPdf/<?php echo $ticket['id']; ?>" class="btn btn-success btn-sm action-btn" target="_blank">
                                <i class="fas fa-download"></i> Download PDF
                            </a>
                            <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <span class="info-label">Booking Reference:</span>
                                    <div class="info-value"><?php echo htmlspecialchars($ticket['booking_reference'] ?? 'N/A'); ?></div>
                                </div>
                                <div class="mb-3">
                                    <span class="info-label">Passenger Name:</span>
                                    <div class="info-value"><?php echo htmlspecialchars($ticket['passenger_name'] ?? 'N/A'); ?></div>
                                </div>
                                <div class="mb-3">
                                    <span class="info-label">Email:</span>
                                    <div class="info-value"><?php echo htmlspecialchars($ticket['email'] ?? 'N/A'); ?></div>
                                </div>
                                <div class="mb-3">
                                    <span class="info-label">Phone:</span>
                                    <div class="info-value"><?php echo htmlspecialchars($ticket['phone'] ?? 'N/A'); ?></div>
                                </div>
                                <div class="mb-3">
                                    <span class="info-label">Payment Status:</span>
                                    <div class="info-value">
                                        <?php
                                        $status = $ticket['payment_status'] ?? 'pending';
                                        $badgeClass = match($status) {
                                            'paid' => 'bg-success',
                                            'pending' => 'bg-warning',
                                            'refunded' => 'bg-secondary',
                                            default => 'bg-light text-dark'
                                        };
                                        ?>
                                        <span class="badge <?php echo $badgeClass; ?> status-badge">
                                            <?php echo ucfirst($status); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <span class="info-label">Departure City:</span>
                                    <div class="info-value"><?php echo htmlspecialchars($ticket['departure_city'] ?? 'N/A'); ?></div>
                                </div>
                                <div class="mb-3">
                                    <span class="info-label">Arrival City:</span>
                                    <div class="info-value"><?php echo htmlspecialchars($ticket['arrival_city'] ?? 'N/A'); ?></div>
                                </div>
                                <div class="mb-3">
                                    <span class="info-label">Departure Date:</span>
                                    <div class="info-value">
                                        <?php echo $ticket['departure_date'] ? date('F j, Y', strtotime($ticket['departure_date'])) : 'N/A'; ?>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <span class="info-label">Return Date:</span>
                                    <div class="info-value">
                                        <?php echo $ticket['return_date'] ? date('F j, Y', strtotime($ticket['return_date'])) : 'One-way'; ?>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <span class="info-label">Flight Number:</span>
                                    <div class="info-value"><?php echo htmlspecialchars($ticket['flight_number'] ?? 'N/A'); ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <span class="info-label">Seat Number:</span>
                                    <div class="info-value"><?php echo htmlspecialchars($ticket['seat_number'] ?? 'Not assigned'); ?></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <span class="info-label">Ticket Price:</span>
                                    <div class="info-value">₹<?php echo number_format($ticket['ticket_price'] ?? 0, 2); ?></div>
                                </div>
                            </div>
                        </div>

                        <?php if (!empty($ticket['special_requests'])): ?>
                            <div class="mb-3">
                                <span class="info-label">Special Requests:</span>
                                <div class="info-value"><?php echo nl2br(htmlspecialchars($ticket['special_requests'])); ?></div>
                            </div>
                        <?php endif; ?>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <span class="info-label">Created Date:</span>
                                    <div class="info-value">
                                        <?php echo $ticket['created_at'] ? date('F j, Y g:i A', strtotime($ticket['created_at'])) : 'N/A'; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <span class="info-label">Last Updated:</span>
                                    <div class="info-value">
                                        <?php echo $ticket['updated_at'] ? date('F j, Y g:i A', strtotime($ticket['updated_at'])) : 'N/A'; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="<?php echo BASE_URL; ?>ticket/dashboard" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Dashboard
                            </a>
                            <div>
                                <a href="<?php echo BASE_URL; ?>ticket/edit/<?php echo $ticket['id']; ?>" class="btn btn-primary">
                                    <i class="fas fa-edit"></i> Edit Ticket
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">
                        <i class="fas fa-exclamation-triangle text-danger"></i> Confirm Delete
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this ticket?</p>
                    <p class="text-muted mb-0">
                        <strong>Passenger:</strong> <?php echo htmlspecialchars($ticket['passenger_name'] ?? 'N/A'); ?><br>
                        <strong>Booking Reference:</strong> <?php echo htmlspecialchars($ticket['booking_reference'] ?? 'N/A'); ?><br>
                        <strong>Flight:</strong> <?php echo htmlspecialchars($ticket['flight_number'] ?? 'N/A'); ?>
                    </p>
                    <p class="text-danger mt-3 mb-0">
                        <strong>This action cannot be undone!</strong>
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <a href="<?php echo BASE_URL; ?>ticket/delete/<?php echo $ticket['id']; ?>" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Delete Ticket
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>