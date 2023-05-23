<?php
require_once '../includes/functions.php';
require_once '../includes/Connection.php';
require_once '../constants/Role.php';
require_once '../includes/authorize.php';

// get tenant id
$userId = getLoggedInUserId();
$user = getUserById($userId);


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

$tenantId = getTenantId();

if (isPost()) {
    $oldPassword = $_POST['oldPassword'];
    $newPassword = $_POST['newPassword'];
    $confirmPassword = $_POST['confirmPassword'];
    $connection = ConnectionHelper::getConnection();

    if(!password_verify($oldPassword, $user['PasswordHash'])){
        addErrorMessage('Old password does not match');
        header("Location: /profile/changePassword.php");
        return;
    }

    if ($newPassword != $confirmPassword) {
        addErrorMessage('New Password and Confirm Password does not match');
        header("Location: /profile/changePassword.php");
        return;
    }

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
        addSuccessMessage('Password changed successfully');
        updateSessionUser();
        header("Location: /profile");
    } else {
        addErrorMessage('Password changed failed');
    }
}


require_once '../includes/themeHeader.php';
?>

<div class="container-fluid">
    <form action="" method="post">

        <a href="/profile" class="btn btn-danger"><i class="fas fa-fw fa-arrow-left"></i> Back to Profile</a>
        <div class="card mt-2  shadow-lg">
            <div class="card-header bg-primary">
                <h4 class="card-title text-light">My Profile</h4>
            </div>
            <div class="card-body bg-gray">
                <?php renderMessages(); ?>
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label for="oldPassword">Old Password*</label>
                        <input type="password" name="oldPassword" id="oldPassword" class="form-control" placeholder="Old Password" required>
                    </div>
                    <div class="col-md-6 mb-4">
                        <label for="newPassword">New Password*</label>
                        <input type="password" name="newPassword" id="newPassword" class="form-control" placeholder="New Password" required>
                    </div>
                    <div class="col-12 mb-4">
                        <label for="newPassword">Confirm Password*</label>
                        <input type="password" name="confirmPassword" id="confirmPassword" class="form-control" placeholder="Confirm Password" required>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button class="btn btn-primary"><i class="fas fa-fw fa-file"></i> Save</button>
            </div>
        </div>
    </form>
</div>

<?php
require_once '../includes/themeFooter.php';
?>