<?php
require_once '../includes/functions.php';
require_once '../includes/Connection.php';
require_once '../constants/Role.php';
require_once '../includes/authorize_admin.php';

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

function getTenant($tenantId)
{
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

if (isPost()) {
    //update tenants table
    $businessName = $_POST['businessName'];
    $businessEmail = $_POST['businessEmail'];
    $businessPhone = $_POST['businessPhone'];
    $businessAddress = $_POST['businessAddress'];


    //get logo from input
    $image = $_FILES['logo'];

    $imageName = "";
    if ($image['size'] > 0) {
        $name = date('Y-m-d-H-i-s');
        $ext = ".png";
        $imageName = $name . $ext;
        saveLogo($image['tmp_name'], $imageName);
    }

    $connection = ConnectionHelper::getConnection();

    if ($imageName == "") {
        $query = "update tenants set Name = :businessName, Email = :businessEmail, Phone = :businessPhone, Address = :businessAddress where Id = :id";
    } else {
        $query = "update tenants set Name = :businessName, Email = :businessEmail, Phone = :businessPhone, Address = :businessAddress, LogoUrl = :logo where Id = :id";
    }

    $statement = $connection->prepare($query);
    $statement->bindParam('businessName', $businessName);
    $statement->bindParam('businessEmail', $businessEmail);
    $statement->bindParam('businessPhone', $businessPhone);
    $statement->bindParam('businessAddress', $businessAddress);
    if ($imageName != "") {
        $statement->bindParam('logo', $imageName);
    }
    $statement->bindParam('id', $tenantId, PDO::PARAM_INT);
    $statement->execute();
    $result = $statement->rowCount();
    if ($result > 0) {
        addSuccessMessage('Profile updated successfully');
        header("Location: /profile");
    } else {
        addErrorMessage('Error updating profile');
    }
}


require_once '../includes/themeHeader.php';
?>

<div class="container-fluid">
    <form method="post" action="" enctype="multipart/form-data">

        <a href="/profile" class="btn btn-danger"><i class="fas fa-fw fa-arrow-left"></i> Back to Profile</a>
        <div class="card mt-2  shadow-lg">
            <div class="card-header bg-primary">
                <h4 class="card-title text-light">My Profile</h4>
            </div>
            <div class="card-body bg-gray">
                <?php renderMessages(); ?>
                <h5 class="text-primary">Business Details</h5>
                <div class="row">
                    <div class="col-md-6">
                        <img src="/assets/imgs/logos/<?= $tenant['LogoUrl'] == null ? "default.png" : $tenant['LogoUrl'] ?>" alt="Logo" class="img-fluid" id="imagePreview">
                    </div>
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label for="businessName">Business Name</label>
                            <input type="text" value="<?= $tenant['Name'] ?>" name="businessName" id="businessName" class="form-control" placeholder="Business Name" required>
                        </div>
                        <div class="mb-4">
                            <label for="businessEmail">Business Email</label>
                            <input type="email" value="<?= $tenant['Email'] ?>" name="businessEmail" id="businessEmail" class="form-control" placeholder="Business Email" required>
                        </div>
                        <div class="mb-4">
                            <label for="businessPhone">Business Phone</label>
                            <input type="text" value="<?= $tenant['Phone'] ?>" name="businessPhone" id="businessPhone" class="form-control" placeholder="Business Phone" required>
                        </div>
                        <div class="mb-4">
                            <label for="businessAddress">Business Address</label>
                            <input type="text" value="<?= $tenant['Address'] ?>" name="businessAddress" id="businessAddress" class="form-control" placeholder="Business Address" required>
                        </div>
                        <div class="mb-4">
                            <label for="logo">Upload Logo</label>
                            <input type="file" name="logo" id="logo" class="form-control-file" accept="image/*" onchange="showPreview(event);">
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button class="btn btn-primary"><i class="fas fa-fw fa-file"></i> Save</button>
            </div>
        </div>
    </form>
</div>

<script>
    function showPreview(event) {
        if (event.target.files.length > 0) {
            var src = URL.createObjectURL(event.target.files[0]);
            var preview = document.getElementById("imagePreview");
            preview.src = src;
        }
    }
</script>


<?php
require_once '../includes/themeFooter.php';
?>