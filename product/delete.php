<?php
require_once '../includes/functions.php';
require_once '../includes/Connection.php';


if (isPost()) {
    $categoryId = $_POST['id']; //getting product id
    //delete product by id
    $connection = ConnectionHelper::getConnection();
    $query = "delete from product where Id = :id";
    $statement = $connection->prepare($query);
    $statement->bindParam('id', $categoryId, PDO::PARAM_INT);
    $statement->execute();
    addSuccessMessage("Product deleted successfully");
    header('Location: /product');
}