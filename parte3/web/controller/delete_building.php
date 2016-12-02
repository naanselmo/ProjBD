<?php

// Include our utils.
include 'utils.php';

///////////////////////
// Fetch the fields! //
///////////////////////

// Get our response array.
$response = $_REQUEST;

// First check if the fields exist!
if (!multipleIsset($response, 'address')) {
  redirect('../index.php');
}

// Fetch the fields.
$address = $response['address'];

///////////////////////////////////
// Check if the fields are good! //
///////////////////////////////////

////////////////////////////////////
// Now that everything is checked //
////////////////////////////////////

require_once '../model/Building.php';
$building = Building::find($address);
if ($building == null || !$building->delete())
  redirect('../index.php');
redirect('../index.php', null, 'Edifício eliminado com sucesso!');