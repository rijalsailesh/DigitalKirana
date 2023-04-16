<?php
require_once '../includes/functions.php';
require_once '../includes/Connection.php';
require_once '../constants/Role.php';
require_once '../includes/authorize_user.php';

$tenantId = getTenantId(); //getting tenant id from session
$supplierId = getParam('id'); //getting supplierId from url


function getSupplierById($supplierId)
{
    $connection = ConnectionHelper::getConnection();
    $query = "select * from supplier where Id = :id";
    $statement = $connection->prepare($query);
    $statement->bindParam('id', $supplierId, PDO::PARAM_INT);
    $statement->execute();
    $result = $statement->fetch(PDO::FETCH_ASSOC);
    return $result;
}

//get supplier by id
$supplier = getSupplierById($supplierId);

//checking supplier tenant id and session tenant id
if ($supplier['TenantId'] != $tenantId) {
    header("Location: /error/accessDenied.php");
}


// check if form is submitted
if (isPost()) {
    // get form data
    $supplierName = $_POST['supplierName'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $address = $_POST['address'];

    // create category
    $connection = ConnectionHelper::getConnection();
    $query = "update supplier set SupplierName = :supplierName, Phone = :phone, Email = :email, Address = :address where Id = :id";
    $statement = $connection->prepare($query);
    $statement->bindParam('id', $supplierId);
    $statement->bindParam('supplierName', $supplierName);
    $statement->bindParam('email', $email);
    $statement->bindParam('address', $address);
    $statement->bindParam('phone', $phone);
    $statement->execute();
    $result = $statement->rowCount();
    if ($result > 0) {
        AddSuccessMessage("Supplier updated successfully");
        header("Location: /supplier");
    } else {
        AddErrorMessage("Failed to update supplier");
    }
}

require_once '../includes/themeHeader.php';
?>

<div class="container-fluid">
    <form action="" method="post">

        <a href="/supplier" class="btn btn-danger"><i class="fas fa-fw fa-arrow-left"></i> Back to Suppliers</a>
        <div class="card mt-2  shadow-lg">
            <div class="card-header bg-primary">
                <h4 class="card-title text-light">Create Category</h4>
            </div>
            <div class="card-body bg-gray">
                <?php renderMessages(); ?>
                <div class="row">
                    <div class="col-12 mb-4">
                        <label for="supplierName">Supplier Name</label>
                        <input type="text" name="supplierName" id="supplierName" class="form-control" placeholder="Supplier Name" value="<?= $supplier['SupplierName'] ?>" required>
                    </div>
                    <div class="col-12 mb-4">
                        <label for="phone">Phone</label>
                        <input type="phone" name="phone" id="phone" class="form-control" placeholder="Phone" value="<?= $supplier['Phone'] ?>" required>
                    </div>
                    <div class="col-12 mb-4">
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email" class="form-control" placeholder="Email" value="<?= $supplier['Email'] ?>" required>
                    </div>
                    <div class="col-12 mb-4">
                        <label for="address">Address</label>
                        <textarea name="address" id="address" class="form-control" placeholder="Address" rows="8"><?= $supplier['Address'] ?></textarea>
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