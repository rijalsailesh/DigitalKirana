<?php
require_once '../includes/functions.php';
require_once '../includes/Connection.php';
require_once '../includes/authorize_user.php';

function getMinimumStock()
{
    $connection = ConnectionHelper::getConnection();
    $query = "select p.ProductCode, p.ProductName, p.Description, p.Quantity, c.CategoryName from product p inner join category c on p.CategoryId = c.Id where p.Quantity <= p.MinimumQuantity and p.TenantId = :tenantId";
    $statement = $connection->prepare($query);
    $tenantId = getTenantId();
    $statement->bindParam('tenantId', $tenantId);
    $statement->execute();
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}

$result = getMinimumStock();


require_once '../includes/themeHeader.php';
?>

<div class="container-fluid">
    <div class="row non-printable">
        <div class="col-6">
            <a href="/supplier/create.php" class="btn btn-primary"><i class="fas fa-fw fa-arrow-left"></i> Go Back</a>
        </div>
        <div class="col-6">
            <button type="button" class="btn btn-secondary float-right" id="printBtn"><i class="fas fa-fw fa-print"></i> Print</button>
        </div>
    </div>
    <div class="card mt-2 shadow-lg">
        <div class="card-header bg-primary">
            <h4 class="card-title text-light">Minimum Stock</h4>
        </div>
        <div class="card-body">
            <?php
            if ($result == null) :
            ?>
                <div class="alert alert-warning">
                    <p class="text-center">💀 There are no minimum stock.</p>
                </div>
            <?php
            else :
            ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Code</th>
                                <th scope="col">Product Name</th>
                                <th scope="col">Category</th>
                                <th scope="col">Description</th>
                                <th scope="col">Quantity</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 1;
                            foreach ($result as $row) :
                            ?>
                                <tr>
                                    <th scope="row"><?= $i++ ?></th>
                                    <td><?= $row['ProductCode'] ?></td>
                                    <td><?= $row['ProductName'] ?></td>
                                    <td><?= $row['CategoryName'] ?></td>
                                    <td><?= $row['Description'] ?></td>
                                    <td><?= $row['Quantity'] ?></td>
                                </tr>
                            <?php
                            endforeach;
                            ?>
                        </tbody>
                    </table>
                </div>

        </div>

    <?php
            endif;
    ?>

    </div>
</div>

<script>
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