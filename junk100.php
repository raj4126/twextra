<?php 

$docroot = $_SERVER['DOCUMENT_ROOT'];

echo "test1";

file_put_contents($docroot.'/junk100.txt', 'test');

echo "<br>test2";

?>