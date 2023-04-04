<?php
//connection helper
require_once '../includes/Connection.php';
require_once '../includes/functions.php';

//check authentication
if (!checkAuth()) {
    header("Location: /?returnUrl=" . $_SERVER['REQUEST_URI']);
}

//check tenant id
$tenantId = getTenantId();

//return supplier by id in json format
function getSupplierById($supplierId)
{
    $connection = ConnectionHelper::getConnection();
    $query = "select * from supplier where Id = :id";
    $statement = $connection->prepare($query);
    $statement->bindParam('id', $supplierId, PDO::PARAM_INT);
    $statement->execute();
    $result = $statement->fetch(PDO::FETCH_ASSOC);
    return json_encode($result);
}

//get supplier by id
if(isset($_GET['id'])){
    $supplierId = $_GET['id'];
    $supplier = getSupplierById($supplierId);
    //check supplier tenant id and current tenant id
    $supplierTenantId = json_decode($supplier)->TenantId;
    if($supplierTenantId != $tenantId){
        header("Location: /error/accessDenied.php");
    }
    echo $supplier;
    exit;
}
?>