<?php include 'layouts/session.php'; ?>
<?php include 'layouts/main.php'; ?>

<head>
    <title>Job Requisitions & Postings | Flyhub Business Apps</title>
    <?php include 'layouts/title-meta.php'; ?>
    <?php include 'layouts/head-css.php'; ?>
    <style>
        .job-status-badge { font-size: 0.97rem; }
        .job-card { border-radius: 12px; border: 1px solid #f1f3fa; background: #fff; margin-bottom: 1.5rem; }
        .job-card-header { border-bottom: 1px solid #f1f3fa; background: #f6f9fe; border-top-left-radius: 12px; border-top-right-radius: 12px; }
        .job-card {
    border-radius: 14px;
    border: 1.5px solid #eef1f7;
    background: #fff;
    margin-bottom: 1.7rem;
    box-shadow: 0 1px 8px #0001;
    padding: 0;
    transition: box-shadow .12s;
}
.job-card-header {
    border-bottom: 1px solid #f0f2f7;
    background: #f7faff;
    border-top-left-radius: 14px;
    border-top-right-radius: 14px;
    padding: 1.25rem 1.4rem 0.9rem 1.4rem;
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
}
.job-title {
    font-size: 1.19rem;
    font-weight: 700;
    margin-bottom: 2px;
    color: #304872;
    letter-spacing: -.01em;
}
.job-meta {
    font-size: 1.01rem;
    color: #61709e;
    font-weight: 500;
}
.job-status-badge {
    font-size: .98rem;
    font-weight: 600;
    padding: 4px 16px;
}
.card-body.job-body {
    padding: 1.3rem 1.4rem 1rem 1.4rem;
}
.job-info-row {
    font-size: .98rem;
    margin-bottom: 6px;
}
.job-info-label {
    color: #6c7ba1;
    font-weight: 500;
}
.job-reqs {
    margin-bottom: 8px;
    font-size: .99rem;
}
.job-reqs ul {
    margin-bottom: 2px;
}
.job-footer {
    margin-top: 1.3rem;
    display: flex;
    gap: 8px;
}
@media (max-width: 600px) {
    .job-card-header, .card-body.job-body { padding: 1rem !important; }
    .job-title { font-size: 1.03rem; }
}
    </style>
</head>

<body>
    <div class="wrapper">

        <?php include 'layouts/menu.php'; ?>

        <div class="content-page">
            <div class="content">

                <div class="container-fluid">
                    <!-- PAGE TITLE -->
                    <div class="row mb-2">
                        <div class="col-12">
                            <div class="page-title-box d-flex align-items-center justify-content-between">
                                <h4 class="page-title">Job Requisitions & Postings</h4>
                                <a href="#jobModal" data-bs-toggle="modal" class="btn btn-primary btn-sm">
                                    <i class="ri-add-line"></i> New Job Requisition
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- JOBS LIST FILTER -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card p-3 mb-3">
                                <form class="row g-2">
                                    <div class="col-md-3">
                                        <input type="text" class="form-control form-control-sm" placeholder="Search job title, department...">
                                    </div>
                                    <div class="col-md-3">
                                        <select class="form-select form-select-sm">
                                            <option selected>Status: All</option>
                                            <option>Open</option>
                                            <option>On Hold</option>
                                            <option>Closed</option>
                                            <option>Filled</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <select class="form-select form-select-sm">
                                            <option selected>Department: All</option>
                                            <option>Sales</option>
                                            <option>IT</option>
                                            <option>HR</option>
                                            <option>Finance</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <select class="form-select form-select-sm">
                                            <option selected>Location: All</option>
                                            <option>Makati</option>
                                            <option>BGC</option>
                                            <option>Cebu</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <button class="btn btn-outline-secondary btn-sm w-100" type="button">
                                            <i class="ri-filter-3-line"></i> Filter
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- JOB CARDS LIST -->
<div class="row">
    <div class="col-md-6 col-lg-4">
        <div class="job-card shadow-sm">
            <div class="job-card-header">
                <div>
                    <div class="job-title">Sales Executive</div>
                    <div class="job-meta">Sales Dept. | Makati | ₱25,000–₱32,000</div>
                </div>
                <span class="badge bg-success job-status-badge">Open</span>
            </div>
            <div class="card-body job-body">
                <div class="job-info-row mb-1">
                    <span class="job-info-label">Hiring Manager:</span>
                    <span class="fw-semibold">Mark Lim</span>
                </div>
                <div class="job-reqs mb-2">
                    <span class="job-info-label">Requirements:</span>
                    <ul class="ps-4">
                        <li>Graduate of any 4-year course</li>
                        <li>2+ years sales experience</li>
                        <li>Excellent communication skills</li>
                    </ul>
                </div>
                <div class="text-muted" style="font-size:.97rem;">Posted: 2024-08-03</div>
                <div class="job-footer">
                    <a href="#" class="btn btn-outline-info btn-sm"><i class="ri-share-forward-line"></i> Publish/Share</a>
                    <a href="#" class="btn btn-outline-primary btn-sm"><i class="ri-eye-line"></i> View</a>
                    <a href="#" class="btn btn-outline-secondary btn-sm"><i class="ri-edit-line"></i> Edit</a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-4">
        <div class="job-card shadow-sm">
            <div class="job-card-header">
                <div>
                    <div class="job-title">IT Support Specialist</div>
                    <div class="job-meta">IT Dept. | BGC | ₱28,000–₱38,000</div>
                </div>
                <span class="badge bg-secondary job-status-badge">On Hold</span>
            </div>
            <div class="card-body job-body">
                <div class="job-info-row mb-1">
                    <span class="job-info-label">Hiring Manager:</span>
                    <span class="fw-semibold">Jane Sison</span>
                </div>
                <div class="job-reqs mb-2">
                    <span class="job-info-label">Requirements:</span>
                    <ul class="ps-4">
                        <li>BS in Computer Science</li>
                        <li>1+ year IT helpdesk experience</li>
                    </ul>
                </div>
                <div class="text-muted" style="font-size:.97rem;">Posted: 2024-08-02</div>
                <div class="job-footer">
                    <a href="#" class="btn btn-outline-info btn-sm"><i class="ri-share-forward-line"></i> Publish/Share</a>
                    <a href="#" class="btn btn-outline-primary btn-sm"><i class="ri-eye-line"></i> View</a>
                    <a href="#" class="btn btn-outline-secondary btn-sm"><i class="ri-edit-line"></i> Edit</a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-4">
        <div class="job-card shadow-sm">
            <div class="job-card-header">
                <div>
                    <div class="job-title">HR Officer</div>
                    <div class="job-meta">HR Dept. | Cebu | ₱20,000–₱25,000</div>
                </div>
                <span class="badge bg-primary job-status-badge">Filled</span>
            </div>
            <div class="card-body job-body">
                <div class="job-info-row mb-1">
                    <span class="job-info-label">Hiring Manager:</span>
                    <span class="fw-semibold">Anna Cruz</span>
                </div>
                <div class="job-reqs mb-2">
                    <span class="job-info-label">Requirements:</span>
                    <ul class="ps-4">
                        <li>BS in Psychology/HRM</li>
                        <li>1+ year HR experience</li>
                    </ul>
                </div>
                <div class="text-muted" style="font-size:.97rem;">Posted: 2024-07-30</div>
                <div class="job-footer">
                    <a href="#" class="btn btn-outline-info btn-sm"><i class="ri-share-forward-line"></i> Publish/Share</a>
                    <a href="#" class="btn btn-outline-primary btn-sm"><i class="ri-eye-line"></i> View</a>
                    <a href="#" class="btn btn-outline-secondary btn-sm"><i class="ri-edit-line"></i> Edit</a>
                </div>
            </div>
        </div>
    </div>
</div>


                </div> <!-- container-fluid -->

                <!-- NEW JOB REQUISITION MODAL -->
                <div class="modal fade" id="jobModal" tabindex="-1" aria-labelledby="jobModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <form>
                                <div class="modal-header">
                                    <h5 class="modal-title" id="jobModalLabel"><i class="ri-add-line"></i> New Job Requisition</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Job Title</label>
                                            <input type="text" class="form-control" placeholder="e.g., Sales Executive">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Department</label>
                                            <select class="form-select">
                                                <option>Sales</option>
                                                <option>IT</option>
                                                <option>HR</option>
                                                <option>Finance</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Location</label>
                                            <select class="form-select">
                                                <option>Makati</option>
                                                <option>BGC</option>
                                                <option>Cebu</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Hiring Manager</label>
                                            <input type="text" class="form-control" placeholder="e.g., Mark Lim">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Salary Range</label>
                                            <input type="text" class="form-control" placeholder="e.g., ₱25,000–₱32,000">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Status</label>
                                            <select class="form-select">
                                                <option>Open</option>
                                                <option>On Hold</option>
                                                <option>Closed</option>
                                                <option>Filled</option>
                                            </select>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Requirements</label>
                                            <textarea class="form-control" rows="3" placeholder="List requirements here"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary">Save Job</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- END MODAL -->

            </div> <!-- content -->
            <?php include 'layouts/footer.php'; ?>
        </div>

    </div>
    <!-- END wrapper -->

    <?php include 'layouts/right-sidebar.php'; ?>
    <?php include 'layouts/footer-scripts.php'; ?>
    <script src="assets/js/app.min.js"></script>

</body>
</html>
