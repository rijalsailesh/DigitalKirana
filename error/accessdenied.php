<?php
require_once('../includes/functions.php');
require_once('../includes/header.php');

// If the user is not logged in, redirect to the login page
if (!checkAuth()) {
    header('Location: /');
    exit;
}

?>

<h1 class="text-center mt--4">Access Denied</h1>
<p class="text-center">You do not have permission to access this page.</p>

<!-- go back button -->
<div class="text-center">
    <a href="javascript:history.back()" class="btn btn-outline-primary">Go Back</a>
</div>

<?php
require_once('../includes/footer.php');
?>