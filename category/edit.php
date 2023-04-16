<?php
require_once '../includes/functions.php';
require_once '../includes/Connection.php';
require_once '../constants/Role.php';
require_once '../includes/authorize_user.php';


$tenantId = getTenantId(); //getting tenant id from session
$categoryId = getParam('id'); //getting categoryId from url


function getCategoryById($categoryId)
{
    $connection = ConnectionHelper::getConnection();
    $query = "select * from category where Id = :id";
    $statement = $connection->prepare($query);
    $statement->bindParam('id', $categoryId, PDO::PARAM_INT);
    $statement->execute();
    $result = $statement->fetch(PDO::FETCH_ASSOC);
    return $result;
}

//get category by id
$category = getCategoryById($categoryId);

//checking category tenant id and session tenant id
if ($category['TenantId'] != $tenantId) {
    header("Location: /error/accessDenied.php");
}


// check if form is submitted
if (isPost()) {
    // get form data
    $categoryName = $_POST['categoryName'];
    $description = $_POST['description'];

    // create category
    $connection = ConnectionHelper::getConnection();
    $query = "update category set CategoryName = :categoryName, Description = :description where Id = :id";
    $statement = $connection->prepare($query);
    $statement->bindParam('id', $categoryId);
    $statement->bindParam('categoryName', $categoryName);
    $statement->bindParam('description', $description);
    $statement->execute();
    $result = $statement->rowCount();
    if ($result > 0) {
        AddSuccessMessage("Category updated successfully");
        header("Location: /category");
    } else {
        AddErrorMessage("Failed to update category");
    }
}

require_once '../includes/themeHeader.php';
?>

<div class="container-fluid">
    <form action="" method="post">

        <a href="/category" class="btn btn-danger"><i class="fas fa-fw fa-arrow-left"></i> Back to Categories</a>
        <div class="card mt-2  shadow-lg">
            <div class="card-header bg-primary">
                <h4 class="card-title text-light">Create Category</h4>
            </div>
            <div class="card-body bg-gray">
                <?php renderMessages(); ?>
                <div class="row">
                    <div class="col-12 mb-4">
                        <label for="categoryName">Category Name</label>
                        <input type="text" value="<?= $category['CategoryName'] ?>" name="categoryName" id="categoryName" class="form-control" placeholder="Category Name" required>
                    </div>
                    <div class="col-12 mb-4">
                        <label for="description">Description</label>
                        <textarea type="text" name="description" id="description" class="form-control" placeholder="Description" rows="8"><?= $category['Description'] ?></textarea>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary"><i class="fas fa-fw fa-save"></i> Save</button>
            </div>
        </div>
    </form>
</div>

<?php
require_once '../includes/themeFooter.php';
?>