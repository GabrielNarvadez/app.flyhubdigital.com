<?php include 'layouts/session.php'; ?>
<?php include 'layouts/main.php'; ?>

<head>
    <title>Jobs & Scheduling | Flyhub Business Apps</title>
    <?php include 'layouts/title-meta.php'; ?>
    <?php include 'layouts/head-css.php'; ?>
    <style>
      .icon-action { font-size: 1.1rem; }
      .table th, .table td { vertical-align: middle !important; }
    </style>
</head>

<body>
    <div class="wrapper">
        <?php include 'layouts/menu.php'; ?>

        <div class="content-page">
            <div class="content">
                <div class="container-fluid">

                    <!-- PAGE TITLE & ACTIONS -->
                    <div class="row mb-2">
                        <div class="col-8">
                            <div class="page-title-box">
                                <h4 class="page-title">Jobs & Scheduling</h4>
                            </div>
                        </div>
                        <div class="col-4 text-end" style="padding-top: 30px;">
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNewJob"><i class="ri-add-line"></i> New Job</button>
                            <button class="btn btn-outline-secondary" data-bs-toggle="tab" data-bs-target="#calendarView"><i class="ri-calendar-event-line"></i> Calendar View</button>
                        </div>
                    </div>

                    <!-- TABS: JOB LIST / CALENDAR VIEW -->
                    <ul class="nav nav-tabs mb-3" id="jobsTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="list-tab" data-bs-toggle="tab" data-bs-target="#listView" type="button" role="tab">Jobs List</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="calendar-tab" data-bs-toggle="tab" data-bs-target="#calendarView" type="button" role="tab">Calendar View</button>
                        </li>
                    </ul>
                    <div class="tab-content" id="jobsTabsContent">

                        <!-- JOBS LIST TAB -->
                        <div class="tab-pane fade show active" id="listView" role="tabpanel" aria-labelledby="list-tab">
                            <div class="card">
                                <div class="card-body">
                                    <!-- Filters -->
                                    <div class="row mb-2">
                                        <div class="col-md-3">
                                            <select class="form-select">
                                                <option>All Statuses</option>
                                                <option>Open</option>
                                                <option>In Progress</option>
                                                <option>Completed</option>
                                                <option>Cancelled</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <select class="form-select">
                                                <option>All Technicians</option>
                                                <option>Juan Dela Cruz</option>
                                                <option>Maria Santos</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <input type="date" class="form-control" placeholder="Date">
                                        </div>
                                        <div class="col-md-3">
                                            <input type="text" class="form-control" placeholder="Search client, job...">
                                        </div>
                                    </div>
                                    <!-- Jobs Table -->
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>#</th>
                                                    <th>Date</th>
                                                    <th>Client/Site</th>
                                                    <th>Job Type</th>
                                                    <th>Assigned</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td><a href="#" data-bs-toggle="modal" data-bs-target="#modalJobDetail">JOB-2024-011</a></td>
                                                    <td>2024-08-15</td>
                                                    <td>Acme Corp<br><span class="small text-muted">Makati HQ</span></td>
                                                    <td>Aircon Installation</td>
                                                    <td>
                                                        <span class="badge bg-primary">Juan Dela Cruz</span>
                                                    </td>
                                                    <td><span class="badge bg-success">Completed</span></td>
                                                    <td>
                                                        <div class="btn-group">
                                                            <button class="btn btn-outline-primary btn-sm icon-action" data-bs-toggle="modal" data-bs-target="#modalJobDetail"><i class="ri-eye-line"></i></button>
                                                            <button class="btn btn-outline-secondary btn-sm icon-action"><i class="ri-edit-2-line"></i></button>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><a href="#">JOB-2024-012</a></td>
                                                    <td>2024-08-17</td>
                                                    <td>Beta Ltd.<br><span class="small text-muted">Pasig Office</span></td>
                                                    <td>Preventive Maintenance</td>
                                                    <td>
                                                        <span class="badge bg-warning text-dark">Maria Santos</span>
                                                    </td>
                                                    <td><span class="badge bg-primary">In Progress</span></td>
                                                    <td>
                                                        <div class="btn-group">
                                                            <button class="btn btn-outline-primary btn-sm icon-action"><i class="ri-eye-line"></i></button>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <!-- Add more jobs as needed -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- CALENDAR VIEW TAB -->
                        <div class="tab-pane fade" id="calendarView" role="tabpanel" aria-labelledby="calendar-tab">
                            <div class="card">
                                <div class="card-body">
                                    <div class="mb-3">
                                        <strong>Week of August 12–18, 2024</strong>
                                        <span class="text-muted small ms-2">*Calendar drag-and-drop coming soon</span>
                                    </div>
                                    <!-- Simple Calendar Table (stub) -->
                                    <div class="table-responsive">
                                        <table class="table table-bordered align-middle">
                                            <thead>
                                                <tr>
                                                    <th>Technician</th>
                                                    <th>Mon</th>
                                                    <th>Tue</th>
                                                    <th>Wed</th>
                                                    <th>Thu</th>
                                                    <th>Fri</th>
                                                    <th>Sat</th>
                                                    <th>Sun</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Juan Dela Cruz</td>
                                                    <td>JOB-2024-011<br><span class="badge bg-success">Completed</span></td>
                                                    <td>-</td>
                                                    <td>JOB-2024-013<br><span class="badge bg-primary">Open</span></td>
                                                    <td>-</td>
                                                    <td>JOB-2024-014<br><span class="badge bg-warning text-dark">In Progress</span></td>
                                                    <td>-</td>
                                                    <td>-</td>
                                                </tr>
                                                <tr>
                                                    <td>Maria Santos</td>
                                                    <td>-</td>
                                                    <td>JOB-2024-012<br><span class="badge bg-primary">In Progress</span></td>
                                                    <td>-</td>
                                                    <td>-</td>
                                                    <td>-</td>
                                                    <td>JOB-2024-015<br><span class="badge bg-success">Completed</span></td>
                                                    <td>-</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- Could add drag-and-drop calendar/JS lib in future -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- MODALS -->
                    <!-- New Job Modal (Stub) -->
                    <div class="modal fade" id="modalNewJob" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <form>
                                    <div class="modal-header">
                                        <h5 class="modal-title">New Job / Work Order</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="alert alert-secondary">Job creation form goes here…</div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary">Save Job</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- Job Detail Modal -->
                    <div class="modal fade" id="modalJobDetail" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Job Details – JOB-2024-011</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row mb-2">
                                        <div class="col-md-6">
                                            <b>Client:</b> Acme Corp<br>
                                            <b>Site:</b> Makati HQ<br>
                                            <b>Date:</b> 2024-08-15<br>
                                            <b>Status:</b> <span class="badge bg-success">Completed</span><br>
                                            <b>Job Type:</b> Aircon Installation<br>
                                            <b>Contact:</b> Juan Dela Cruz – 0917-123-4567
                                        </div>
                                        <div class="col-md-6">
                                            <b>Assigned Technician:</b> Juan Dela Cruz<br>
                                            <b>Job Description:</b>
                                            <p>Install 3 new split-type AC units. Test and commission after installation.</p>
                                            <b>Notes:</b>
                                            <ul>
                                                <li>Access to electrical room required</li>
                                                <li>Bring extension ladder</li>
                                            </ul>
                                        </div>
                                    </div>
                                    <hr>
                                    <b>Job Log</b>
                                    <ul>
                                        <li><b>2024-08-10 08:30</b> – Job created, assigned to Juan Dela Cruz</li>
                                        <li><b>2024-08-12 13:10</b> – Status changed to In Progress</li>
                                        <li><b>2024-08-15 15:42</b> – Marked as Completed</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- END MODALS -->

                </div> <!-- container-fluid -->
            </div> <!-- content -->
            <?php include 'layouts/footer.php'; ?>
        </div>
    </div>
    <?php include 'layouts/right-sidebar.php'; ?>
    <?php include 'layouts/footer-scripts.php'; ?>
    <script src="assets/js/app.min.js"></script>
</body>
</html>
