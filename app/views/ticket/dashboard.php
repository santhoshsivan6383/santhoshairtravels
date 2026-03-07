<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Santhosh Air Travels</title>
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
        .stats-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            border: none;
            transition: all 0.3s;
        }
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }
        .stats-icon {
            font-size: 48px;
            margin-bottom: 15px;
        }
        .stats-number {
            font-size: 32px;
            font-weight: 700;
            color: #333;
            margin-bottom: 5px;
        }
        .stats-label {
            color: #666;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 1px;
        }
        .table-container {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            margin-top: 30px;
        }
        .table {
            margin-bottom: 0;
        }
        .table th {
            background: #667eea;
            color: white;
            border: none;
            font-weight: 600;
            padding: 15px;
        }
        .table td {
            border-color: #dee2e6;
            padding: 15px;
            vertical-align: middle;
        }
        .btn-action {
            padding: 8px 15px;
            font-size: 14px;
            border-radius: 6px;
            margin-right: 5px;
            border: none;
            transition: all 0.3s;
        }
        .btn-view {
            background: #17a2b8;
            color: white;
        }
        .btn-view:hover {
            background: #138496;
            color: white;
        }
        .btn-edit {
            background: #ffc107;
            color: #212529;
        }
        .btn-edit:hover {
            background: #e0a800;
            color: #212529;
        }
        .btn-pdf {
            background: #28a745;
            color: white;
        }
        .btn-pdf:hover {
            background: #218838;
            color: white;
        }
        .btn-delete {
            background: #dc3545;
            color: white;
        }
        .btn-delete:hover {
            background: #c82333;
            color: white;
        }
        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .status-confirmed {
            background: #d4edda;
            color: #155724;
        }
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        .status-cancelled {
            background: #f8d7da;
            color: #721c24;
        }
        .alert {
            border-radius: 8px;
            border: none;
        }
        .btn-add-ticket {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px 30px;
            font-weight: 600;
            border-radius: 8px;
            color: white;
            transition: all 0.3s;
        }
        .btn-add-ticket:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
            color: white;
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
                <span class="navbar-text me-3">
                    Welcome, <?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?>
                </span>
                <a class="nav-link" href="<?php echo BASE_URL; ?>auth/logout">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
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

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="stats-card text-center">
                    <div class="stats-icon text-primary">
                        <i class="bi bi-ticket-perforated"></i>
                    </div>
                    <div class="stats-number"><?php echo $stats['total']; ?></div>
                    <div class="stats-label">Total Tickets</div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="stats-card text-center">
                    <div class="stats-icon text-success">
                        <i class="bi bi-check-circle"></i>
                    </div>
                    <div class="stats-number"><?php echo $stats['confirmed']; ?></div>
                    <div class="stats-label">Confirmed</div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="stats-card text-center">
                    <div class="stats-icon text-info">
                        <i class="bi bi-currency-rupee"></i>
                    </div>
                    <div class="stats-number">₹<?php echo number_format($stats['revenue'], 0); ?></div>
                    <div class="stats-label">Total Revenue</div>
                </div>
            </div>
        </div>

        <!-- Tickets Table -->
        <div class="table-container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="mb-0"><i class="bi bi-table"></i> Flight Tickets</h3>
                <a href="<?php echo BASE_URL; ?>ticket/add" class="btn btn-add-ticket">
                    <i class="bi bi-plus-circle"></i> Add New Ticket
                </a>
            </div>

            <?php if (empty($tickets)): ?>
                <div class="text-center py-5">
                    <i class="bi bi-inbox" style="font-size: 48px; color: #ccc;"></i>
                    <h4 class="text-muted mt-3">No tickets found</h4>
                    <p class="text-muted">Start by adding your first flight ticket.</p>
                    <a href="<?php echo BASE_URL; ?>ticket/add" class="btn btn-add-ticket">
                        <i class="bi bi-plus-circle"></i> Add First Ticket
                    </a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Booking Ref</th>
                                <th>Passenger</th>
                                <th>Flight</th>
                                <th>Route</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Amount</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tickets as $ticket): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($ticket['booking_reference']); ?></strong>
                                    </td>
                                    <td>
                                        <div><?php echo htmlspecialchars($ticket['client_name']); ?></div>
                                        <small class="text-muted"><?php echo htmlspecialchars($ticket['client_email']); ?></small>
                                    </td>
                                    <td>
                                        <div><?php echo htmlspecialchars($ticket['airline_name']); ?></div>
                                        <small class="text-muted"><?php echo htmlspecialchars($ticket['flight_number']); ?></small>
                                    </td>
                                    <td>
                                        <div><?php echo htmlspecialchars($ticket['departure_city']); ?> → <?php echo htmlspecialchars($ticket['arrival_city']); ?></div>
                                        <small class="text-muted"><?php echo date('H:i', strtotime($ticket['departure_time'])); ?> - <?php echo date('H:i', strtotime($ticket['arrival_time'])); ?></small>
                                    </td>
                                    <td><?php echo date('d M Y', strtotime($ticket['departure_date'])); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo $ticket['status']; ?>">
                                            <?php echo ucfirst($ticket['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <strong>₹<?php echo number_format($ticket['total_price'], 0); ?></strong>
                                    </td>
                                    <td>
                                        <a href="<?php echo BASE_URL; ?>ticket/view/<?php echo $ticket['id']; ?>" class="btn btn-action btn-view btn-sm" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="<?php echo BASE_URL; ?>ticket/downloadPdf/<?php echo $ticket['id']; ?>" class="btn btn-action btn-pdf btn-sm" title="Download PDF">
                                            <i class="bi bi-download"></i>
                                        </a>
                                        <a href="<?php echo BASE_URL; ?>ticket/edit/<?php echo $ticket['id']; ?>" class="btn btn-action btn-edit btn-sm" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="<?php echo BASE_URL; ?>ticket/delete/<?php echo $ticket['id']; ?>" class="btn btn-action btn-delete btn-sm" title="Delete" onclick="return confirm('Are you sure you want to delete this ticket?')">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>