<?php

// Include our utils.
include 'utils.php';

///////////////////////
// Fetch the fields! //
///////////////////////

// Get our response array.
$response = $_REQUEST;

// First check if the fields exist!
if (!multipleIsset($response, 'address', 'code', 'start_date')) {
  redirect('../offers.php');
}

// Fetch the fields.
$address = $response['address'];
$code = $response['code'];
$start_date = $response['start_date'];

///////////////////////////////////
// Check if the fields are good! //
///////////////////////////////////

$start_date = parseDate($start_date);
if ($start_date == null)
  redirect('../offers.php');

////////////////////////////////////
// Now that everything is checked //
////////////////////////////////////

require_once '../model/Offer.php';
$offer = Offer::find($address, $code, $start_date);
if ($offer == null || !$offer->delete())
  redirect('../offers.php');
redirect('../offers.php', null, 'Oferta eliminada com sucesso!');