<?php include 'session/check_login.php' ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Edifícios, Espaços e Postos</title>

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
      <li class="nav-item active"><a class="nav-link" href="index.php">Edifícios, Espaços e Postos</a></li>
      <li class="nav-item"><a class="nav-link" href="offers.php">Minhas ofertas</a></li>
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
      <h2>Edifícios</h2>
    </div>
    <div class="col-md-8">
      <?php require_once 'model/Building.php' ?>
      <?php $buildings = Building::all(); ?>
      <?php if (count($buildings) == 0): ?>
        <p>Não há edifícios!</p>
      <?php else: ?>
        <table class="table table-striped tablesorter">
          <thead>
          <tr>
            <th>Morada</th>
            <th class="no-sort">Ações</th>
          </tr>
          </thead>
          <tbody>
          <?php foreach ($buildings as $building): ?>
            <tr>
              <td><?php echo $building->getAddress(); ?></td>
              <td>
                <a href="controller/delete_building.php?address=<?php echo $building->getAddress(); ?>">
                  Remover
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>
    <div class="col-md-4">
      <div class="creation-form">
        <h5>Adicionar edifício</h5>
        <form action="controller/create_building.php">
          <div class="form-group">
            <label for="address">Morada do edifício: </label>
            <input class="form-control" type="text" id="address" name="address" placeholder="Morada do Edifício">
          </div>
          <input type="submit" class="btn btn-primary btn-themed" value="Adicionar Edificio">
        </form>
      </div>
    </div>
  </div>
  <?php if (count($buildings) > 0): ?>
    <div class="row">
      <div class="dynamic-selector">
        <div class="col-md-12">
          <div class="dynamic-selector-title">
            <h2 class="dynamic-selector-heading">Espaços do Edifício</h2>
            <select class="dynamic-selector-list form-control" title="Morada do Edificio">
              <?php foreach ($buildings as $building): ?>
                <option value="<?php echo $building->getAddress(); ?>"><?php echo $building->getAddress(); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <?php foreach ($buildings as $building): ?>
          <div class="dynamic-selector-info" data-value="<?php echo $building->getAddress() ?>">
            <div class="col-md-8">
              <?php $workspaces = $building->getWorkspaces(); ?>
              <?php if (count($workspaces) == 0): ?>
                <p>Não há espaços de trabalho!</p>
              <?php else: ?>
                <table class="table table-striped tablesorter">
                  <thead>
                  <tr>
                    <th>Código</th>
                    <th>Imagem</th>
                    <th>Total (€)</th>
                    <th class="no-sort">Ações</th>
                  </tr>
                  </thead>
                  <tbody>
                  <?php foreach ($workspaces as $workspace): ?>
                    <tr>
                      <td><?php echo $workspace->getCode() ?></td>
                      <td>
                        <a href="<?php echo $workspace->getImage() ?>">
                          <?php echo $workspace->getImage() ?>
                        </a>
                      </td>
                      <td class="number-column">
                        <?php echo $workspace->getTotal() ?>
                      </td>
                      <td>
                        <a href="controller/delete_rentable.php?address=<?php echo $building->getAddress() ?>&code=<?php echo $workspace->getCode() ?>">
                          Remover
                        </a>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                  </tbody>
                </table>
              <?php endif; ?>
            </div>
            <div class="col-md-4">
              <div class="creation-form">
                <h5>Adicionar espaço</h5>
                <form action="controller/create_workspace.php">
                  <input type="hidden" name="address" value="<?php echo $building->getAddress() ?>">
                  <input type="hidden" name="nif" value="<?php echo $user->getNif() ?>">
                  <div class="form-group">
                    <label for="code">Código do espaço:</label>
                    <input class="form-control" type="text" id="code" name="code" placeholder="Código do espaço">
                  </div>
                  <div class="form-group">
                    <label for="image">Imagem do espaço:</label>
                    <input class="form-control" type="text" id="image" name="image"
                           placeholder="URL da imagem do espaço">
                  </div>
                  <input type="submit" class="btn btn-primary btn-themed" value="Adicionar espaço">
                </form>
              </div>
            </div>
            <?php if (count($workspaces) > 0): ?>
              <div class="dynamic-selector">
                <div class="col-md-12">
                  <div class="dynamic-selector-title">
                    <h2 class="dynamic-selector-heading">Postos do Espaço</h2>
                    <select class="dynamic-selector-list form-control" title="Código do Espaço">
                      <?php foreach ($workspaces as $workspace): ?>
                        <option value="<?php echo $workspace->getCode() ?>">
                          <?php echo $workspace->getCode() ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                </div>
                <?php foreach ($workspaces as $workspace): ?>
                  <div class="dynamic-selector-info" data-value="<?php echo $workspace->getCode() ?>">
                    <div class="col-md-8">
                      <?php $workstations = $workspace->getWorkstations(); ?>
                      <?php if (count($workstations) == 0): ?>
                        <p>Não há postos de trabalho!</p>
                      <?php else: ?>
                        <table class="table table-striped tablesorter">
                          <thead>
                          <tr>
                            <th>Código</th>
                            <th>Imagem</th>
                            <th class="no-sort">Ações</th>
                          </tr>
                          </thead>
                          <tbody>
                          <?php foreach ($workstations as $workstation): ?>
                            <tr>
                              <td><?php echo $workstation->getCode() ?></td>
                              <td>
                                <a href="<?php echo $workstation->getImage() ?>">
                                  <?php echo $workstation->getImage() ?>
                                </a>
                              </td>
                              <td>
                                <a href="controller/delete_rentable.php?address=<?php echo $building->getAddress() ?>&code=<?php echo $workstation->getCode() ?>">
                                  Remover
                                </a>
                              </td>
                            </tr>
                          <?php endforeach; ?>
                          </tbody>
                        </table>
                      <?php endif; ?>
                    </div>
                    <div class="col-md-4">
                      <div class="creation-form">
                        <h5>Adicionar posto</h5>
                        <form action="controller/create_workstation.php">
                          <input type="hidden" name="address" value="<?php echo $building->getAddress() ?>">
                          <input type="hidden" name="nif" value="<?php echo $user->getNif() ?>">
                          <input type="hidden" name="workspace_code" value="<?php echo $workspace->getCode() ?>">
                          <div class="form-group">
                            <label for="code">Código do Posto:</label>
                            <input class="form-control" type="text" id="code" name="code" placeholder="Código do posto">
                          </div>
                          <div class="form-group">
                            <label for="image">Imagem do Posto:</label>
                            <input class="form-control" type="text" id="image" name="image"
                                   placeholder="URL da imagem do posto">
                          </div>
                          <input type="submit" class="btn btn-primary btn-themed" value="Adicionar posto">
                        </form>
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endif; ?>
</div>
<script src="js/main.js"></script>
</body>
</html>