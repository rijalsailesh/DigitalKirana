<?php
require_once '../includes/header.php';
require_once '../includes/functions.php';

// check authentication
if (!isset($_SESSION['user']) || $_SESSION['user'] == null) {
    header("Location: /?returnUrl=" . $_SERVER['REQUEST_URI']);
}

if (isPost()) {
    session_destroy();
    header("Location: /");
}
?>
<div class="container mx-auto mt-4 row">
    <?php renderMessages(); ?>
    <div class="col-6">
        <h1>Category</h1>
    </div>
    <div class="col-6 text-end">
        <form method="post" action="">
            <button type="submit" class="btn btn-danger">Logout</button>
        </form>
    </div>
</div>