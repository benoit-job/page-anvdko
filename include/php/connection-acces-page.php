<?php
session_start();
if($_SESSION['utilisateurs']['statut'] != 'actif') 
{
	header('location: index.php');
}

?>