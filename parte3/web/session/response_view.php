<?php // Creates a error or success block on the page. Should be included where you want the block to show. ?>
<?php if (isset($_SESSION['error']) || isset($_SESSION['success'])): ?>
  <div class="row">
    <div class="col-md-12">
      <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade in" role="alert">
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <?php echo $_SESSION['error'] ?>
          <?php unset($_SESSION['error']) ?>
        </div>
      <?php endif; ?>
      <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade in" role="alert">
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <?php echo $_SESSION['success'] ?>
          <?php unset($_SESSION['success']) ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
<?php endif; ?>