<?php
require_once '../includes/functions.php';
require_once '../includes/Connection.php';
require_once '../constants/Role.php';

// check authentication
if (!checkAuth()) {
    header("Location: /?returnUrl=" . $_SERVER['REQUEST_URI']);
}

$tenantId = getTenantId();

//get product by id
function getProductById($id)
{
    $connection = ConnectionHelper::getConnection();
    $query = "select * from product where Id = :id";
    $statement = $connection->prepare($query);
    $statement->bindParam('id', $id, PDO::PARAM_INT);
    $statement->execute();
    $result = $statement->fetch(PDO::FETCH_ASSOC);
    return $result;
}

// get product by id
$productId = getParam('id');
$product = getProductById($productId);

//check tenant id and current user tenant id
if ($product['TenantId'] != $tenantId) {
    header("Location: /error/accessDenied.php");
    exit();
}


function getAllCategories()
{
    //get all users by tenant id
    $connection = ConnectionHelper::getConnection();
    $query = "select * from category where TenantId = :tenantId";
    $statement = $connection->prepare($query);
    $tenantId = getTenantId();
    $statement->bindParam('tenantId', $tenantId, PDO::PARAM_INT);
    $statement->execute();
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}

// get all categories
$categories = getAllCategories();


//check duplicate product code by tenant id
function checkDuplicateProductCode($productCode)
{
    $connection = ConnectionHelper::getConnection();
    $query = "select * from product where ProductCode = :productCode and TenantId = :tenantId";
    $statement = $connection->prepare($query);
    $tenantId = getTenantId();
    $statement->bindParam('productCode', $productCode, PDO::PARAM_STR);
    $statement->bindParam('tenantId', $tenantId, PDO::PARAM_INT);
    $statement->execute();
    $result = $statement->fetch(PDO::FETCH_ASSOC);
    return $result;
}

// check if form is submitted
if (isPost()) {
    // get form data
    $productName = $_POST['productName'];
    $description = $_POST['description'];
    $productCode = $_POST['productCode'];
    $categoryId = $_POST['categoryId'];
    $costPrice = $_POST['costPrice'];
    $sellingPrice = $_POST['sellingPrice'];
    $wholesalePrice = $_POST['wholesalePrice'];
    $unit = $_POST['unit'];
    $quantity = $_POST['quantity'];
    $maximumQuantity = $_POST['maximumQuantity'];
    $minimumQuantity = $_POST['minimumQuantity'];

    // check duplicate product code
    $duplicateProductCode = checkDuplicateProductCode($productCode);

    if (count($duplicateProductCode) > 0 && $duplicateProductCode['Id'] != $productId) {
        AddErrorMessage("Product code already exists");
        header("Location: /product/edit.php?id=" . $productId);
        exit();
    }

    // upload image
    //get logo from input
    $image = $_FILES['productImage'];

    $imageName = "";
    if ($image['size'] > 0) {
        $name = date('Y-m-d-H-i-s');
        $ext = ".png";
        $imageName = $name . $ext;
        saveProductImage($image['tmp_name'], $imageName);
    }


    // create user
    $connection = ConnectionHelper::getConnection();

    //if image is not uploaded
    if ($imageName == "") {
        $query = "update product set ProductName = :productName, ProductCode = :productCode, Description = :description, CategoryId = :categoryId, CostPrice = :costPrice, SellingPrice = :sellingPrice, WholesalePrice = :wholesalePrice, Unit = :unit, Quantity = :quantity, MaximumQuantity = :maximumQuantity, MinimumQuantity = :minimumQuantity where Id = :id";
    } else {
        $query = "update product set ProductName = :productName, ProductCode = :productCode, Description = :description, CategoryId = :categoryId, CostPrice = :costPrice, SellingPrice = :sellingPrice, WholesalePrice = :wholesalePrice, Unit = :unit, Quantity = :quantity, ImageUrl = :imageUrl, MaximumQuantity = :maximumQuantity, MinimumQuantity = :minimumQuantity where Id = :id";
    }
    $statement = $connection->prepare($query);

    $statement->bindParam('productName', $productName, PDO::PARAM_STR);
    $statement->bindParam('productCode', $productCode, PDO::PARAM_STR);
    $statement->bindParam('description', $description, PDO::PARAM_STR);
    $statement->bindParam('categoryId', $categoryId, PDO::PARAM_INT);
    $statement->bindParam('costPrice', $costPrice, PDO::PARAM_STR);
    $statement->bindParam('sellingPrice', $sellingPrice, PDO::PARAM_STR);
    $statement->bindParam('wholesalePrice', $wholesalePrice, PDO::PARAM_STR);
    $statement->bindParam('unit', $unit, PDO::PARAM_STR);
    $statement->bindParam('quantity', $quantity, PDO::PARAM_INT);
    $statement->bindParam('id', $productId, PDO::PARAM_INT);
    $statement->bindParam('maximumQuantity', $maximumQuantity, PDO::PARAM_INT);
    $statement->bindParam('minimumQuantity', $minimumQuantity, PDO::PARAM_INT);

    if ($imageName != "") {
        $statement->bindParam('imageUrl', $imageName, PDO::PARAM_STR);
    }

    $statement->execute();
    $result = $statement->rowCount();
    if ($result > 0) {
        AddSuccessMessage("Product updated successfully");
        header("Location: /product");
    } else {
        AddErrorMessage("Failed to update product");
    }
}

require_once '../includes/themeHeader.php';
?>

<div class="container-fluid">
    <form action="" method="post" enctype="multipart/form-data">

        <a href="/product" class="btn btn-danger"><i class="fas fa-fw fa-arrow-left"></i> Back to Products</a>
        <div class="card mt-2  shadow-lg">
            <div class="card-header bg-primary">
                <h4 class="card-title text-light">Create Product</h4>
            </div>
            <div class="card-body bg-gray">
                <?php renderMessages(); ?>
                <div class="row">
                    <div class="col-9">
                        <div class="col-12 mb-4">
                            <label for="productName">Product Name</label>
                            <input type="text" name="productName" id="productName" class="form-control"
                                placeholder="Product Name" value="<?= $product['ProductName'] ?>" required>
                        </div>

                        <div class="col-12 mb-4">
                            <label for="productCode">Product Code</label>
                            <input type="text" name="productCode" id="productCode" class="form-control"
                                placeholder="Product Code" value="<?= $product['ProductCode'] ?>" required>
                        </div>

                        <div class="col-12 mb-4">
                            <label for="description">Description</label>
                            <textarea type="text" name="description" id="description" class="form-control"
                                placeholder="Description" rows="6"> <?= $product['Description'] ?> </textarea>
                        </div>

                        <div class="col-12 mb-4">
                            <label for="categoryId">Category</label>
                            <select type="text" name="categoryId" id="category" class="form-control" required>
                                <option value="">Select Category</option>

                                <?php
                                foreach ($categories as $category):
                                    $selected = $category['Id'] == $product['CategoryId'] ? 'selected' : '';
                                    ?>
                                    <option value="<?= $category['Id'] ?>" <?= $selected ?>><?= $category['CategoryName'] ?>
                                    </option>
                                    <?php
                                endforeach;
                                ?>
                            </select>
                        </div>

                        <div class="col-12 mb-4">
                            <label for="unit">Unit</label>
                            <input type="text" name="unit" id="unit" class="form-control" placeholder="Unit"
                                value="<?= $product['Unit'] ?>" required>
                        </div>

                        <div class="col-12 mb-4">
                            <label for="sellingPrice">Selling Price</label>
                            <input type="number" min="0" name="sellingPrice" id="sellingPrice" class="form-control"
                                placeholder="Product Code" value="<?= $product['SellingPrice'] ?>" required>
                        </div>

                        <div class="col-12 mb-4">
                            <label for="costPrice">Cost Price</label>
                            <input type="number" min="0" name="costPrice" id="costPrice" class="form-control"
                                placeholder="Cost Price" value="<?= $product['CostPrice'] ?>" required>
                        </div>

                        <div class="col-12 mb-4">
                            <label for="wholesalePrice">Wholesale Price</label>
                            <input type="number" min="0" name="wholesalePrice" id="wholesalePrice" class="form-control"
                                placeholder="Wholesale Price" value="<?= $product['WholesalePrice'] ?>" required>
                        </div>

                        <div class="col-12 mb-4">
                            <label for="quantity">Quantity</label>
                            <input type="number" min="0" name="quantity" id="quantity"
                                value="<?= $product['Quantity'] ?>" class="form-control" placeholder="Quantity"
                                required>
                        </div>


                        <div class="col-12 mb-4">
                            <label for="minimumQuantity">Minimum Quantity</label>
                            <input type="number" min="0" name="minimumQuantity"
                                value="<?= $product['MinimumQuantity'] ?>" id="minimumQuantity" class="form-control"
                                placeholder="Minimum Quantity" required>
                        </div>

                        <div class="col-12 mb-4">
                            <label for="maximumQuantity">Maximum Quantity</label>
                            <input type="number" min="0" name="maximumQuantity"
                                value="<?= $product['MaximumQuantity'] ?>" id="maximumQuantity" class="form-control"
                                placeholder="Maximum Quantity" required>
                        </div>

                        <div class="col-12 mb-4">
                            <label for="productImage">Upload Image</label>
                            <input type="file" name="productImage" id="productImage" class="form-control-file"
                                accept="image/*" onchange="showPreview(event);">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <img src="/assets/imgs/products/<?= $product['ImageUrl'] == null ? "default.png" : $product['ImageUrl'] ?>"
                            class="img-fluid" id="imagePreview" />
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary"><i class="fas fa-fw fa-save"></i> Save</button>
            </div>
        </div>
    </form>
</div>

<script>
    function showPreview(event) {
        if (event.target.files.length > 0) {
            var src = URL.createObjectURL(event.target.files[0]);
            var preview = document.getElementById("imagePreview");
            preview.src = src;
        }
    }
</script>

<?php
require_once '../includes/themeFooter.php';
?>