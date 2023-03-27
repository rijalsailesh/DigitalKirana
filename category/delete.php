<?php
require_once '../includes/functions.php';
require_once '../includes/Connection.php';


if (isPost()) {
    $categoryId = $_POST['id'];
    //get all users by tenant id
    $connection = ConnectionHelper::getConnection();
    $query = "delete from category where Id = :id";
    $statement = $connection->prepare($query);
    $statement->bindParam('id', $categoryId, PDO::PARAM_INT);
    $statement->execute();
    addSuccessMessage("Category deleted successfully");
    header('Location: /category');
}
