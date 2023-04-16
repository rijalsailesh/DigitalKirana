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
$supplierId = getParam('supplierId');
$userId = getParam('userId');

function getAllPurchase($billNumber, $fromDate, $toDate, $supplierId, $userId)
{
    //get all suppliers by tenant id
    $connection = ConnectionHelper::getConnection();
    $query = "select p.Id, s.SupplierName, p.BillNumber, p.Vat, p.Discount, p.GrossTotal, p.NetTotal, p.Remarks, u.FirstName, u.LastName ,p.CreatedAt from purchase p inner join user u on p.UserId  = u.Id inner join supplier s on p.SupplierId = s.Id where ((:search is null) or (p.BillNumber like :search)) and ((:fromDate is null) or (p.CreatedAt >= :fromDate)) and ((:toDate is null) or (p.CreatedAt <= :toDate)) and ((:supplierId is null) or (p.SupplierId = :supplierId)) and ((:userId is null) or (p.UserId = :userId)) and s.TenantId = :tenantId";
    $statement = $connection->prepare($query);
    $tenantId = getTenantId();
    $statement->bindParam('tenantId', $tenantId, PDO::PARAM_INT);
    $statement->bindParam('search', $billNumber, PDO::PARAM_STR);
    $statement->bindParam('fromDate', $fromDate, PDO::PARAM_STR);
    $statement->bindParam('toDate', $toDate, PDO::PARAM_STR);
    $statement->bindParam('supplierId', $supplierId, PDO::PARAM_INT);
    $statement->bindParam('userId', $userId, PDO::PARAM_INT);

    $statement->execute();
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}

function getAllSuppliers()
{
    //get all suppliers by tenant id
    $connection = ConnectionHelper::getConnection();
    $query = "select * from supplier where TenantId = :tenantId";
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

$purchases = getAllPurchase($billNumber, $fromDate, $toDate, $supplierId, $userId);

$suppliers = getAllSuppliers();

$tenantId = getTenantId();

$users = getAllUsersByTenantId($tenantId);


require_once '../includes/themeHeader.php';

?>

<div class="container-fluid">
    <div class="row non-printable">
        <div class="col-6">
            <a href="/purchase/new.php" class="btn btn-primary"><i class="fas fa-fw fa-plus"></i> New Purchase</a>
        </div>
        <div class="col-6">
            <button type="button" class="btn btn-secondary float-right" id="printBtn"><i class="fas fa-fw fa-print"></i> Print</button>
        </div>
    </div>
    <div class="card mt-2 shadow-lg">
        <div class="card-header bg-primary">
            <h4 class="card-title text-light">List of Purchase</h4>
        </div>
        <div class="card-body">
            <form action="" method="get">
                <div class="row mb-3 non-printable">
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
                        <label for="supplier">Supplier</label>
                        <select name="supplierId" id="supplier" class="singleSelect form-control">
                            <option value="">Select Supplier</option>
                            <?php
                            foreach ($suppliers as $supplier) :
                            ?>
                                <option <?= $supplierId == $supplier['Id'] ? "selected" : "" ?> value="<?= $supplier['Id'] ?>">
                                    <?= $supplier['SupplierName'] ?>
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
            <hr class="sidebar-divider non-printable">
            <?php renderMessages(); ?>
            <?php
            if (count($purchases) > 0) :
            ?>
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
                                <th scope="col" class="non-printable">Action</th>
                            </tr>
                        </thead>
                        <tbody id="tbody">
                            <?php
                            $sn = 0;
                            foreach ($purchases as $purchase) :
                            ?>
                                <tr>
                                    <td scope="row">
                                        <?= ++$sn ?>
                                    </td>
                                    <td>
                                        <?= $purchase['BillNumber'] ?>
                                    </td>
                                    <td>
                                        <?= $purchase['SupplierName'] ?>
                                    </td>
                                    <td>
                                        <?= $purchase['Vat'] ?>
                                    </td>
                                    <td>
                                        <?= $purchase['Discount'] ?>
                                    </td>
                                    <td>
                                        <?= $purchase['GrossTotal'] ?>
                                    </td>
                                    <td class="netTotal">
                                        <?= $purchase['NetTotal'] ?>
                                    </td>
                                    <td>
                                        <?= $purchase['Remarks'] ?>
                                    </td>
                                    <td>
                                        <?= $purchase['FirstName'] . " " . $purchase['LastName'] ?>
                                    </td>
                                    <td>
                                        <?= $purchase['CreatedAt'] ?>
                                    </td>
                                    <td class="non-printable">
                                        <a href="/purchase/details.php?id=<?= $purchase['Id'] ?>" class="btn btn-sm btn-info"><i class="fas fa-fw fa-info"></i> Details</a>
                                    </td>
                                </tr>
                            <?php
                            endforeach;
                            ?>
                        </tbody>
                    </table>
                </div>
            <?php
            else :
            ?>
                <div class="alert alert-warning">
                    <p class="text-center">💀 There are no purchase in selected date.</p>
                </div>
            <?php
            endif;
            ?>
        </div>
    </div>
</div>


<script>
    //finding total netTotal amount of purchase
    let netTotal = document.querySelectorAll('.netTotal');
    let totalNetTotal = 0;
    netTotal.forEach((item) => {
        totalNetTotal += parseFloat(item.innerHTML);
    });
    let tr = document.querySelector('#tbody').appendChild(document.createElement('tr'));
    tr.classList.add('bg-primary', 'text-light');
    tr.innerHTML = `<td colspan="6" class="text-left">Total</td><td id="totalNetTotal" class="text-right">${totalNetTotal.toFixed(2)}</td><td colspan="4"></td>`;




    //print printable area on click
    const printBtn = document.getElementById('printBtn');
    printBtn.addEventListener('click', () => {
        printSection();
    });

    const printSection = () => {
        window.print();
    }
</script>

<?php
require_once '../includes/themeFooter.php';
?>