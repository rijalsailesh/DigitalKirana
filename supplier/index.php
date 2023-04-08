<?php
require_once '../includes/functions.php';
require_once '../includes/Connection.php';

// check authentication
if (!checkAuth()) {
    header("Location: /?returnUrl=" . $_SERVER['REQUEST_URI']);
}

$search = getParam('search');

function getAllSuppliers($search)
{
    //get all suppliers by tenant id
    $connection = ConnectionHelper::getConnection();
    $query = "select s.Id, s.SupplierName, s.CreatedAt, s.Email, s.Address, s.Phone ,u.FirstName, u.LastName from supplier s inner join user u on s.UserId = u.Id where (:search is null) or (s.SupplierName like concat(:search, '%')) or (s.Email like concat(:search, '%')) or (s.Address like concat(:search, '%')) or (s.Phone like concat(:search, '%')) and s.TenantId = :tenantId";
    $statement = $connection->prepare($query);
    $tenantId = getTenantId();
    $statement->bindParam('tenantId', $tenantId, PDO::PARAM_INT);
    $statement->bindParam('search', $search, PDO::PARAM_STR);
    $statement->execute();
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}

// get all suppliers
$suppliers = getAllSuppliers($search);

require_once '../includes/themeHeader.php';
?>

<div class="container-fluid">
    <a href="/supplier/create.php" class="btn btn-primary"><i class="fas fa-fw fa-plus"></i> New Supplier</a>
    <div class="card mt-2 shadow-lg">
        <div class="card-header bg-primary">
            <h4 class="card-title text-light">List of Suppliers</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-lg-4 col-md-6 col-sm-12">
                    <form method="get" action="">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Search by name" value="<?= $search ?>" />
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-fw fa-search"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!-- line -->
            <hr class="sidebar-divider" />
            <?php renderMessages(); ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Name</th>
                            <th scope="col">Email</th>
                            <th scope="col">Phone</th>
                            <th scope="col">Address</th>
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
                                    <?= $supplier['Email'] ?>
                                </td>
                                <td>
                                    <?= $supplier['Phone'] ?>
                                </td>
                                <td>
                                    <?= $supplier['Address'] ?>
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