<?php
require_once '../includes/functions.php';
require_once '../includes/Connection.php';

// check authentication
if (!checkAuth()) {
    header("Location: /?returnUrl=" . $_SERVER['REQUEST_URI']);
}

$supplierId = getParam('supplierId', 0);

//get all suppliers by tenant id
$connection = ConnectionHelper::getConnection();
$query = "select p.ProductCode, p.ProductName, c.CategoryName, p.Unit from supplier_products sp inner join product p on p.Id = sp.ProductId inner join Category c on p.CategoryId = c.Id where sp.SupplierId = :supplierId and sp.TenantId = :tenantId";
$statement = $connection->prepare($query);
$tenantId = getTenantId();
$statement->bindParam('supplierId', $supplierId, PDO::PARAM_INT);
$statement->bindParam('tenantId', $tenantId, PDO::PARAM_INT);
$statement->execute();
$products = $statement->fetchAll(PDO::FETCH_ASSOC);


function getAllSuppliers()
{
    $connection = ConnectionHelper::getConnection();
    $query = "select * from supplier where TenantId = :tenantId";
    $statement = $connection->prepare($query);
    $tenantId = getTenantId();
    $statement->bindParam('tenantId', $tenantId, PDO::PARAM_INT);
    $statement->execute();
    $suppliers = $statement->fetchAll(PDO::FETCH_ASSOC);
    return $suppliers;
}

$suppliers = getAllSuppliers();

require_once '../includes/themeHeader.php';
?>

<div class="container-fluid">
    <div class="row non-printable">
        <div class="col-6">
            <a href="/supplier/create.php" class="btn btn-primary"><i class="fas fa-fw fa-arrow-left"></i> Go Back</a>
        </div>
        <div class="col-6">
            <button type="button" class="btn btn-secondary float-right" id="printBtn"><i class="fas fa-fw fa-print"></i> Print</button>
        </div>
    </div>
    <div class="card mt-2 shadow-lg">
        <div class="card-header bg-primary">
            <h4 class="card-title text-light">Supplier Products</h4>
        </div>
        <div class="card-body">
            <form action="" method="get">
                <div class="row non-printable">
                    <div class="col-sm-4">
                        <div>
                            <label for="supplier">Search Supplier</label>
                            <select name="supplierId" id="supplier" class="singleSelect form-control">
                                <option value="" selected>Select Supplier</option>
                                <?php
                                foreach ($suppliers as $supplier) :
                                ?>
                                    <option <?= $supplier['Id'] == $supplierId ? "selected" : "" ?> value="<?= $supplier['Id'] ?>"><?= $supplier['SupplierName'] ?></option>
                                <?php
                                endforeach;
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div>
                            <label for="filterBtn">&nbsp;</label>
                            <button class="btn btn-sm btn-primary d-block" id="filterBtn"><i class="fas fa-fw fa-filter"></i> </button>
                        </div>
                    </div>
                </div>
            </form>
            <!-- line -->
            <hr class="sidebar-divider non-printable">

            <?php
            if ($products == null) :
            ?>
                <div class="alert alert-warning">
                    <p class="text-center">💀 There are no suppliers for the selected product.</p>
                </div>
            <?php
            else :
            ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Product Code</th>
                                <th scope="col">Product Name</th>
                                <th scope="col">Unit</th>
                                <th scope="col">Category</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sn = 0;
                            foreach ($products as $product) :
                            ?>
                                <tr>
                                    <td scope="row">
                                        <?= ++$sn ?>
                                    </td>
                                    <td>
                                        <?= $product['ProductCode'] ?>
                                    </td>
                                    <td>
                                        <?= $product['ProductName'] ?>
                                    </td>
                                    <td>
                                        <?= $product['Unit'] ?>
                                    </td>
                                    <td>
                                        <?= $product['CategoryName'] ?>
                                    </td>
                                </tr>
                            <?php
                            endforeach;
                            ?>
                        </tbody>
                    </table>
                </div>

        </div>

    <?php
            endif;
    ?>

    </div>
</div>

<script>
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