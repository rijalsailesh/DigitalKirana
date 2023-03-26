<?php
require_once '../includes/functions.php';
require_once '../includes/Connection.php';

// check authentication
if (!checkAuth()) {
    header("Location: /?returnUrl=" . $_SERVER['REQUEST_URI']);
}

if (isPost()) {
    session_destroy();
    header("Location: /");
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

// get all users
$categories = getAllCategories();


require_once '../includes/themeHeader.php';
?>

<div class="container-fluid">
    <a href="/category/create.php" class="btn btn-primary"><i class="fas fa-fw fa-plus"></i> New Category</a>
    <div class="card mt-2">
        <div class="card-header bg-primary">
            <h4 class="card-title text-light">List of Categories</h4>
        </div>
        <div class="card-body">
            <?php renderMessages(); ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Name</th>
                            <th scope="col">Description</th>
                            <th scope="col">Created At</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sn = 0;
                        foreach ($categories as $category) :
                        ?>
                            <tr>
                                <td scope="row"><?= ++$sn ?></td>
                                <td><?= $category['CategoryName'] ?></td>
                                <td><?= $category['Description'] ?></td>
                                <td><?= $category['CreatedAt'] ?></td>
                                <td>
                                    <a href="#" class="btn btn-sm btn-primary"><i class="fas fa-fw fa-edit"></i> Edit</a>
                                    <a href="#" class="btn btn-sm btn-danger"><i class="fas fa-fw fa-trash"></i> Delete</a>
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