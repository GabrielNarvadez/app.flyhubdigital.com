<?php
// Card 1: Units Sold (from invoices)
$units_sql = "SELECT COUNT(*) AS units_sold FROM invoices WHERE status IN ('paid', 'sent')";
$units_res = mysqli_query($link, $units_sql);
$units_row = mysqli_fetch_assoc($units_res);
$units_sold = $units_row['units_sold'] ?? 0;

// Card 2: Revenue YTD (invoices + soas)
$ytd_invoice_sql = "SELECT SUM(total) AS total FROM invoices WHERE status = 'paid' AND YEAR(issue_date) = YEAR(CURDATE())";
$ytd_invoice_res = mysqli_query($link, $ytd_invoice_sql);
$ytd_invoice = mysqli_fetch_assoc($ytd_invoice_res)['total'] ?? 0;

$ytd_soa_sql = "SELECT SUM(total_paid) AS total FROM soas WHERE YEAR(issue_date) = YEAR(CURDATE())";
$ytd_soa_res = mysqli_query($link, $ytd_soa_sql);
$ytd_soa = mysqli_fetch_assoc($ytd_soa_res)['total'] ?? 0;

$revenue_ytd = ($ytd_invoice ?: 0) + ($ytd_soa ?: 0);
?>

<div class="row mb-4 g-3">
    <div class="col-12">  <!-- full width column -->
        <div class="card text-success border-success border shadow h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="display-6 me-3"><i class="ri-home-2-line"></i></div>
                    <div>
                        <h6 class="mb-1 fw-bold">Units Sold</h6>
                        <div class="fs-3 fw-semibold"><?= number_format($units_sold) ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12">  <!-- full width column -->
        <div class="card text-primary border-primary border shadow h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="display-6 me-3"><i class="ri-currency-line"></i></div>
                    <div>
                        <h6 class="mb-1 fw-bold">Revenue (YTD)</h6>
                        <div class="fs-3 fw-semibold">₱<?= number_format($revenue_ytd, 2) ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>