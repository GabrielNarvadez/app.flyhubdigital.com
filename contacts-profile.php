<?php include 'layouts/session.php'; ?>
<?php include 'layouts/main.php'; ?>

<head>
    <title>Contacts Profile | Flyhub Business Apps</title>
    <?php include 'layouts/title-meta.php'; ?>

    <?php include 'layouts/head-css.php'; ?>
</head>

<body>
    <!-- Begin page -->
    <div class="wrapper">

        <?php include 'layouts/menu.php'; ?>

        <!-- ============================================================== -->
        <!-- Start Page Content here -->
        <!-- ============================================================== -->

        <div class="content-page">
            <div class="content">

                <!-- Start Content-->
                <div class="container-fluid">

                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box">
                                <div class="page-title-right">
                                   
                                </div>
                                <h3 class="page-title">
                                  <a href="contacts.php" style="text-decoration: none; color: inherit;">
                                    &#60; Contacts
                                  </a>
                                </h3>

                            </div>
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-xl-3 col-lg-5">
                            <div class="card text-center">
                                <div class="card-body">
                                    <img src="assets/images/users/avatar-1.jpg" class="rounded-circle avatar-lg img-thumbnail" alt="profile-image">

                                    <h4 class="mb-1 mt-2">Tosha Minner</h4>
                                    <p class="text-muted">Founder at ABC Company</p>

                                    <div class="btn-group mb-2">
                                            <button type="button" class="btn btn-primary">Edit Profile</button>
                                            <button type="button" class="btn btn-success">Send Message</button>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-light dropdown-toggle show" data-bs-toggle="dropdown" aria-expanded="true"> Actions <span class="caret"></span> </button>
                                                <div class="dropdown-menu show" style="position: absolute; inset: 0px auto auto 0px; margin: 0px; transform: translate(0px, 39px);" data-popper-placement="bottom-start">
                                                    <a class="dropdown-item" href="#">Customize Properties</a>
                                                    <a class="dropdown-item" href="#">View All Properties</a>
                                                </div>
                                            </div>
                                        </div>

                                    <div class="text-start mt-3">

                                        <h4 class="text-primary mb-2"><strong>About this contact</strong><span class="ms-2"></span></h4>

                                        <p class="text-muted mb-2"><strong>Mobile :</strong><span class="ms-2">(123)
                                                123 1234</span></p>

                                        <p class="text-muted mb-2"><strong>Email :</strong> <span class="ms-2 ">user@email.domain</span></p>

                                        <p class="text-muted mb-2"><strong>Location :</strong> <span class="ms-2">USA</span></p>

                                        <p class="text-muted mb-2"><strong>Contact Owner :</strong> <span class="ms-2">Lester Caraan</span></p>

                                        <p class="text-muted mb-2"><strong>Status :</strong> <span class="ms-2">Active</span></p>
                                    </div>

                                    <ul class="social-list list-inline mt-3 mb-0">
                                        <li class="list-inline-item">
                                            <a href="javascript: void(0);" class="social-list-item border-primary text-primary"><i class="ri-facebook-circle-fill"></i></a>
                                        </li>
                                        <li class="list-inline-item">
                                            <a href="javascript: void(0);" class="social-list-item border-danger text-danger"><i class="ri-google-fill"></i></a>
                                        </li>
                                        <li class="list-inline-item">
                                            <a href="javascript: void(0);" class="social-list-item border-info text-info"><i class="ri-twitter-fill"></i></a>
                                        </li>
                                    </ul>
                                </div> <!-- end card-body -->
                            </div> <!-- end card -->

                            <!-- Messages-->
                            <div class="card">
                                 <!-- end card-body-->
                            </div> <!-- end card-->

                        </div> <!-- end col-->

                        <div class="col-xl-9 col-lg-7">
                            <!-- Chart-->
                            <!-- <div class="card">
                                <div class="card-body">
                                    <h4 class="header-title mb-3">Orders & Revenue</h4>
                                    <div dir="ltr">
                                        <div style="height: 260px;" class="chartjs-chart">
                                            <canvas id="high-performing-product"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div> -->
                            <!-- End Chart-->

                            <div class="card">
                                <div class="card-body">
                                    <ul class="nav nav-pills bg-nav-pills nav-justified mb-3">
                                        <li class="nav-item">
                                            <a href="#aboutme" data-bs-toggle="tab" aria-expanded="false" class="nav-link rounded-start rounded-0 active">
                                                Overview
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="#timeline" data-bs-toggle="tab" aria-expanded="true" class="nav-link rounded-0">
                                                Timeline
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="#settings" data-bs-toggle="tab" aria-expanded="false" class="nav-link rounded-end rounded-0">
                                                Associations
                                            </a>
                                        </li>
                                    </ul>
                                    <div class="tab-content">
                                        <div class="tab-pane show active" id="aboutme">

                                            <div class="accordion" id="accordionExample">
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="headingOne">
                                                <button class="accordion-button fw-medium collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                                                    <h4><strong>Invoices</strong></h4>
                                                </button>
                                            </h2>
                                            <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionExample" style="">
                                                <div class="accordion-body">
                                                    
                                                    <div class="table-responsive-sm">
                                        <table class="table table-hover table-centered mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Product</th>
                                                    <th>Price</th>
                                                    <th>Quantity</th>
                                                    <th>Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>ASOS Ridley High Waist</td>
                                                    <td>$79.49</td>
                                                    <td><span class="badge bg-primary">82 Pcs</span></td>
                                                    <td>$6,518.18</td>
                                                </tr>
                                                <tr>
                                                    <td>Marco Lightweight Shirt</td>
                                                    <td>$128.50</td>
                                                    <td><span class="badge bg-primary">37 Pcs</span></td>
                                                    <td>$4,754.50</td>
                                                </tr>
                                                <tr>
                                                    <td>Half Sleeve Shirt</td>
                                                    <td>$39.99</td>
                                                    <td><span class="badge bg-primary">64 Pcs</span></td>
                                                    <td>$2,559.36</td>
                                                </tr>
                                                <tr>
                                                    <td>Lightweight Jacket</td>
                                                    <td>$20.00</td>
                                                    <td><span class="badge bg-primary">184 Pcs</span></td>
                                                    <td>$3,680.00</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                                    
                                                </div>
                                            </div>
                                        </div>
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="headingTwo">
                                                <button class="accordion-button fw-medium" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
                                                    <h4><strong>Tickets</strong></h4>
                                                </button>
                                            </h2>
                                            <div id="collapseTwo" class="accordion-collapse collapse show" aria-labelledby="headingTwo" data-bs-parent="#accordionExample" style="">
                                                <div class="accordion-body">
                                                    <strong>This is the second item's accordion body.</strong> It is hidden by default, until the collapse
                                                    plugin adds the appropriate classes that we use to style each element. These classes control the overall
                                                    appearance, as well as the showing and hiding via CSS transitions. You can modify any of this with
                                                    custom CSS or overriding our default variables. It's also worth noting that just about any HTML can go
                                                    within the <code>.accordion-body</code>, though the transition does limit overflow.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="headingThree">
                                                <button class="accordion-button fw-medium collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                                    <h4><strong>Deals</strong></h4>
                                                </button>
                                            </h2>
                                            <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                                                <div class="accordion-body">
                                                    <strong>This is the third item's accordion body.</strong> It is hidden by default, until the collapse
                                                    plugin adds the appropriate classes that we use to style each element. These classes control the overall
                                                    appearance, as well as the showing and hiding via CSS transitions. You can modify any of this with
                                                    custom CSS or overriding our default variables. It's also worth noting that just about any HTML can go
                                                    within the <code>.accordion-body</code>, though the transition does limit overflow.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                            <!-- end timeline -->



                                        </div> <!-- end tab-pane -->
                                        <!-- end about me section content -->

                                        <div class="tab-pane" id="timeline">

                                            <!-- comment box -->
                                            <div class="border rounded mt-2 mb-3">
                                                <form action="#" class="comment-area-box">
                                                    <textarea rows="3" class="form-control border-0 resize-none" placeholder="Write something...."></textarea>
                                                    
                                                    <div class="p-2 bg-light d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <!-- Notes -->
                                                            <a href="#" class="btn btn-sm px-2 fs-16 btn-light active" title="Add Note"><i class="ri-sticky-note-line"></i></a>
                                                            <!-- Calls -->
                                                            <a href="#" class="btn btn-sm px-2 fs-16 btn-light" title="Log Call"><i class="ri-phone-line"></i></a>
                                                            <!-- Meetings -->
                                                            <a href="#" class="btn btn-sm px-2 fs-16 btn-light" title="Schedule Meeting"><i class="ri-calendar-event-line"></i></a>
                                                            <!-- Tasks -->
                                                            <a href="#" class="btn btn-sm px-2 fs-16 btn-light" title="Create Task"><i class="ri-task-line"></i></a>
                                                        </div>
                                                        <button type="submit" class="btn btn-sm btn-dark">Add Activity</button>
                                                    </div>




                                                </form>
                                            </div> <!-- end .border-->
                                            <!-- end comment box -->

                                            <!-- Story Box-->
                                            <div class="border border-light rounded p-2 mb-3">
                                                <div class="d-flex">
                                                    <img class="me-2 rounded-circle" src="assets/images/users/avatar-4.jpg" alt="Generic placeholder image" height="32">
                                                    <div>
                                                        <h5 class="m-0">Thelma Fridley</h5>
                                                        <p class="text-muted"><small>about 1 hour ago</small></p>
                                                    </div>
                                                </div>
                                                <div class="fs-16 text-center fst-italic text-dark">
                                                    <i class="ri-double-quotes-l fs-20"></i> Cras sit amet nibh libero, in
                                                    gravida nulla. Nulla vel metus scelerisque ante sollicitudin. Cras
                                                    purus odio, vestibulum in vulputate at, tempus viverra turpis. Duis
                                                    sagittis ipsum. Praesent mauris. Fusce nec tellus sed augue semper
                                                    porta. Mauris massa.
                                                </div>

                                                <div class="mx-n2 p-2 mt-3 bg-light">
                                                    <div class="d-flex">
                                                        <img class="me-2 rounded-circle" src="assets/images/users/avatar-3.jpg" alt="Generic placeholder image" height="32">
                                                        <div>
                                                            <h5 class="mt-0">Jeremy Tomlinson <small class="text-muted">about 2 minuts ago</small></h5>
                                                            Nice work, makes me think of The Money Pit.

                                                            <br />
                                                            <a href="javascript: void(0);" class="text-muted fs-13 d-inline-block mt-2"><i class="ri-reply-line"></i> Reply</a>

                                                            <div class="d-flex mt-3">
                                                                <a class="pe-2" href="#">
                                                                    <img src="assets/images/users/avatar-4.jpg" class="rounded-circle" alt="Generic placeholder image" height="32">
                                                                </a>
                                                                <div>
                                                                    <h5 class="mt-0">Thelma Fridley <small class="text-muted">5 hours ago</small></h5>
                                                                    i'm in the middle of a timelapse animation myself! (Very different though.) Awesome stuff.
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="d-flex mt-2">
                                                        <a class="pe-2" href="#">
                                                            <img src="assets/images/users/avatar-1.jpg" class="rounded-circle" alt="Generic placeholder image" height="32">
                                                        </a>
                                                        <div class="w-100">
                                                            <input type="text" id="simpleinput" class="form-control border-0 form-control-sm" placeholder="Add comment">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="mt-2">
                                                    <a href="javascript: void(0);" class="btn btn-sm btn-link text-danger"><i class="ri-heart-line"></i> Like (28)</a>
                                                    <a href="javascript: void(0);" class="btn btn-sm btn-link text-muted"><i class="ri-share-line"></i> Share</a>
                                                </div>
                                            </div>

                                            <!-- Story Box-->
                                            <div class="border border-light rounded p-2 mb-3">
                                                <div class="d-flex">
                                                    <img class="me-2 rounded-circle" src="assets/images/users/avatar-3.jpg" alt="Generic placeholder image" height="32">
                                                    <div>
                                                        <h5 class="m-0">Jeremy Tomlinson</h5>
                                                        <p class="text-muted"><small>3 hours ago</small></p>
                                                    </div>
                                                </div>
                                                <p>Story based around the idea of time lapse, animation to post soon!</p>

                                                <img src="assets/images/small/small-1.jpg" alt="post-img" class="rounded me-1" height="60" />
                                                <img src="assets/images/small/small-2.jpg" alt="post-img" class="rounded me-1" height="60" />
                                                <img src="assets/images/small/small-3.jpg" alt="post-img" class="rounded" height="60" />

                                                <div class="mt-2">
                                                    <a href="javascript: void(0);" class="btn btn-sm btn-link text-muted"><i class="ri-reply-line"></i> Reply</a>
                                                    <a href="javascript: void(0);" class="btn btn-sm btn-link text-muted"><i class="ri-heart-line"></i> Like</a>
                                                    <a href="javascript: void(0);" class="btn btn-sm btn-link text-muted"><i class="ri-share-line"></i> Share</a>
                                                </div>
                                            </div>

                                            <!-- Story Box-->
                                            <div class="border border-light p-2 mb-3">
                                                <div class="d-flex">
                                                    <img class="me-2 rounded-circle" src="assets/images/users/avatar-6.jpg" alt="Generic placeholder image" height="32">
                                                    <div>
                                                        <h5 class="m-0">Martin Williamson</h5>
                                                        <p class="text-muted"><small>15 hours ago</small></p>
                                                    </div>
                                                </div>
                                                <p>The parallax is a little odd but O.o that house build is awesome!!</p>

                                                <iframe src='https://player.vimeo.com/video/87993762' height='300' class="img-fluid border-0"></iframe>
                                            </div>

                                            <div class="text-center">
                                                <a href="javascript:void(0);" class="text-danger"><i class="ri-loader-fill me-1"></i> Load more </a>
                                            </div>

                                        </div>
                                        <!-- end timeline content-->

                                        <div class="tab-pane" id="settings">
                                            
                                            <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">

                                            <div class="card mb-3 shadow-sm" style="max-width: 480px;">
                                              <div class="row g-0 align-items-center">
                                                <!-- Logo preview -->
                                                <div class="col-auto">

                                                  <img src="assets/images/brands/slack.png" class="rounded-start" alt="Flyhub Logo" style="width:50px; height:50px; object-fit:cover;">
                                                </div>
                                                <div class="col">
                                                  <div class="card-body py-2 px-3">
                                                    <!-- Contact Name & Phone -->
                                                    <div class="d-flex align-items-center mb-1">
                                                      <span class="badge bg-light text-secondary"><i class="ri-phone-line me-1"></i>+63 912 345 6789</span>
                                                    </div>
                                                    <!-- Company association (clickable) -->
                                                    <div class="d-flex align-items-center gap-2 mb-1">
                                                      <a href="company-profile.php?id=1" class="text-decoration-none fw-semibold text-primary" target="_blank">
                                                        <i class="ri-building-line me-1"></i>Flyhub Digital
                                                      </a>
                                                    </div>
                                                    <!-- City & State -->
                                                    <p class="mb-1 text-muted fs-14"><i class="ri-map-pin-line me-1"></i>Quezon City, Metro Manila</p>
                                                    
                                                    <!-- Deals (as icons, example) -->
                                                    <div class="d-flex align-items-center gap-2">
                                                      <span class="text-success" title="Deal: Website Redesign">
                                                        <i class="ri-briefcase-line fs-18 align-middle"></i> Website Redesign
                                                      </span>
                                                      <!-- Add more deals as needed -->
                                                    </div>
                                                  </div>
                                                </div>
                                              </div>
                                            </div>



                                             <!-- end inbox-widget -->

                                        </div>
                                        <!-- end settings content-->

                                    </div> <!-- end tab-content -->
                                </div> <!-- end card body -->
                            </div> <!-- end card -->
                        </div> <!-- end col -->
                    </div>
                    <!-- end row-->

                </div>
                <!-- container -->

            </div>
            <!-- content -->

            <?php include 'layouts/footer.php'; ?>

        </div>

        <!-- ============================================================== -->
        <!-- End Page content -->
        <!-- ============================================================== -->

    </div>
    <!-- END wrapper -->

    <?php include 'layouts/right-sidebar.php'; ?>

    <?php include 'layouts/footer-scripts.php'; ?>

    <!-- Chart.js -->
    <script src="assets/vendor/chart.js/chart.min.js"></script>

    <!-- Profile Demo App js -->
    <script src="assets/js/pages/demo.profile.js"></script>

    <!-- App js -->
    <script src="assets/js/app.min.js"></script>

</body>

</html>