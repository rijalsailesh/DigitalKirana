<?php
require_once '../includes/functions.php';
require_once '../includes/Connection.php';
require_once '../constants/Role.php';
require_once '../includes/authorize_user.php';

$tenantId = getTenantId(); //getting tenant id from session
$customerId = getParam('id'); //getting customerId from url


function getCustomerById($supplierId)
{
    $connection = ConnectionHelper::getConnection();
    $query = "select * from customer where Id = :id";
    $statement = $connection->prepare($query);
    $statement->bindParam('id', $supplierId, PDO::PARAM_INT);
    $statement->execute();
    $result = $statement->fetch(PDO::FETCH_ASSOC);
    return $result;
}

//get customer by id
$customer = getCustomerById($customerId);

//checking customer tenant id and session tenant id
if ($customer['TenantId'] != $tenantId) {
    header("Location: /error/accessDenied.php");
}

// check if form is submitted
if (isPost()) {
    // get form data
    $customerName = $_POST['customerName'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $address = $_POST['address'];

    // update customer
    $connection = ConnectionHelper::getConnection();
    $query = "update customer set CustomerName = :customerName, Phone = :phone, Email = :email, Address = :address where Id = :id";
    $statement = $connection->prepare($query);
    $statement->bindParam('id', $customerId);
    $statement->bindParam('customerName', $customerName);
    $statement->bindParam('email', $email);
    $statement->bindParam('address', $address);
    $statement->bindParam('phone', $phone);
    $statement->execute();
    $result = $statement->rowCount();
    if ($result > 0) {
        AddSuccessMessage("Customer updated successfully");
        header("Location: /customer");
    } else {
        AddErrorMessage("Failed to update customer");
    }
}

require_once '../includes/themeHeader.php';
?>

<div class="container-fluid">
    <form action="" method="post">

        <a href="/customer" class="btn btn-danger"><i class="fas fa-fw fa-arrow-left"></i> Back to Customers</a>
        <div class="card mt-2  shadow-lg">
            <div class="card-header bg-primary">
                <h4 class="card-title text-light">Create Customer</h4>
            </div>
            <div class="card-body bg-gray">
                <?php renderMessages(); ?>
                <div class="row">
                    <div class="col-12 mb-4">
                        <label for="customerName">Customer Name</label>
                        <input type="text" name="customerName" id="customerName" class="form-control" placeholder="Customer Name" value="<?= $customer['CustomerName'] ?>" required>
                    </div>
                    <div class="col-12 mb-4">
                        <label for="phone">Phone</label>
                        <input type="phone" name="phone" id="phone" class="form-control" placeholder="Phone" value="<?= $customer['Phone'] ?>" required>
                    </div>
                    <div class="col-12 mb-4">
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email" class="form-control" placeholder="Email" value="<?= $customer['Email'] ?>" required>
                    </div>
                    <div class="col-12 mb-4">
                        <label for="address">Address</label>
                        <textarea name="address" id="address" class="form-control" placeholder="Address" rows="8"><?= $customer['Address'] ?></textarea>
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