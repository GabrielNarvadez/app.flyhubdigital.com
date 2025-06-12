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


                        <!-- personal profile -->
                        <div class="col-xl-3 col-lg-5">
                            <?php include 'modules/contact-card.php'; ?>
                        </div>
                        <!-- personal profile end -->

                        <!-- personal profile -->
                        <div class="col-xl-6 col-lg-7">
                        <?php include 'modules/middle-card.php'; ?>
                        </div>
                        <!-- end col -->

                        <div class="col-xl-3 col-lg-7">
                        <?php include 'modules/associations-card.php'; ?>
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