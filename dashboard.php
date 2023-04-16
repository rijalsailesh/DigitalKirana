<?php
require_once 'includes/functions.php';
require_once 'includes/Connection.php';
require_once 'includes/authorize.php';


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

function getMinimumStock()
{
    $connection = ConnectionHelper::getConnection();
    $query = "select ProductCode, ProductName, Quantity from product where Quantity <= MinimumQuantity and TenantId = :tenantId";
    $statement = $connection->prepare($query);
    $tenantId = getTenantId();
    $statement->bindParam('tenantId', $tenantId);
    $statement->execute();
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}


$todaySales = getTodaysTotalSales($today);
$todayPurchase = getTodaysTotalPurchase($today);
$totalCustomers = getCustomersForLastWeek();
$totalProducts = getProductsForLastWeek();
$minimumStock = getMinimumStock();

require_once('includes/themeHeader.php');
?>
<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
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
                    <h6 class="m-0 font-weight-bold text-primary">Minimum Stock</h6>
                </div>
                <!-- Card Body -->
                <div class="card-body">
                    <div>
                        <?php
                        $sn = 0;
                        foreach ($minimumStock as $stock) :
                        ?>
                            <span class="bg-danger rounded mb-3 p-2 d-block text-white text-bold">
                                <?= ++$sn ?>. <?= $stock['ProductCode'] ?> - <?= $stock['ProductName'] ?> (<?= $stock['Quantity'] ?>)
                            </span>
                        <?php
                        endforeach;
                        ?>
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