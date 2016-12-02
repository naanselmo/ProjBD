<?php

// Include our utils.
include 'utils.php';

///////////////////////
// Fetch the fields! //
///////////////////////

// Get our response array.
$response = $_REQUEST;

// First check if the fields exist!
if (!multipleIsset($response, 'address', 'code', 'image', 'nif')) {
  redirect('../index.php');
}

// Fetch the fields.
$address = $response['address'];
$code = $response['code'];
$image = $response['image'];
$nif = $response['nif'];

///////////////////////////////////
// Check if the fields are good! //
///////////////////////////////////

if (strlen(trim($code)) == 0)
  redirect('../index.php', 'Código inválido!');
if (strlen(trim($image)) == 0)
  redirect('../index.php', 'URL da imagem do espaço inválido!');

////////////////////////////////////
// Now that everything is checked //
////////////////////////////////////

require_once '../model/Workspace.php';
if (Workspace::create($address, $code, $image, $nif) == null)
  redirect('../index.php', "Já existe um espaço com esse código dentro do edifício $address!");
redirect('../index.php', null, 'Espaço criado com sucesso!');