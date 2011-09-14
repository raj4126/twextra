<?php


$docroot = $_SERVER['DOCUMENT_ROOT'];
require_once  $docroot."/sessions.php"; 

/**
 * @file
 * Clears PHP sessions and redirects to the connect page.
 */
 
/* Load and clear sessions */
//$sess = new SessionManager();
session_start();
session_destroy();
 
/* Redirect to page with the connect to Twitter option. */
header('Location: ./connect.php');
