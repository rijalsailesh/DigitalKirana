<?php
require_once '../includes/functions.php';
require_once '../includes/Connection.php';

// check authentication
if (!checkAuth()) {
    header("Location: /?returnUrl=" . $_SERVER['REQUEST_URI']);
}

$productId = getParam('productId', 0);
$categoryId = getParam('categoryId', 0);

$connection = ConnectionHelper::getConnection();
//get by categoryId and productId
$query = "select p.ProductCode, p.ProductName, p.Quantity, p.Description, c.CategoryName from product p inner join category c on p.CategoryId = c.Id where p.CategoryId = :categoryId or p.Id = :productId and p.TenantId = :tenantId";
$statement = $connection->prepare($query);
$tenantId = getTenantId();
$statement->bindParam('productId', $productId, PDO::PARAM_INT);
$statement->bindParam('categoryId', $categoryId, PDO::PARAM_INT);
$statement->bindParam('tenantId', $tenantId, PDO::PARAM_INT);
$statement->execute();
$result = $statement->fetchAll(PDO::FETCH_ASSOC);

// dd($result);

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

function getAllCategories()
{
    $connection = ConnectionHelper::getConnection();
    $query = "select * from category where TenantId = :tenantId";
    $statement = $connection->prepare($query);
    $tenantId = getTenantId();
    $statement->bindParam('tenantId', $tenantId, PDO::PARAM_INT);
    $statement->execute();
    $categories = $statement->fetchAll(PDO::FETCH_ASSOC);
    return $categories;
}

$categories = getAllCategories();
$products = getAllProducts();


require_once '../includes/themeHeader.php';
?>



<div class="container-fluid">
    <a href="/supplier/create.php" class="btn btn-primary"><i class="fas fa-fw fa-arrow-left"></i> Go Back</a>
    <div class="card mt-2 shadow-lg">
        <div class="card-header bg-primary">
            <h4 class="card-title text-light">Stock</h4>
        </div>
        <div class="card-body">
            <form action="" method="get">
                <div class="row">
                    <div class="col-sm-3">
                        <div>
                            <label for="product">Product</label>
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
                    <div class="col-sm-3">
                        <div>
                            <label for="category">Category</label>
                            <select name="categoryId" id="category" class="singleSelect form-control">
                                <option value="" selected>Select Category</option>
                                <?php
                                foreach ($categories as $category) :
                                ?>
                                    <option <?= $category['Id'] == $categoryId ? "selected" : "" ?> value="<?= $category['Id'] ?>"><?= $category['CategoryName'] ?></option>
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
            if ($result == null) :
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
                                <th scope="col">Code</th>
                                <th scope="col">Product Name</th>
                                <th scope="col">Category</th>
                                <th scope="col">Description</th>
                                <th scope="col">Quantity</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 1;
                            foreach ($result as $row) :
                            ?>
                                <tr>
                                    <th scope="row"><?= $i++ ?></th>
                                    <td><?= $row['ProductCode'] ?></td>
                                    <td><?= $row['ProductName'] ?></td>
                                    <td><?= $row['CategoryName'] ?></td>
                                    <td><?= $row['Description'] ?></td>
                                    <td><?= $row['Quantity'] ?></td>
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