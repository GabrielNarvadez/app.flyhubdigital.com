<div class="card shadow-sm mb-4 overflow-hidden">
    <img src="https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&amp;fit=crop&amp;w=800&amp;q=80" class="w-100" alt="Aerial Property View" style="max-height:180px;object-fit:cover;">
    <div class="card-img-overlay p-3 d-flex flex-column justify-content-end align-items-end">
        <span class="badge bg-primary shadow">Featured Property</span>
    </div>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <div class="row align-items-end mb-3">
            <div class="col-md-6">
                <form method="get" class="row g-2">
                    <div class="col-auto">
                        <label class="form-label mb-0">Filter:</label>
                    </div>
                    <div class="col-auto">
                        <select name="range" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">All Dates</option>
                            <option value="today" <?= ($filter_range == 'today') ? 'selected' : '' ?>>Today</option>
                            <option value="week" <?= ($filter_range == 'week') ? 'selected' : '' ?>>This Week</option>
                            <option value="month" <?= ($filter_range == 'month') ? 'selected' : '' ?>>This Month</option>
                            <option value="year" <?= ($filter_range == 'year') ? 'selected' : '' ?>>This Year</option>
                        </select>
                    </div>
                    <div class="col-auto">
                        <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                            <?php foreach ($statuses as $key => $label): ?>
                                <option value="<?= htmlspecialchars($key) ?>" <?= ($status_filter === $key) ? 'selected' : '' ?>><?= htmlspecialchars($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <input type="hidden" name="page" value="<?= htmlspecialchars($_GET['page'] ?? '') ?>">
                </form>
            </div>
            <div class="col-md-6 d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-outline-primary btn-sm" onclick="printTable()">Print</button>
                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="exportTableToCSV('invoices.csv')">Export</button>
            </div>
        </div>

        <div class="table-responsive">
            <table id="invoice-table" class="table table-bordered table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Invoice #</th>
                        <th>Client Name</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Invoice Date</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <?php 
                            $status = strtolower($row['status']);
                            $badgeClass = $statusBadgeClass[$status] ?? 'secondary';
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($row['invoice_number']) ?></td>
                            <td><?= htmlspecialchars(trim($row['client_name']) ?: '—') ?></td>
                            <td><strong><?= number_format($row['total'], 2) ?></strong></td>
                            <td>
                                <span class="badge bg-<?= $badgeClass ?>">
                                    <?= htmlspecialchars(ucfirst($status)) ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($row['issue_date']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted">No invoices found.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function printTable() {
    var printContents = document.getElementById('invoice-table').outerHTML;
    var win = window.open('', '', 'height=700,width=900');
    win.document.write('<html><head><title>Print Invoices</title>');
    win.document.write('<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">');
    win.document.write('</head><body>');
    win.document.write('<h3>Invoices</h3>');
    win.document.write(printContents);
    win.document.write('</body></html>');
    win.document.close();
    win.focus();
    win.print();
    win.close();
}

function exportTableToCSV(filename) {
    var csv = [];
    var rows = document.querySelectorAll("#invoice-table tr");
    for (var i = 0; i < rows.length; i++) {
        var row = [], cols = rows[i].querySelectorAll("th,td");
        for (var j = 0; j < cols.length; j++)
            row.push('"' + cols[j].innerText.replace(/"/g, '""') + '"');
        csv.push(row.join(","));
    }
    var csvFile = new Blob([csv.join("\n")], {type: "text/csv"});
    var downloadLink = document.createElement("a");
    downloadLink.download = filename;
    downloadLink.href = window.URL.createObjectURL(csvFile);
    downloadLink.style.display = "none";
    document.body.appendChild(downloadLink);
    downloadLink.click();
}
</script>
