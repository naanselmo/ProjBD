<?php

// Include our utils.
include 'utils.php';

///////////////////////
// Fetch the fields! //
///////////////////////

// Get our response array.
$response = $_REQUEST;

// First check if the fields exist!
if (!multipleIsset($response, 'address', 'code', 'image', 'nif', 'workspace_code')) {
  redirect('../index.php');
}

// Fetch the fields.
$address = $response['address'];
$code = $response['code'];
$image = $response['image'];
$nif = $response['nif'];
$workspace_code = $response['workspace_code'];

///////////////////////////////////
// Check if the fields are good! //
///////////////////////////////////

if (strlen(trim($code)) == 0)
  redirect('../index.php', 'Código inválido!');
if (strlen(trim($image)) == 0)
  redirect('../index.php', 'URL da imagem do posto inválido!');

////////////////////////////////////
// Now that everything is checked //
////////////////////////////////////

require_once '../model/Workstation.php';
if (Workstation::create($address, $code, $image, $nif, $workspace_code) == null)
  redirect('../index.php',
      "Já existe um posto com esse código dentro do espaço $workspace_code presente no edifício $address!");
redirect('../index.php', null, 'Posto criado com sucesso!');