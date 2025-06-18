<?php include 'layouts/session.php'; ?>
<?php include 'layouts/main.php'; ?>

<head>
    <title>Property Inventory | Flyhub Business Apps</title>
    <?php include 'layouts/title-meta.php'; ?>
    <?php include 'layouts/head-css.php'; ?>
    <!-- Only icon sizing, rest from theme -->
    <style>
        .unit-photo-thumb { width:48px;height:48px;object-fit:cover;border-radius:8px;}
        .unit-doc-badge { font-size:.95em; }
    </style>
</head>

<body>
<div class="wrapper">
    <?php include 'layouts/menu.php'; ?>

    <div class="content-page">
        <div class="content">
            <div class="container-fluid">

                <!-- PAGE TITLE & BUTTONS -->
                <div class="row mb-1">
                    <div class="col-md-6">
                        <div class="page-title-box">
                            <h4 class="page-title">Units Inventory</h4>
                        </div>
                    </div>
                    <div class="col-md-6 text-end d-flex align-items-center justify-content-end gap-2">
                        <button class="btn btn-primary"><i class="ri-add-line"></i> Add Unit</button>
                        <button class="btn btn-outline-secondary">Export</button>
                        <button class="btn btn-outline-info">Print</button>
                        <div>
                            <button class="btn btn-light" id="btn-table-view"><i class="ri-list-unordered"></i></button>
                            <button class="btn btn-light" id="btn-grid-view"><i class="ri-layout-grid-line"></i></button>
                        </div>
                    </div>
                </div>
                <!-- FILTER BAR -->
                <div class="row mb-2">
                    <div class="col-md-3">
                        <select class="form-select">
                            <option>All Projects</option>
                            <option>Sunrise Estates</option>
                            <option>Green Meadows</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select">
                            <option>All Statuses</option>
                            <option>Available</option>
                            <option>Reserved</option>
                            <option>Sold</option>
                            <option>For Turnover</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select">
                            <option>All Classes</option>
                            <option>Inner Lot</option>
                            <option>Prime Lot</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="text" class="form-control" placeholder="Search units...">
                    </div>
                </div>
                <!-- BATCH ACTIONS BAR -->
                <div class="row mb-2">
                    <div class="col-12">
                        <span class="me-3 text-muted"><b>0 selected</b> | <a href="#">Select all 42</a> | <a href="#" class="text-danger">Archive</a></span>
                    </div>
                </div>

                <!-- TABLE VIEW -->
                <div class="card mb-4" id="tableView">
                    <div class="card-body p-0">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th><input type="checkbox"></th>
                                    <th>Unit</th>
                                    <th>Class</th>
                                    <th>Lot Area (sqm)</th>
                                    <th>Price/sqm</th>
                                    <th>Total Price</th>
                                    <th>Status</th>
                                    <th>Site</th>
                                    <th>Owner/Buyer</th>
                                    <th>Docs</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- SAMPLE ROW -->
                                <tr>
                                    <td><input type="checkbox"></td>
                                    <td>
                                        <a href="#" class="fw-bold text-primary" data-bs-toggle="modal" data-bs-target="#unitDetailModal">Sunrise Estates - Block 1, Lot 12</a>
                                    </td>
                                    <td>Residential</td>
                                    <td>120.5</td>
                                    <td>₱10,000.00</td>
                                    <td>₱1,205,000.00</td>
                                    <td><span class="badge bg-success">Available</span></td>
                                    <td>Santa Rosa, Laguna</td>
                                    <td class="text-muted">—</td>
                                    <td>
                                        <span class="badge bg-light text-dark unit-doc-badge"><i class="ri-attachment-2"></i> 1</span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#unitDetailModal"><i class="ri-eye-line"></i></button>
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown"></button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="#">Edit</a></li>
                                                <li><a class="dropdown-item" href="#">Upload Doc</a></li>
                                                <li><a class="dropdown-item" href="#">Print SOA</a></li>
                                                <li><a class="dropdown-item" href="#">Mark as Reserved</a></li>
                                                <li><a class="dropdown-item text-danger" href="#">Archive</a></li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                <!-- SAMPLE: SOLD -->
                                <tr>
                                    <td><input type="checkbox"></td>
                                    <td>
                                        <a href="#" class="fw-bold text-primary" data-bs-toggle="modal" data-bs-target="#unitDetailModal">Green Meadows - Block 3, Lot 9</a>
                                    </td>
                                    <td>Corner/End Lot</td>
                                    <td>110.00</td>
                                    <td>₱11,000.00</td>
                                    <td>₱1,210,000.00</td>
                                    <td><span class="badge bg-primary">Sold</span></td>
                                    <td>Tagaytay Highlands</td>
                                    <td class="text-dark">Juan Dela Cruz</td>
                                    <td>
                                        <span class="badge bg-light text-dark unit-doc-badge"><i class="ri-attachment-2"></i> 2</span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#unitDetailModal"><i class="ri-eye-line"></i></button>
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown"></button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="#">Edit</a></li>
                                                <li><a class="dropdown-item" href="#">View Owner</a></li>
                                                <li><a class="dropdown-item" href="#">Upload Doc</a></li>
                                                <li><a class="dropdown-item" href="#">Print SOA</a></li>
                                                <li><a class="dropdown-item text-danger" href="#">Archive</a></li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                <!-- Add more rows as needed -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- GRID VIEW -->
                <div class="row g-3 mb-4 d-none" id="gridView">
                    <div class="col-md-4 col-lg-3">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="badge bg-success">Available</span>
                                    <span class="badge bg-light text-dark unit-doc-badge"><i class="ri-attachment-2"></i> 1</span>
                                </div>
                                <div class="mb-2">
                                    <img src="https://images.unsplash.com/photo-1506744038136-46273834b3fb?fit=crop&w=200&q=80" class="unit-photo-thumb me-2 float-start" alt="Property Photo">
                                    <span class="fw-bold">Sunrise Estates</span><br>
                                    <span class="text-muted small">Santa Rosa, Laguna</span>
                                </div>
                                <div class="mb-2 small">
                                    <b>Phase:</b> 1 &nbsp; <b>Class:</b> Residential<br>
                                    <b>Block:</b> 1 &nbsp; <b>Lot:</b> 12<br>
                                    <b>Lot Area:</b> 120.5 sqm<br>
                                    <b>Price/sqm:</b> ₱10,000.00<br>
                                    <b>Total Price:</b> ₱1,205,000.00
                                </div>
                                <div class="small text-muted mb-1"><b>Owner:</b> —</div>
                                <div class="d-flex gap-1">
                                    <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#unitDetailModal"><i class="ri-eye-line"></i></button>
                                    <button class="btn btn-outline-secondary btn-sm"><i class="ri-edit-2-line"></i></button>
                                    <button class="btn btn-outline-info btn-sm"><i class="ri-attachment-2"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-3">
                        <div class="card h-100 shadow-sm border-info">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="badge bg-primary">Sold</span>
                                    <span class="badge bg-light text-dark unit-doc-badge"><i class="ri-attachment-2"></i> 2</span>
                                </div>
                                <div class="mb-2">
                                    <img src="https://images.unsplash.com/photo-1465101178521-c1a9136a6a94?fit=crop&w=200&q=80" class="unit-photo-thumb me-2 float-start" alt="Property Photo">
                                    <span class="fw-bold">Green Meadows</span><br>
                                    <span class="text-muted small">Tagaytay Highlands</span>
                                </div>
                                <div class="mb-2 small">
                                    <b>Phase:</b> 1 &nbsp; <b>Class:</b> Corner Lot<br>
                                    <b>Block:</b> 3 &nbsp; <b>Lot:</b> 9<br>
                                    <b>Lot Area:</b> 110.00 sqm<br>
                                    <b>Price/sqm:</b> ₱11,000.00<br>
                                    <b>Total Price:</b> ₱1,210,000.00
                                </div>
                                <div class="small text-muted mb-1"><b>Owner:</b> Juan Dela Cruz</div>
                                <div class="d-flex gap-1">
                                    <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#unitDetailModal"><i class="ri-eye-line"></i></button>
                                    <button class="btn btn-outline-secondary btn-sm"><i class="ri-edit-2-line"></i></button>
                                    <button class="btn btn-outline-info btn-sm"><i class="ri-attachment-2"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Add more cards as needed -->
                </div>

                <!-- DETAILS MODAL -->
                <div class="modal fade" id="unitDetailModal" tabindex="-1">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title fw-bold">Unit Details – Sunrise Estates Block 1, Lot 12</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row mb-3">
                                    <div class="col-md-4 text-center">
                                        <img src="https://images.unsplash.com/photo-1506744038136-46273834b3fb?fit=crop&w=300&q=80" class="img-fluid rounded" alt="Property Photo">
                                        <div class="mt-2 small text-muted">Photo</div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="mb-2">
                                            <b>Project:</b> Sunrise Estates<br>
                                            <b>Site:</b> Santa Rosa, Laguna<br>
                                            <b>Status:</b> <span class="badge bg-success">Available</span>
                                            <b>Class:</b> Residential<br>
                                            <b>Phase:</b> 1 <b>Block:</b> 1 <b>Lot:</b> 12<br>
                                            <b>Lot Area:</b> 120.5 sqm<br>
                                            <b>Price/sqm:</b> ₱10,000.00<br>
                                            <b>Total Price:</b> ₱1,205,000.00
                                        </div>
                                        <div class="mb-2">
                                            <b>Owner/Buyer:</b> —<br>
                                            <b>Documents:</b> <a href="#" class="badge bg-light text-dark unit-doc-badge"><i class="ri-attachment-2"></i> Contract.pdf</a>
                                        </div>
                                        <div class="mb-2">
                                            <b>Notes:</b> —<br>
                                            <b>Last Updated:</b> 2024-08-10 11:28AM by Lester
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-outline-secondary"><i class="ri-edit-2-line"></i> Edit Unit</button>
                                    <button class="btn btn-outline-info"><i class="ri-attachment-2"></i> Upload/View Docs</button>
                                    <button class="btn btn-outline-warning"><i class="ri-file-list-3-line"></i> Print SOA</button>
                                    <button class="btn btn-outline-primary">Change Status</button>
                                    <button class="btn btn-outline-danger">Archive</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END DETAILS MODAL -->

            </div> <!-- container-fluid -->
        </div> <!-- content -->
        <?php include 'layouts/footer.php'; ?>
    </div> <!-- content-page -->
</div> <!-- wrapper -->

<?php include 'layouts/right-sidebar.php'; ?>
<?php include 'layouts/footer-scripts.php'; ?>
<script src="assets/js/app.min.js"></script>
<script>
    // Toggle views
    document.getElementById('btn-table-view').onclick = function() {
        document.getElementById('tableView').classList.remove('d-none');
        document.getElementById('gridView').classList.add('d-none');
    };
    document.getElementById('btn-grid-view').onclick = function() {
        document.getElementById('gridView').classList.remove('d-none');
        document.getElementById('tableView').classList.add('d-none');
    };
</script>
</body>
</html>
