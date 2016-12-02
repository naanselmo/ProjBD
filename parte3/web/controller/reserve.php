<?php

// Include our utils.
include 'utils.php';

///////////////////////
// Fetch the fields! //
///////////////////////

// Get our response array.
$response = $_REQUEST;

// First check if the fields exist!
if (!multipleIsset($response, 'address', 'code', 'start_date', 'nif', 'number')) {
  redirect('../reserve.php');
}

// Fetch the fields.
$address = $response['address'];
$code = $response['code'];
$start_date = $response['start_date'];
$nif = $response['nif'];
$number = $response['number'];

///////////////////////////////////
// Check if the fields are good! //
///////////////////////////////////

$start_date = parseDate($start_date);
if ($start_date == null)
  redirect('../reserve.php');

////////////////////////////////////
// Now that everything is checked //
////////////////////////////////////

require_once '../model/User.php';
require_once '../model/Offer.php';
$user = User::find($nif);
if ($user == null)
  redirect('../reserve.php');
$offer = Offer::find($address, $code, $start_date);
if ($offer == null)
  redirect('../reserve.php');
if ($offer->reserve($user, $number) == null)
  redirect('../reserve.php', 'Já existe uma reserva com esse número! Por favor utilize outro.');
redirect('../reserve.php', null, 'Reserva efectuada com successo! Por favor espere que seja aceite pelo arrendatário.');