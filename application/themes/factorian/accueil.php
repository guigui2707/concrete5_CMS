<?php $view->inc('elements/header.php'); ?>
<div class="container">
  <div class="row">
    <div class="col-md-12">
      <?php
        $a = new Area('Diaporama');
        $a->enableGridContainer();
        $a->display($c);
      ?>
    </div>
  </div>
</div>
<div class="container">
  <br />
  <div class="row">
    <div id="col1" class=" col-md-4">
      <div>
        <?php
          $a = new Area('col1');
          //$a->enableGridContainer();
          $a->display($c);
        ?>
      </div>
    </div>
    <div id="col2" class=" col-md-4">
      <div>
        <?php
          $a = new Area('col2');
          //$a->enableGridContainer();
          $a->display($c);
        ?>
      </div>
    </div>
    <div id="col3" class=" col-md-4">
      <div>
      <?php
        $a = new Area('col3');
        //$a->enableGridContainer();
        $a->display($c);
      ?>
    </div>
  </div>
</div>
<div id="cta">
  <div class="container">
    <div class="row">
      <div class="col-md-9">
        <?php
          $a = new Area('cta1');
          //$a->enableGridContainer();
          $a->display($c);
        ?>
      </div>
      <div class="col-md-3">
        <?php
          $a = new Area('cta2');
          //$a->enableGridContainer();
          $a->display($c);
        ?>
      </div>
    </div>
  </div>
</div>
<div id="contenu" class="container">
  <div class="row">
    <div class="col-md-offset-4 col-md-4">
      <?php
        $a = new Area('contenuTitre');
        //$a->enableGridContainer();
        $a->display($c);
      ?>
    </div>
  </div>
  <div class="row">
    <div class="col-md-4">
      <?php
        $a = new Area('contenuCol1');
        //$a->enableGridContainer();
        $a->display($c);
      ?>
    </div>
    <div class="col-md-4">
      <?php
        $a = new Area('contenuCol2');
        //$a->enableGridContainer();
        $a->display($c);
      ?>
    </div>
    <div class="col-md-4">
      <?php
        $a = new Area('contenuCol3');
        //$a->enableGridContainer();
        $a->display($c);
      ?>
    </div>
  </div>
  <div class="row">
    <div class="col-md-offset-4 col-md-4">
      <?php
        $a = new Area('contenuBas');
        //$a->enableGridContainer();
        $a->display($c);
      ?>
    </div>
  </div>
</div>
</div>
<?php $view->inc('elements/footer.php'); ?>
