<?php include 'session/check_login.php' ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Reservar</title>

  <script src="js/vendor/jquery.min.js"></script>
  <script src="js/vendor/bootstrap.min.js"></script>
  <script src="js/vendor/jquery.tablesorter.min.js"></script>
  <link rel="stylesheet" href="css/vendor/bootstrap-flex.min.css">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
<nav class="navbar navbar-full navbar-dark bg-themed">
  <div class="container">
    <ul class="nav navbar-nav">
      <li class="nav-item"><a class="nav-link" href="index.php">Edifícios, Espaços e Postos</a></li>
      <li class="nav-item"><a class="nav-link" href="offers.php">Minhas ofertas</a></li>
      <li class="nav-item active"><a class="nav-link" href="reserve.php">Reservar</a></li>
      <li class="nav-item"><a class="nav-link" href="pay.php">Pagar</a></li>
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
  <div class="row">
    <div class="col-md-12">
      <h2>Ofertas atuais</h2>
    </div>
  </div>
  <?php require_once 'model/Offer.php' ?>
  <?php $offers = Offer::allAvailable(); ?>
  <?php if (count($offers) == 0): ?>
    <div class="row">
      <div class="col-md-12">
        <p>Não há ofertas disponiveis!</p>
      </div>
    </div>
  <?php else: ?>
    <div class="row">
      <?php foreach ($offers as $index => $offer): ?>
        <?php $address = $offer->getRentable()->getBuilding()->getAddress(); ?>
        <?php $code = $offer->getRentable()->getCode(); ?>
        <?php $start_date = Database::formatDate($offer->getStartDate()); ?>
        <?php $end_date = Database::formatDate($offer->getEndDate()); ?>
        <?php $nif = $user->getNif(); ?>
        <div class="col-md-4">
          <div class="card">
            <img class="card-img-top img-fluid" src="<?php echo $offer->getRentable()->getImage() ?>"
                 alt="Imagem">
            <div class="card-block">
              <h5 class="card-title"><?php echo $address ?> - <?php echo $code ?></h5>
              <p class="card-subtitle text-muted">
                Arrendado por <?php echo $offer->getRentable()->getLeaser()->getName() ?>
              </p>
            </div>
            <ul class="list-group list-group-flush">
              <li class="list-group-item">Data início: <?php echo $start_date ?></li>
              <li class="list-group-item">Data fim: <?php echo $end_date ?></li>
              <li class="list-group-item">Tarifa: <?php echo $offer->getPrice() ?>€</li>
            </ul>
            <div class="card-block">
              <a href="" class="btn btn-primary btn-themed"
                 data-toggle="modal"
                 data-target="#reserveModal"
                 data-address="<?php echo $address ?>"
                 data-code="<?php echo $code ?>"
                 data-start_date="<?php echo $start_date ?>">
                Reservar
              </a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>
<div class="modal fade" id="reserveModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" title="Fechar">
          <span>&times;</span>
        </button>
        <h4 class="modal-title">Efetuar reserva</h4>
      </div>
      <form action="controller/reserve.php">
        <div class="modal-body">
          <input type="hidden" name="address" value="">
          <input type="hidden" name="code" value="">
          <input type="hidden" name="start_date" value="">
          <input type="hidden" name="nif" value="<?php echo $user->getNif() ?>">
          <div class="form-group">
            <label for="number">Número da reserva:</label>
            <input type="text" class="form-control" id="number" name="number"
                   placeholder="Número da reserva">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary btn-themed">Reservar</button>
        </div>
      </form>
    </div>
  </div>
</div>
<script src="js/modal.js"></script>
</body>
</html>