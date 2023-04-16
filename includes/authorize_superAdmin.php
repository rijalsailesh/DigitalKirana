<?php
require_once('Connection.php');
require_once('functions.php');

if (!checkAuth()) {
    header("Location: /?returnUrl=" . $_SERVER['REQUEST_URI']);
}

$tenantId = getTenantId();

//get role of current user
$role = getLoggedInUserRole();


if ($tenantId != null) {
    header("Location: /error/accessDenied.php");
}
