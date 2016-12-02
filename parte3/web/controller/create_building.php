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

if (strlen(trim($address)) == 0)
  redirect('../index.php', 'Morada inválida!');

////////////////////////////////////
// Now that everything is checked //
////////////////////////////////////

require_once '../model/Building.php';
if (Building::create($address) == null)
  redirect('../index.php', 'Já existe um edifício com essa morada!');
redirect('../index.php', null, 'Edifício criado com sucesso!');