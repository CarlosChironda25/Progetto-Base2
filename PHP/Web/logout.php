<?php
session_start();
session_unset();
session_destroy();
require_once 'ControllerMongoDBLogger.php';
$logger = new ControllerMongoDBLogger();

$logger->logEvent($_SESSION['user'],'logout ', [ 'email_utente'=>$_SESSION['Email']]);
header("Location: index.php");
exit();
