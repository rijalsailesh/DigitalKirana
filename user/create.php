<?php
require_once '../includes/functions.php';
require_once '../includes/Connection.php';
require_once '../constants/Role.php';

// check authentication
if (!checkAuth()) {
    header("Location: /?returnUrl=" . $_SERVER['REQUEST_URI']);
}

// check if user is admin
if (!isAdmin()) {
    header("Location: /error/accessDenied.php");
}

// check if form is submitted
if (isPost()) {
    // get form data
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $role = $_POST['role'];
    $password = $_POST['password'];


    if (checkUsernameExists($username)) {
        AddErrorMessage("Username already exists");
    } else if (checkEmailExists($email)) {
        AddErrorMessage("Email already exists");
    } else {
        // create user
        $connection = ConnectionHelper::getConnection();
        $query = "INSERT INTO user (FirstName, LastName, Email, Username, Phone, Address, Role, PasswordHash, TenantId, CreatedAt) VALUES (:firstName, :lastName, :email, :username, :phone, :address, :role, :passwordHash, :tenantId, :createdAt)";
        $statement = $connection->prepare($query);
        $statement->bindParam(':firstName', $firstName, PDO::PARAM_STR);
        $statement->bindParam(':lastName', $lastName, PDO::PARAM_STR);
        $statement->bindParam(':email', $email, PDO::PARAM_STR);
        $statement->bindParam(':username', $username, PDO::PARAM_STR);
        $statement->bindParam(':phone', $phone, PDO::PARAM_STR);
        $statement->bindParam(':address', $address, PDO::PARAM_STR);
        $statement->bindParam(':role', $role, PDO::PARAM_STR);
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $statement->bindParam(':passwordHash', $passwordHash, PDO::PARAM_STR);
        $tenantId = getTenantId(); // get tenant id from session
        $statement->bindParam(':tenantId', $tenantId, PDO::PARAM_INT);
        $createdAt = date('Y-m-d H:i:s');
        $statement->bindParam(':createdAt', $createdAt, PDO::PARAM_STR);
        $statement->execute();
        $result = $statement->rowCount();
        if ($result > 0) {
            AddSuccessMessage("User created successfully");
            header("Location: /user");
        } else {
            AddErrorMessage("Failed to create user");
        }
        // redirect to user list
    }
}

// function to check username already exists or not
function checkUsernameExists($username)
{
    $connection = ConnectionHelper::getConnection();
    $query = "SELECT * FROM user WHERE username = :username";
    $statement = $connection->prepare($query);
    $statement->bindParam(':username', $username, PDO::PARAM_STR);
    $statement->execute();
    $user = $statement->fetch();
    return $user;
}

// function to check email already exists or not
function checkEmailExists($email)
{
    $connection = ConnectionHelper::getConnection();
    $query = "SELECT * FROM user WHERE email = :email";
    $statement = $connection->prepare($query);
    $statement->bindParam(':email', $email, PDO::PARAM_STR);
    $statement->execute();
    $user = $statement->fetch();
    return $user;
}


require_once '../includes/themeHeader.php';
?>

<div class="container-fluid">
    <form action="" method="post">

        <a href="/user" class="btn btn-danger"><i class="fas fa-fw fa-arrow-left"></i> Back to Users</a>
        <div class="card mt-2  shadow-lg">
            <div class="card-header bg-primary">
                <h4 class="card-title text-light">Create User</h4>
            </div>
            <div class="card-body bg-gray">
                <?php renderMessages(); ?>
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label for="firstName">First Name</label>
                        <input type="text" name="firstName" id="firstName" class="form-control" placeholder="First Name" required>
                    </div>
                    <div class="col-md-6 mb-4">
                        <label for="lastName">Last Name</label>
                        <input type="text" name="lastName" id="lastName" class="form-control" placeholder="Last Name" required>
                    </div>
                    <div class="col-md-6 mb-4">
                        <label for="email">Email</label>
                        <input type="text" name="email" id="email" class="form-control" placeholder="Email" required>
                    </div>
                    <div class="col-md-6 mb-4">
                        <label for="username">Username</label>
                        <input type="text" name="username" id="username" class="form-control" placeholder="Username" required>
                    </div>
                    <div class="col-md-6 mb-4">
                        <label for="phone">Phone</label>
                        <input type="text" name="phone" id="phone" class="form-control" placeholder="Phone" required>
                    </div>
                    <div class="col-md-6 mb-4">
                        <label for="address">Address</label>
                        <input type="text" name="address" id="address" class="form-control" placeholder="Address" required>
                    </div>
                    <div class="col-md-6 mb-4">
                        <label for="role">Role</label>
                        <select name="role" id="role" class="form-control" required>
                            <option value="0">Select Role</option>
                            <option value="<?= Role::$User ?>">User</option>
                            <option value="<?= Role::$SalesPerson ?>">Sales Person</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-4">
                        <label for="password">Password</label>
                        <input type="password" name="password" id="password" class="form-control" placeholder="Password" required>
                    </div>

                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary"><i class="fas fa-fw fa-save"></i> Save</button>
            </div>
        </div>
    </form>
</div>

<?php
require_once '../includes/themeFooter.php';
?>