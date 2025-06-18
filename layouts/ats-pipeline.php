<?php include 'layouts/session.php'; ?>
<?php include 'layouts/main.php'; ?>

<head>
    <title>Recruitment Pipeline | Flyhub Business Apps</title>
    <?php include 'layouts/title-meta.php'; ?>
    <?php include 'layouts/head-css.php'; ?>
    <style>
      .pipeline-board { display: flex; gap: 18px; overflow-x: auto; }
      .pipeline-stage {
        min-width: 260px;
        background: #f8f9fb;
        border-radius: 12px;
        box-shadow: 0 2px 6px #0001;
        padding: 1rem .6rem;
        flex: 1 0 260px;
      }
      .pipeline-stage-title {
        font-size: 1.07rem;
        font-weight: 600;
        margin-bottom: 10px;
        color: #3185FC;
        text-align: center;
      }
      .pipeline-card {
        background: #fff;
        border-radius: 7px;
        box-shadow: 0 1px 4px #0001;
        padding: .7rem .8rem;
        margin-bottom: 14px;
        border-left: 3px solid #3185FC;
        cursor: grab;
      }
      .pipeline-card .name { font-weight: 600; font-size: 1.02rem; }
      .pipeline-card .role { color: #666; font-size: .93rem; }
      .pipeline-card .meta { color: #999; font-size: .85rem; }
      .pipeline-card .status { font-size: .93rem; }
    </style>
</head>

<body>
    <div class="wrapper">
        <?php include 'layouts/menu.php'; ?>
        <div class="content-page">
            <div class="content">
                <div class="container-fluid">
                    <!-- TITLE -->
                    <div class="row mb-2">
                        <div class="col-12">
                            <div class="page-title-box d-flex align-items-center justify-content-between">
                                <h4 class="page-title">Recruitment Pipeline</h4>
                                <span class="text-muted small">ATS Kanban & Table view</span>
                            </div>
                        </div>
                    </div>

                    <!-- KANBAN BOARD -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="pipeline-board">
                              <!-- STAGE 1: Sourced -->
                              <div class="pipeline-stage">
                                <div class="pipeline-stage-title bg-light rounded py-1">Sourced</div>
                                <div class="pipeline-card">
                                  <div class="name">Jane S.</div>
                                  <div class="role">Accountant</div>
                                  <div class="meta">Applied: 2024-08-01</div>
                                  <div class="status text-muted">Source: LinkedIn</div>
                                </div>
                                <div class="pipeline-card">
                                  <div class="name">Ryan T.</div>
                                  <div class="role">Data Analyst</div>
                                  <div class="meta">Applied: 2024-08-02</div>
                                  <div class="status text-muted">Source: Jobstreet</div>
                                </div>
                              </div>
                              <!-- STAGE 2: Phone Screen -->
                              <div class="pipeline-stage">
                                <div class="pipeline-stage-title bg-light rounded py-1">Phone Screen</div>
                                <div class="pipeline-card">
                                  <div class="name">Mike D.</div>
                                  <div class="role">Sales Executive</div>
                                  <div class="meta">Screen: 2024-08-04</div>
                                  <div class="status text-primary">Scheduled: Aug 6, 9:30AM</div>
                                </div>
                              </div>
                              <!-- STAGE 3: Interview -->
                              <div class="pipeline-stage">
                                <div class="pipeline-stage-title bg-light rounded py-1">Interview</div>
                                <div class="pipeline-card">
                                  <div class="name">Jane S.</div>
                                  <div class="role">Accountant</div>
                                  <div class="meta">Interview: 2024-08-05</div>
                                  <div class="status text-success">Passed</div>
                                </div>
                              </div>
                              <!-- STAGE 4: Offer -->
                              <div class="pipeline-stage">
                                <div class="pipeline-stage-title bg-light rounded py-1">Offer</div>
                                <div class="pipeline-card">
                                  <div class="name">Jane S.</div>
                                  <div class="role">Accountant</div>
                                  <div class="meta">Offer sent: 2024-08-06</div>
                                  <div class="status text-info">Pending Acceptance</div>
                                </div>
                              </div>
                              <!-- STAGE 5: Hired -->
                              <div class="pipeline-stage">
                                <div class="pipeline-stage-title bg-light rounded py-1">Hired</div>
                                <div class="pipeline-card">
                                  <div class="name">Jane S.</div>
                                  <div class="role">Accountant</div>
                                  <div class="meta">Hired: 2024-08-07</div>
                                  <div class="status text-success">Onboarded</div>
                                </div>
                              </div>
                              <!-- STAGE 6: Rejected -->
                              <div class="pipeline-stage">
                                <div class="pipeline-stage-title bg-light rounded py-1">Rejected</div>
                                <div class="pipeline-card">
                                  <div class="name">Ryan T.</div>
                                  <div class="role">Data Analyst</div>
                                  <div class="meta">Rejected: 2024-08-04</div>
                                  <div class="status text-danger">Failed interview</div>
                                </div>
                              </div>
                            </div>
                        </div>
                    </div>

                    <!-- TABLE VIEW -->
                    <div class="row">
                      <div class="col-12">
                        <div class="card">
                          <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                              <div>
                                <span class="pipeline-stage-title">Pipeline Table View</span>
                              </div>
                              <div>
                                <input type="text" class="form-control form-control-sm" placeholder="Search candidate, job...">
                              </div>
                            </div>
                            <div class="table-responsive">
                              <table class="table table-sm table-hover align-middle">
                                <thead class="table-light">
                                  <tr>
                                    <th>Candidate</th>
                                    <th>Role</th>
                                    <th>Stage</th>
                                    <th>Status</th>
                                    <th>Last Update</th>
                                    <th>Notes</th>
                                    <th>Actions</th>
                                  </tr>
                                </thead>
                                <tbody>
                                  <tr>
                                    <td>Jane S.</td>
                                    <td>Accountant</td>
                                    <td>Offer</td>
                                    <td><span class="badge bg-info">Pending Acceptance</span></td>
                                    <td>2024-08-06</td>
                                    <td>Ready to start after clearance</td>
                                    <td>
                                      <button class="btn btn-outline-primary btn-sm"><i class="ri-eye-line"></i></button>
                                      <button class="btn btn-outline-success btn-sm"><i class="ri-arrow-right-line"></i></button>
                                    </td>
                                  </tr>
                                  <tr>
                                    <td>Ryan T.</td>
                                    <td>Data Analyst</td>
                                    <td>Rejected</td>
                                    <td><span class="badge bg-danger">Failed Interview</span></td>
                                    <td>2024-08-04</td>
                                    <td>Did not meet criteria</td>
                                    <td>
                                      <button class="btn btn-outline-primary btn-sm"><i class="ri-eye-line"></i></button>
                                    </td>
                                  </tr>
                                  <tr>
                                    <td>Mike D.</td>
                                    <td>Sales Executive</td>
                                    <td>Phone Screen</td>
                                    <td><span class="badge bg-primary">Scheduled: Aug 6</span></td>
                                    <td>2024-08-04</td>
                                    <td>To be interviewed by Mark</td>
                                    <td>
                                      <button class="btn btn-outline-primary btn-sm"><i class="ri-eye-line"></i></button>
                                      <button class="btn btn-outline-success btn-sm"><i class="ri-arrow-right-line"></i></button>
                                    </td>
                                  </tr>
                                  <!-- More rows... -->
                                </tbody>
                              </table>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>

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
