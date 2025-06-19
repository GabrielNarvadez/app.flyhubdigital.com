<?php include 'layouts/session.php'; ?>
<?php include 'layouts/main.php'; ?>

<head>
    <title>Recruitment Dashboard | Flyhub Business Apps</title>
    <?php include 'layouts/title-meta.php'; ?>
    <?php include 'layouts/head-css.php'; ?>
    <style>
      .recruitment-metric-card {
        border-radius: 13px;
        box-shadow: 0 2px 8px #0001;
        background: #fff;
        text-align: center;
      }
      .recruitment-metric-card .icon {
        font-size: 2.2rem;
        margin-bottom: 4px;
        color: #3185FC;
      }
      .recruitment-section-title {
        font-weight: 700;
        font-size: 1.06rem;
        color: #3185FC;
        margin-bottom: .6rem;
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
                                <h4 class="page-title">Recruitment Dashboard</h4>
                            </div>
                        </div>
                    </div>

                    <!-- METRICS/KEY STATS -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-2 col-6">
                            <div class="recruitment-metric-card p-3">
                                <div class="icon"><i class="ri-briefcase-4-line"></i></div>
                                <div class="text-muted small mb-1">Open Jobs</div>
                                <div class="fs-4 fw-bold text-primary">12</div>
                            </div>
                        </div>
                        <div class="col-md-2 col-6">
                            <div class="recruitment-metric-card p-3">
                                <div class="icon"><i class="ri-user-search-line"></i></div>
                                <div class="text-muted small mb-1">Active Candidates</div>
                                <div class="fs-4 fw-bold text-success">83</div>
                            </div>
                        </div>
                        <div class="col-md-2 col-6">
                            <div class="recruitment-metric-card p-3">
                                <div class="icon"><i class="ri-git-merge-line"></i></div>
                                <div class="text-muted small mb-1">Interviews Scheduled</div>
                                <div class="fs-4 fw-bold text-warning">9</div>
                            </div>
                        </div>
                        <div class="col-md-2 col-6">
                            <div class="recruitment-metric-card p-3">
                                <div class="icon"><i class="ri-trophy-line"></i></div>
                                <div class="text-muted small mb-1">Offers Extended</div>
                                <div class="fs-4 fw-bold text-info">4</div>
                            </div>
                        </div>
                        <div class="col-md-2 col-6">
                            <div class="recruitment-metric-card p-3">
                                <div class="icon"><i class="ri-user-smile-line"></i></div>
                                <div class="text-muted small mb-1">Placements This Month</div>
                                <div class="fs-4 fw-bold text-primary">2</div>
                            </div>
                        </div>
                        <div class="col-md-2 col-6">
                            <div class="recruitment-metric-card p-3">
                                <div class="icon"><i class="ri-time-line"></i></div>
                                <div class="text-muted small mb-1">Avg. Time-to-Hire</div>
                                <div class="fs-5 fw-bold text-secondary">18 days</div>
                            </div>
                        </div>
                    </div>

                    <!-- PIPELINE OVERVIEW -->
                    <div class="row mb-3">
                        <div class="col-lg-8">
                            <div class="recruitment-section-title">Pipeline Overview</div>
                            <div class="card">
                              <div class="card-body">
                                <div class="table-responsive">
                                  <table class="table table-sm mb-0">
                                    <thead class="table-light">
                                      <tr>
                                        <th>Stage</th>
                                        <th>Count</th>
                                        <th>In Progress</th>
                                        <th>Hired</th>
                                        <th>Rejected</th>
                                      </tr>
                                    </thead>
                                    <tbody>
                                      <tr>
                                        <td>Sourced</td>
                                        <td>24</td>
                                        <td>18</td>
                                        <td>3</td>
                                        <td>3</td>
                                      </tr>
                                      <tr>
                                        <td>Phone Screen</td>
                                        <td>12</td>
                                        <td>8</td>
                                        <td>2</td>
                                        <td>2</td>
                                      </tr>
                                      <tr>
                                        <td>Interview</td>
                                        <td>9</td>
                                        <td>7</td>
                                        <td>1</td>
                                        <td>1</td>
                                      </tr>
                                      <tr>
                                        <td>Offer</td>
                                        <td>4</td>
                                        <td>2</td>
                                        <td>2</td>
                                        <td>0</td>
                                      </tr>
                                    </tbody>
                                  </table>
                                </div>
                              </div>
                            </div>
                        </div>
                        <!-- SHORTCUTS -->
                        <div class="col-lg-4">
                            <div class="recruitment-section-title">Quick Actions</div>
                            <div class="card mb-2">
                              <div class="card-body d-grid gap-2">
                                <a href="ats-jobs.php" class="btn btn-outline-primary btn-sm"><i class="ri-briefcase-line"></i> Post New Job</a>
                                <a href="ats-candidates.php" class="btn btn-outline-success btn-sm"><i class="ri-user-add-line"></i> Add Candidate</a>
                                <a href="ats-pipeline.php" class="btn btn-outline-warning btn-sm"><i class="ri-git-merge-line"></i> View Pipeline</a>
                                <a href="ats-calendar.php" class="btn btn-outline-info btn-sm"><i class="ri-calendar-event-line"></i> Schedule Interview</a>
                              </div>
                            </div>
                        </div>
                    </div>

                    <!-- RECENT ACTIVITY / NOTIFICATIONS -->
                    <div class="row">
                      <div class="col-lg-7 mb-3">
                        <div class="recruitment-section-title">Recent Activity</div>
                        <div class="card">
                          <div class="card-body">
                            <ul class="list-unstyled mb-0">
                              <li class="mb-2"><i class="ri-checkbox-circle-line text-success"></i> Candidate <b>Jane S.</b> accepted offer for <b>Accountant</b> – <span class="text-muted small">2h ago</span></li>
                              <li class="mb-2"><i class="ri-time-line text-warning"></i> Interview scheduled for <b>Mike D.</b> – <span class="text-muted small">Today 2:00pm</span></li>
                              <li class="mb-2"><i class="ri-close-circle-line text-danger"></i> Candidate <b>Ryan T.</b> rejected for <b>Data Analyst</b> – <span class="text-muted small">Yesterday</span></li>
                              <li><i class="ri-briefcase-4-line text-info"></i> New job posted: <b>Sales Executive</b> – <span class="text-muted small">Yesterday</span></li>
                            </ul>
                          </div>
                        </div>
                      </div>
                      <!-- SHORT REPORTS -->
                      <div class="col-lg-5">
                        <div class="recruitment-section-title">Reports</div>
                        <div class="card">
                          <div class="card-body">
                            <ul class="mb-0">
                              <li><i class="ri-arrow-right-s-line"></i> Most active source: <b>LinkedIn</b></li>
                              <li><i class="ri-arrow-right-s-line"></i> Avg. interviews/job: <b>2.8</b></li>
                              <li><i class="ri-arrow-right-s-line"></i> Pipeline drop-off: <b>21%</b></li>
                              <li><i class="ri-arrow-right-s-line"></i> Offer acceptance: <b>67%</b></li>
                            </ul>
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
