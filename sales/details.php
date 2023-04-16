<?php
require_once '../includes/functions.php';
require_once '../includes/Connection.php';
require_once 'includes/authorize.php';


// get purchase id
$salesId = getParam('id');

//get all purchased products by purchaseId
function getAllSoldProducts($salesId)
{
    $connection = ConnectionHelper::getConnection();
    $query = "select p.ProductCode, p.ProductName, sd.Quantity, sd.Rate, sd.TotalAmount from sales_details sd inner join product p on sd.ProductId = p.Id where sd.SalesId = :salesId";
    $statement = $connection->prepare($query);
    $statement->bindParam('salesId', $salesId, PDO::PARAM_INT);
    $statement->execute();
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}

//get purchase by purchaseId
function getSales($salesId)
{
    $connection = ConnectionHelper::getConnection();
    $query = "select c.CustomerName, c.Email, c.Phone, c.Address, s.Vat, s.Discount, s.BillNumber, s.GrossTotal, s.NetTotal, s.Remarks, s.CreatedAt, s.TenderAmount, s.ReturnAmount, t.LogoUrl, t.Name, t.Email, t.Phone, t.Address from sales s inner join customer c on s.CustomerId = c.Id inner join Tenants t on s.TenantId = t.Id where s.Id = :salesId";
    $statement = $connection->prepare($query);
    $statement->bindParam('salesId', $salesId, PDO::PARAM_INT);
    $statement->execute();
    $result = $statement->fetch(PDO::FETCH_ASSOC);
    return $result;
}


// get all purchased products
$soldProducts = getAllSoldProducts($salesId);
// get purchase
$sales = getSales($salesId);

require_once '../includes/themeHeader.php';
?>
<div class="container-fluid ">
    <div class="row non-printable">
        <div class="col-6">
            <a href="/sales" class="btn btn-primary"><i class="fas fa-fw fa-arrow-left"></i> View Sales</a>
        </div>
        <div class="col-6">
            <button type="button" class="btn btn-secondary float-right" id="printBtn"><i class="fas fa-fw fa-print"></i> Print</button>
        </div>
    </div>
    <div class="card mt-2">
        <div class="card-header bg-primary text-white non-printable">
            <h4 class="card-title">Sales Details</h4>
        </div>
        <div class="card-body bg-white printable">
            <!-- template for printing -->
            <div class="d-none printable">
                <div class="border mb-3 p-3">
                    <div class="row d-flex align-items-center">
                        <div class="col-12 mb-4">
                            <div class="text-center mb-3">
                                <?php
                                if ($sales['LogoUrl'] != '') :
                                ?>
                                    <img src="/assets/imgs/logos/<?= $sales['LogoUrl'] ?>" alt="" width="70" class="rounded-circle border border-4">
                                <?php
                                endif;
                                ?>
                            </div>
                            <h2 class="text-primary text-center mb-3"><?= $sales['Name'] ?></h2>
                            <div class="row">
                                <div class="col-4">
                                    <p class="my-0 text-center"><span style="font-weight: 800;">Phone:</span> <?= $sales['Phone'] ?></p>
                                </div>
                                <div class="col-4">
                                    <p class="my-0 text-center"><span style="font-weight: 800;">Address:</span> <?= $sales['Address'] ?></p>
                                </div>
                                <div class="col-4">
                                    <p class="my-0 text-center"><span style="font-weight: 800;">Email:</span> <?= $sales['Email'] ?></p>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card shadow-lg">
                <div class="card-header">
                    <h6 class="card-title text-primary">Products</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Product Code</th>
                                    <th scope="col">Product Name</th>
                                    <th scope="col">Rate</th>
                                    <th scope="col">Quantity</th>
                                    <th scope="col">Total Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sn = 0;
                                foreach ($soldProducts as $soldProduct) :
                                ?>
                                    <tr>
                                        <td scope="row">
                                            <?= ++$sn ?>
                                        </td>
                                        <td>
                                            <?= $soldProduct['ProductCode'] ?>
                                        </td>
                                        <td>
                                            <?= $soldProduct['ProductName'] ?>
                                        </td>
                                        <td>
                                            <?= $soldProduct['Rate'] ?>
                                        </td>
                                        <td>
                                            <?= $soldProduct['Quantity'] ?>
                                        </td>
                                        <td>
                                            <?= $soldProduct['TotalAmount'] ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <!-- add total row below -->
                                <tr>
                                    <td colspan="5" class="text-right">Total</td>
                                    <td><?= $sales['GrossTotal'] ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="card my-4 shadow-lg">
                        <div class="card-header">
                            <h6 class="card-title text-primary">Transaction Details</h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th scope="row">Date</th>
                                        <td><?= $sales['CreatedAt'] ?></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Bill Number</th>
                                        <td><?= $sales['BillNumber'] ?></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Gross Total</th>
                                        <td><?= $sales['GrossTotal'] ?></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Discount(%)</th>
                                        <td><?= $sales['Discount'] ?></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Vat(%)</th>
                                        <td><?= $sales['Vat'] ?></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Net Total</th>
                                        <td><?= $sales['NetTotal'] ?></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Tender Amount</th>
                                        <td><?= $sales['TenderAmount'] ?></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Return Amount</th>
                                        <td><?= $sales['ReturnAmount'] ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card mt-4">
                        <div class="card shadow-lg">
                            <div class="card-header">
                                <h6 class="card-title text-primary">Supplier Info</h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered">
                                    <tbody>
                                        <tr>
                                            <th scope="row">Name</th>
                                            <td><?= $sales['CustomerName'] ?></td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Email</th>
                                            <td><?= $sales['Email'] ?></td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Phone</th>
                                            <td><?= $sales['Phone'] ?></td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Address</th>
                                            <td><?= $sales['Address'] ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        </div>

    </div>
</div>


<style>
    @media print {
        .printable {
            display: block !important;
        }

        .non-printable {
            display: none !important;
        }

        #headerPrint {
            display: block !important;
        }
    }
</style>


<script>
    //print printable area on click
    const printBtn = document.getElementById('printBtn');
    printBtn.addEventListener('click', () => {
        printSection();
    });

    function printSection() {
        window.print();
    }
</script>

<?php
require_once '../includes/themeFooter.php';
?>