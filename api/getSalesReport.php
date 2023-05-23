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

//get last week sales report with total sales in json format with name of day and get sales 0 if no sales
$report = getLastWeekSalesReport(getTenantId());
for($i=0;$i<7;$i++)
{
    $day = date('l', strtotime("-$i day"));
    $found = false;
    foreach(json_decode($report) as $item)
    {
        if($item->Day == $day)
        {
            $found = true;
            break;
        }
    }
    if(!$found)
    {
        $report = substr_replace($report, '{"Total":"0","Day":"'.$day.'"},', 1, 0);
    }
}


echo $report;
