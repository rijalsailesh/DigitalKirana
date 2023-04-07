<?php
require_once '../includes/functions.php';
require_once '../includes/Connection.php';

// check authentication
if (!checkAuth()) {
    header("Location: /?returnUrl=" . $_SERVER['REQUEST_URI']);
}

$billNumber = getParam('billNumber');
$fromDate = getParam('fromDate', date('Y-m-d'));
$toDate = getParam('toDate', date('Y-m-d'));
$customerId = getParam('customerId');
$userId = getParam('userId');




function getAllSales($billNumber, $fromDate, $toDate, $customerId, $userId)
{
    //get all suppliers by tenant id
    $connection = ConnectionHelper::getConnection();

    $query = "select s.Id, c.CustomerName, s.BillNumber, s.Vat, s.Discount, s.GrossTotal, s.NetTotal, s.Remarks, u.FirstName, u.LastName, s.CreatedAt from sales s inner join user u on s.UserId = u.Id inner join customer c on s.CustomerId = c.Id where ((:search is null) or (s.BillNumber like :search)) and ((:fromDate is null) or (s.CreatedAt >= :fromDate)) and ((:toDate is null) or (s.CreatedAt <= :toDate)) and ((:customerId is null) or (s.CustomerId = :customerId)) and ((:userId is null) or (s.UserId = :userId)) and c.TenantId = :tenantId";

    $statement = $connection->prepare($query);
    $tenantId = getTenantId();
    $statement->bindParam('tenantId', $tenantId, PDO::PARAM_INT);
    $statement->bindParam('search', $billNumber, PDO::PARAM_STR);
    $statement->bindParam('fromDate', $fromDate, PDO::PARAM_STR);
    $statement->bindParam('toDate', $toDate, PDO::PARAM_STR);
    $statement->bindParam('customerId', $customerId, PDO::PARAM_INT);
    $statement->bindParam('userId', $userId, PDO::PARAM_INT);

    $statement->execute();
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}

function getAllCustomers()
{
    //get all suppliers by tenant id
    $connection = ConnectionHelper::getConnection();
    $query = "select * from customer where TenantId = :tenantId";
    $statement = $connection->prepare($query);
    $tenantId = getTenantId();
    $statement->bindParam('tenantId', $tenantId, PDO::PARAM_INT);
    $statement->execute();
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}


function getAllUsersByTenantId($id)
{
    //get all users by tenant id
    $connection = ConnectionHelper::getConnection();
    $query = "select * from user where TenantId = :tenantId";
    $statement = $connection->prepare($query);
    $statement->bindParam('tenantId', $id, PDO::PARAM_INT);
    $statement->execute();
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}
$tenantId = getTenantId();



$sales = getAllSales($billNumber, $fromDate, $toDate, $customerId, $userId);

$customers = getAllCustomers();


$users = getAllUsersByTenantId($tenantId);


require_once '../includes/themeHeader.php';

?>

<div class="container-fluid">
    <a href="/sales/new.php" class="btn btn-primary"><i class="fas fa-fw fa-plus"></i> New Sale</a>
    <div class="card mt-2 shadow-lg">
        <div class="card-header bg-primary">
            <h4 class="card-title text-light">List of Sales</h4>
        </div>
        <div class="card-body">
            <form action="" method="get">
                <div class="row mb-3">
                    <div class="col-2">
                        <label for="billNumber">Bill Number</label>
                        <input type="text" class="form-control" value="<?= $billNumber ?>" id="billNumber" name="billNumber">
                    </div>
                    <div class="col-2">
                        <label for="fromDate">From</label>
                        <input type="date" class="form-control" value="<?= $fromDate ?>" id="fromDate" name="fromDate">
                    </div>
                    <div class="col-2">
                        <label for="toDate">To</label>
                        <input type="date" class="form-control" value="<?= $toDate ?>" id="toDate" name="toDate">
                    </div>
                    <div class="col-2">
                        <label for="customer">Supplier</label>
                        <select name="customerId" id="customer" class="singleSelect form-control">
                            <option value="">Select Customer</option>
                            <?php
                            foreach ($customers as $customer) :
                            ?>
                                <option <?= $customerId == $customer['Id'] ? "selected" : "" ?> value="<?= $customer['Id'] ?>">
                                    <?= $customer['CustomerName'] ?>
                                </option>
                            <?php
                            endforeach;
                            ?>
                        </select>
                    </div>
                    <div class="col-2">
                        <label for="userId">Supplier</label>
                        <select name="userId" id="user" class="singleSelect form-control">
                            <option value="">Select User</option>
                            <?php
                            foreach ($users as $user) :
                            ?>
                                <option <?= $userId == $user['Id'] ? "selected" : "" ?> value="<?= $user['Id'] ?>">
                                    <?= $user['FirstName'] . " " . $user['LastName'] ?>
                                </option>
                            <?php
                            endforeach;
                            ?>
                        </select>
                    </div>
                    <div class="col-2">
                        <label for="search">&nbsp;</label>
                        <button type="submit" class="btn btn-primary form-control"><i class="fas fa-fw fa-search"></i> Search</button>
                    </div>
                </div>
            </form>
            <!-- line -->
            <hr class="sidebar-divider">
            <?php renderMessages(); ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Bill Number</th>
                            <th scope="col">Supplier Name</th>
                            <th scope="col">VAT</th>
                            <th scope="col">Discount</th>
                            <th scope="col">Gross Total</th>
                            <th scope="col">Net Total</th>
                            <th scope="col">Remarks</th>
                            <th scope="col">Added By</th>
                            <th scope="col">Created At</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sn = 0;
                        foreach ($sales as $sale) :
                        ?>
                            <tr>
                                <td scope="row">
                                    <?= ++$sn ?>
                                </td>
                                <td>
                                    <?= $sale['BillNumber'] ?>
                                </td>
                                <td>
                                    <?= $sale['CustomerName'] ?>
                                </td>
                                <td>
                                    <?= $sale['Vat'] ?>
                                </td>
                                <td>
                                    <?= $sale['Discount'] ?>
                                </td>
                                <td>
                                    <?= $sale['GrossTotal'] ?>
                                </td>
                                <td>
                                    <?= $sale['NetTotal'] ?>
                                </td>
                                <td>
                                    <?= $sale['Remarks'] ?>
                                </td>
                                <td>
                                    <?= $sale['FirstName'] . " " . $sale['LastName'] ?>
                                </td>
                                <td>
                                    <?= $sale['CreatedAt'] ?>
                                </td>
                                <td>
                                    <a href="/sales/details.php?id=<?= $sale['Id'] ?>" class="btn btn-sm btn-info"><i class="fas fa-fw fa-info"></i> Details</a>
                                </td>
                            </tr>
                        <?php
                        endforeach;
                        ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

<?php
require_once '../includes/themeFooter.php';
?>