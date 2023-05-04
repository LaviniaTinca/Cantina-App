<?php
$db_name = 'mysql:host=localhost;dbname=shop';
$db_user = 'root';
$db_password = '';

$conn = new PDO($db_name, $db_user, $db_password);


function unique_id()
{
	$chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$charLength = strlen($chars);
	$randomString = '';
	for ($i = 0; $i < 20; $i++) {
		$randomString .= $chars[mt_rand(0, $charLength - 1)];
	}
	return $randomString;
}

function check_login($conn)
{
	if (isset($_SESSION['user_email'])) {
		$email = $_SESSION['user_email'];
		$select_user = $conn->prepare("SELECT * FROM `users` WHERE email = ?");
		$select_user->execute([$email]);

		if ($select_user->rowCount() > 0) {
			$user_data = $select_user->fetch(PDO::FETCH_ASSOC);
			return $user_data;
		}
	}

	//redirect to login
	header("Location: login.php");
	die;
}
//function for user_type select
function test_input($data)
{
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
	return $data;
}
