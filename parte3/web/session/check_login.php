<?php
// Checks if there is any logged in user and if it is fetch the user.
// Put in top of every page that no user cant see unless he is logged in.
include 'model/User.php';
session_start();
if (!isset($_SESSION['user'])) {
  header('Location: login.php');
  exit();
}
$user = $_SESSION['user'];