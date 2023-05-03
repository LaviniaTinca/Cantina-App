<?php

session_start();

if(isset($_SESSION['user_email']))
{
	unset($_SESSION['user_email']);
	session_destroy();
}

header("Location: login.php");
die;