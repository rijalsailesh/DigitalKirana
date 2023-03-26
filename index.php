<?php
require_once 'includes/functions.php';
require_once 'includes/Connection.php';
require_once 'constants/TenantStatus.php';

if (checkAuth()) {
    header('Location: /dashboard.php');
}

$connection = ConnectionHelper::getConnection();

if (isPost()) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $query = "select * from user where username = :username";
    $statement = $connection->prepare($query);
    $statement->bindParam('username', $username);
    $statement->execute();
    $result = $statement->fetch(PDO::FETCH_ASSOC);
    if ($result) {
        $passwordHash = $result['PasswordHash'];
        if (password_verify($password, $passwordHash)) {

            if ($result['TenantId'] == null) {
                session_start();
                $_SESSION['user'] = $result;
                header('Location: /tenants.php');
            } else {
                $query = "select * from tenants where id = :tenantId";
                $statement = $connection->prepare($query);
                $statement->bindParam('tenantId', $result['TenantId']);
                $statement->execute();
                $tenantResult = $statement->fetch(PDO::FETCH_ASSOC);
                if ($tenantResult['Status'] == TenantStatus::$Pending) {
                    addErrorMessage("Your tenant is still pending");
                    header('Location: /');
                } else if ($tenantResult['Status'] == TenantStatus::$Rejected) {
                    addErrorMessage("Your tenant is rejected");
                    header('Location: /');
                } else {
                    if (!$result['Status']) {
                        addErrorMessage("You are no longer active");
                        header('Location: /');
                    } else {
                        session_start();
                        $_SESSION['user'] = $result;
                        addSuccessMessage("Login Successful");
                        $returnUrl = getParam("returnUrl", "/");
                        header('Location: ' . $returnUrl);
                    }
                }
            }
        } else {
            addErrorMessage("Invalid password");
            header('Location: /');
        }
    } else {
        addErrorMessage("Invalid username");
        header('Location: /');
    }
}
require_once 'includes/header.php';
?>

<div class="container-fluid min-vh-100 d-flex align-items-center">
    <div class="container">
        <div class="row py-5">
            <div class="col-8">
                <div class="row">
                    <div class="col-2">
                        <img class="img-fluid" src="assets/imgs/logo.png" height="100" width="100" />
                    </div>
                    <div class="col-10">
                        <h1 class="text-dark display-2 fw-bold">Digital Kirana</h1>
                    </div>
                </div>
                <p class="text-dark py-4" style="text-align:justify">Lorem ipsum dolor sit amet consectetur, adipisicing elit. Quaerat fugiat tempore commodi, nesciunt assumenda aspernatur deleniti molestias reiciendis voluptas enim eos repudiandae est temporibus eum in ducimus sed facilis sapiente officia debitis, officiis cupiditate itaque placeat. Accusantium necessitatibus, nostrum inventore, itaque repellendus eius est quae eos ex, quibusdam quaerat dolorum! Lorem ipsum dolor, sit amet consectetur adipisicing elit. Hic fuga quibusdam nihil tempora molestiae. Recusandae voluptatibus voluptas illum ex eius impedit deleniti mollitia ducimus! Molestias impedit expedita incidunt laborum dicta magni obcaecati. Ab dolor, mollitia exercitationem accusamus saepe accusantium quae, corrupti eum distinctio, earum nisi officiis tempore. Nobis, dignissimos molestiae?</p>
            </div>
            <div class="col-4">
                <div class="card bg-light shadow-lg">
                    <div class="card-body pt-5">
                        <?php renderMessages(); ?>
                        <form action="" method="post">
                            <div class="form-floating mb-3">
                                <input type="text" id="username" name="username" class="form-control" placeholder="Enter Username (*)" required>
                                <label for="floatingInput">Username (*)</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="password" id="password" name="password" class="form-control" placeholder="Enter Password (*)" required>
                                <label for="floatingPassword">Password (*)</label>
                            </div>
                            <div class="mb-3 form-check">
                                <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
                                <label class="form-check-label" for="flexCheckDefault">
                                    Remember Me
                                </label>
                            </div>

                            <div class="mb-3">
                                <button type="submit" class="btn btn-outline-primary w-100">Login</button>
                            </div>
                            <div class="mb-3">
                                <p class="text-center"><span>Want to register? </span><a href="/register.php">Register Here</a></p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once 'includes/footer.php';
?>