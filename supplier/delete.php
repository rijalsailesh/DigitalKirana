<?php
require_once '../includes/functions.php';
require_once '../includes/Connection.php';
require_once '../includes/authorize_user.php';

function isSupplierUsed($supplierId){
    $connection = ConnectionHelper::getConnection();
    $query = "select * from purchase where SupplierId = :supplierId";
    $statement = $connection->prepare($query);
    $statement->bindParam('supplierId', $supplierId, PDO::PARAM_INT);
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

    if(isSupplierUsed($supplierId)){
        addErrorMessage("Supplier is used in purchase or sale. Cannot delete.");
        header('Location: /supplier');
        return;
    }

    //delete supplier
    $connection = ConnectionHelper::getConnection();
    $query = "delete from supplier where Id = :id";
    $statement = $connection->prepare($query);
    $statement->bindParam('id', $supplierId, PDO::PARAM_INT);
    $statement->execute();
    addSuccessMessage("Supplier deleted successfully");
    header('Location: /supplier');
}
