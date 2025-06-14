

<form method="post" id="projects-form">

    <!-- Bulk Panel -->
    <div id="bulk-panel" class="d-flex align-items-center bg-light border rounded px-3 py-2 mb-3" style="display:none;">
        <small id="selected-count" class="me-3">0 selected</small>
        <a href="#" id="select-all-link" class="me-3">Select all <span id="total-count"><?= mysqli_num_rows($result) ?></span></a>
        <button type="button" id="bulk-archive" class="btn btn-link p-0 me-3" style="text-decoration:none;color:#ffc107;">Archive</button>
    </div>

    <!-- Filters + Action Buttons -->
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-3">
        <div class="row g-2 align-items-end" style="flex-grow:1; min-width:350px;">
            <div class="col-md-4">
                <label class="form-label">Project</label>
                <select class="form-select" id="filter-project">
                    <option value="">All Projects</option>
                    <?php foreach ($project_titles as $title): ?>
                        <option value="<?= htmlspecialchars($title) ?>"><?= htmlspecialchars($title) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Status</label>
                <select class="form-select" id="filter-status">
                    <option value="">All Statuses</option>
                    <option value="Available">Available</option>
                    <option value="Sold">Sold</option>
                    <option value="Reserved">Reserved</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Class</label>
                <select class="form-select" id="filter-class">
                    <option value="">All Classes</option>
                </select>
            </div>
        </div>

        <div class="btn-group" style="margin-top: 30px;">
            <button type="button" id="print-pdf-btn" class="btn btn-outline-primary">Print</button>
            <button type="button" id="export-csv-btn" class="btn btn-outline-secondary">Export</button>
            <button type="button" class="btn btn-success" data-bs-toggle="offcanvas" data-bs-target="#addUnitCanvas" aria-controls="addUnitCanvas">Add Unit</button>
        </div>
    </div>

    <!-- Units Table -->
    <div class="card shadow-sm mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle mb-0" id="projects-table">
                    <thead class="table-light">
                        <tr>
                            <th style="width:36px;"><input type="checkbox" id="master-checkbox" /></th>
                            <th>Unit</th>
                            <th>Class</th>
                            <th>Lot Area (sqm)</th>
                            <th>Price/sqm</th>
                            <th>Total Price</th>
                            <th>Status</th>
                            <th>Site</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($result) > 0): ?>
                            <?php $i = 0; while ($row = mysqli_fetch_assoc($result)): $i++;
                                $total_price = $row['lot_area'] * $row['price_per_sqm'];
                                $unitStatus = "Available"; // Adjust accordingly if real status field
                            ?>
                                <tr data-project="<?= htmlspecialchars($row['project_title']) ?>"
                                    data-status="<?= $unitStatus ?>"
                                    data-class="<?= htmlspecialchars($row['lot_class']) ?>">
                                    <td><input type="checkbox" class="row-checkbox" name="selected[]" value="<?= $row['id'] ?>"></td>
                                    <td>
                                        <a href="#"
                                           class="text-decoration-underline fw-semibold"
                                           data-bs-toggle="offcanvas"
                                           data-bs-target="#unitDetails<?= $i ?>"
                                           aria-controls="unitDetails<?= $i ?>">
                                            <?= htmlspecialchars($row['project_title'] . ' - Block ' . $row['block'] . ', Lot ' . $row['lot']) ?>
                                        </a>
                                    </td>
                                    <td><?= htmlspecialchars($row['lot_class']) ?></td>
                                    <td><?= htmlspecialchars($row['lot_area']) ?></td>
                                    <td>₱<?= number_format($row['price_per_sqm'], 2) ?></td>
                                    <td>₱<?= number_format($total_price, 2) ?></td>
                                    <td><span class="badge bg-success"><?= $unitStatus ?></span></td>
                                    <td><?= htmlspecialchars($row['project_site']) ?></td>
                                </tr>

                                <!-- Offcanvas Modal for Details -->
                                <div class="offcanvas offcanvas-end" tabindex="-1" id="unitDetails<?= $i ?>" aria-labelledby="unitDetailsLabel<?= $i ?>">
                                    <div class="offcanvas-header">
                                        <h5 class="offcanvas-title" id="unitDetailsLabel<?= $i ?>">Unit Details</h5>
                                        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                                    </div>
                                    <div class="offcanvas-body">
                                        <dl class="row">
                                            <dt class="col-sm-4">Project Title</dt>
                                            <dd class="col-sm-8"><?= htmlspecialchars($row['project_title']) ?></dd>
                                            <dt class="col-sm-4">Site</dt>
                                            <dd class="col-sm-8"><?= htmlspecialchars($row['project_site']) ?></dd>
                                            <dt class="col-sm-4">Phase</dt>
                                            <dd class="col-sm-8"><?= htmlspecialchars($row['phase']) ?></dd>
                                            <dt class="col-sm-4">Block</dt>
                                            <dd class="col-sm-8"><?= htmlspecialchars($row['block']) ?></dd>
                                            <dt class="col-sm-4">Lot</dt>
                                            <dd class="col-sm-8"><?= htmlspecialchars($row['lot']) ?></dd>
                                            <dt class="col-sm-4">Class</dt>
                                            <dd class="col-sm-8"><?= htmlspecialchars($row['lot_class']) ?></dd>
                                            <dt class="col-sm-4">Lot Area</dt>
                                            <dd class="col-sm-8"><?= htmlspecialchars($row['lot_area']) ?> sqm</dd>
                                            <dt class="col-sm-4">Price per sqm</dt>
                                            <dd class="col-sm-8">₱<?= number_format($row['price_per_sqm'], 2) ?></dd>
                                            <dt class="col-sm-4">Total Price</dt>
                                            <dd class="col-sm-8">₱<?= number_format($total_price, 2) ?></dd>
                                            <dt class="col-sm-4">Created At</dt>
                                            <dd class="col-sm-8"><?= htmlspecialchars($row['created_at']) ?></dd>
                                        </dl>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="8" class="text-center text-muted">No projects found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</form>

<!-- Add Unit Offcanvas -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="addUnitCanvas" aria-labelledby="addUnitCanvasLabel">
    <div class="offcanvas-header">
        <h4 class="offcanvas-title" id="addUnitCanvasLabel">Add New Unit</h4>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form id="add-unit-form" method="post">
            <input type="hidden" name="add_unit" value="1" />
            <div class="mb-3">
                <label for="project_id" class="form-label">Project</label>
                <select id="project_id" name="project_id" class="form-select" required>
                    <option value="">Select Project</option>
                    <?php foreach ($projects_for_dropdown as $proj): ?>
                        <option value="<?= $proj['id'] ?>" data-site="<?= htmlspecialchars($proj['project_site']) ?>"><?= htmlspecialchars($proj['project_title']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="site" class="form-label">Site</label>
                <input type="text" id="site" name="site" class="form-control" readonly>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="phase" class="form-label">Phase</label>
                    <input type="text" id="phase" name="phase" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="lot_class" class="form-label">Class</label>
                    <select id="lot_class" name="lot_class" class="form-select" required>
                        <option value="">Select Class</option>
                        <option value="Economy">Economy</option>
                        <option value="Standard">Standard</option>
                        <option value="Premium">Premium</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="block" class="form-label">Block</label>
                    <input type="text" id="block" name="block" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="lot" class="form-label">Lot</label>
                    <input type="text" id="lot" name="lot" class="form-control" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="lot_area" class="form-label">Lot Area (sqm)</label>
                    <input type="number" step="0.01" min="0" id="lot_area" name="lot_area" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="price_per_sqm" class="form-label">Price per sqm (₱)</label>
                    <input type="number" step="0.01" min="0" id="price_per_sqm" name="price_per_sqm" class="form-control" required>
                </div>
            </div>
            <div class="mb-3">
                <label for="total_price" class="form-label">Total Price (₱)</label>
                <input type="text" id="total_price" name="total_price" class="form-control" readonly>
            </div>
            <button type="submit" class="btn btn-primary">Add Unit</button>
        </form>
    </div>
</div>

<!-- Archive Disclaimer Modal -->
<div class="modal fade" id="archiveModal" tabindex="-1" aria-labelledby="archiveModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="archiveModalLabel">Archive Project(s)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><strong>Projects cannot be deleted if they have related Statement of Account (SOA) records.</strong></p>
                <p>Instead, selected projects will be archived and hidden from this table. You can view them in the Archived Projects page.</p>
                <p>Are you sure you want to archive the selected project(s)?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="confirm-archive" class="btn btn-warning">Archive</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const table = document.getElementById('projects-table');
    const bulkPanel = document.getElementById('bulk-panel');
    const selectedCount = document.getElementById('selected-count');
    const selectAllLink = document.getElementById('select-all-link');
    const masterCheckbox = document.getElementById('master-checkbox');
    const archiveBtn = document.getElementById('bulk-archive');
    const form = document.getElementById('projects-form');

    function updatePanel() {
        const visibleRows = table.querySelectorAll('tbody tr:not([style*="display: none"])');
        const checkedBoxes = Array.from(visibleRows)
            .map(row => row.querySelector('.row-checkbox'))
            .filter(cb => cb && cb.checked);
        bulkPanel.style.display = checkedBoxes.length > 0 ? 'flex' : 'none';
        selectedCount.textContent = `${checkedBoxes.length} selected`;
    }

    // Row checkboxes
    document.querySelectorAll('.row-checkbox').forEach(cb => {
        cb.addEventListener('change', () => {
            updatePanel();
            const visibleBoxes = Array.from(table.querySelectorAll('tbody tr:not([style*="display: none"]) .row-checkbox'));
            const checkedBoxes = visibleBoxes.filter(box => box.checked);
            masterCheckbox.checked = visibleBoxes.length > 0 && visibleBoxes.length === checkedBoxes.length;
        });
    });

    // Master checkbox
    masterCheckbox.addEventListener('change', () => {
        const visibleBoxes = table.querySelectorAll('tbody tr:not([style*="display: none"]) .row-checkbox');
        visibleBoxes.forEach(cb => cb.checked = masterCheckbox.checked);
        updatePanel();
    });

    // Select all link
    selectAllLink.addEventListener('click', e => {
        e.preventDefault();
        const visibleBoxes = table.querySelectorAll('tbody tr:not([style*="display: none"]) .row-checkbox');
        visibleBoxes.forEach(cb => cb.checked = true);
        masterCheckbox.checked = true;
        updatePanel();
    });

    // Bulk archive button
    archiveBtn.addEventListener('click', e => {
        e.preventDefault();
        const visibleBoxes = table.querySelectorAll('tbody tr:not([style*="display: none"]) .row-checkbox');
        const checked = Array.from(visibleBoxes).filter(cb => cb.checked);
        if (checked.length === 0) return;
        const archiveModal = new bootstrap.Modal(document.getElementById('archiveModal'));
        archiveModal.show();
    });

    // Confirm archive button in modal
    document.getElementById('confirm-archive').addEventListener('click', () => {
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'bulk_archive';
        hiddenInput.value = '1';
        form.appendChild(hiddenInput);
        form.submit();
    });

    // FILTER LOGIC
    function normalize(str) {
        return (str || '').toLowerCase().replace(/\s+/g, '').trim();
    }

    function applyTableFilters() {
        const proj = normalize(document.getElementById('filter-project').value);
        const stat = normalize(document.getElementById('filter-status').value);
        const lotClass = normalize(document.getElementById('filter-class').value);

        document.querySelectorAll("#projects-table tbody tr").forEach(tr => {
            let matches = true;
            if (proj && normalize(tr.getAttribute('data-project')) !== proj) matches = false;
            if (stat && normalize(tr.getAttribute('data-status')) !== stat) matches = false;
            if (lotClass && normalize(tr.getAttribute('data-class')) !== lotClass) matches = false;
            tr.style.display = matches ? "" : "none";
        });
        updatePanel();
    }

    document.getElementById('filter-project').addEventListener('change', applyTableFilters);
    document.getElementById('filter-status').addEventListener('change', applyTableFilters);
    document.getElementById('filter-class').addEventListener('change', applyTableFilters);

    // Add Unit form logic
    const projectSelect = document.getElementById('project_id');
    const siteInput = document.getElementById('site');
    const lotAreaInput = document.getElementById('lot_area');
    const pricePerSqmInput = document.getElementById('price_per_sqm');
    const totalPriceInput = document.getElementById('total_price');

    projectSelect.addEventListener('change', () => {
        const selectedOption = projectSelect.selectedOptions[0];
        siteInput.value = selectedOption ? selectedOption.dataset.site : '';
    });

    function calculateTotalPrice() {
        const area = parseFloat(lotAreaInput.value) || 0;
        const price = parseFloat(pricePerSqmInput.value) || 0;
        totalPriceInput.value = area && price ? '₱' + (area * price).toFixed(2) : '';
    }
    lotAreaInput.addEventListener('input', calculateTotalPrice);
    pricePerSqmInput.addEventListener('input', calculateTotalPrice);

    // Print to PDF button
    document.getElementById('print-pdf-btn').addEventListener('click', () => {
        window.print();
    });

    // Export CSV button
    document.getElementById('export-csv-btn').addEventListener('click', () => {
        const rows = Array.from(table.querySelectorAll('tbody tr:not([style*="display: none"])'));
        if (!rows.length) return alert('No visible rows to export.');

        let csv = '"Unit","Class","Lot Area (sqm)","Price/sqm","Total Price","Status","Site"\n';
        rows.forEach(tr => {
            const cols = tr.querySelectorAll('td');
            const rowData = [
                cols[1].innerText.trim(),
                cols[2].innerText.trim(),
                cols[3].innerText.trim(),
                cols[4].innerText.trim(),
                cols[5].innerText.trim(),
                cols[6].innerText.trim(),
                cols[7].innerText.trim(),
            ];
            csv += '"' + rowData.join('","') + '"\n';
        });

        const blob = new Blob([csv], {type: 'text/csv'});
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'units_export.csv';
        a.click();
        URL.revokeObjectURL(url);
    });
});
</script>
