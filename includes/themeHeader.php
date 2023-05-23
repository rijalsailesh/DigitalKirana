<?php
require_once 'Connection.php';
require_once 'functions.php';

$loggedInUser = getLoggedInUser();

function getTenantById($tenantId)
{
    $connection = ConnectionHelper::getConnection();
    $query = "select * from tenants where Id = :id";
    $statement = $connection->prepare($query);
    $statement->bindParam('id', $tenantId, PDO::PARAM_INT);
    $statement->execute();
    $result = $statement->fetch(PDO::FETCH_ASSOC);
    return $result;
}

$tenant = getTenantById(getTenantId());

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Digital Kirana - Admin Panel</title>

    <!-- Custom fonts for this template-->
    <link href="../assets/theme/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="../assets/theme/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">

    <link rel="shortcut icon" href="../assets/imgs/logo.png" type="image/x-icon">

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />


</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion non-printable" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="/dashboard.php">
                <div class="sidebar-brand-icon">
                    <!-- <i class="fas fa-laugh-wink"></i> -->
                    <img src="../assets/imgs/logo.png" height="40" weight="40" />
                </div>
                <div class="sidebar-brand-text mx-3">Digital Kirana</div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item <?= $_SERVER['REQUEST_URI'] == "/dashboard.php" ? "active" : "" ?>">
                <a class="nav-link" href="/dashboard.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Interface
            </div>

            <?php
            if (isAdmin()) :
            ?>
                <li class="nav-item <?= strpos($_SERVER['REQUEST_URI'], "user/") ? "active" : "" ?>">
                    <a class="nav-link" href="/user">
                        <i class="fas fa-fw fa-user"></i>
                        <span>User</span></a>
                </li>
            <?php
            endif;
            ?>
            <?php
            if (getLoggedInUserRole() == 'SalesPerson') :
            ?>
                <li class="nav-item  <?= strpos($_SERVER['REQUEST_URI'], "sales/") ? "active" : "" ?>">
                    <a class=" nav-link" href="/sales">
                        <i class="fas fa-fw fa-hand-holding"></i>
                        <span>Sales</span></a>
                </li>
            <?php
            else :
            ?>
                <li class="nav-item <?= strpos($_SERVER['REQUEST_URI'], "category/") ? "active" : "" ?>">
                    <a class=" nav-link" href="/category">
                        <i class="fas fa-fw fa-wrench"></i>
                        <span>Category</span></a>
                </li>


                <li class="nav-item  <?= strpos($_SERVER['REQUEST_URI'], "product/") ? "active" : "" ?>">
                    <a class=" nav-link" href="/product">
                        <i class="fas fa-fw fa-fire"></i>
                        <span>Product</span></a>
                </li>
                <hr class="sidebar-divider">

                <!-- Nav Item - Pages Collapse Menu -->
                <li class="nav-item  <?= (strpos($_SERVER['REQUEST_URI'], "supplier/") || (strpos($_SERVER['REQUEST_URI'], "customer/"))) ? "active" : "" ?>">
                    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
                        <i class="fas fa-fw fa-users"></i>
                        <span>Supplier / Customer</span>
                    </a>
                    <div id="collapseTwo" class="collapse <?= (strpos($_SERVER['REQUEST_URI'], "supplier/") || (strpos($_SERVER['REQUEST_URI'], "customer/"))) ? "show" : "" ?>" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                        <div class="bg-white py-2 collapse-inner rounded">
                            <h6 class="collapse-header">Suppliers & Customers</h6>
                            <a class="collapse-item  <?= strpos($_SERVER['REQUEST_URI'], "supplier/") ? "active" : "" ?>" href="/supplier">Supplier</a>
                            <a class="collapse-item  <?= strpos($_SERVER['REQUEST_URI'], "customer/") ? "active" : "" ?>" href="/customer">Customer</a>
                        </div>
                    </div>
                </li>

                <li class="nav-item  <?= strpos($_SERVER['REQUEST_URI'], "purchase/") ? "active" : "" ?>">
                    <a class=" nav-link" href="/purchase">
                        <i class="fas fa-fw fa-cart-plus"></i>
                        <span>Purchase</span></a>
                </li>
                <li class="nav-item  <?= strpos($_SERVER['REQUEST_URI'], "sales/") ? "active" : "" ?>">
                    <a class=" nav-link" href="/sales">
                        <i class="fas fa-fw fa-hand-holding"></i>
                        <span>Sales</span></a>
                </li>


                <li class="nav-item <?= strpos($_SERVER['REQUEST_URI'], "reports/") ? "active" : "" ?>">
                    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseThree" aria-expanded="true" aria-controls="collapseThree">
                        <i class="fas fa-fw fa-file"></i>
                        <span>Reports</span>
                    </a>
                    <div id="collapseThree" class="collapse <?= strpos($_SERVER['REQUEST_URI'], "reports/") ? "show" : "" ?>" aria-labelledby=" headingTwo" data-parent="#accordionSidebar">
                        <div class="bg-white py-2 collapse-inner rounded">
                            <h6 class="collapse-header">Reports</h6>
                            <a class="collapse-item <?= strpos($_SERVER['REQUEST_URI'], "reports/productSuppliers.php") ? "active" : "" ?>" href=" /reports/productSuppliers.php">Product Supplier</a>
                            <a class="collapse-item <?= strpos($_SERVER['REQUEST_URI'], "reports/supplierProducts.php") ? "active" : "" ?>" href="/reports/supplierProducts.php">Supplier Product</a>
                            <a class="collapse-item <?= strpos($_SERVER['REQUEST_URI'], "reports/stock.php") ? "active" : "" ?>" href="/reports/stock.php">Stock</a>
                            <a class="collapse-item <?= strpos($_SERVER['REQUEST_URI'], "reports/minimumStock.php") ? "active" : "" ?>" href="/reports/minimumStock.php">Minimum Stock</a>
                            <a class="collapse-item <?= strpos($_SERVER['REQUEST_URI'], "reports/maximumStock.php") ? "active" : "" ?>" href="/reports/maximumStock.php">Maximum Stock</a>
                        </div>
                    </div>
                </li>
            <?php
            endif;
            ?>



            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>
        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <p class="d-none d-sm-inline-block mr-auto ml-md-3 my-2 my-md-0 mw-100">
                        <span class="fw-bolder text-dark" style="font-size:1.2rem;font-weight:500;">
                            <?= $tenant['Name'] ?>
                        </span>
                    </p>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto non-printable">

                        <!-- Nav Item - Alerts -->

                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                    <?= $loggedInUser['Role'] . ": " ?>
                                    <?= $loggedInUser['FirstName'] . " " . $loggedInUser['LastName'] ?>
                                </span>
                                <img class="img-profile rounded-circle" src="/assets/imgs/logos/<?= $tenant['LogoUrl'] == null ? "default.png" : $tenant['LogoUrl'] ?>">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="/profile">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Profile
                                </a>
                                <div class="dropdown-divider"></div>
                                <button class="dropdown-item" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </button>
                            </div>
                        </li>
                    </ul>
                </nav>

                <!-- Logout Modal-->
                <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                            </div>
                            <div class="modal-body">Select "Logout" below if you are ready to logout out.</div>
                            <div class="modal-footer">
                                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                                <form action="/logout.php" method="post">
                                    <button class="btn btn-primary" href="login.html">Logout</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>