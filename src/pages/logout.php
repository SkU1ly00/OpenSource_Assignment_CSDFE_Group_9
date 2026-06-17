<?php
/**
 * Logout Page
 */

session_start();
require_once '../../config/config.php';
require_once '../../src/classes/Auth.php';
require_once '../../src/functions.php';

$auth = new Auth();
$auth->logout();

header('Location: ../../login.php');
exit();
?>