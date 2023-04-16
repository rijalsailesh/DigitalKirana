<?php
require_once '../includes/functions.php';
require_once '../includes/Connection.php';

// check authentication
if (!checkAuth()) {
    header("Location: /?returnUrl=" . $_SERVER['REQUEST_URI']);
}

$search = getParam('search');

function getAllCustomers($search)
{
    //get all customers by tenant id
    $connection = ConnectionHelper::getConnection();
    $query = "select c.Id, c.CustomerName, c.CreatedAt, c.Email, c.Address, c.Phone ,u.FirstName, u.LastName from customer c inner join user u on c.UserId = u.Id where (:search is null) or (c.CustomerName like concat(:search, '%')) or (c.Email like concat(:search, '%')) or (c.Address like concat(:search, '%')) or (c.Phone like concat(:search, '%')) and c.TenantId = :tenantId";
    $statement = $connection->prepare($query);
    $tenantId = getTenantId();
    $statement->bindParam('tenantId', $tenantId, PDO::PARAM_INT);
    $statement->bindParam('search', $search, PDO::PARAM_STR);
    $statement->execute();
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}

// get all customers
$customers = getAllCustomers($search);

require_once '../includes/themeHeader.php';
?>

<div class="container-fluid">
    <div class="row non-printable">
        <div class="col-6">
            <a href="/customer/create.php" class="btn btn-primary"><i class="fas fa-fw fa-plus"></i> New Customer</a>
        </div>
        <div class="col-6">
            <button type="button" class="btn btn-secondary float-right" id="printBtn"><i class="fas fa-fw fa-print"></i> Print</button>
        </div>
    </div>
    <div class="card mt-2 shadow-lg">
        <div class="card-header bg-primary">
            <h4 class="card-title text-light">List of Customers</h4>
        </div>
        <div class="card-body">
            <div class="row non-printable">
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
            <hr class="sidebar-divider non-printable" />
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
                            <th scope="col" class="non-printable">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sn = 0;
                        foreach ($customers as $customer) :
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
                                <td class="non-printable">
                                    <a href="/customer/edit.php?id=<?= $customer['Id'] ?>" class="btn btn-sm btn-primary"><i class="fas fa-fw fa-edit"></i> Edit</a>
                                    <form id="deleteForm" method="post" action="/customer/delete.php" class="d-inline">
                                        <input type="hidden" name="id" value="<?= $customer['Id'] ?>" />
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