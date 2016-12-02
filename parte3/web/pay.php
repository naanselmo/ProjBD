<?php include 'session/check_login.php' ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Pagar</title>

  <script src="js/vendor/jquery.min.js"></script>
  <script src="js/vendor/bootstrap.min.js"></script>
  <script src="js/vendor/jquery.tablesorter.min.js"></script>
  <link rel="stylesheet" href="css/vendor/bootstrap.min.css">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
<nav class="navbar navbar-full navbar-dark bg-themed">
  <div class="container">
    <ul class="nav navbar-nav">
      <li class="nav-item"><a class="nav-link" href="index.php">Edifícios, Espaços e Postos</a></li>
      <li class="nav-item"><a class="nav-link" href="offers.php">Minhas ofertas</a></li>
      <li class="nav-item"><a class="nav-link" href="reserve.php">Reservar</a></li>
      <li class="nav-item active"><a class="nav-link" href="pay.php">Pagar</a></li>
      <li class="nav-item dropdown float-md-right">
        <a class="nav-link dropdown-toggle" href="#" id="user" data-toggle="dropdown" aria-haspopup="true"
           aria-expanded="false">
          <?php echo $user->getName() ?>
        </a>
        <div class="dropdown-menu" aria-labelledby="user">
          <a class="dropdown-item" href="session/logout.php">Logout</a>
        </div>
      </li>
    </ul>
  </div>
</nav>
<div class="container">
  <?php include 'session/response_view.php' ?>
  <?php
  require_once 'model/Reservation.php';
  $paid = [];
  $notPaid = [];
  // Filter the paid and not paid reservations.
  foreach (Reservation::allFrom($user->getNif()) as $reservation) {
    if ($reservation->isPaid()) {
      array_push($paid, $reservation);
    } else {
      // Only pay the states that are accepted!
      if ($reservation->getState()->isAccepted())
        array_push($notPaid, $reservation);
    }
  }
  ?>
  <div class="row">
    <div class="col-md-12">
      <h2>Reservas por pagar</h2>
      <?php if (count($notPaid) == 0): ?>
        <p>Não há nada por pagar!</p>
      <?php else: ?>
        <table class="table table-striped tablesorter">
          <thead>
          <tr>
            <th>Número</th>
            <th>Morada</th>
            <th>Código</th>
            <th>Início</th>
            <th>Fim</th>
            <th>Tarifa (€)</th>
            <th class="no-sort">Ações</th>
          </tr>
          </thead>
          <tbody>
          <?php foreach ($notPaid as $reservation): ?>
            <tr>
              <td><?php echo $reservation->getNumber() ?></td>
              <td><?php echo $reservation->getOffer()->getRentable()->getBuilding()->getAddress() ?></td>
              <td><?php echo $reservation->getOffer()->getRentable()->getCode() ?></td>
              <td><?php echo Database::formatDate($reservation->getOffer()->getStartDate()) ?></td>
              <td><?php echo Database::formatDate($reservation->getOffer()->getEndDate()) ?></td>
              <td class="number-column"><?php echo $reservation->getOffer()->getPrice() ?></td>
              <td>
                <a href=""
                   data-toggle="modal"
                   data-target="#paymentModal" data-number="<?php echo $reservation->getNumber() ?>">
                  Pagar
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <h2>Reservas pagas</h2>
      <?php if (count($paid) == 0): ?>
        <p>Não há nada pago!</p>
      <?php else: ?>
        <table class="table table-striped tablesorter">
          <thead>
          <tr>
            <th>Número</th>
            <th>Morada</th>
            <th>Código</th>
            <th>Início</th>
            <th>Fim</th>
            <th>Tarifa (€)</th>
            <th>Método</th>
            <th>Data</th>
          </tr>
          </thead>
          <tbody>
          <?php foreach ($paid as $reservation): ?>
            <tr>
              <td><?php echo $reservation->getNumber() ?></td>
              <td><?php echo $reservation->getOffer()->getRentable()->getBuilding()->getAddress() ?></td>
              <td><?php echo $reservation->getOffer()->getRentable()->getCode() ?></td>
              <td><?php echo Database::formatDate($reservation->getOffer()->getStartDate()) ?></td>
              <td><?php echo Database::formatDate($reservation->getOffer()->getEndDate()) ?></td>
              <td class="number-column"><?php echo $reservation->getOffer()->getPrice() ?></td>
              <td><?php echo $reservation->getPayment()->getMethod() ?></td>
              <td><?php echo Database::formatTimestamp($reservation->getPayment()->getTimestamp()) ?></td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>
  </div>
</div>
<div class="modal fade" id="paymentModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" title="Fechar">
          <span>&times;</span>
        </button>
        <h4 class="modal-title">Finalizar pagamento</h4>
      </div>
      <form action="controller/pay.php">
        <div class="modal-body">
          <input type="hidden" name="number" value="">
          <div class="form-group">
            <label for="method">Método de pagamento:</label>
            <input type="text" class="form-control" id="method" name="method"
                   placeholder="Método de pagamento">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary btn-themed">Pagar</button>
        </div>
      </form>
    </div>
  </div>
</div>
<script src="js/modal.js"></script>
<script src="js/main.js"></script>
</body>
</html>