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
$userId = getLoggedInUserId();
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

function getTenant($tenantId){
    $connection = ConnectionHelper::getConnection();
    $query = "select * from tenants where Id = :id";
    $statement = $connection->prepare($query);
    $statement->bindParam('id', $tenantId, PDO::PARAM_INT);
    $statement->execute();
    $result = $statement->fetch(PDO::FETCH_ASSOC);
    return $result;
}

//check user tenant id and session tenant id
$user = getUserById($userId);
$tenant = getTenant($tenantId);


if ($user['TenantId'] != $tenantId) {
    header("Location: /error/accessDenied.php");
}

require_once '../includes/themeHeader.php';
?>

<div class="container-fluid">
    <form >

        <a href="/dashboard.php" class="btn btn-danger"><i class="fas fa-fw fa-arrow-left"></i> Back to Dashboard</a>
        <div class="card mt-2  shadow-lg">
            <div class="card-header bg-primary">
                <h4 class="card-title text-light">My Profile</h4>
            </div>
            <div class="card-body bg-gray">
                <?php renderMessages(); ?>
                <h5 class="text-primary">Personal Details</h5>
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label for="firstName">First Name</label>
                        <input type="text" name="firstName" id="firstName" class="form-control" value="<?= $user['FirstName'] ?>" placeholder="First Name" readonly required>
                    </div>
                    <div class="col-md-6 mb-4">
                        <label for="lastName">Last Name</label>
                        <input type="text" name="lastName" id="lastName" class="form-control" placeholder="Last Name" value="<?= $user['LastName'] ?>" readonly required>
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
                        <input type="text" name="phone" id="phone" class="form-control" placeholder="Phone" value="<?= $user['Phone'] ?>" readonly required>
                    </div>
                    <div class="col-md-6 mb-4">
                        <label for="address">Address</label>
                        <input type="text" name="address" id="address" class="form-control" placeholder="Address" value="<?= $user['Address'] ?>" readonly required>
                    </div>

                    <hr/>
                </div>
                <h5 class="text-primary">Business Details</h5>
                <div class="row">
                    <div class="col-md-6">
                        <img src="/assets/imgs/logos/<?=$tenant['LogoUrl']==null?"default.png":$tenant['LogoUrl']?>" alt="Logo">
                    </div>   
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label for="businessName">Business Name</label>
                            <input type="text" value="<?=$tenant['Name']?>" name="businessName" id="businessName" class="form-control" placeholder="Business Name" readonly required>
                        </div>
                        <div class="mb-4">
                            <label for="businessEmail">Business Email</label>
                            <input type="email" value="<?=$tenant['Email']?>" name="businessEmail" id="businessEmail" class="form-control" placeholder="Business Email" readonly required>
                        </div>
                        <div class="mb-4">
                            <label for="businessPhone">Business Phone</label>
                            <input type="text" value="<?=$tenant['Phone']?>" name="businessPhone" id="businessPhone" class="form-control" placeholder="Business Phone" readonly required>
                        </div>
                        <div class="mb-4">
                            <label for="businessAddress">Business Address</label>
                            <input type="text" value="<?=$tenant['Address']?>" name="businessAddress" id="businessAddress" class="form-control" placeholder="Business Address" readonly required>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <a class="btn btn-primary" href="/profile/editPersonalInfo.php"><i class="fas fa-fw fa-edit"></i> Edit Personal Info</a>
                <a class="btn btn-secondary" href="/profile/editBusinessInfo.php"><i class="fas fa-fw fa-edit"></i> Edit Business Info</a>
            </div>
        </div>
    </form>
</div>

<?php
require_once '../includes/themeFooter.php';
?>