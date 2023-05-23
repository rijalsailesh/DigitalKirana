<?php
require_once '../includes/functions.php';
require_once '../includes/Connection.php';
require_once '../constants/Role.php';
require_once '../includes/authorize_admin.php';

$userId = getParam('id');
// get tenant id
$tenantId = getTenantId();

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

$user = getUserById($userId);

//check user tenant id and session tenant id
if ($user['TenantId'] != $tenantId) {
    header("Location: /error/accessDenied.php");
}

//check user is admin
if ($user['Role'] == Role::$Admin) {
    header("Location: /user");
}

if (isPost()) {
    $newPassword = $_POST['newPassword'];
    $confirmPassword = $_POST['confirmPassword'];

    if($newPassword != $confirmPassword){
        addErrorMessage('Password and Confirm Password does not match');
        header("Location: /user/resetPassword.php?id=$userId");
    }

    $connection = ConnectionHelper::getConnection();

    $query = "update user set PasswordHash = :password where Id = :id and TenantId = :tenantId";

    $statement = $connection->prepare($query);
    //hashed password
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    $statement->bindParam('password', $hashedPassword);
    $statement->bindParam('tenantId', $tenantId, PDO::PARAM_INT);
    $statement->bindParam('id', $userId, PDO::PARAM_INT);
    $statement->execute();
    $result = $statement->rowCount();
    if ($result > 0) {
        addSuccessMessage('Password reset successfull');
        header("Location: /user");
    } else {
        addErrorMessage('Password reset failed');
    }
}

require_once '../includes/themeHeader.php';
?>

<div class="container-fluid">
    <form action="" method="post">

        <a href="/user" class="btn btn-danger"><i class="fas fa-fw fa-arrow-left"></i> Back to Users</a>
        <div class="card mt-2  shadow-lg">
            <div class="card-header bg-primary">
                <h4 class="card-title text-light">Reset Password of
                    <span style="font-weight: 800;">(<?= $user['Id'] . ": " . $user['FirstName'] . " " . $user['LastName'] ?>)</span>
                </h4>
            </div>
            <div class="card-body bg-gray">
                <?php renderMessages(); ?>
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label for="password">New Password*</label>
                        <input type="password" name="newPassword" id="newPassword" class="form-control" placeholder="New Password" required>
                    </div>
                    <div class="col-md-6 mb-4">
                        <label for="password">Confirm Password*</label>
                        <input type="password" name="confirmPassword" id="confirmPassword" class="form-control" placeholder="Confirm Password" required>
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