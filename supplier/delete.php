<?php
require_once '../includes/functions.php';
require_once '../includes/Connection.php';
require_once '../includes/authorize_user.php';
//if form is submitted
if (isPost()) {
    $supplierId = $_POST['id']; //getting supplier id
    //delete supplier
    $connection = ConnectionHelper::getConnection();
    $query = "delete from supplier where Id = :id";
    $statement = $connection->prepare($query);
    $statement->bindParam('id', $supplierId, PDO::PARAM_INT);
    $statement->execute();
    addSuccessMessage("Supplier deleted successfully");
    header('Location: /supplier');
}
