<?php
require_once '../includes/Connection.php';
require_once '../includes/functions.php';

//check authentication
if (!checkAuth()) {
    header("Location: /?returnUrl=" . $_SERVER['REQUEST_URI']);
}

//get product by code and id in json format
function getProductByCode($productCode, $tenantId)
{
    $connection = ConnectionHelper::getConnection();
    $query = "select * from product where ProductCode = :productCode and TenantId = :tenantId";
    $statement = $connection->prepare($query);
    $statement->bindParam('productCode', $productCode, PDO::PARAM_STR);
    $statement->bindParam('tenantId', $tenantId, PDO::PARAM_INT);
    $statement->execute();
    $result = $statement->fetch(PDO::FETCH_ASSOC);
    return json_encode($result);
}

//get product by id in json format
function getProductById($productId, $tenantId)
{
    $connection = ConnectionHelper::getConnection();
    $query = "select * from product where Id = :id and TenantId = :tenantId";
    $statement = $connection->prepare($query);
    $statement->bindParam('id', $productId, PDO::PARAM_INT);
    $statement->bindParam('tenantId', $tenantId, PDO::PARAM_INT);
    $statement->execute();
    $result = $statement->fetch(PDO::FETCH_ASSOC);
    return json_encode($result);
}

//get product by code
if(isset($_GET['code'])){
    $productCode = $_GET['code'];
    $tenantId = getTenantId();
    $product = getProductByCode($productCode, $tenantId);
    //check product tenant id and current tenant id
    $productTenantId = json_decode($product)->TenantId;
    if($productTenantId != $tenantId){
        header("Location: /error/accessDenied.php");
    }
    echo $product;
    exit;
}

//get product by id
if(isset($_GET['id'])){
    $productId = $_GET['id'];
    $tenantId = getTenantId();
    $product = getProductById($productId, $tenantId);
    //check product tenant id and current tenant id
    $productTenantId = json_decode($product)->TenantId;
    if($productTenantId != $tenantId){
        header("Location: /error/accessDenied.php");
    }
    echo $product;
    exit;
}
