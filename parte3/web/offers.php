<?php include 'session/check_login.php' ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Edificios, Espacos e Postos</title>

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
      <li class="nav-item active"><a class="nav-link" href="offers.php">Minhas ofertas</a></li>
      <li class="nav-item"><a class="nav-link" href="reserve.php">Reservar</a></li>
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
      <h2>Minhas ofertas</h2>
      <?php require_once 'model/Offer.php' ?>
      <?php $offers = Offer::allFrom($user->getNif()) ?>
      <?php if (count($offers) == 0): ?>
        <p>De momento não tem nenhuma oferta!</p>
      <?php else: ?>
        <table class="table table-striped tablesorter">
          <thead>
          <tr>
            <th>Morada</th>
            <th>Código</th>
            <th>Início</th>
            <th>Fim</th>
            <th>Tarifa (€)</th>
            <th class="no-sort">Ações</th>
          </tr>
          </thead>
          <tbody>
          <?php foreach ($offers as $offer): ?>
            <tr>
              <?php $address = $offer->getRentable()->getBuilding()->getAddress(); ?>
              <?php $code = $offer->getRentable()->getCode(); ?>
              <?php $start_date = Database::formatDate($offer->getStartDate()) ?>
              <td><?php echo $address ?></td>
              <td><?php echo $code ?></td>
              <td><?php echo $start_date ?></td>
              <td><?php echo Database::formatDate($offer->getEndDate()) ?></td>
              <td class="number-columnTarif"><?php echo $offer->getPrice() ?></td>
              <td>
                <a href="controller/delete_offer.php?address=<?php echo $address ?>&code=<?php echo $code ?>&start_date=<?php echo $start_date ?>">
                  Remover
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
      <?php require_once 'model/Rentable.php' ?>
      <?php $rentables = Rentable::allFrom($user->getNif()) ?>
      <div class="creation-form">
        <h5>Adicionar oferta</h5>
        <?php if (count($rentables) == 0): ?>
            <p>De momento não arrenda nenhum alugável. Crie um para fazer uma oferta!</p>
        <?php else: ?>
          <form action="controller/create_offer.php">
            <input type="hidden" name="address" value="">
            <input type="hidden" name="code" value="">
            <div class="form-group">
              <label for="rentable">Alugável:</label>
              <select id="rentable" class="form-control">
                <?php foreach ($rentables as $rentable): ?>
                  <option data-address="<?php echo $rentable->getBuilding()->getAddress() ?>"
                          data-code="<?php echo $rentable->getCode() ?>">
                    <?php echo $rentable->getBuilding()->getAddress() ?> - <?php echo $rentable->getCode() ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group">
              <label for="start_date">Data início:</label>
              <input class="form-control" type="date" placeholder="YYYY-MM-DD" id="start_date" name="start_date">
            </div>
            <div class="form-group">
              <label for="end_date">Data fim:</label>
              <input class="form-control" type="date" placeholder="YYYY-MM-DD" id="end_date" name="end_date">
            </div>
            <div class="form-group">
              <label for="price">Tarifa:</label>
              <div class="input-group">
                <div class="input-group-addon">€</div>
                <input type="text" class="form-control" id="price" name="price" placeholder="Tarifa">
              </div>
            </div>
            <input type="submit" class="btn btn-primary btn-themed" value="Adicionar oferta">
          </form>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
<script src="js/main.js"></script>
</body>
</html>