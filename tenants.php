<?php
require_once('includes/functions.php');
require_once('includes/Connection.php');
require_once('constants/TenantStatus.php');
require_once('includes/authorize_superAdmin.php');


$connection = ConnectionHelper::getConnection();

$query = "SELECT * FROM tenants";

$statement = $connection->prepare($query);
$statement->execute();
$result = $statement->fetchAll(PDO::FETCH_ASSOC);

//approve tenant
if (isPost()) {
    $id = $_POST['id'];
    $query = "UPDATE tenants SET Status = :status WHERE Id = :id";
    $statement = $connection->prepare($query);
    $statement->bindParam('id', $id, PDO::PARAM_INT);
    if (isset($_POST['approve'])) {
        $status = TenantStatus::$Approved;
    } else if (isset($_POST['reject'])) {
        $status = TenantStatus::$Rejected;
    }
    $statement->bindParam('status', $status);
    $statement->execute();
    addSuccessMessage("Tenant updated successfully");
    header("Location: /tenants.php");
}

require_once('includes/header.php');
?>


<!-- ======= Services Section ======= -->
<section id="services" class="services section-bg">
    <div class="container" data-aos="fade-up">

        <div class="section-title">
            <h2>List of Tenants</h2>
            <div class="d-flex gap-2 justify-content-center">
                <form method="post" action="/logout.php" class="text-start">
                    <button type="submit" class="btn btn-danger">Logout</button>
                </form>
                <a href="/" class="btn btn-primary">Home</a>
            </div>
        </div>

        <div class="icon-box text-center">
            <div class="container">
                <?php renderMessages(); ?>

                <div>
                    <div class="table-responsive mt-4">
                        <table class="table table-bordered table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th scope="col">S.N.</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Phone</th>
                                    <th scope="col">Address</th>
                                    <th scope="col">CreatedAt</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sn = 0;
                                foreach ($result as $tenant) :
                                ?>
                                    <tr>
                                        <th><?= ++$sn ?></th>
                                        <td><?= $tenant['Name'] ?></td>
                                        <td><?= $tenant['Email'] ?></td>
                                        <td><?= $tenant['Phone'] ?></td>
                                        <td><?= $tenant['Address'] ?></td>
                                        <td><?= $tenant['CreatedAt'] ?></td>
                                        <td><span class='<?= $tenant['Status']=="Accepted"?"bg-success":"bg-secondary" ?>  text-white p-1 rounded-2'>
                                                <?= $tenant['Status'] ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php
                                            if ($tenant['Status'] == TenantStatus::$Pending) :
                                            ?>
                                                <form method="post" action="">
                                                    <input type="hidden" name="id" value="<?= $tenant['Id'] ?>">
                                                    <button type="submit" name="approve" class="btn btn-sm btn-outline-primary">Approve</button>
                                                    <button type="submit" name="reject" class="btn btn-sm btn-outline-danger">Reject</button>
                                                </form>
                                            <?php
                                            endif;
                                            ?>
                                        </td>
                                    <?php
                                endforeach;
                                    ?>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section><!-- End Services Section -->




<?php
require_once 'includes/footer.php';
?>