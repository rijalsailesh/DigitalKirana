<?php
require_once('../includes/functions.php');
require_once('../includes/Connection.php');
require_once '../includes/authorize_admin.php';

if (isPost()) {
    $connection = ConnectionHelper::getConnection();
    $userId = $_POST['id'];
    $status = $_POST['status'];
    $query = "UPDATE user SET Status = :status WHERE Id = :id";
    $statement = $connection->prepare($query);
    $statement->bindParam('id', $userId, PDO::PARAM_INT);
    $updateStatus = !$status;
    $statement->bindParam('status', $updateStatus, PDO::PARAM_BOOL);
    $statement->execute();
    addSuccessMessage("User status updated successfully");
    header("Location: /user/index.php");
}
