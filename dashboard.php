<?php
require_once 'includes/functions.php';
require_once 'includes/Connection.php';
//check authentication
if (!checkAuth()) {
    header("Location: /");
} else {
    if (getTenantId() == null) {
        header("Location: /error/accessDenied.php");
    }
}
//retrieve total sales for today
$today = date("Y-m-d");
function getTodaysTotalSales($today)
{
    $connection = ConnectionHelper::getConnection();
    $query = "select sum(NetTotal) as total from sales where CreatedAt = :date and TenantId = :tenantId";
    $statement = $connection->prepare($query);
    $statement->bindParam('date', $today);
    $tenantId = getTenantId();
    $statement->bindParam('tenantId', $tenantId);
    $statement->execute();
    $result = $statement->fetch(PDO::FETCH_ASSOC);
    return $result['total'];
}

function getTodaysTotalPurchase($today)
{
    $connection = ConnectionHelper::getConnection();
    $query = "select sum(NetTotal) as total from purchase where CreatedAt = :date and TenantId = :tenantId";
    $statement = $connection->prepare($query);
    $statement->bindParam('date', $today);
    $tenantId = getTenantId();
    $statement->bindParam('tenantId', $tenantId);
    $statement->execute();
    $result = $statement->fetch(PDO::FETCH_ASSOC);
    return $result['total'];
}

function getCustomersForLastWeek()
{
    $connection = ConnectionHelper::getConnection();
    $query = "select count(*) as total from customer where CreatedAt >= DATE_SUB(NOW(), INTERVAL 7 DAY) and TenantId = :tenantId";
    $statement = $connection->prepare($query);
    $tenantId = getTenantId();
    $statement->bindParam('tenantId', $tenantId);
    $statement->execute();
    $result = $statement->fetch(PDO::FETCH_ASSOC);
    return $result['total'];
}

function getProductsForLastWeek()
{
    $connection = ConnectionHelper::getConnection();
    $query = "select count(*) as total from product where CreatedAt >= DATE_SUB(NOW(), INTERVAL 7 DAY) and TenantId = :tenantId";
    $statement = $connection->prepare($query);
    $tenantId = getTenantId();
    $statement->bindParam('tenantId', $tenantId);
    $statement->execute();
    $result = $statement->fetch(PDO::FETCH_ASSOC);
    return $result['total'];
}


$todaySales = getTodaysTotalSales($today);
$todayPurchase = getTodaysTotalPurchase($today);
$totalCustomers = getCustomersForLastWeek();
$totalProducts = getProductsForLastWeek();




require_once('includes/themeHeader.php');
?>
<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
        <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="fas fa-download fa-sm text-white-50"></i> Generate Report</a>
    </div>

    <!-- Content Row -->
    <div class="row">

        <!-- Earnings (Monthly) Card Example -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Sales (Today's)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rs. <?= $todaySales == null ? "0" : $todaySales ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Earnings (Monthly) Card Example -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Purchase (Today's)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rs. <?= $todayPurchase == null ? "0" : $todayPurchase ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Earnings (Monthly) Card Example -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Weekly Customers</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">+<?= $totalCustomers == null ? "0" : $totalCustomers ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Requests Card Example -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Weekly Products</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">+<?= $totalProducts == null ? "0" : $totalProducts ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-gift fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row -->

    <div class="row">

        <!-- Area Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <!-- Card Header - Dropdown -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Sales Overview</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                            <div class="dropdown-header">Dropdown Header:</div>
                            <a class="dropdown-item" href="#">Action</a>
                            <a class="dropdown-item" href="#">Another action</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#">Something else here</a>
                        </div>
                    </div>
                </div>
                <!-- Card Body -->
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="myAreaChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pie Chart -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <!-- Card Header - Dropdown -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Revenue Sources</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                            <div class="dropdown-header">Dropdown Header:</div>
                            <a class="dropdown-item" href="#">Action</a>
                            <a class="dropdown-item" href="#">Another action</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#">Something else here</a>
                        </div>
                    </div>
                </div>
                <!-- Card Body -->
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="myPieChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        <span class="mr-2">
                            <i class="fas fa-circle text-primary"></i> Direct
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-success"></i> Social
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-info"></i> Referral
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<!-- /.container-fluid -->




<?php
require_once('includes/themeFooter.php');
?>