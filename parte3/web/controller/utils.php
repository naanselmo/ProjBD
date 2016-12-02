<?php

/**
 * Check if multiple keys are set in a given array.
 * @param $array array The array to check the keys of.
 * @param string[] ...$names The keys to be checked.
 * @return bool true if they are all set. Otherwise false.
 */
function multipleIsset($array, ...$names)
{
  foreach ($names as $name) {
    if (!isset($array[$name])) return false;
  }
  return true;
}

/**
 * Redirects to a page with the given relative url.
 * @param $relativeUrl string The relative url.
 * @param $error string|null The error message.
 * @param $success string|null The success message.
 */
function redirect($relativeUrl, $error = null, $success = null)
{
  if ($error != null || $success != null) session_start();
  if ($error != null) {
    $_SESSION['error'] = $error;
  }
  if ($success != null) {
    $_SESSION['success'] = $success;
  }
  header("Location: $relativeUrl");
  exit();
}

/**
 * Parses a date with the format YYYY-MM-DD to a DateTime instance.
 * @param $date string The date as a string.
 * @return DateTime The parsed DateTime.
 */
function parseDate($date)
{
  return DateTime::createFromFormat('Y-m-d', $date);
}