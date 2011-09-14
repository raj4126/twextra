<?php
//validate access;
$docroot = $_SERVER ['DOCUMENT_ROOT'];
require_once $docroot . "/tw.lib.php";
require_once $docroot . "/models/twextra_model.php";
//validate_access_twetest();
validate_access_admin();


//enable only for testing
//phpinfo();

//apc_clear_cache();

//print_r(apc_cache_info());




?>