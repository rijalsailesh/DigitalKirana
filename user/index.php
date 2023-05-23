<?php
require_once '../includes/functions.php';
require_once '../includes/Connection.php';
require_once '../constants/Role.php';
require_once '../includes/authorize_admin.php';

$name = getParam('name');
$email = getParam('email');
$status = getParam('status');
$role = getParam('role');


function getAllUsers($name, $email, $status, $role)
{
    //get all users by tenant id
    $connection = ConnectionHelper::getConnection();

    $query = "select * from user where ((:name is null) or (FirstName like concat(:name, '%')) or (LastName like concat(:name, '%')) or (Username like concat(:name, '%'))) and (((:email is null) or (Email like concat(:email, '%')))) and (((:status is null) or (Status = :status))) and (((:role is null) or (Role = :role))) and TenantId = :tenantId and Role != 'Admin'";
    $statement = $connection->prepare($query);
    $tenantId = getTenantId();
    $statement->bindParam('tenantId', $tenantId, PDO::PARAM_INT);
    $statement->bindParam('name', $name, PDO::PARAM_STR);
    $statement->bindParam('firstName', $name, PDO::PARAM_STR);
    $statement->bindParam('lastName', $name, PDO::PARAM_STR);
    $statement->bindParam('email', $email, PDO::PARAM_STR);
    $statement->bindParam('status', $status, PDO::PARAM_STR);
    $statement->bindParam('role', $role, PDO::PARAM_STR);
    $statement->execute();
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}

// get all users
$users = getAllUsers($name, $email, $status, $role);

require_once '../includes/themeHeader.php';
?>

<div class="container-fluid">
    <div class="row non-printable">
        <div class="col-6">
            <a href="/user/create.php" class="btn btn-primary"><i class="fas fa-fw fa-plus"></i> New User</a>
        </div>
        <div class="col-6">
            <button type="button" class="btn btn-secondary float-right" id="printBtn"><i class="fas fa-fw fa-print"></i> Print</button>
        </div>
    </div>
    <div class="card mt-2 shadow-lg">
        <div class="card-header bg-primary">
            <h4 class="card-title text-light">List of Users</h4>
        </div>
        <div class="card-body">
            <form action="">
                <div class="row non-printable">
                    <div class="col-2">
                        <label for="Name">Name</label>
                        <input type="text" value="<?= $name ?>" name="name" class="form-control">
                    </div>
                    <div class="col-2">
                        <label for="Email">Email</label>
                        <input type="text" value="<?= $email ?>" id="Email" name="email" class="form-control">
                    </div>
                    <div class="col-2">
                        <label for="Name">Status</label>
                        <select name="status" class="form-control">
                            <option>Select Status</option>
                            <option <?= $status == 1 ? "selected" : "" ?> value="1">Active</option>
                            <option <?= $status == 0 ? "selected" : "" ?> value="0">Inactive</option>
                        </select>
                    </div>
                    <div class="col-2">
                        <label for="Name">Role</label>
                        <select name="role" class="form-control">
                            <option value="">Select Role</option>
                            <option <?= $role == Role::$User ? "selected" : "" ?> value="<?= Role::$User ?>"><?= Role::$User ?></option>
                            <option <?= $role == Role::$SalesPerson ? "selected" : "" ?> value="<?= Role::$SalesPerson ?>"><?= Role::$SalesPerson ?></option>
                        </select>
                    </div>
                    <div class="col-3">
                        <label for="">&nbsp;</label>
                        <button type="submit" class="btn btn-primary d-block"><i class="fas fa-fw fa-search"></i> Search</button>
                    </div>
                </div>
            </form>
            <!-- line -->
            <hr class="sidebar-divider non-printable" />

            <?php renderMessages(); ?>
            <div class="table-responsive">
                <table class="table table-hover">
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
                            <th scope="col" class="non-printable">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sn = 0;
                        foreach ($users as $user) :
                        ?>
                            <tr class="">
                                <td scope="row"><?= ++$sn ?></td>
                                <td><?= $user['FirstName'] ?></td>
                                <td><?= $user['LastName'] ?></td>
                                <td><?= $user['Username'] ?></td>
                                <td><?= $user['Phone'] ?></td>
                                <td><?= $user['Address'] ?></td>
                                <td><?= $user['Email'] ?></td>
                                <td><span class="<?= $user['Status'] ? "text-primary" : "text-dark" ?> fw-bolder" style="font-weight:700;"><?= $user['Status'] ? "Active" : "Inactive" ?></span></td>
                                <td><?= $user['Role'] ?></td>
                                <td><?= $user['CreatedAt'] ?></td>
                                <td class="non-printable">
                                    <form action="/user/toggleStatus.php" method="post" class="d-inline">
                                        <input type="hidden" name="id" value="<?= $user['Id'] ?>">
                                        <input type="hidden" name="status" value="<?= $user['Status'] ?>">
                                        <button type="submit" class='btn btn-sm <?= $user['Status'] ? "btn-danger" : "btn-primary" ?>' title='<?= $user['Status'] ? "Deactivate" : "Activate" ?>'>
                                            <i class=" fas fa-fw <?= $user['Status'] ? "fa-ban" : "fa-check" ?>"></i>

                                        </button>
                                    </form>

                                    <a href="/user/edit.php?id=<?= $user['Id'] ?>" class="btn btn-sm btn-info" title="edit"><i class="fas fa-fw fa-edit"></i></a>

                                    <form action="/user/resetPassword.php" method="get" class="d-inline">
                                        <input type="hidden" name="id" value="<?= $user['Id'] ?>">
                                        <button title="Reset Password" class="btn btn-sm btn-warning"><i class="fas fa-fw fa-key"></i></button>
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