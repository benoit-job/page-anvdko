<?php
session_start();
$_SESSION['utilisateur'] = 'test';
include('api_recap_exceptionnels.php');
