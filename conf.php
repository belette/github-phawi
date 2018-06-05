<?php
$bdd_host="127.0.0.1";
$bdd_user="root";
$bdd_pass="rienrien";
$bdd_bddName="pihome";
try
{
    $bdd = new PDO('mysql:host='.$bdd_host.';dbname='.$bdd_bddName , $bdd_user, $bdd_pass, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
    
}
catch (PDOException $e) 
{
        print_r( "erreur SQL: ".$e->getMessage());
        die('Erreur dans la requete SQL: ' . $e->getMessage());
}