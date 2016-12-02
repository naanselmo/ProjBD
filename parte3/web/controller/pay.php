<?php

// Include our utils.
include 'utils.php';

///////////////////////
// Fetch the fields! //
///////////////////////

// Get our response array.
$response = $_REQUEST;

// First check if the fields exist!
if (!multipleIsset($response, 'number', 'method')) {
  redirect('../pay.php');
}

// Fetch the fields.
$number = $response['number'];
$date = new DateTime();
$method = $response['method'];

///////////////////////////////////
// Check if the fields are good! //
///////////////////////////////////

if (strlen(trim($method)) == 0)
  redirect('../pay.php', 'Método de pagamento inválido!');

////////////////////////////////////
// Now that everything is checked //
////////////////////////////////////

require_once '../model/Reservation.php';
if (Reservation::pay($number, $date, $method) == null)
  redirect('../pay.php', 'Algo correu mal! Por favor contacte o administrador do sistema.');
redirect('../pay.php', null, 'Reserva paga com sucesso!');