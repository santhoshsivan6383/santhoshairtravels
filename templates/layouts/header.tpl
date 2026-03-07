<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) . ' - ' : ''; ?>Santhosh Air Travels</title>
    <meta name="description" content="Santhosh Air Travels - Professional Flight Ticket Management System">
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>✈️</text></svg>">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        const BASE_URL = '<?php echo rtrim(BASE_URL, '/') . '/'; ?>';
        // Global App JS Router
        const App = {
            route: function(routeName, param = null) {
                let url = BASE_URL + routeName;
                if (param !== null) {
                    url += '/' + param;
                }
                window.location.href = url;
            },
            submitForm: function(routeName, param = null) {
                let form = document.querySelector('form');
                let url = BASE_URL + routeName;
                if (param !== null) {
                    url += '/' + param;
                }
                form.action = url;
                form.submit();
                return false;
            }
        };
    </script>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div style="display: flex; align-items: center; gap: 30px;">
            <a class="navbar-brand" href="#" onclick="App.route('dashboard'); return false;">
                <span class="brand-icon">✈</span>
                Santhosh Air Travels
            </a>
            <ul class="navbar-nav">
                <li><a class="nav-link" href="#" onclick="App.route('dashboard'); return false;"><i class="bi bi-grid-1x2-fill"></i> Dashboard</a></li>
                <li><a class="nav-link" href="#" onclick="App.route('list_tickets'); return false;"><i class="bi bi-list-ul"></i> All Tickets</a></li>
                <li><a class="nav-link" href="#" onclick="App.route('add_ticket'); return false;"><i class="bi bi-plus-circle"></i> New Ticket</a></li>
            </ul>
        </div>
        <ul class="navbar-nav">
            <?php if (isset($_SESSION['username'])): ?>
                <li><span class="nav-user">Welcome, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong></span></li>
            <?php endif; ?>
            <li><a class="nav-link" href="#" onclick="App.route('logout'); return false;"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
        </ul>
    </nav>
