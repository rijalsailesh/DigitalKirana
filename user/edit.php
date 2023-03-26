<?php
require_once '../includes/functions.php';
require_once '../includes/Connection.php';
require_once '../constants/Role.php';

// check authentication
if (!checkAuth()) {
    header("Location: /?returnUrl=" . $_SERVER['REQUEST_URI']);
}

// check if user is admin
if (!isAdmin()) {
    header("Location: /error/accessDenied.php");
}

// get tenant id
$userId = getParam('id');
$tenantId = getTenantId();

// get user by id
function getUserById($userId)
{
    $connection = ConnectionHelper::getConnection();
    $query = "select * from user where Id = :id";
    $statement = $connection->prepare($query);
    $statement->bindParam('id', $userId, PDO::PARAM_INT);
    $statement->execute();
    $result = $statement->fetch(PDO::FETCH_ASSOC);
    return $result;
}

//check user tenant id and session tenant id
$user = getUserById($userId);

if ($user['TenantId'] != $tenantId) {
    header("Location: /error/accessDenied.php");
}

if (isPost()) {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $role = $_POST['role'];

    $connection = ConnectionHelper::getConnection();
    $query = "update user set FirstName = :firstName, LastName = :lastName, Phone = :phone, Address = :address, Role = :role where Id = :id and TenantId = :tenantId";
    $statement = $connection->prepare($query);
    $statement->bindParam('firstName', $firstName);
    $statement->bindParam('lastName', $lastName);
    $statement->bindParam('phone', $phone);
    $statement->bindParam('address', $address);
    $statement->bindParam('role', $role);
    $statement->bindParam('tenantId', $tenantId, PDO::PARAM_INT);
    $statement->bindParam('id', $userId, PDO::PARAM_INT);
    $statement->execute();
    $result = $statement->rowCount();
    if ($result > 0) {
        addSuccessMessage('User updated successfully');
        header("Location: /user");
    } else {
        addErrorMessage('User update failed');
    }
}


require_once '../includes/themeHeader.php';
?>

<div class="container-fluid">
    <form action="" method="post">

        <a href="/user" class="btn btn-danger"><i class="fas fa-fw fa-arrow-left"></i> Back to Users</a>
        <div class="card mt-2  shadow-lg">
            <div class="card-header bg-primary">
                <h4 class="card-title text-light">Create User</h4>
            </div>
            <div class="card-body bg-gray">
                <?php renderMessages(); ?>
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label for="firstName">First Name</label>
                        <input type="text" name="firstName" id="firstName" class="form-control" value="<?= $user['FirstName'] ?>" placeholder="First Name" required>
                    </div>
                    <div class="col-md-6 mb-4">
                        <label for="lastName">Last Name</label>
                        <input type="text" name="lastName" id="lastName" class="form-control" placeholder="Last Name" value="<?= $user['LastName'] ?>" required>
                    </div>
                    <div class="col-md-6 mb-4">
                        <label for="email">Email</label>
                        <input type="text" readonly name="email" id="email" class="form-control" placeholder="Email" value="<?= $user['Email'] ?>" required>
                    </div>
                    <div class="col-md-6 mb-4">
                        <label for="username">Username</label>
                        <input type="text" readonly name="username" id="username" class="form-control" placeholder="Username" value="<?= $user['Username'] ?>" required>
                    </div>
                    <div class="col-md-6 mb-4">
                        <label for="phone">Phone</label>
                        <input type="text" name="phone" id="phone" class="form-control" placeholder="Phone" value="<?= $user['Phone'] ?>" required>
                    </div>
                    <div class="col-md-6 mb-4">
                        <label for="address">Address</label>
                        <input type="text" name="address" id="address" class="form-control" placeholder="Address" value="<?= $user['Address'] ?>" required>
                    </div>
                    <div class="col-md-6 mb-4">
                        <label for="role">Role</label>
                        <select name="role" id="role" class="form-control" required>
                            <option value="0">Select Role</option>
                            <option <?= $user['Role'] == Role::$Admin ? "selected" : "" ?> value="<?= Role::$Admin ?>">Admin</option>
                            <option <?= $user['Role'] == Role::$User ? "selected" : "" ?> value="<?= Role::$User ?>">User</option>
                        </select>
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