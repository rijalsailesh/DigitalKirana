<?php
require_once '../includes/functions.php';
require_once '../includes/Connection.php';

//if form is submitted
if (isPost()) {
    $supplierId = $_POST['id']; //getting supplier id
    //delete customer
    $connection = ConnectionHelper::getConnection();
    $query = "delete from customer where Id = :id";
    $statement = $connection->prepare($query);
    $statement->bindParam('id', $supplierId, PDO::PARAM_INT);
    $statement->execute();
    addSuccessMessage("Customer deleted successfully");
    header('Location: /customer');
}