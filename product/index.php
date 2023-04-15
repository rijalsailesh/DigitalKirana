<?php
require_once '../includes/functions.php';
require_once '../includes/Connection.php';

// check authentication
if (!checkAuth()) {
    header("Location: /?returnUrl=" . $_SERVER['REQUEST_URI']);
}

$productName = getParam('productName', '');
$categoryId = getParam('categoryId',);

function getAllProducts($productName, $categoryId)
{
    //get all products by tenant id
    $connection = ConnectionHelper::getConnection();
    $query = "select p.Id, p.ImageUrl, p.ProductCode, p.ProductName, p.SellingPrice, p.CostPrice, p.WholesalePrice, p.Unit, p.Quantity, p.CreatedAt, p.Description, c.CategoryName from product p inner join category c on p.CategoryId = c.Id where ((:productName is null) or (ProductName like concat(:productName, '%')) or (ProductCode like concat(:productName, '%'))) and   ((:categoryId is null) or (CategoryId = :categoryId)) and p.TenantId = :tenantId";
    $statement = $connection->prepare($query);
    $tenantId = getTenantId();
    $statement->bindParam('tenantId', $tenantId, PDO::PARAM_INT);
    $statement->bindParam('productName', $productName, PDO::PARAM_STR);
    $statement->bindParam('categoryId', $categoryId, PDO::PARAM_INT);
    $statement->execute();
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}

function getAllCategories()
{
    //get all categories by tenant id
    $connection = ConnectionHelper::getConnection();
    $query = "select Id, CategoryName from category where TenantId = :tenantId";
    $statement = $connection->prepare($query);
    $tenantId = getTenantId();
    $statement->bindParam('tenantId', $tenantId, PDO::PARAM_INT);
    $statement->execute();
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}

// get all users
$products = getAllProducts($productName, $categoryId);
$categories = getAllCategories();

require_once '../includes/themeHeader.php';
?>

<div class="container-fluid">
    <a href="/product/create.php" class="btn btn-primary"><i class="fas fa-fw fa-plus"></i> New Product</a>
    <div class="card mt-2 shadow-lg">
        <div class="card-header bg-primary">
            <h4 class="card-title text-light">List of Products</h4>
        </div>
        <div class="card-body">

            <form action="">
                <div class="row">
                    <div class="col-3">
                        <label for="ProductName">Product Name / Code</label>
                        <input type="text" class="form-control" value="<?= $productName ?>" name="productName">
                    </div>
                    <div class="col-3">
                        <label for="Category">Category</label>
                        <select name="categoryId" id="Category" class="form-control">
                            <option value="">Select Category</option>
                            <?php
                            foreach ($categories as $category) :
                            ?>
                                <option <?= $categoryId == $category['Id'] ? "selected" : "" ?> value="<?= $category['Id'] ?>"><?= $category['CategoryName'] ?></option>
                            <?php
                            endforeach;
                            ?>
                        </select>
                    </div>
                    <div class="col-3">
                        <label for="">&nbsp;</label>
                        <button class="btn btn-primary d-block"><i class="fas fa-fw fa-search"></i> Search</button>
                    </div>
                </div>
            </form>
            <hr class="sidebar-divider">

            <?php renderMessages(); ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped" style="width:100%">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Image</th>
                            <th scope="col">Name</th>
                            <th scope="col">Product Code</th>
                            <th scope="col">Category</th>
                            <th scope="col">Unit</th>
                            <th scope="col">Selling Price</th>
                            <th scope="col">Cost Price</th>
                            <th scope="col">Wholesale Price</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sn = 0;
                        foreach ($products as $product) :
                        ?>
                            <tr>
                                <td scope="row" class="">
                                    <?= ++$sn ?>
                                </td>
                                <td class="">
                                    <a href="/assets/imgs/products/<?= $product['ImageUrl'] == null ? "default.png" : $product['ImageUrl'] ?>">
                                        <img src="/assets/imgs/products/<?= $product['ImageUrl'] == null ? "default.png" : $product['ImageUrl'] ?>" width="60" height="60" class="rounded-circle border border-5 border-primary shadow-sm" style="object-fit:cover" />
                                    </a>
                                </td>

                                <td>
                                    <?= $product['ProductName'] ?>
                                </td>

                                <td>
                                    <?= $product['ProductCode'] ?>
                                </td>

                                <td>
                                    <?= $product['CategoryName'] ?>
                                </td>
                                <td>
                                    <?= $product['Unit'] ?>
                                </td>
                                <td>
                                    <?= $product['SellingPrice'] ?>
                                </td>
                                <td>
                                    <?= $product['CostPrice'] ?>
                                </td>
                                <td>
                                    <?= $product['WholesalePrice'] ?>
                                </td>
                                <td>
                                    <a href="/product/edit.php?id=<?= $product['Id'] ?>" class="btn btn-sm btn-primary"><i class="fas fa-fw fa-edit"></i> Edit</a>
                                    <form id="deleteForm" method="post" action="/product/delete.php" class="d-inline">
                                        <input type="hidden" name="id" value="<?= $product['Id'] ?>" />
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