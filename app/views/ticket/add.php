<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Ticket - Santhosh Air Travels</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background: #f8f9fa;
        }
        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .navbar-brand {
            font-weight: 700;
            font-size: 20px;
        }
        .form-container {
            background: white;
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            max-width: 800px;
            margin: 30px auto;
        }
        .form-header {
            margin-bottom: 30px;
            border-bottom: 2px solid #667eea;
            padding-bottom: 15px;
        }
        .form-header h2 {
            color: #333;
            font-weight: 700;
        }
        .form-section {
            margin-bottom: 30px;
        }
        .form-section-title {
            color: #667eea;
            font-weight: 700;
            font-size: 16px;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }
        .form-control, .form-select {
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            padding: 10px 12px;
            font-size: 14px;
            transition: all 0.3s;
        }
        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-submit, .btn-cancel {
            padding: 12px 30px;
            font-weight: 600;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s;
            border: none;
        }
        .btn-submit {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
            color: white;
        }
        .btn-cancel {
            background: #e0e0e0;
            color: #333;
        }
        .btn-cancel:hover {
            background: #d0d0d0;
            color: #333;
        }
        .btn-group-form {
            display: flex;
            gap: 10px;
            margin-top: 30px;
        }
        .required::after {
            content: " *";
            color: red;
        }
        .alert {
            border-radius: 8px;
            border: none;
        }
        .row {
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="bi bi-airplane"></i> Santhosh Air Travels
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="<?php echo BASE_URL; ?>ticket/dashboard">
                    <i class="bi bi-house"></i> Dashboard
                </a>
                <a class="nav-link" href="<?php echo BASE_URL; ?>auth/logout">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="form-container">
            <div class="form-header">
                <h2><i class="bi bi-plus-circle"></i> Add New Flight Ticket</h2>
                <p class="text-muted mb-0">Fill in the details to create a new flight ticket booking</p>
            </div>

            <!-- Success/Error Messages -->
            <?php if (!empty($success)): ?>
                <div class="alert alert-success">
                    <i class="bi bi-check-circle"></i> <?php echo $success; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <!-- Passenger Information -->
                <div class="form-section">
                    <div class="form-section-title">
                        <i class="bi bi-person"></i> Passenger Information
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="client_name" class="form-label required">Passenger Name</label>
                                <input type="text" class="form-control" id="client_name" name="client_name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="client_email" class="form-label required">Email Address</label>
                                <input type="email" class="form-control" id="client_email" name="client_email" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="client_phone" class="form-label required">Phone Number</label>
                                <input type="tel" class="form-control" id="client_phone" name="client_phone" required>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Flight Information -->
                <div class="form-section">
                    <div class="form-section-title">
                        <i class="bi bi-airplane"></i> Flight Information
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="airline_name" class="form-label required">Airline Name</label>
                                <input type="text" class="form-control" id="airline_name" name="airline_name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="flight_number" class="form-label required">Flight Number</label>
                                <input type="text" class="form-control" id="flight_number" name="flight_number" required>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Journey Details -->
                <div class="form-section">
                    <div class="form-section-title">
                        <i class="bi bi-geo-alt"></i> Journey Details
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="departure_city" class="form-label required">Departure City</label>
                                <input type="text" class="form-control" id="departure_city" name="departure_city" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="arrival_city" class="form-label required">Arrival City</label>
                                <input type="text" class="form-control" id="arrival_city" name="arrival_city" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="departure_date" class="form-label required">Departure Date</label>
                                <input type="date" class="form-control" id="departure_date" name="departure_date" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="departure_time" class="form-label required">Departure Time</label>
                                <input type="time" class="form-control" id="departure_time" name="departure_time" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="arrival_time" class="form-label required">Arrival Time</label>
                                <input type="time" class="form-control" id="arrival_time" name="arrival_time" required>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pricing & Status -->
                <div class="form-section">
                    <div class="form-section-title">
                        <i class="bi bi-cash"></i> Pricing & Status
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="passenger_count" class="form-label required">Number of Passengers</label>
                                <input type="number" class="form-control" id="passenger_count" name="passenger_count" value="1" min="1" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="ticket_price" class="form-label required">Price per Passenger (₹)</label>
                                <input type="number" class="form-control" id="ticket_price" name="ticket_price" step="0.01" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="status" class="form-label required">Status</label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="pending">Pending</option>
                                    <option value="confirmed">Confirmed</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="notes" class="form-label">Additional Notes</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Any special requests or notes..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="btn-group-form">
                    <button type="submit" class="btn btn-submit flex-fill">
                        <i class="bi bi-check-circle"></i> Create Ticket
                    </button>
                    <a href="<?php echo BASE_URL; ?>ticket/dashboard" class="btn btn-cancel flex-fill">
                        <i class="bi bi-x-circle"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-calculate total price
        function calculateTotal() {
            const passengerCount = parseInt(document.getElementById('passenger_count').value) || 0;
            const ticketPrice = parseFloat(document.getElementById('ticket_price').value) || 0;
            const total = passengerCount * ticketPrice;

            // You can add a total display field if needed
            console.log('Total: ₹' + total.toFixed(2));
        }

        // Add event listeners
        document.getElementById('passenger_count').addEventListener('input', calculateTotal);
        document.getElementById('ticket_price').addEventListener('input', calculateTotal);

        // Set minimum date to today
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('departure_date').setAttribute('min', today);
    </script>
</body>
</html>