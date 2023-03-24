<?php
require_once 'includes/functions.php';
require_once 'includes/Connection.php';

if (!existDefaultUser()) {
    seedDefaultUser();
    echo "Default user created";
}else{
    echo "Default user already exists";
}
