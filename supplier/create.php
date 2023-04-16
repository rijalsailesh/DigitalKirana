<?php
require_once '../includes/functions.php';
require_once '../includes/Connection.php';
require_once '../constants/Role.php';
require_once '../includes/authorize_user.php';

$tenantId = getTenantId(); //getting tenant id from session

// check if form is submitted
if (isPost()) {
    // get form data
    $supplierName = $_POST['supplierName'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $address = $_POST['address'];

    // create category
    $connection = ConnectionHelper::getConnection();
    $query = "INSERT INTO supplier (SupplierName, Phone, Email, Address, TenantId, CreatedAt, UserId) VALUES (:supplierName, :phone, :email, :address, :tenantId, :createdAt, :userId)";
    $statement = $connection->prepare($query);
    $statement->bindParam(':supplierName', $supplierName);
    $statement->bindParam(':phone', $phone);
    $statement->bindParam(':email', $email);
    $statement->bindParam(':address', $address);
    $statement->bindParam(':tenantId', $tenantId);
    $createdDate = date('Y-m-d H:i:s');
    $statement->bindParam(':createdAt', $createdDate);
    $userId = getLoggedInUserId();
    $statement->bindParam(':userId', $userId);
    $statement->execute();
    $result = $statement->rowCount();
    if ($result > 0) {
        AddSuccessMessage("Supplier created successfully");
        header("Location: /supplier");
    } else {
        AddErrorMessage("Failed to create supplier");
    }
}

require_once '../includes/themeHeader.php';
?>

<div class="container-fluid">
    <form action="" method="post">

        <a href="/supplier" class="btn btn-danger"><i class="fas fa-fw fa-arrow-left"></i> Back to Suppliers</a>
        <div class="card mt-2  shadow-lg">
            <div class="card-header bg-primary">
                <h4 class="card-title text-light">Create Supplier</h4>
            </div>
            <div class="card-body bg-gray">
                <?php renderMessages(); ?>
                <div class="row">
                    <div class="col-12 mb-4">
                        <label for="supplierName">Supplier Name</label>
                        <input type="text" name="supplierName" id="supplierName" class="form-control" placeholder="Supplier Name" required>
                    </div>
                    <div class="col-12 mb-4">
                        <label for="phone">Phone</label>
                        <input type="phone" name="phone" id="phone" class="form-control" placeholder="Phone" required>
                    </div>
                    <div class="col-12 mb-4">
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email" class="form-control" placeholder="Email" required>
                    </div>
                    <div class="col-12 mb-4">
                        <label for="address">Address</label>
                        <textarea name="address" id="address" class="form-control" placeholder="Address" rows="8"></textarea>
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