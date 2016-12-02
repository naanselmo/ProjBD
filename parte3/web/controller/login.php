<?php

// Include our utils.
include 'utils.php';

///////////////////////
// Fetch the fields! //
///////////////////////

// Get our response array.
$response = $_REQUEST;

// First check if the fields exist!
if (!multipleIsset($response, 'nif')) {
  redirect('../login.php');
}

// Fetch the login fields.
$nif = $response['nif'];

///////////////////////////////////
// Check if the fields are good! //
///////////////////////////////////

////////////////////////////////////
// Now that everything is checked //
////////////////////////////////////

require_once "../model/User.php";
$user = User::find($nif);
if ($user == null)
  redirect('../login.php', 'Não existe utilizador com tal NIF!');

session_start();
$_SESSION['user'] = $user;
redirect('../index.php');


