<?php
// Checks if there is any logged in user and if it is redirect to index.
session_start();
if (isset($_SESSION['user'])) {
  header('Location: index.php');
  exit();
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Login</title>
  <link rel="stylesheet" href="css/vendor/bootstrap.min.css">
  <link rel="stylesheet" href="css/style.css">
  <style>
    .login {
      position: absolute;
      display: flex;
      top: 0;
      bottom: 0;
      left: 0;
      right: 0;
      align-items: center;
      justify-content: center;
      background-color: #154254;
    }

    .login-container {
      background-color: white;
      padding: 40px;
    }

    .login-container input[type="submit"] {
      width: 100%;
    }
  </style>
</head>
<body>
<div class="login">
  <div class="login-container">
    <form action="controller/login.php">
      <?php include 'session/response_view.php' ?>
      <div class="form-group">
        <label for="nif">NIF:</label>
        <input type="text" class="form-control" id="nif" name="nif" placeholder="Introduzir NIF">
      </div>
      <input type="submit" class="btn btn-primary btn-themed" value="Login">
    </form>
  </div>
</div>
</body>
</html>