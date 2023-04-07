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
function getCustomerById($customerId)
{
    $connection = ConnectionHelper::getConnection();
    $query = "select * from customer where Id = :id";
    $statement = $connection->prepare($query);
    $statement->bindParam('id', $customerId, PDO::PARAM_INT);
    $statement->execute();
    $result = $statement->fetch(PDO::FETCH_ASSOC);
    return json_encode($result);
}

//get supplier by id
if (isset($_GET['id'])) {
    $customerId = $_GET['id'];
    $customer = getCustomerById($customerId);
    //check supplier tenant id and current tenant id
    $customerTenantId = json_decode($customer)->TenantId;
    if ($customerTenantId != $tenantId) {
        header("Location: /error/accessDenied.php");
    }
    echo $customer;
    exit;
}
