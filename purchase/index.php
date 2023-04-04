<?php
require_once '../includes/functions.php';
require_once '../includes/Connection.php';

// check authentication
if (!checkAuth()) {
    header("Location: /?returnUrl=" . $_SERVER['REQUEST_URI']);
}

function getAllPurchase()
{
    //get all suppliers by tenant id
    $connection = ConnectionHelper::getConnection();
    $query = "select p.Id, s.SupplierName, p.Vat, p.Discount, p.GrossTotal, p.NetTotal, p.Remarks, u.FirstName, u.LastName ,p.CreatedAt from purchase p inner join user u on p.UserId  = u.Id inner join supplier s on p.SupplierId = s.Id where s.TenantId = :tenantId";
    $statement = $connection->prepare($query);
    $tenantId = getTenantId();
    $statement->bindParam('tenantId', $tenantId, PDO::PARAM_INT);
    $statement->execute();
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}

// get all suppliers
$suppliers = getAllPurchase();

require_once '../includes/themeHeader.php';
?>

<div class="container-fluid">
    <a href="/purchase/new.php" class="btn btn-primary"><i class="fas fa-fw fa-plus"></i> New Purchase</a>
    <div class="card mt-2 shadow-lg">
        <div class="card-header bg-primary">
            <h4 class="card-title text-light">List of Purchase</h4>
        </div>
        <div class="card-body">
            <?php renderMessages(); ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
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
                        foreach ($suppliers as $supplier) :
                        ?>
                            <tr>
                                <td scope="row">
                                    <?= ++$sn ?>
                                </td>
                                <td>
                                    <?= $supplier['SupplierName'] ?>
                                </td>
                                <td>
                                    <?= $supplier['Vat'] ?>
                                </td>
                                <td>
                                    <?= $supplier['Discount'] ?>
                                </td>
                                <td>
                                    <?= $supplier['GrossTotal'] ?>
                                </td>
                                <td>
                                    <?= $supplier['NetTotal'] ?>
                                </td>
                                <td>
                                    <?= $supplier['Remarks'] ?>
                                </td>
                                <td>
                                    <?= $supplier['FirstName'] . " " . $supplier['LastName'] ?>
                                </td>
                                <td>
                                    <?= $supplier['CreatedAt'] ?>
                                </td>
                                <td>
                                    <a href="/supplier/edit.php?id=<?= $supplier['Id'] ?>" class="btn btn-sm btn-primary"><i class="fas fa-fw fa-edit"></i> Edit</a>
                                    <form id="deleteForm" method="post" action="/supplier/delete.php" class="d-inline">
                                        <input type="hidden" name="id" value="<?= $supplier['Id'] ?>" />
                                        <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-fw fa-trash"></i> Delete</button>
                                    </form>
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