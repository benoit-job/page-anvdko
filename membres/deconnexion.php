<?php
session_start();

$_SESSION['membre']['statut'] = 'déconnexion'; 
header('Location: index.php');
 
?>