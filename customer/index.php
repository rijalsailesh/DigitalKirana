<?php
require_once '../includes/functions.php';
require_once '../includes/Connection.php';

// check authentication
if (!checkAuth()) {
    header("Location: /?returnUrl=" . $_SERVER['REQUEST_URI']);
}

function getAllCustomers()
{
    //get all customers by tenant id
    $connection = ConnectionHelper::getConnection();
    $query = "select c.Id, c.CustomerName, c.CreatedAt, c.Email, c.Address, c.Phone ,u.FirstName, u.LastName from customer c inner join user u on c.UserId = u.Id where c.TenantId = :tenantId";
    $statement = $connection->prepare($query);
    $tenantId = getTenantId();
    $statement->bindParam('tenantId', $tenantId, PDO::PARAM_INT);
    $statement->execute();
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}

// get all customers
$customers = getAllCustomers();

require_once '../includes/themeHeader.php';
?>

<div class="container-fluid">
    <a href="/customer/create.php" class="btn btn-primary"><i class="fas fa-fw fa-plus"></i> New Customer</a>
    <div class="card mt-2 shadow-lg">
        <div class="card-header bg-primary">
            <h4 class="card-title text-light">List of Customers</h4>
        </div>
        <div class="card-body">
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
                        foreach ($customers as $customer):
                            ?>
                            <tr>
                                <td scope="row">
                                    <?= ++$sn ?>
                                </td>
                                <td>
                                    <?= $customer['CustomerName'] ?>
                                </td>
                                <td>
                                    <?= $customer['Email'] ?>
                                </td>
                                <td>
                                    <?= $customer['Phone'] ?>
                                </td>
                                <td>
                                    <?= $customer['Address'] ?>
                                </td>
                                <td>
                                    <?= $customer['FirstName'] . " " . $customer['LastName'] ?>
                                </td>
                                <td>
                                    <?= $customer['CreatedAt'] ?>
                                </td>
                                <td>
                                    <a href="/customer/edit.php?id=<?= $customer['Id'] ?>" class="btn btn-sm btn-primary"><i
                                            class="fas fa-fw fa-edit"></i> Edit</a>
                                    <form id="deleteForm" method="post" action="/customer/delete.php" class="d-inline">
                                        <input type="hidden" name="id" value="<?= $customer['Id'] ?>" />
                                        <button type="submit" class="btn btn-sm btn-danger"><i
                                                class="fas fa-fw fa-trash"></i> Delete</button>
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