<?php
session_start();
if($_SESSION['membre']['statut'] != 'actif') 
{
	header('location: index.php');
}
?>