<?php
require_once '../includes/functions.php';
require_once '../includes/Connection.php';
require_once '../includes/authorize.php';

$billNumber = getParam('billNumber');
$fromDate = getParam('fromDate', date('Y-m-d'));
$toDate = getParam('toDate', date('Y-m-d'));


function getAllSales($billNumber, $fromDate, $toDate)
{
    //get all suppliers by tenant id
    $connection = ConnectionHelper::getConnection();

    //get all sales by tenant id and logged in user id
    $query = "select s.Id, c.CustomerName, s.BillNumber, s.Vat, s.Discount, s.GrossTotal, s.NetTotal, s.Remarks, u.FirstName, u.LastName, s.CreatedAt from sales s inner join user u on s.UserId = u.Id inner join customer c on s.CustomerId = c.Id where ((:search is null) or (s.BillNumber like :search)) and ((:fromDate is null) or (s.CreatedAt >= :fromDate)) and ((:toDate is null) or (s.CreatedAt <= :toDate)) and u.Id = :userId and c.TenantId = :tenantId";

    $statement = $connection->prepare($query);
    $tenantId = getTenantId();
    $statement->bindParam('tenantId', $tenantId, PDO::PARAM_INT);
    $statement->bindParam('search', $billNumber, PDO::PARAM_STR);
    $statement->bindParam('fromDate', $fromDate, PDO::PARAM_STR);
    $statement->bindParam('toDate', $toDate, PDO::PARAM_STR);
    $userId = getLoggedInUserId();
    $statement->bindParam('userId', $userId, PDO::PARAM_INT);

    $statement->execute();
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}


$tenantId = getTenantId();



$sales = getAllSales($billNumber, $fromDate, $toDate);



require_once '../includes/themeHeader.php';

?>

<div class="container-fluid">
    <div class="row non-printable">
        <div class="col-6">
            <a href="/sales/new.php" class="btn btn-primary"><i class="fas fa-fw fa-plus"></i> New Sale</a>
        </div>
        <div class="col-6">
            <button type="button" class="btn btn-secondary float-right" id="printBtn"><i class="fas fa-fw fa-print"></i> Print</button>
        </div>
    </div>
    <div class="card mt-2 shadow-lg">
        <div class="card-header bg-primary">
            <h4 class="card-title text-light">List of Sales</h4>
        </div>
        <div class="card-body">
            <form action="" method="get">
                <div class="row mb-3 non-printable">
                    <div class="col-2">
                        <label for="billNumber">Bill Number</label>
                        <input type="text" class="form-control" value="<?= $billNumber ?>" id="billNumber" name="billNumber">
                    </div>
                    <div class="col-2">
                        <label for="fromDate">From</label>
                        <input type="date" class="form-control" value="<?= $fromDate ?>" id="fromDate" name="fromDate">
                    </div>
                    <div class="col-2">
                        <label for="toDate">To</label>
                        <input type="date" class="form-control" value="<?= $toDate ?>" id="toDate" name="toDate">
                    </div>
                    <div class="col-2">
                        <label for="search">&nbsp;</label>
                        <button type="submit" class="btn btn-primary form-control"><i class="fas fa-fw fa-search"></i> Search</button>
                    </div>
                </div>
            </form>
            <!-- line -->
            <hr class="sidebar-divider non-printable">
            <?php renderMessages(); ?>
            <?php
            if (count($sales) > 0) :
            ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Bill Number</th>
                                <th scope="col">Supplier Name</th>
                                <th scope="col">VAT</th>
                                <th scope="col">Discount</th>
                                <th scope="col">Gross Total</th>
                                <th scope="col">Net Total</th>
                                <th scope="col">Remarks</th>
                                <th scope="col">Created At</th>
                                <th scope="col" class="non-printable">Action</th>
                            </tr>
                        </thead>
                        <tbody id="tbody">
                            <?php
                            $sn = 0;
                            foreach ($sales as $sale) :
                            ?>
                                <tr>
                                    <td scope="row">
                                        <?= ++$sn ?>
                                    </td>
                                    <td>
                                        <?= $sale['BillNumber'] ?>
                                    </td>
                                    <td>
                                        <?= $sale['CustomerName'] ?>
                                    </td>
                                    <td>
                                        <?= $sale['Vat'] ?>
                                    </td>
                                    <td>
                                        <?= $sale['Discount'] ?>
                                    </td>
                                    <td>
                                        <?= $sale['GrossTotal'] ?>
                                    </td>
                                    <td class="netTotal">
                                        <?= $sale['NetTotal'] ?>
                                    </td>
                                    <td>
                                        <?= $sale['Remarks'] ?>
                                    </td>
                                    <td>
                                        <?= $sale['CreatedAt'] ?>
                                    </td>
                                    <td class="non-printable">
                                        <a href="/sales/details.php?id=<?= $sale['Id'] ?>" class="btn btn-sm btn-info"><i class="fas fa-fw fa-info"></i> Details</a>
                                    </td>
                                </tr>
                            <?php
                            endforeach;
                            ?>
                        </tbody>
                    </table>
                </div>

            <?php
            else :
            ?>
                <div class="alert alert-warning">
                    <p class="text-center">💀 There are no sales in selected date.</p>
                </div>
            <?php
            endif;
            ?>

        </div>
    </div>
</div>

<script>
    //finding total netTotal amount of purchase
    let netTotal = document.querySelectorAll('.netTotal');
    let totalNetTotal = 0;
    netTotal.forEach((item) => {
        totalNetTotal += parseFloat(item.innerHTML);
    });
    let tr = document.querySelector('#tbody').appendChild(document.createElement('tr'));
    tr.classList.add('bg-primary', 'text-light');
    tr.innerHTML = `<td colspan="6" class="text-left">Total</td><td id="totalNetTotal" class="text-right">${totalNetTotal.toFixed(2)}</td><td colspan="4"></td>`;




    //print printable area on click
    const printBtn = document.getElementById('printBtn');
    printBtn.addEventListener('click', () => {
        printSection();
    });

    const printSection = () => {
        window.print();
    }
</script>

<?php
require_once '../includes/themeFooter.php';
?>