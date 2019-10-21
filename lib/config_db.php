<?php
$dbhost ='localhost';
$dbuser = 'root';
$dbpass = '';
$dbname='p297492_fss24';

/*$dbhost = 'p297492.mysql.ihc.ru';
$dbuser = 'p297492_fss24';
$dbpass = 'p555777q';
$dbname = 'p297492_fss24';*/

//Charset
$sqlchar='utf8';
try {
	$db = new PDO ( 'mysql:host=' . $dbhost . ';port=3306;dbname=' . $dbname, $dbuser, $dbpass);
	$db->query ( 'SET character_set_connection = '.$sqlchar );
	$db->query ( 'SET character_set_client = '.$sqlchar );
	$db->query ( 'SET character_set_results = '.$sqlchar );
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(PDOException $e) {
	die("Error: ".$e->getMessage());
}