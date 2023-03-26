<?php
require_once '../includes/functions.php';

// check authentication
if (!checkAuth()) {
    header("Location: /?returnUrl=" . $_SERVER['REQUEST_URI']);
}

if (isPost()) {
    session_destroy();
    header("Location: /");
}
require_once '../includes/themeHeader.php';
?>

<div class="container-fluid">
    <a href="user/create.php" class="btn btn-primary"><i class="fas fa-fw fa-plus"></i> New Category</a>
    <div class="card mt-2">
        <div class="card-header bg-primary">
            <h4 class="card-title text-light">List of Categories</h4>
        </div>
        <div class="card-body">
            <?php renderMessages(); ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Name</th>
                            <th scope="col">Description</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="">
                            <td scope="row">1</td>
                            <td>R1C2</td>
                            <td>R1C3</td>
                            <td>
                                <a href="#" class="btn btn-sm btn-primary">Edit</a>
                                <a href="#" class="btn btn-sm btn-danger">Delete</a>
                            </td>
                        </tr>
                        <tr class="">
                            <td scope="row">2</td>
                            <td>Item</td>
                            <td>Item</td>
                            <td>
                                <a href="#" class="btn btn-sm btn-primary">Edit</a>
                                <a href="#" class="btn btn-sm btn-danger">Delete</a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

<?php
require_once '../includes/themeFooter.php';
?>