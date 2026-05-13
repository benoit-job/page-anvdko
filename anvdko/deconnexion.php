<?php
session_start();

$_SESSION['utilisateur']['statut'] = 'déconnexion'; 
header('Location: index.php');
 
?>