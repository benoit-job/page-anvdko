<?php
session_start();
if($_SESSION['utilisateur']['statut'] != 'actif') 
{
	header('location: index.php');
}
?>