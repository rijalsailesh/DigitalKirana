<?php
require_once '../includes/functions.php';
require_once '../includes/Connection.php';

// check authentication
if (!checkAuth()) {
    header("Location: /?returnUrl=" . $_SERVER['REQUEST_URI']);
}

$productId = getParam('productId', 0);

$connection = ConnectionHelper::getConnection();
$query = "select s.SupplierName, s.Email, s.Phone, s.Address from supplier_products sp inner join supplier s on s.Id = sp.SupplierId inner join product p on p.Id = sp.ProductId where sp.ProductId = :productId and sp.TenantId = :tenantId";
$statement = $connection->prepare($query);
$tenantId = getTenantId();
$statement->bindParam('productId', $productId, PDO::PARAM_INT);
$statement->bindParam('tenantId', $tenantId, PDO::PARAM_INT);
$statement->execute();
$suppliers = $statement->fetchAll(PDO::FETCH_ASSOC);


function getAllProducts()
{
    $connection = ConnectionHelper::getConnection();
    $query = "select * from product where TenantId = :tenantId";
    $statement = $connection->prepare($query);
    $tenantId = getTenantId();
    $statement->bindParam('tenantId', $tenantId, PDO::PARAM_INT);
    $statement->execute();
    $products = $statement->fetchAll(PDO::FETCH_ASSOC);
    return $products;
}

$products = getAllProducts();


require_once '../includes/themeHeader.php';
?>



<div class="container-fluid">
    <a href="/supplier/create.php" class="btn btn-primary"><i class="fas fa-fw fa-arrow-left"></i> Go Back</a>
    <div class="card mt-2 shadow-lg">
        <div class="card-header bg-primary">
            <h4 class="card-title text-light">Product Suppliers</h4>
        </div>
        <div class="card-body">
            <form action="" method="get">
                <div class="row">
                    <div class="col-sm-3">
                        <div>
                            <label for="product">Search Supplier</label>
                            <select name="productId" id="product" class="singleSelect form-control">
                                <option value="" selected>Select Product</option>
                                <?php
                                foreach ($products as $product) :
                                ?>
                                    <option <?= $product['Id'] == $productId ? "selected" : "" ?> value="<?= $product['Id'] ?>"><?= $product['ProductName'] ?></option>
                                <?php
                                endforeach;
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div>
                            <label for="filterBtn">&nbsp;</label>
                            <button class="btn btn-primary btn-sm d-block" id="filterBtn"><i class="fas fa-fw fa-filter"></i> </button>
                        </div>
                    </div>
                </div>
            </form>
            <!-- line -->
            <hr class="sidebar-divider">

            <?php
            if ($suppliers == null) :
            ?>
                <div class="alert alert-warning">
                    <p class="text-center">💀 There are no products for the selected supplier.</p>
                </div>
            <?php
            else :
            ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Supplier Name</th>
                                <th scope="col">Email</th>
                                <th scope="col">Phone</th>
                                <th scope="col">Address</th>
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
                                        <?= $supplier['Email'] ?>
                                    </td>
                                    <td>
                                        <?= $supplier['Phone'] ?>
                                    </td>
                                    <td>
                                        <?= $supplier['Address'] ?>
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

<?php
require_once '../includes/themeFooter.php';
?>