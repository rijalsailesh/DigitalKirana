<?php
require_once '../includes/functions.php';
require_once '../includes/Connection.php';

function checkCategoryInProduct($categoryId)
{
    $connection = ConnectionHelper::getConnection();
    $query = "select * from product where CategoryId = :categoryId";
    $statement = $connection->prepare($query);
    $statement->bindParam('categoryId', $categoryId, PDO::PARAM_INT);
    $statement->execute();
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}

//if form is submitted
if (isPost()) {
    $categoryId = $_POST['id']; //getting category id

    // check if category is used in product
    $categoryInProduct = checkCategoryInProduct($categoryId);
    if (count($categoryInProduct) > 0) {
        addErrorMessage("Category is used in product. It cannot be deleted.");
        header('Location: /category');
        return;
    }

    //delete category
    $connection = ConnectionHelper::getConnection();
    $query = "delete from category where Id = :id";
    $statement = $connection->prepare($query);
    $statement->bindParam('id', $categoryId, PDO::PARAM_INT);
    $statement->execute();
    addSuccessMessage("Category deleted successfully");
    header('Location: /category');
}