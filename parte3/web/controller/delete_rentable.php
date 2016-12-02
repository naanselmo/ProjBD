<?php

// Include our utils.
include 'utils.php';

///////////////////////
// Fetch the fields! //
///////////////////////

// Get our response array.
$response = $_REQUEST;

// First check if the fields exist!
if (!multipleIsset($response, 'address', 'code')) {
  redirect('../index.php');
}

// Fetch the fields.
$address = $response['address'];
$code = $response['code'];

///////////////////////////////////
// Check if the fields are good! //
///////////////////////////////////

if (!is_numeric($code))
  redirect('../index.php');

////////////////////////////////////
// Now that everything is checked //
////////////////////////////////////

require_once '../model/Rentable.php';
$rentable = Rentable::find($address, $code);
if ($rentable == null || !$rentable->delete())
  redirect('../index.php');
redirect('../index.php', null, 'Alugável eliminado com sucesso!');