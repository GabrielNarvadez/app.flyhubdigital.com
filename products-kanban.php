<?php include 'layouts/session.php'; ?>
<?php include 'layouts/main.php'; ?>

<head>
    <title>Products Kanban | Flyhub Business Apps</title>
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
                                            <div class="page-title-left" style="margin-top: 30px;">
                                                <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                                                    <h3 class="mb-0">Units</h3>
                                                    <div class="d-flex gap-2">
                                                        <a href="estate-units.php" class="btn btn-outline-primary">
                                                            <i class="ri-list-unordered"></i>
                                                        </a>
                                                        <a href="products-kanban.php" class="btn btn-outline-primary active">
                                                            <i class="ri-grid-fill"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <?php include 'modules/products-kanban.php'; ?>

                            </div>
                        </div>
                    </div>

                </div> <!-- container -->

            </div> <!-- content -->

            <?php include 'layouts/footer.php'; ?>

        </div>

        <!-- ============================================================== -->
        <!-- End Page content -->
        <!-- ============================================================== -->

    </div>
    <!-- END wrapper -->

    <?php include 'layouts/right-sidebar.php'; ?>

    <?php include 'layouts/footer-scripts.php'; ?>

    <!-- App js -->
    <script src="assets/js/app.min.js"></script>

</body>

</html>