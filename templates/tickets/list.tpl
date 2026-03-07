<?php include TEMPLATE_DIR . 'layouts/header.tpl'; ?>

    <div class="page-container" style="max-width: 98%;">
        <!-- Page Header -->
        <div class="page-header">
            <div>
                <h1>All Flight Tickets</h1>
                <p>Manage and search through your past travels</p>
            </div>
            <div style="display: flex; gap: 10px;">
                <button onclick="App.route('dashboard')" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Dashboard
                </button>
            </div>
        </div>

        <!-- Toolbar (Search & Export) -->
        <div class="card" style="margin-bottom: 24px; padding: 20px;">
            <form method="GET" action="<?php echo BASE_URL; ?>list_tickets" style="display: flex; gap: 15px; align-items: center; flex-wrap: wrap;">
                <div style="flex: 1; min-width: 250px;">
                    <div style="position: relative;">
                        <i class="bi bi-search" style="position: absolute; left: 14px; top: 12px; color: var(--text-muted);"></i>
                        <input type="text" name="search" class="form-control" placeholder="Search by name, ref, city..." value="<?php echo htmlspecialchars($search); ?>" style="padding-left: 40px; border-radius: 30px;">
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary" style="border-radius: 30px;">
                    <i class="bi bi-funnel"></i> Search
                </button>
                
                <?php if(!empty($search)): ?>
                    <a href="<?php echo BASE_URL; ?>list_tickets" class="btn btn-secondary" style="border-radius: 30px;">
                        <i class="bi bi-x-circle"></i> Clear
                    </a>
                <?php endif; ?>

                <div style="margin-left: auto; display: flex; gap: 10px;">
                    <button type="button" class="btn btn-secondary" style="border-radius: 30px;" onclick="openColumnModal()">
                        <i class="bi bi-layout-three-columns"></i> Manage Columns
                    </button>
                    <button type="button" onclick="App.route('export_excel')" class="btn btn-success" style="border-radius: 30px;">
                        <i class="bi bi-file-earmark-excel"></i> Export All
                    </button>
                </div>
            </form>
        </div>

        <!-- Tickets Table -->
        <div class="table-container">
            <?php if (empty($tickets)): ?>
                <div class="empty-state" style="padding: 60px 20px;">
                    <div class="empty-icon">✈️</div>
                    <h4>No tickets found</h4>
                    <p>We couldn't find any tickets matching your search.</p>
                    <button onclick="App.route('add_ticket')" class="btn btn-primary" style="margin-top: 15px;">
                        <i class="bi bi-plus-circle"></i> Create New Ticket
                    </button>
                </div>
            <?php else: ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>S/No</th>
                            <?php foreach($selected_columns as $col): ?>
                                <th><?php echo htmlspecialchars($available_columns[$col]); ?></th>
                            <?php endforeach; ?>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $s_no = ($current_page - 1) * $limit + 1; ?>
                        <?php foreach ($tickets as $t): ?>
                            <tr>
                                <td><strong style="color: var(--text-muted);"><?php echo $s_no++; ?></strong></td>
                                <?php foreach($selected_columns as $col): ?>
                                    <td>
                                        <?php if ($col === 'booking_reference'): ?>
                                            <strong style="color: var(--primary); font-family: 'Outfit', sans-serif;">
                                                <?php echo htmlspecialchars($t['booking_reference']); ?>
                                            </strong>
                                        <?php elseif ($col === 'route'): ?>
                                            <span class="route-cell">
                                                <?php echo htmlspecialchars($t['departure_city']); ?>
                                                <span class="route-arrow">→</span>
                                                <?php echo htmlspecialchars($t['arrival_city']); ?>
                                            </span>
                                        <?php elseif ($col === 'departure_date'): ?>
                                            <?php echo date('d M Y', strtotime($t['departure_date'])); ?>
                                        <?php elseif ($col === 'departure_time' || $col === 'arrival_time'): ?>
                                            <?php echo !empty($t[$col]) ? date('h:i A', strtotime($t[$col])) : '-'; ?>
                                        <?php elseif ($col === 'ticket_price' || $col === 'total_price'): ?>
                                            <strong><?php echo formatCurrency($t[$col]); ?></strong>
                                        <?php elseif ($col === 'status'): ?>
                                            <span class="status-badge status-<?php echo $t['status']; ?>">
                                                <?php echo ucfirst($t['status']); ?>
                                            </span>
                                        <?php else: ?>
                                            <?php echo htmlspecialchars($t[$col] ?? '-'); ?>
                                        <?php endif; ?>
                                    </td>
                                <?php endforeach; ?>
                                <td>
                                    <div class="btn-group">
                                        <button onclick="App.route('view_ticket', <?php echo $t['id']; ?>)" class="btn btn-secondary btn-sm" title="View"><i class="bi bi-eye"></i></button>
                                        <button onclick="App.route('edit_ticket', <?php echo $t['id']; ?>)" class="btn btn-secondary btn-sm" title="Edit"><i class="bi bi-pencil"></i></button>
                                        <button class="btn btn-secondary btn-sm" onclick="confirmDelete(<?php echo $t['id']; ?>, '<?php echo htmlspecialchars($t['booking_reference']); ?>')" title="Delete" id="delBtn_<?php echo $t['id']; ?>">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                        <button onclick="App.route('download_pdf', <?php echo $t['id']; ?>)" class="btn btn-danger btn-sm" title="PDF"><i class="bi bi-file-earmark-pdf"></i></button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
        
        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 24px; padding: 0 10px;">
            <div style="font-size: 13.5px; color: var(--text-muted);">
                Showing page <strong><?php echo $current_page; ?></strong> of <strong><?php echo $total_pages; ?></strong> 
                (Total: <?php echo $total_results; ?> tickets)
            </div>
            
            <div style="display: flex; gap: 8px;">
                <?php 
                    $searchParam = !empty($search) ? '&search=' . urlencode($search) : '';
                ?>
                
                <?php if ($current_page > 1): ?>
                    <a href="?route=list_tickets&page=<?php echo ($current_page - 1) . $searchParam; ?>" class="btn btn-secondary btn-sm" style="border-radius: 5px;">
                        <i class="bi bi-chevron-left"></i> Prev
                    </a>
                <?php endif; ?>
                
                <?php for($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?route=list_tickets&page=<?php echo $i . $searchParam; ?>" class="btn btn-sm <?php echo $i == $current_page ? 'btn-primary' : 'btn-secondary'; ?>" style="border-radius: 5px; width: 34px;">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
                
                <?php if ($current_page < $total_pages): ?>
                    <a href="?route=list_tickets&page=<?php echo ($current_page + 1) . $searchParam; ?>" class="btn btn-secondary btn-sm" style="border-radius: 5px;">
                        Next <i class="bi bi-chevron-right"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

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

    <!-- Manage Columns Modal -->
    <div class="modal-overlay" id="columnModal">
        <div class="modal-box" style="width: 500px; text-align: left;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 style="margin: 0;">Manage Columns</h3>
                <button class="btn btn-sm btn-secondary" onclick="closeColumnModal()" style="border: none; background: transparent; font-size: 20px;">&times;</button>
            </div>
            <p style="color: var(--text-muted); font-size: 14px; margin-bottom: 20px;">Select the columns you want to display in the ticket list:</p>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 25px;">
                <?php foreach($available_columns as $key => $label): ?>
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="checkbox" class="col-checkbox" value="<?php echo htmlspecialchars($key); ?>" <?php echo in_array($key, $selected_columns) ? 'checked' : ''; ?>>
                        <?php echo htmlspecialchars($label); ?>
                    </label>
                <?php endforeach; ?>
            </div>
            <div class="modal-actions" style="justify-content: flex-end;">
                <button class="btn btn-secondary" onclick="closeColumnModal()">Cancel</button>
                <button class="btn btn-primary" onclick="saveColumns()">
                    <i class="bi bi-check2"></i> Apply Columns
                </button>
            </div>
        </div>
    </div>

    <script>
        function openColumnModal() {
            document.getElementById('columnModal').classList.add('active');
        }

        function closeColumnModal() {
            document.getElementById('columnModal').classList.remove('active');
        }

        function saveColumns() {
            let selected = [];
            let checkboxes = document.querySelectorAll('.col-checkbox:checked');
            checkboxes.forEach(function(cb) {
                selected.push(cb.value);
            });
            if (selected.length === 0) {
                alert('Please select at least one column.');
                return;
            }
            // Save to cookie (expire in 365 days)
            let date = new Date();
            date.setTime(date.getTime() + (365*24*60*60*1000));
            document.cookie = "ticket_columns=" + encodeURIComponent(JSON.stringify(selected)) + "; expires=" + date.toUTCString() + "; path=/";
            
            // Reload page
            window.location.reload();
        }

        document.getElementById('columnModal').addEventListener('click', function(e) {
            if (e.target === this) closeColumnModal();
        });

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
            if (e.key === 'Escape') {
                closeDeleteModal();
                closeColumnModal();
            }
        });
    </script>
    

<?php include TEMPLATE_DIR . 'layouts/footer.tpl'; ?>
