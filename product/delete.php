<?php
require_once '../includes/functions.php';
require_once '../includes/Connection.php';
require_once '../includes/authorize_user.php';

//check if product is used in purchase or sale
function isProductUsed($productId)
{
    $connection = ConnectionHelper::getConnection();
    $query = "select * from purchase_details where ProductId = :productId";
    $statement = $connection->prepare($query);
    $statement->bindParam('productId', $productId, PDO::PARAM_INT);
    $statement->execute();
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    if (count($result) > 0) {
        return true;
    }
    $query = "select * from sales_details where ProductId = :productId";
    $statement = $connection->prepare($query);
    $statement->bindParam('productId', $productId, PDO::PARAM_INT);
    $statement->execute();
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    if (count($result) > 0) {
        return true;
    }
    return false;
}

if (isPost()) {

    if(isProductUsed($_POST['id'])){
        addErrorMessage("Product is used in purchase or sale. Cannot delete.");
        header('Location: /product');
        return;
    }


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
