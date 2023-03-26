<?php
require_once '../includes/functions.php';
require_once '../includes/Connection.php';

// check authentication
if (!checkAuth()) {
    header("Location: /?returnUrl=" . $_SERVER['REQUEST_URI']);
}

// check if user is admin
if (!isAdmin()) {
    header("Location: /error/accessDenied.php");
}

function getAllUsers()
{
    //get all users by tenant id
    $connection = ConnectionHelper::getConnection();
    $query = "select * from user where TenantId = :tenantId";
    $statement = $connection->prepare($query);
    $tenantId = getTenantId();
    $statement->bindParam('tenantId', $tenantId, PDO::PARAM_INT);
    $statement->execute();
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}

// get all users
$users = getAllUsers();

require_once '../includes/themeHeader.php';
?>

<div class="container-fluid">
    <a href="user/create.php" class="btn btn-primary"><i class="fas fa-fw fa-plus"></i> New User</a>
    <div class="card mt-2">
        <div class="card-header bg-primary">
            <h4 class="card-title text-light">List of Users</h4>
        </div>
        <div class="card-body">
            <?php renderMessages(); ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">First Name</th>
                            <th scope="col">Last Name</th>
                            <th scope="col">Username</th>
                            <th scope="col">Phone</th>
                            <th scope="col">Address</th>
                            <th scope="col">Email</th>
                            <th scope="col">Status</th>
                            <th scope="col">Role</th>
                            <th scope="col">Created Date</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($users as $user) :
                        ?>
                            <tr class="">
                                <td scope="row">1</td>
                                <td><?= $user['FirstName'] ?></td>
                                <td><?= $user['LastName'] ?></td>
                                <td><?= $user['Username'] ?></td>
                                <td><?= $user['Phone'] ?></td>
                                <td><?= $user['Address'] ?></td>
                                <td><?= $user['Email'] ?></td>
                                <td><?= $user['Status'] ? "Active" : "Not Active" ?></td>
                                <td><?= $user['Role'] ?></td>
                                <td><?= $user['CreatedAt'] ?></td>
                                <td>
                                    <a href="#" class="btn btn-sm btn-primary">Edit</a>
                                    <a href="#" class="btn btn-sm btn-danger">Delete</a>
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