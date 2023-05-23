<?php
require_once '../includes/functions.php';
require_once '../includes/Connection.php';
require_once '../includes/authorize_user.php';

//check if customer is used in purchase or sale
function isCustomerUsed($customerId){
    $connection = ConnectionHelper::getConnection();
    $query = "select * from sales where CustomerId = :customerId";
    $statement = $connection->prepare($query);
    $statement->bindParam('customerId', $customerId, PDO::PARAM_INT);
    $statement->execute();
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    if (count($result) > 0) {
        return true;
    }
    return false;
}

//if form is submitted
if (isPost()) {
    $supplierId = $_POST['id']; //getting supplier id

    if(isCustomerUsed($supplierId)){
        addErrorMessage("Customer is used in purchase or sale. Cannot delete.");
        header('Location: /customer');
        return;
    }
    //delete customer
    $connection = ConnectionHelper::getConnection();
    $query = "delete from customer where Id = :id";
    $statement = $connection->prepare($query);
    $statement->bindParam('id', $supplierId, PDO::PARAM_INT);
    $statement->execute();
    addSuccessMessage("Customer deleted successfully");
    header('Location: /customer');
}
