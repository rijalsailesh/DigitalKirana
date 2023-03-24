<?php
require_once 'includes/functions.php';

if (isPost()) {
    session_destroy();
    header("Location: /");
}
?>