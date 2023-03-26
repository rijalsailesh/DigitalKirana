<?php
require_once('includes/functions.php');
require_once('includes/Connection.php');
require_once('constants/TenantStatus.php');

//check authentication
if (!checkAuth()) {
    header("Location: /");
} else {
    if (getTenantId() != null) {
        header("Location: /dashboard.php");
    }
}

$connection = ConnectionHelper::getConnection();

$query = "SELECT * FROM tenants";

$statement = $connection->prepare($query);
$statement->execute();
$result = $statement->fetchAll(PDO::FETCH_ASSOC);

//approve tenant
if (isPost()) {
    $id = $_POST['id'];
    $query = "UPDATE tenants SET Status = :status WHERE Id = :id";
    $statement = $connection->prepare($query);
    $statement->bindParam('id', $id, PDO::PARAM_INT);
    if (isset($_POST['approve'])) {
        $status = TenantStatus::$Approved;
    } else if (isset($_POST['reject'])) {
        $status = TenantStatus::$Rejected;
    }
    $statement->bindParam('status', $status);
    $statement->execute();
    addSuccessMessage("Tenant updated successfully");
    header("Location: /tenants.php");
}

require_once('includes/header.php');
?>

<div class="container mx-auto mt-4 row">
    <?php renderMessages(); ?>
    <div class="col-6">
        <h1>List Of Tenants</h1>
    </div>
    <div class="col-6 text-end">
        <form method="post" action="/logout.php">
            <button type="submit" class="btn btn-outline-danger">Logout</button>
        </form>
    </div>
    <div>
        <div class="table-responsive mt-4">
            <table class="table table-bordered table-light table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th scope="col">S.N.</th>
                        <th scope="col">Name</th>
                        <th scope="col">Email</th>
                        <th scope="col">Phone</th>
                        <th scope="col">Address</th>
                        <th scope="col">CreatedAt</th>
                        <th scope="col">Status</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sn = 0;
                    foreach ($result as $tenant) :
                    ?>
                        <tr>
                            <th><?= ++$sn ?></th>
                            <td><?= $tenant['Name'] ?></td>
                            <td><?= $tenant['Email'] ?></td>
                            <td><?= $tenant['Phone'] ?></td>
                            <td><?= $tenant['Address'] ?></td>
                            <td><?= $tenant['CreatedAt'] ?></td>
                            <td><?= $tenant['Status'] ?></span></td>
                            <td>
                                <?php
                                if ($tenant['Status'] == TenantStatus::$Pending) :
                                ?>
                                    <form method="post" action="">
                                        <input type="hidden" name="id" value="<?= $tenant['Id'] ?>">
                                        <button type="submit" name="approve" class="btn btn-sm btn-outline-primary">Approve</button>
                                        <button type="submit" name="reject" class="btn btn-sm btn-outline-danger">Reject</button>
                                    </form>
                                <?php
                                endif;
                                ?>
                            </td>
                        <?php
                    endforeach;
                        ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<?php
require_once 'includes/footer.php';
?>