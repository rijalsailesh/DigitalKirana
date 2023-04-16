<?php
require_once '../includes/functions.php';
require_once '../includes/Connection.php';
require_once '../includes/authorize_user.php';

$search = getParam('search');

function getAllCategories($search)
{
    //get all users by tenant id
    $connection = ConnectionHelper::getConnection();
    $query = "select c.Id, c.CategoryName, c.CreatedAt, c.Description, u.FirstName, u.LastName from category c inner join user u on c.UserId = u.Id where ((:search is null) or (c.CategoryName like concat(:search, '%')) or (c.Description like concat(:search, '%'))) and c.TenantId = :tenantId";
    $statement = $connection->prepare($query);
    $tenantId = getTenantId();
    $statement->bindParam('tenantId', $tenantId, PDO::PARAM_INT);
    $statement->bindParam('search', $search, PDO::PARAM_STR);
    $statement->execute();
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}

// get all categories
$categories = getAllCategories($search);

require_once '../includes/themeHeader.php';
?>

<div class="container-fluid">
    <div class="row non-printable">
        <div class="col-6">
            <a href="/category/create.php" class="btn btn-primary"><i class="fas fa-fw fa-plus"></i> New Category</a>
        </div>
        <div class="col-6">
            <button type="button" class="btn btn-secondary float-right" id="printBtn"><i class="fas fa-fw fa-print"></i> Print</button>
        </div>
    </div>
    <div class="card mt-2 shadow-lg">
        <div class="card-header bg-primary">
            <h4 class="card-title text-light">List of Categories</h4>
        </div>
        <div class="card-body">

            <div class="row non-printable">
                <div class="col-lg-4 col-md-6 col-sm-12">
                    <form method="get" action="">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Search by name or description" value="<?= $search ?>" />
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-fw fa-search"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!-- line -->
            <hr class="sidebar-divider non-printable" />

            <?php renderMessages(); ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Name</th>
                            <th scope="col">Description</th>
                            <th scope="col">Added By</th>
                            <th scope="col">Created At</th>
                            <th scope="col" class="non-printable">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sn = 0;
                        foreach ($categories as $category) :
                        ?>
                            <tr>
                                <td scope="row">
                                    <?= ++$sn ?>
                                </td>
                                <td>
                                    <?= $category['CategoryName'] ?>
                                </td>
                                <td>
                                    <?= $category['Description'] ?>
                                </td>
                                <td>
                                    <?= $category['FirstName'] . " " . $category['LastName'] ?>
                                </td>
                                <td>
                                    <?= $category['CreatedAt'] ?>
                                </td>
                                <td class="non-printable">
                                    <a href="/category/edit.php?id=<?= $category['Id'] ?>" class="btn btn-sm btn-primary"><i class="fas fa-fw fa-edit"></i> Edit</a>
                                    <form id="deleteForm" method="post" action="/category/delete.php" class="d-inline">
                                        <input type="hidden" name="id" value="<?= $category['Id'] ?>" />
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