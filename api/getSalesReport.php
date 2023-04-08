<?php
require_once '../includes/Connection.php';
require_once '../includes/functions.php';

//check authentication
if (!checkAuth()) {
    header("Location: /?returnUrl=" . $_SERVER['REQUEST_URI']);
}

function getLastWeekSalesReport($tenantId)
{
    $connection = ConnectionHelper::getConnection();
    $query = "select sum(NetTotal) as Total, dayname(CreatedAt) as Day from sales where TenantId = :tenantId and CreatedAt between date_sub(now(), interval 7 day) and now() group by date(CreatedAt)";
    $statement = $connection->prepare($query);
    $statement->bindParam('tenantId', $tenantId, PDO::PARAM_INT);
    $statement->execute();
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    return json_encode($result);
}


//get last week sales report with total sales in json format with name of day
$report = getLastWeekSalesReport(getTenantId());
echo $report;
