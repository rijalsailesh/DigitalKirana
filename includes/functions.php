<?php
session_start();
$u_success_message = $_SESSION['success_messagex'] ?? null;
$u_error_message = $_SESSION['error_messagex'] ?? null;

unset($_SESSION['success_messagex']);
unset($_SESSION['error_messagex']);

function existDefaultUser()
{
    $defaultUsername = "super.admin";
    $connection = ConnectionHelper::getConnection();
    $query = "select count(*) from user where username = :username";
    $statement = $connection->prepare($query);
    $statement->bindParam('username', $defaultUsername);
    $statement->execute();
    $result = $statement->fetchColumn();

    if ($result == 0) {
        return false;
    } else {
        return true;
    }
}

function seedDefaultUser()
{
    $defaultUsername = "super.admin";
    $defaultEmail = "super.admin@gmail.com";
    $defaultFirstName = "Super";
    $defaultLastName = "Admin";
    $defaultStatus = true;
    $defaultRole = "Admin";

    $connection = ConnectionHelper::getConnection();
    $defaultPasswordHash = password_hash("Admin@123", PASSWORD_DEFAULT);
    $query = "insert into user (Username, Email, FirstName, LastName, Status, Role, PasswordHash, CreatedAt) values (:username, :email, :firstName, :lastName, :status, :role, :passwordHash, :createdAt)";
    $statement = $connection->prepare($query);
    $statement->bindParam('username', $defaultUsername);
    $statement->bindParam('email', $defaultEmail);
    $statement->bindParam('firstName', $defaultFirstName);
    $statement->bindParam('lastName', $defaultLastName);
    $statement->bindParam('status', $defaultStatus);
    $statement->bindParam('role', $defaultRole);
    $statement->bindParam('createdAt', date('Y-m-d H:i:s'));
    $statement->bindParam('passwordHash', $defaultPasswordHash);
    $statement->execute();
    $result = $statement->rowCount();
    if ($result > 0) {
        return true;
    } else {
        return false;
    }
}

function checkAuth()
{
    if (isset($_SESSION['user']) && $_SESSION['user'] != null) {
        return true;
    } else {
        return false;
    }
}

function getTenantId()
{
    return $_SESSION['user']['TenantId'];
}

function getLoggedInUser()
{
    return $_SESSION['user'];
}

function getLoggedInUserRole()
{
    return $_SESSION['user']['Role'];
}

function isAdmin()
{
    return $_SESSION['user']['Role'] == 'Admin';
}

function isPost()
{
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}


function getParam($name, $defaultValue = null)
{
    $value = $_GET[$name] ?? null;
    if ($value === "") {
        $value = null;
    }
    return $value ?? $defaultValue;
}

function dd($value)
{
    var_dump($value);
    die;
}


function addSuccessMessage($message)
{
    $_SESSION['success_messagex'] = $message;
}

function addErrorMessage($message)
{
    $_SESSION['error_messagex'] = $message;
}

function renderMessages()
{
    global $u_success_message;
    global $u_error_message;
    $u_success_message != null && renderMessage($u_success_message, 'success');
    $u_error_message != null && renderMessage($u_error_message, 'error');
}

function renderMessage($message, $type)
{
    if ($type == 'success') :
?>
        <div class="alert alert-success" role="alert">
            🎉
            <?= $message ?>
        </div>
    <?php
    else :
    ?>
        <div class="alert alert-danger" role="alert">
            💀
            <?= $message ?>
        </div>
<?php
    endif;
}
?>