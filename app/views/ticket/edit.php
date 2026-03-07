<?php
/**
 * Edit Ticket View
 * @var array $ticket
 * @var array $errors
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Ticket - Santhosh Air Travels</title>
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
        .form-label {
            font-weight: 600;
            color: #495057;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #004085;
        }
        .error-message {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
        .required {
            color: #dc3545;
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
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-edit"></i> Edit Ticket
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (isset($errors) && !empty($errors)): ?>
                            <div class="alert alert-danger">
                                <h6>Please fix the following errors:</h6>
                                <ul class="mb-0">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?php echo htmlspecialchars($error); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="<?php echo BASE_URL; ?>ticket/update/<?php echo $ticket['id']; ?>" id="editTicketForm">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="passenger_name" class="form-label">
                                            Passenger Name <span class="required">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="passenger_name" name="passenger_name"
                                               value="<?php echo htmlspecialchars($ticket['passenger_name'] ?? ''); ?>" required>
                                        <?php if (isset($errors['passenger_name'])): ?>
                                            <div class="error-message"><?php echo htmlspecialchars($errors['passenger_name']); ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">
                                            Email <span class="required">*</span>
                                        </label>
                                        <input type="email" class="form-control" id="email" name="email"
                                               value="<?php echo htmlspecialchars($ticket['email'] ?? ''); ?>" required>
                                        <?php if (isset($errors['email'])): ?>
                                            <div class="error-message"><?php echo htmlspecialchars($errors['email']); ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="phone" class="form-label">
                                            Phone <span class="required">*</span>
                                        </label>
                                        <input type="tel" class="form-control" id="phone" name="phone"
                                               value="<?php echo htmlspecialchars($ticket['phone'] ?? ''); ?>" required>
                                        <?php if (isset($errors['phone'])): ?>
                                            <div class="error-message"><?php echo htmlspecialchars($errors['phone']); ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="booking_reference" class="form-label">
                                            Booking Reference
                                        </label>
                                        <input type="text" class="form-control" id="booking_reference" name="booking_reference"
                                               value="<?php echo htmlspecialchars($ticket['booking_reference'] ?? ''); ?>" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="departure_city" class="form-label">
                                            Departure City <span class="required">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="departure_city" name="departure_city"
                                               value="<?php echo htmlspecialchars($ticket['departure_city'] ?? ''); ?>" required>
                                        <?php if (isset($errors['departure_city'])): ?>
                                            <div class="error-message"><?php echo htmlspecialchars($errors['departure_city']); ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="arrival_city" class="form-label">
                                            Arrival City <span class="required">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="arrival_city" name="arrival_city"
                                               value="<?php echo htmlspecialchars($ticket['arrival_city'] ?? ''); ?>" required>
                                        <?php if (isset($errors['arrival_city'])): ?>
                                            <div class="error-message"><?php echo htmlspecialchars($errors['arrival_city']); ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="departure_date" class="form-label">
                                            Departure Date <span class="required">*</span>
                                        </label>
                                        <input type="date" class="form-control" id="departure_date" name="departure_date"
                                               value="<?php echo htmlspecialchars($ticket['departure_date'] ?? ''); ?>" required>
                                        <?php if (isset($errors['departure_date'])): ?>
                                            <div class="error-message"><?php echo htmlspecialchars($errors['departure_date']); ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="return_date" class="form-label">
                                            Return Date
                                        </label>
                                        <input type="date" class="form-control" id="return_date" name="return_date"
                                               value="<?php echo htmlspecialchars($ticket['return_date'] ?? ''); ?>">
                                        <?php if (isset($errors['return_date'])): ?>
                                            <div class="error-message"><?php echo htmlspecialchars($errors['return_date']); ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="flight_number" class="form-label">
                                            Flight Number <span class="required">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="flight_number" name="flight_number"
                                               value="<?php echo htmlspecialchars($ticket['flight_number'] ?? ''); ?>" required>
                                        <?php if (isset($errors['flight_number'])): ?>
                                            <div class="error-message"><?php echo htmlspecialchars($errors['flight_number']); ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="seat_number" class="form-label">
                                            Seat Number
                                        </label>
                                        <input type="text" class="form-control" id="seat_number" name="seat_number"
                                               value="<?php echo htmlspecialchars($ticket['seat_number'] ?? ''); ?>">
                                        <?php if (isset($errors['seat_number'])): ?>
                                            <div class="error-message"><?php echo htmlspecialchars($errors['seat_number']); ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="ticket_price" class="form-label">
                                            Ticket Price <span class="required">*</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text">₹</span>
                                            <input type="number" class="form-control" id="ticket_price" name="ticket_price"
                                                   value="<?php echo htmlspecialchars($ticket['ticket_price'] ?? ''); ?>" step="0.01" required>
                                        </div>
                                        <?php if (isset($errors['ticket_price'])): ?>
                                            <div class="error-message"><?php echo htmlspecialchars($errors['ticket_price']); ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="payment_status" class="form-label">
                                            Payment Status <span class="required">*</span>
                                        </label>
                                        <select class="form-select" id="payment_status" name="payment_status" required>
                                            <option value="">Select Status</option>
                                            <option value="pending" <?php echo ($ticket['payment_status'] ?? '') === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                            <option value="paid" <?php echo ($ticket['payment_status'] ?? '') === 'paid' ? 'selected' : ''; ?>>Paid</option>
                                            <option value="refunded" <?php echo ($ticket['payment_status'] ?? '') === 'refunded' ? 'selected' : ''; ?>>Refunded</option>
                                        </select>
                                        <?php if (isset($errors['payment_status'])): ?>
                                            <div class="error-message"><?php echo htmlspecialchars($errors['payment_status']); ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="special_requests" class="form-label">
                                    Special Requests
                                </label>
                                <textarea class="form-control" id="special_requests" name="special_requests" rows="3"><?php echo htmlspecialchars($ticket['special_requests'] ?? ''); ?></textarea>
                                <?php if (isset($errors['special_requests'])): ?>
                                    <div class="error-message"><?php echo htmlspecialchars($errors['special_requests']); ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="<?php echo BASE_URL; ?>ticket/dashboard" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update Ticket
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Form validation
        document.getElementById('editTicketForm').addEventListener('submit', function(e) {
            const departureDate = new Date(document.getElementById('departure_date').value);
            const returnDate = new Date(document.getElementById('return_date').value);
            const today = new Date();
            today.setHours(0, 0, 0, 0);

            if (departureDate < today) {
                alert('Departure date cannot be in the past.');
                e.preventDefault();
                return false;
            }

            if (document.getElementById('return_date').value && returnDate <= departureDate) {
                alert('Return date must be after departure date.');
                e.preventDefault();
                return false;
            }
        });

        // Auto-format phone number
        document.getElementById('phone').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length >= 10) {
                value = value.substring(0, 10);
                e.target.value = value.replace(/(\d{3})(\d{3})(\d{4})/, '$1-$2-$3');
            }
        });
    </script>
</body>
</html>