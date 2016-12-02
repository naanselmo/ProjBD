<?php

// Include our utils.
include 'utils.php';

///////////////////////
// Fetch the fields! //
///////////////////////

// Get our response array.
$response = $_REQUEST;

// First check if the fields exist!
if (!multipleIsset($response, 'address', 'code', 'start_date', 'end_date', 'price')) {
  redirect('../offers.php');
}

// Fetch the fields.
$address = $response['address'];
$code = $response['code'];
$start_date = $response['start_date'];
$end_date = $response['end_date'];
$price = $response['price'];

///////////////////////////////////
// Check if the fields are good! //
///////////////////////////////////

$start_date = parseDate($start_date);
if ($start_date == null)
  redirect('../offers.php', 'Data de início inválida!');
$end_date = parseDate($end_date);
if ($end_date == null)
  redirect('../offers.php', 'Data de fim inválida!');
if (!is_numeric($price))
  redirect('../offers.php', 'Tarifa inválida!');

////////////////////////////////////
// Now that everything is checked //
////////////////////////////////////

require_once '../model/Offer.php';
if (Offer::create($address, $code, $start_date, $end_date, $price) == null)
  redirect('../offers.php', 'Não foi possivel criar a oferta. Verifique se o intervalo de datas não colide com nenhuma outra oferta do mesmo alugável.');
redirect('../offers.php', null, 'Oferta criada com successo!');